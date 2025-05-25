<?php

namespace Modules\Accounting\Services;

use Modules\Accounting\Entities\ChartOfAccount;
use Modules\Accounting\Entities\Journal;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportExportService
{
    public function importChartOfAccounts($file)
    {
        $spreadsheet = IOFactory::load($file->getRealPath());
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        // Skip header row
        array_shift($rows);

        $imported = 0;
        $errors = [];

        foreach ($rows as $index => $row) {
            try {
                if (empty($row[0])) continue; // Skip empty rows

                ChartOfAccount::create([
                    'company_id' => user()->company_id,
                    'account_code' => $row[0],
                    'account_name' => $row[1],
                    'account_type' => $row[2],
                    'account_sub_type' => $row[3],
                    'description' => $row[4] ?? null,
                    'opening_balance' => $row[5] ?? 0,
                    'current_balance' => $row[5] ?? 0,
                    'is_active' => true
                ]);

                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
            }
        }

        return [
            'imported' => $imported,
            'errors' => $errors
        ];
    }

    public function exportChartOfAccounts()
    {
        $accounts = ChartOfAccount::where('company_id', user()->company_id)->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Headers
        $sheet->setCellValue('A1', 'Account Code');
        $sheet->setCellValue('B1', 'Account Name');
        $sheet->setCellValue('C1', 'Account Type');
        $sheet->setCellValue('D1', 'Account Sub Type');
        $sheet->setCellValue('E1', 'Description');
        $sheet->setCellValue('F1', 'Current Balance');
        $sheet->setCellValue('G1', 'Status');

        // Data
        $row = 2;
        foreach ($accounts as $account) {
            $sheet->setCellValue('A' . $row, $account->account_code);
            $sheet->setCellValue('B' . $row, $account->account_name);
            $sheet->setCellValue('C' . $row, $account->account_type);
            $sheet->setCellValue('D' . $row, $account->account_sub_type);
            $sheet->setCellValue('E' . $row, $account->description);
            $sheet->setCellValue('F' . $row, $account->current_balance);
            $sheet->setCellValue('G' . $row, $account->is_active ? 'Active' : 'Inactive');
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'chart_of_accounts_' . date('Y-m-d_H-i-s') . '.xlsx';
        $path = storage_path('app/' . $filename);
        $writer->save($path);

        return response()->download($path)->deleteFileAfterSend();
    }

    public function exportTrialBalance($asOfDate)
    {
        // Implementation for trial balance export
        // Similar structure to chart of accounts export
    }
}
