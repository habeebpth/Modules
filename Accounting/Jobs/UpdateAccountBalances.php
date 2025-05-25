<?php
namespace Modules\Accounting\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Accounting\Entities\ChartOfAccount;
use Modules\Accounting\Services\AccountingService;

class UpdateAccountBalances implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $companyId;
    protected $accountIds;

    public function __construct($companyId, $accountIds = null)
    {
        $this->companyId = $companyId;
        $this->accountIds = $accountIds;
    }

    public function handle()
    {
        $accountingService = app(AccountingService::class);

        $query = ChartOfAccount::where('company_id', $this->companyId);

        if ($this->accountIds) {
            $query->whereIn('id', $this->accountIds);
        }

        $accounts = $query->get();

        foreach ($accounts as $account) {
            $accountingService->updateAccountBalance($account->id);
        }
    }
}
