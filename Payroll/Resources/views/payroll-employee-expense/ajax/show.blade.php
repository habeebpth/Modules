<div id="salary-advance-section">
    <h3 class="heading-h1 mb-3">{{ $EmployeeExpense->employeeuser->name }}</h3>
    <div class="row">
        <div class="col-sm-12">
            <div class="card bg-white border-0 b-shadow-4">
                <div class="card-body">
                    <div class="col-12 px-0 pb-3 d-block d-lg-flex d-md-flex">
                        <p class="mb-0 text-lightest f-14 w-30 d-inline-block text-capitalize">Expense Type</p>
                        <p class="mb-0 text-dark-grey f-14 w-70">{{ $EmployeeExpense->expense_type ?? '--' }}</p>
                    </div>

                    <div class="col-12 px-0 pb-3 d-block d-lg-flex d-md-flex">
                        <p class="mb-0 text-lightest f-14 w-30 d-inline-block text-capitalize">Employee</p>
                        <p class="mb-0 text-dark-grey f-14 w-70">{{ $EmployeeExpense->employeeuser->name ?? '--' }}</p>
                    </div>

                    <div class="col-12 px-0 pb-3 d-block d-lg-flex d-md-flex">
                        <p class="mb-0 text-lightest f-14 w-30 d-inline-block text-capitalize">Expense Date</p>
                        <p class="mb-0 text-dark-grey f-14 w-70">{{ $EmployeeExpense->expense_date ?? '--' }}</p>
                    </div>

                    <div class="col-12 px-0 pb-3 d-block d-lg-flex d-md-flex">
                        <p class="mb-0 text-lightest f-14 w-30 d-inline-block text-capitalize">Amount</p>
                        <p class="mb-0 text-dark-grey f-14 w-70">{{ $EmployeeExpense->amount ?? '--' }}</p>
                    </div>
                </div>
            </div>

            <!-- EmployeeExpense TABS START -->
            <div class="bg-additional-grey rounded my-3">
                <div class="s-b-inner s-b-notifications bg-white b-shadow-4 rounded">
                    <div class="s-b-n-content">
                        <div class="tab-content" id="nav-tabContent">
                            <div class="table-responsive p-20">
                                <x-table class="table-bordered">
                                    <x-slot name="thead">
                                        <th>#</th>
                                        <th>@lang('app.paidDate')</th>
                                        <th>@lang('app.dueDate')</th>
                                        <th>@lang('app.amount')</th>
                                        <th>@lang('app.addedby')</th>
                                        <th>@lang('app.updatedby')</th>
                                        <th class="text-right">@lang('app.action')</th>
                                    </x-slot>

                                    @forelse($advancerepayment as $key => $repayment)
                                        <tr class="row{{ $repayment->id }}">
                                            <td>{{ ($key+1) }}</td>
                                            <td>{{ $repayment->paid_date }}</td>
                                            <td>{{ $repayment->due_date }}</td>
                                            <td>{{ $repayment->amount }}</td>
                                            <td>{{ $repayment->addedby->name }}</td>
                                            <td>{{ $repayment->lastupdatedby->name }}</td>
                                            <td class="text-right">
                                                <div class="task_view mt-1 mt-lg-0 mt-md-0">
                                                    <a href="{{ route('employee-expense.repayment.edit', $repayment->id) }}"
                                                        data-repayment-id="{{ $repayment->id }}"
                                                        class="task_view_more d-flex align-items-center justify-content-center openRightModal dropdown-toggle edit-repayment">
                                                         <i class="fa fa-edit icons mr-2"></i> @lang('app.edit')
                                                     </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8">
                                                <x-cards.no-record icon="list" :message="__('messages.noRepaymentAdded')" />
                                            </td>
                                        </tr>
                                    @endforelse
                                </x-table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- EmployeeExpense TABS END -->
        </div>
    </div>
    <script src="{{ asset('vendor/jquery/clipboard.min.js') }}"></script>
    <script>
    </script>
</div>
