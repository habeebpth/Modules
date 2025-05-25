<?php
// =======================
// FILE: Accounting/Console/Commands/RecalculateAccountBalances.php
// =======================

namespace Modules\Accounting\Console\Commands;

use Illuminate\Console\Command;
use Modules\Accounting\Entities\ChartOfAccount;
use Modules\Accounting\Services\AccountingService;

class RecalculateAccountBalances extends Command
{
    protected $signature = 'accounting:recalculate-balances {--company=}';
    protected $description = 'Recalculate all account balances from journal entries';

    public function handle()
    {
        $companyId = $this->option('company');
        $accountingService = app(AccountingService::class);

        $query = ChartOfAccount::query();
        if ($companyId) {
            $query->where('company_id', $companyId);
            $this->info("Recalculating balances for company ID: {$companyId}");
        } else {
            $this->info("Recalculating balances for all companies");
        }

        $accounts = $query->get();
        $bar = $this->output->createProgressBar($accounts->count());
        $bar->start();

        $recalculated = 0;
        foreach ($accounts as $account) {
            $oldBalance = $account->current_balance;
            $accountingService->updateAccountBalance($account->id);
            $account->refresh();

            if ($oldBalance != $account->current_balance) {
                $recalculated++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Recalculation complete! {$recalculated} account balances were updated.");
    }
}
