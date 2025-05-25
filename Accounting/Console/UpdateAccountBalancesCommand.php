<?php
namespace Modules\Accounting\Console;

use Illuminate\Console\Command;
use Modules\Accounting\Services\AccountingService;
use Modules\Accounting\Entities\ChartOfAccount;

class UpdateAccountBalancesCommand extends Command
{
    protected $signature = 'accounting:update-balances {--company-id=}';
    protected $description = 'Update all account balances from journal entries';

    public function handle()
    {
        $companyId = $this->option('company-id');
        $accountingService = app(AccountingService::class);

        $query = ChartOfAccount::query();
        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        $accounts = $query->get();
        $bar = $this->output->createProgressBar($accounts->count());

        foreach ($accounts as $account) {
            $accountingService->updateAccountBalance($account->id);
            $bar->advance();
        }

        $bar->finish();
        $this->info("\nAccount balances updated successfully!");
    }
}
