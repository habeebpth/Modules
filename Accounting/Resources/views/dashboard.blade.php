@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="d-flex">
        <div class="w-100">
            <!-- STATS CARDS -->
            <div class="row">
                <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
                    <x-cards.widget :title="__('Total Accounts')" :value="$totalAccounts" icon="chart-line" />
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
                    <x-cards.widget :title="__('Posted Journals')" :value="$totalJournals" icon="book" />
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
                    <x-cards.widget :title="__('Assets')" :value="currency_format($assets)" icon="building" />
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
                    <x-cards.widget :title="__('Revenue')" :value="currency_format($revenue)" icon="dollar-sign" />
                </div>
            </div>

            <!-- FINANCIAL SUMMARY -->
            <div class="row">
                <div class="col-md-6">
                    <x-cards.data :title="__('Financial Position')" padding="false" otherClasses="h-300">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <tbody>
                                    <tr>
                                        <td class="f-14 text-dark-grey">@lang('Assets')</td>
                                        <td class="text-right f-14 f-w-500">{{ currency_format($assets) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="f-14 text-dark-grey">@lang('Liabilities')</td>
                                        <td class="text-right f-14 f-w-500">{{ currency_format($liabilities) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="f-14 text-dark-grey">@lang('Equity')</td>
                                        <td class="text-right f-14 f-w-500">{{ currency_format($equity) }}</td>
                                    </tr>
                                    <tr class="border-top">
                                        <td class="f-14 f-w-500">@lang('Total Liabilities + Equity')</td>
                                        <td class="text-right f-14 f-w-500">{{ currency_format($liabilities + $equity) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </x-cards.data>
                </div>
                
                <div class="col-md-6">
                    <x-cards.data :title="__('Income Summary')" padding="false" otherClasses="h-300">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <tbody>
                                    <tr>
                                        <td class="f-14 text-dark-grey">@lang('Revenue')</td>
                                        <td class="text-right f-14 f-w-500 text-success">{{ currency_format($revenue) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="f-14 text-dark-grey">@lang('Expenses')</td>
                                        <td class="text-right f-14 f-w-500 text-danger">{{ currency_format($expenses) }}</td>
                                    </tr>
                                    <tr class="border-top">
                                        <td class="f-14 f-w-500">@lang('Net Income')</td>
                                        <td class="text-right f-14 f-w-500 {{ ($revenue - $expenses) >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ currency_format($revenue - $expenses) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </x-cards.data>
                </div>
            </div>

            <!-- QUICK ACTIONS -->
            <div class="row">
                <div class="col-md-12">
                    <x-cards.data :title="__('Quick Actions')">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <a href="{{ route('accounting.journals.create') }}" class="btn btn-primary btn-block openRightModal">
                                    <i class="fa fa-plus mr-2"></i>@lang('Create Journal Entry')
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="{{ route('accounting.chart-of-accounts.create') }}" class="btn btn-success btn-block openRightModal">
                                    <i class="fa fa-plus mr-2"></i>@lang('Add Account')
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="{{ route('accounting.reports.trial-balance') }}" class="btn btn-info btn-block">
                                    <i class="fa fa-chart-bar mr-2"></i>@lang('Trial Balance')
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="{{ route('accounting.reports.balance-sheet') }}" class="btn btn-warning btn-block">
                                    <i class="fa fa-balance-scale mr-2"></i>@lang('Balance Sheet')
                                </a>
                            </div>
                        </div>
                    </x-cards.data>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection