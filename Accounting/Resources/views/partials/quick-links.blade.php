<div class="row mb-4">
    <div class="col-md-12">
        <x-cards.data :title="__('accounting::app.quickActions')" class="quick-actions-card">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <a href="{{ route('accounting.journals.create') }}" class="btn btn-primary btn-block openRightModal">
                        <i class="fa fa-plus mr-2"></i>@lang('accounting::app.newJournalEntry')
                    </a>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="{{ route('accounting.chart-of-accounts.create') }}" class="btn btn-success btn-block openRightModal">
                        <i class="fa fa-plus mr-2"></i>@lang('accounting::app.addAccount')
                    </a>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="{{ route('accounting.budgets.create') }}" class="btn btn-info btn-block">
                        <i class="fa fa-chart-line mr-2"></i>@lang('accounting::app.createBudget')
                    </a>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="{{ route('accounting.reconciliations.create') }}" class="btn btn-warning btn-block">
                        <i class="fa fa-balance-scale mr-2"></i>@lang('accounting::app.bankReconciliation')
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3 mb-3">
                    <a href="{{ route('accounting.reports.trial-balance') }}" class="btn btn-outline-primary btn-block">
                        <i class="fa fa-list mr-2"></i>@lang('accounting::app.trialBalance')
                    </a>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="{{ route('accounting.reports.balance-sheet') }}" class="btn btn-outline-success btn-block">
                        <i class="fa fa-chart-bar mr-2"></i>@lang('accounting::app.balanceSheet')
                    </a>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="{{ route('accounting.reports.income-statement') }}" class="btn btn-outline-info btn-block">
                        <i class="fa fa-chart-area mr-2"></i>@lang('accounting::app.incomeStatement')
                    </a>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="{{ route('accounting.settings.index') }}" class="btn btn-outline-secondary btn-block">
                        <i class="fa fa-cog mr-2"></i>@lang('accounting::app.settings')
                    </a>
                </div>
            </div>
        </x-cards.data>
    </div>
</div>
