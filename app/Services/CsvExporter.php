<?php

class CsvExporter {
    public static function download(string $filename, array $headers, array $data): void {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');
        
        // UTF-8 BOM for Excel
        fputs($output, $bom = chr(0xEF) . chr(0xBB) . chr(0xBF));
        
        fputcsv($output, $headers);

        foreach ($data as $row) {
            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }
}
