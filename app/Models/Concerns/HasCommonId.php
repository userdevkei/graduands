<?php

namespace App\Models\Concerns;

use App\Services\Common; // <-- adjust if your Common class lives elsewhere

/**
 * Every module ID in this system comes from Common::IDGenerator().
 */
trait HasCommonId
{
    public static function bootHasCommonId(): void
    {
        static::creating(function ($model) {
            $key = $model->getKeyName();
            if (empty($model->{$key})) {
                $model->{$key} = (new Common())->IDGenerator();
            }
        });
    }

    public function getIncrementing(): bool
    {
        return false;
    }

    public function getKeyType(): string
    {
        return 'string';
    }
}
