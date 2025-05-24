<div class="row">
    <div class="col-sm-12">
        <x-form id="update-journal-entry-data-form" method="PUT">
            <div class="edit-journal-entry bg-white rounded">
                <h4 class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-bottom-grey">
                    @lang('app.editJournalEntry')</h4>

                <!-- Entry Details Section -->
                <div class="row p-20">
                    <!-- Reference Number -->
                    <div class="col-md-4">
                        <x-forms.text fieldId="reference_number" :fieldLabel="__('app.referenceNumber')" fieldName="reference_number" fieldRequired="true"
                            :fieldValue="$journalEntry->reference_number" fieldReadOnly="true">
                        </x-forms.text>
                    </div>

                    <!-- Entry Date -->
                    <div class="col-md-4">
                        <x-forms.datepicker fieldId="entry_date" :fieldLabel="__('app.date')" fieldName="entry_date" fieldRequired="true"
                            :fieldValue="$journalEntry->entry_date->format(company()->date_format)" />
                    </div>
                </div>

                <!-- Entry Description -->
                <div class="row px-4">
                    <div class="col-md-12">
                        <x-forms.textarea fieldId="description" :fieldLabel="__('app.description')" fieldName="description"
                            fieldRequired="false" :fieldPlaceholder="__('placeholders.description')" :fieldValue="$journalEntry->description">
                        </x-forms.textarea>
                    </div>
                </div>

                <!-- Journal Entry Items Section -->
                <div class="row p-20">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="journal-entry-items">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="30%">@lang('app.account')</th>
                                        <th width="25%">@lang('app.description')</th>
                                        <th width="15%">@lang('app.debit')</th>
                                        <th width="15%">@lang('app.credit')</th>
                                        <th width="10%">@lang('app.action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($journalEntry->items as $index => $item)
                                        <tr class="item-row" id="item-row-{{ $index + 1 }}">
                                            <td>
                                                <div class="form-group my-0">
                                                    <x-forms.select fieldId="account_id_{{ $index + 1 }}" fieldName="account_id[]" fieldRequired="true"
                                                        search="true">
                                                        <option value="">@lang('app.select') @lang('app.account')</option>
                                                        @foreach($accounts as $account)
                                                            <option value="{{ $account->id }}" 
                                                                @if($item->account_id == $account->id) selected @endif>
                                                                {{ $account->name }} ({{ $account->code }})
                                                            </option>
                                                        @endforeach
                                                    </x-forms.select>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group my-0">
                                                    <input type="text" class="form-control f-14" name="item_description[]"
                                                        placeholder="@lang('app.description')" value="{{ $item->description }}">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group my-0">
                                                    <input type="number" min="0" step="0.01" class="form-control f-14 debit-amount"
                                                        name="debit[]" placeholder="0.00" value="{{ $item->debit }}">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group my-0">
                                                    <input type="number" min="0" step="0.01" class="form-control f-14 credit-amount"
                                                        name="credit[]" placeholder="0.00" value="{{ $item->credit }}">
                                                </div>
                                            </td>
                                            <td>
                                                <a href="javascript:;" class="btn btn-danger btn-sm delete-item">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2" class="text-right">
                                            <button type="button" class="btn btn-secondary add-item">
                                                <i class="fa fa-plus"></i> @lang('app.addItem')
                                            </button>
                                        </td>
                                        <td>
                                            <div class="form-group my-0">
                                                <input type="text" class="form-control f-14 total-debit" placeholder="0.00" readonly>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group my-0">
                                                <input type="text" class="form-control f-14 total-credit" placeholder="0.00" readonly>
                                            </div>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="text-right">
                                            <strong>@lang('app.difference')</strong>
                                        </td>
                                        <td colspan="2">
                                            <div class="form-group my-0">
                                                <input type="text" class="form-control f-14 difference" placeholder="0.00" readonly>
                                            </div>
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <x-form-actions>
                    <x-forms.button-primary id="update-journal-entry-form" class="mr-3" icon="check">@lang('app.update')
                    </x-forms.button-primary>
                    <x-forms.button-cancel :link="route('journal-entries.index')" class="border-0">@lang('app.cancel')
                    </x-forms.button-cancel>
                </x-form-actions>
            </div>
        </x-form>
    </div>
</div>


<script>
    $(document).ready(function() {
        // Initialize datepicker
        $('.custom-date-picker').each(function(ind, el) {
            datepicker(el, {
                position: 'bl',
                ...datepickerConfig
            });
        });
        
        // Counter for adding new items
        let itemRowCounter = {{ count($journalEntry->items) }};
        
        // Add new item row
        $('.add-item').click(function() {
            itemRowCounter++;
            
            const itemRow = `<tr class="item-row" id="item-row-${itemRowCounter}">
                <td>
                    <div class="form-group my-0">
                        <select class="form-control select-picker" name="account_id[]" id="account_id_${itemRowCounter}" data-live-search="true" required>
                            <option value="">@lang('app.select') @lang('app.account')</option>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}">{{ $account->name }} ({{ $account->code }})</option>
                            @endforeach
                        </select>
                    </div>
                </td>
                <td>
                    <div class="form-group my-0">
                        <input type="text" class="form-control f-14" name="item_description[]" placeholder="@lang('app.description')">
                    </div>
                </td>
                <td>
                    <div class="form-group my-0">
                        <input type="number" min="0" step="0.01" class="form-control f-14 debit-amount" name="debit[]" placeholder="0.00" value="0">
                    </div>
                </td>
                <td>
                    <div class="form-group my-0">
                        <input type="number" min="0" step="0.01" class="form-control f-14 credit-amount" name="credit[]" placeholder="0.00" value="0">
                    </div>
                </td>
                <td>
                    <a href="javascript:;" class="btn btn-danger btn-sm delete-item">
                        <i class="fa fa-trash"></i>
                    </a>
                </td>
            </tr>`;
            
            $('#journal-entry-items tbody').append(itemRow);
            $(`#account_id_${itemRowCounter}`).selectpicker();
            
            calculateTotals();
        });
        
        // Delete item row
        $(document).on('click', '.delete-item', function() {
            const rowCount = $('.item-row').length;
            
            if (rowCount > 2) {
                $(this).closest('tr').remove();
                calculateTotals();
            } else {
                Swal.fire({
                    icon: 'warning',
                    text: "@lang('messages.minimumTwoItemsRequired')",
                    showConfirmButton: false,
                    timer: 2000
                });
            }
        });
        
        // Calculate totals when debit or credit amount changes
        $(document).on('input', '.debit-amount, .credit-amount', function() {
            // Auto-zero the other field when entering a value
            const $row = $(this).closest('tr');
            
            if ($(this).hasClass('debit-amount') && $(this).val() > 0) {
                $row.find('.credit-amount').val(0);
            } else if ($(this).hasClass('credit-amount') && $(this).val() > 0) {
                $row.find('.debit-amount').val(0);
            }
            
            calculateTotals();
        });
        
        // Calculate totals function
        function calculateTotals() {
            let totalDebit = 0;
            let totalCredit = 0;
            
            $('.debit-amount').each(function() {
                const amount = parseFloat($(this).val()) || 0;
                totalDebit += amount;
            });
            
            $('.credit-amount').each(function() {
                const amount = parseFloat($(this).val()) || 0;
                totalCredit += amount;
            });
            
            $('.total-debit').val(totalDebit.toFixed(2));
            $('.total-credit').val(totalCredit.toFixed(2));
            
            const difference = Math.abs(totalDebit - totalCredit).toFixed(2);
            $('.difference').val(difference);
            
            // Highlight difference if not balanced
            if (difference > 0) {
                $('.difference').addClass('bg-danger text-white');
            } else {
                $('.difference').removeClass('bg-danger text-white');
            }
        }
        
        // Initialize totals
        calculateTotals();
        
        // Update journal entry form
        $('#update-journal-entry-form').click(function() {
            const difference = parseFloat($('.difference').val());
            
            if (difference > 0) {
                Swal.fire({
                    icon: 'error',
                    title: "@lang('app.error')",
                    text: "@lang('messages.journalEntryNotBalanced')",
                    showConfirmButton: true
                });
                
                return false;
            }
            
            const url = "{{ route('journal-entries.update', $journalEntry->id) }}";
            
            $.easyAjax({
                url: url,
                container: '#update-journal-entry-data-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                buttonSelector: "#update-journal-entry-form",
                data: $('#update-journal-entry-data-form').serialize(),
                success: function(response) {
                    if (response.status == 'success') {
                        window.location.href = response.redirectUrl;
                    }
                }
            });
        });
        
        init(RIGHT_MODAL);
    });
</script>