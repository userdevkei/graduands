<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

/**
 * Used with Excel::toArray() to read a sheet with a heading row.
 * ToArray is what newer Laravel Excel versions require for the $import argument;
 * array() is never called because Excel::toArray() returns the rows directly.
 */
class RowsImport implements ToArray, WithHeadingRow
{
    public function array(array $array): void
    {
    }
}
