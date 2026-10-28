<?php

namespace App\Models;

use App\Models\Concerns\HasCommonId;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportRow extends Model
{
    use HasCommonId;

    protected $fillable = ['import_batch_id', 'row_number', 'data', 'status', 'errors'];

    protected $casts = ['data' => 'array', 'errors' => 'array'];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(ImportBatch::class, 'import_batch_id');
    }
}
