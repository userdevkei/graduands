<?php

namespace App\Support;

use Illuminate\Support\Str;

/** Turns audit-log action keys into human labels, an icon and a tone for the UI. */
class ActionLabel
{
    private const MAP = [
        'student.lookup_success'   => ['Student opened their record', 'bi-box-arrow-in-right', 'muted'],
        'student.lookup_failed'    => ['Failed student lookup', 'bi-exclamation-triangle', 'danger'],
        'student.confirmed_record' => ['Student confirmed details', 'bi-check2-circle', 'ok'],
        'student.updated_record'   => ['Student updated details', 'bi-pencil-square', 'info'],
        'admin.login'              => ['Admin signed in', 'bi-shield-lock', 'muted'],
        'admin.logout'             => ['Admin signed out', 'bi-door-open', 'muted'],
        'admin.updated_record'     => ['Record edited by registry', 'bi-pencil', 'info'],
        'admin.unlocked_record'    => ['Record unlocked', 'bi-unlock', 'warn'],
        'import.staged'            => ['Import uploaded', 'bi-upload', 'muted'],
        'import.committed'         => ['Import completed', 'bi-database-check', 'ok'],
        'import.cancelled'         => ['Import cancelled', 'bi-x-circle', 'muted'],
        'export.graduands'         => ['List exported', 'bi-download', 'muted'],
        'cohort.created'           => ['Cohort created', 'bi-plus-circle', 'ok'],
        'cohort.updated'           => ['Cohort updated', 'bi-pencil', 'info'],
        'cohort.deleted'           => ['Cohort deleted', 'bi-trash', 'danger'],
    ];

    /** @return array{label:string, icon:string, tone:string} */
    public static function for(string $action): array
    {
        [$label, $icon, $tone] = self::MAP[$action] ?? [Str::headline($action), 'bi-circle', 'muted'];

        return compact('label', 'icon', 'tone');
    }
}
