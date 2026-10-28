<?php

namespace App\Services;

use App\Models\Graduand;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GraduandExportService
{
    public function download(Builder $query): StreamedResponse
    {
        $filename = 'graduands-'.now()->format('Ymd-His').'.csv';

        ActivityLogService::log('export.graduands', null, null, ['filename' => $filename]);

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fputcsv($out, [
                'Registration Number', 'First Name', 'Middle Name', 'Last Name', 'Name On Certificate',
                'Gender', 'Date Of Birth', 'Nationality', 'National ID/Passport', 'Email', 'Phone',
                'Programme', 'Award', 'Classification', 'Cohort', 'Status', 'Submitted At',
            ]);

            $query->with('cohort')->orderBy('last_name')->orderBy('first_name')->chunk(500, function ($rows) use ($out) {
                foreach ($rows as $g) {
                    /** @var Graduand $g */
                    fputcsv($out, array_map([$this, 'safe'], [
                        $g->registration_number, $g->first_name, $g->middle_name, $g->last_name, $g->full_name,
                        $g->gender, $g->date_of_birth?->format('Y-m-d'), $g->nationality, $g->national_id_number,
                        $g->email, $g->phone, $g->programme, $g->award, $g->classification,
                        $g->cohort?->name, $g->status, $g->submitted_at?->format('Y-m-d H:i'),
                    ]));
                }
            });

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    /** Stop spreadsheet formula injection from student-entered values. */
    private function safe(?string $value): ?string
    {
        return ($value !== null && preg_match('/^[=+\-@\t\r]/', $value)) ? "'".$value : $value;
    }
}
