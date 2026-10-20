<?php

namespace App\Support;

use RuntimeException;
use SimpleXMLElement;
use ZipArchive;

final class ImportCodeWorkbook
{
    /**
     * Read every column headed IMPORT CODE from an .xlsx workbook.
     *
     * The workbook may contain several worksheets. Each non-empty import code
     * is expected to contain five comma-separated values:
     * student_id, midquarter, final, effort, deportment.
     */
    public static function read(string $path): array
    {
        $zip = new ZipArchive;

        if ($zip->open($path) !== true) {
            throw new RuntimeException('The uploaded file is not a readable Excel workbook.');
        }

        try {
            $sharedStrings = self::sharedStrings($zip);
            $records = [];

            for ($index = 0; $index < $zip->numFiles; $index++) {
                $name = $zip->getNameIndex($index);

                if (! is_string($name) || ! preg_match('#^xl/worksheets/sheet\d+\.xml$#', $name)) {
                    continue;
                }

                $xml = $zip->getFromName($name);

                if ($xml === false) {
                    continue;
                }

                $records = array_merge($records, self::readSheet($xml, $sharedStrings));
            }

            if ($records === []) {
                throw new RuntimeException('No non-empty IMPORT CODE values were found in the workbook.');
            }

            return $records;
        } finally {
            $zip->close();
        }
    }

    private static function sharedStrings(ZipArchive $zip): array
    {
        $xml = $zip->getFromName('xl/sharedStrings.xml');

        if ($xml === false) {
            return [];
        }

        $document = self::parseXml($xml);
        $strings = [];

        foreach ($document->si as $item) {
            $strings[] = self::textValue($item);
        }

        return $strings;
    }

    private static function readSheet(string $xml, array $sharedStrings): array
    {
        $document = self::parseXml($xml);
        $headerColumn = null;
        $records = [];

        foreach ($document->sheetData->row as $row) {
            $values = [];

            foreach ($row->c as $cell) {
                $reference = (string) ($cell['r'] ?? '');
                $column = preg_replace('/\d+$/', '', $reference);

                if (! is_string($column) || $column === '') {
                    continue;
                }

                $values[$column] = self::cellValue($cell, $sharedStrings);

                if (strcasecmp(trim($values[$column]), 'IMPORT CODE') === 0) {
                    $headerColumn = $column;
                }
            }

            if ($headerColumn === null || ! array_key_exists($headerColumn, $values)) {
                continue;
            }

            $code = trim($values[$headerColumn]);

            if ($code === '' || $code === '.' || ! str_contains($code, ',')) {
                continue;
            }

            $parts = array_map('trim', explode(',', $code));

            if (count($parts) < 5) {
                throw new RuntimeException('An IMPORT CODE value must contain student ID, midquarter, final, effort, and deportment.');
            }

            if ($parts[0] === '') {
                continue;
            }

            $records[] = [
                'student_id' => $parts[0],
                'midquarter' => self::emptyValue($parts[1]),
                'final' => self::emptyValue($parts[2]),
                'effort' => self::emptyValue($parts[3]),
                'deportment' => self::emptyValue($parts[4]),
            ];
        }

        return $records;
    }

    private static function cellValue(SimpleXMLElement $cell, array $sharedStrings): string
    {
        $type = (string) ($cell['t'] ?? '');

        if ($type === 'inlineStr') {
            return self::textValue($cell->is);
        }

        $value = (string) ($cell->v ?? '');

        if ($type === 's' && $value !== '' && isset($sharedStrings[(int) $value])) {
            return $sharedStrings[(int) $value];
        }

        return $value;
    }

    private static function textValue(SimpleXMLElement $node): string
    {
        $text = '';

        foreach ($node->xpath('.//*[local-name()="t"]') ?: [] as $part) {
            $text .= (string) $part;
        }

        return $text;
    }

    private static function emptyValue(string $value): ?string
    {
        return $value === '' || $value === '.' ? null : $value;
    }

    private static function parseXml(string $xml): SimpleXMLElement
    {
        $document = simplexml_load_string($xml);

        if (! $document instanceof SimpleXMLElement) {
            throw new RuntimeException('The uploaded workbook contains an unreadable worksheet.');
        }

        return $document;
    }
}
