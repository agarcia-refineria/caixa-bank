<?php

namespace App\Console\Commands;

use App\Exports\AccountsExport;
use App\Exports\BalancesExport;
use App\Exports\TransactionsExport;
use App\Http\Controllers\ExportController;
use App\Models\Account;
use App\Models\Balance;
use App\Models\Transaction;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Exception;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportExamples extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export example files for accounts, transactions, and balances in both xlsx and csv formats';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $types = ['xlsx', 'csv'];

        foreach ($types as $type) {
            try {
                $this->exportFiles($type);
            } catch (Exception $e) {
                $this->error("Failed to export files in $type format: " . $e->getMessage());
            } catch (\Exception $e) {
                $this->error("An unexpected error occurred while exporting files in $type format: " . $e->getMessage());
            }
        }
    }

    /**
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function exportFiles(string $type): void
    {
        $accountExport = new AccountsExport();
        $accountExport->setCollection(collect([Account::getExampleModel()]));
        $file = Excel::download($accountExport, 'import_accounts.'.$type, (new ExportController)->getType($type));
        $this->saveFileDirectory($file, 'import_accounts.'.$type, $type);

        $transactionsExport = new TransactionsExport();
        $transactionsExport->setCollection(collect([Transaction::getExampleModel()]));
        $file = Excel::download($transactionsExport, 'import_transactions.'.$type, (new ExportController)->getType($type));
        $this->saveFileDirectory($file, 'import_transactions.'.$type, $type);

        $balancesExport = new BalancesExport();
        $balancesExport->setCollection(collect([Balance::getExampleModel()]));
        $file = Excel::download($balancesExport, 'import_balances.'.$type, (new ExportController)->getType($type));
        $this->saveFileDirectory($file, 'import_balances.'.$type, $type);
    }

    private function saveFileDirectory(BinaryFileResponse $file, string $fileName, string $directory): void
    {
        $file->getFile()->move(public_path($directory), $fileName);
        $this->info("File {$fileName} has been saved to public/{$directory}/{$fileName}");
        $this->line('You can now use these files as examples for your imports.');
    }
}
