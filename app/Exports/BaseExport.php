<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class BaseExport implements FromArray, WithHeadings, WithCustomCsvSettings
{
    protected array $rows = [];

    protected array $headings = [];

    public function __construct(array $rows = [], array $headings = [])
    {
        $this->rows = $rows;
        $this->headings = $headings;
    }

    /**
     * Normalize and return rows for export.
     *
     * @return array
     */
    public function array(): array
    {
        return array_map(fn($r) => $this->normalizeRow($r), $this->rows);
    }

    public function headings(): array
    {
        return $this->headings;
    }

    /**
     * Maatwebsite CSV settings. `use_bom` ensures Excel treats CSV as UTF-8.
     *
     * @return array
     */
    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ',',
            'enclosure' => '"',
            'line_ending' => PHP_EOL,
            'use_bom' => true,
        ];
    }

    /**
     * Recursively normalize row values to UTF-8 strings.
     *
     * @param mixed $row
     * @return mixed
     */
    protected function normalizeRow(mixed $row): mixed
    {
        if (is_array($row)) {
            return array_map(fn($v) => $this->normalizeRow($v), $row);
        }

        if (is_string($row)) {
            $enc = mb_detect_encoding($row, ['UTF-8', 'Windows-1252', 'ISO-8859-1', 'ASCII'], true);
            if ($enc !== 'UTF-8') {
                return mb_convert_encoding($row, 'UTF-8', $enc ?: 'UTF-8');
            }
            // Ensure string is valid UTF-8
            if (!mb_check_encoding($row, 'UTF-8')) {
                return mb_convert_encoding($row, 'UTF-8', 'UTF-8');
            }
            return $row;
        }

        return $row;
    }
}
