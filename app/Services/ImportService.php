<?php

namespace App\Services;

use App\Services\Common; // <-- adjust if your Common class lives elsewhere
use App\Imports\RowsImport;
use App\Models\Cohort;
use App\Models\Graduand;
use App\Models\ImportBatch;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ImportService
{
    public const COLUMNS = [
        'registration_number', 'first_name', 'middle_name', 'last_name', 'gender',
        'date_of_birth', 'nationality', 'national_id_number', 'email', 'phone',
        'programme', 'award', 'classification',
    ];

    /**
     * Step 1 - read the file into staging tables. Nothing touches `graduands` yet.
     */
    public function stage(UploadedFile $file, Cohort $cohort, ?int $userId): ImportBatch
    {
        $sheet = Excel::toArray(new RowsImport(), $file)[0] ?? [];
        $common = new Common();

        $existing = array_flip(array_map('mb_strtolower', Graduand::pluck('registration_number')->all()));
        $seen = [];
        $rows = [];
        $counts = ['valid' => 0, 'duplicate' => 0, 'invalid' => 0];

        foreach ($sheet as $index => $raw) {
            if (! array_filter($raw, fn ($v) => filled($v))) {
                continue; // skip blank lines
            }

            $rowNumber = $index + 2; // header is row 1
            $data = $this->normalise($raw);
            $key = mb_strtolower($data['registration_number'] ?? '');

            $validator = Validator::make($data, [
                'registration_number' => ['required', 'string', 'max:100'],
                'first_name' => ['required', 'string', 'max:100'],
                'middle_name' => ['nullable', 'string', 'max:150'],
                'last_name' => ['required', 'string', 'max:100'],
                'gender' => ['nullable', 'in:Male,Female,Other'],
                'date_of_birth' => ['nullable', 'date_format:Y-m-d'],
                'nationality' => ['nullable', 'string', 'max:100'],
                'national_id_number' => ['nullable', 'string', 'max:100'],
                'email' => ['nullable', 'email', 'max:150'],
                'phone' => ['nullable', 'string', 'max:50'],
                'programme' => ['required', 'string', 'max:255'],
                'award' => ['nullable', 'string', 'max:255'],
                'classification' => ['nullable', 'string', 'max:100'],
            ]);

            $errors = null;
            if ($validator->fails()) {
                $status = 'invalid';
                $errors = $validator->errors()->all();
            } elseif (isset($existing[$key])) {
                $status = 'duplicate';
                $errors = ['Registration number already exists in the system.'];
            } elseif (isset($seen[$key])) {
                $status = 'duplicate';
                $errors = ["Repeated in this file (first seen on row {$seen[$key]})."];
            } else {
                $status = 'valid';
                $seen[$key] = $rowNumber;
            }

            $counts[$status]++;
            $rows[] = [
                'row_number' => $rowNumber,
                'data' => json_encode($data),
                'status' => $status,
                'errors' => $errors ? json_encode($errors) : null,
            ];
        }

        return DB::transaction(function () use ($file, $cohort, $userId, $rows, $counts, $common) {
            $batch = ImportBatch::create([
                'cohort_id' => $cohort->id,
                'user_id' => $userId,
                'original_filename' => $file->getClientOriginalName(),
                'status' => 'staged',
                'total_rows' => count($rows),
                'valid_rows' => $counts['valid'],
                'duplicate_rows' => $counts['duplicate'],
                'invalid_rows' => $counts['invalid'],
            ]);

            foreach (array_chunk($rows, 500) as $chunk) {
                DB::table('import_rows')->insert(array_map(fn ($r) => $r + [
                    'id' => $common->IDGenerator(),
                    'import_batch_id' => $batch->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ], $chunk));
            }

            ActivityLogService::log('import.staged', $batch, null, [
                'file' => $batch->original_filename,
                'valid' => $counts['valid'],
                'duplicate' => $counts['duplicate'],
                'invalid' => $counts['invalid'],
            ]);

            return $batch;
        });
    }

    /**
     * Step 2 - after the admin has previewed, save the valid rows into `graduands`.
     *
     * @return array{inserted:int, skipped:int}
     */
    public function commit(ImportBatch $batch): array
    {
        abort_unless($batch->isStaged(), 422, 'This import has already been processed.');

        return DB::transaction(function () use ($batch) {
            $common = new Common();
            $inserted = 0;
            $skipped = 0;

            $batch->rows()->where('status', 'valid')->orderBy('row_number')->chunk(500, function ($chunk) use ($batch, $common, &$inserted, &$skipped) {
                // re-check: someone may have added the same registration numbers since the preview
                $taken = array_flip(array_map('mb_strtolower', Graduand::whereIn(
                    'registration_number',
                    $chunk->pluck('data.registration_number')->all()
                )->pluck('registration_number')->all()));

                $insert = [];
                foreach ($chunk as $row) {
                    $d = $row->data;
                    if (isset($taken[mb_strtolower($d['registration_number'])])) {
                        $skipped++;
                        continue;
                    }
                    $insert[] = $d + [
                        'id' => $common->IDGenerator(),
                        'cohort_id' => $batch->cohort_id,
                        'portal_first_name' => $d['first_name'],
                        'status' => Graduand::STATUS_PENDING,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                if ($insert) {
                    DB::table('graduands')->insert($insert);
                    $inserted += count($insert);
                }
            });

            $batch->update(['status' => 'committed', 'committed_at' => now()]);

            ActivityLogService::log('import.committed', $batch, null, [
                'inserted' => $inserted,
                'skipped' => $skipped,
            ]);

            return compact('inserted', 'skipped');
        });
    }

    public function cancel(ImportBatch $batch): void
    {
        abort_unless($batch->isStaged(), 422, 'This import has already been processed.');

        $batch->rows()->delete();
        $batch->update(['status' => 'cancelled']);

        ActivityLogService::log('import.cancelled', $batch);
    }

    private function normalise(array $raw): array
    {
        $out = [];
        foreach (self::COLUMNS as $column) {
            $v = $raw[$column] ?? null;
            $v = is_string($v) ? trim($v) : $v;
            $out[$column] = ($v === '' || $v === null) ? null : (is_scalar($v) ? (string) $v : null);
        }

        $out['date_of_birth'] = $this->parseDate($out['date_of_birth']);
        $out['gender'] = $out['gender'] ? ucfirst(mb_strtolower($out['gender'])) : null;

        return $out;
    }

    /**
     * Accepts Excel serial dates and day-first text dates (15/04/2000, 15-04-2000, 15.04.2000, 2000-04-15).
     * Always returns Y-m-d (what the database stores); anything unreadable is returned as-is so validation flags it.
     */
    private function parseDate(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        try {
            if (is_numeric($value)) {
                return Date::excelToDateTimeObject((float) $value)->format('Y-m-d');
            }

            foreach (['Y-m-d', 'd/m/Y', 'd-m-Y', 'd.m.Y', 'j/n/Y', 'j-n-Y', 'j.n.Y'] as $format) {
                if (Carbon::hasFormat($value, $format)) {
                    return Carbon::createFromFormat($format, $value)->format('Y-m-d');
                }
            }
        } catch (\Throwable) {
            // fall through
        }

        return $value;
    }
}
