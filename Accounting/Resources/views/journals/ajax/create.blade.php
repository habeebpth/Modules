<div class="row">
    <div class="col-sm-12">
        <x-form id="save-journal-form">
            <div class="add-client bg-white rounded">
                <h4 class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-bottom-grey">
                    @lang('Create Journal Entry')
                </h4>

                <div class="row px-4">
                    <div class="col-md-6">
                        <x-forms.datepicker
                            fieldPlaceholder="@lang('Select Date')"
                            fieldId="date"
                            :fieldLabel="__('Date')"
                            fieldName="date"
                            fieldRequired="true"
                            :fieldValue="now()->format(company()->date_format)" />
                    </div>
                    <div class="col-md-6">
                        <x-forms.text fieldId="reference" :fieldLabel="__('Reference')"
                            fieldName="reference" fieldPlaceholder="@lang('Enter Reference')" />
                    </div>
                </div>

                <div class="row px-4">
                    <div class="col-md-12">
                        <x-forms.textarea fieldId="description" :fieldLabel="__('Description')"
                            fieldName="description" fieldRequired="true"
                            fieldPlaceholder="@lang('Enter description for this journal entry')" />
                    </div>
                </div>

                <div class="row px-4">
                    <div class="col-md-12">
                        <h5 class="mb-3">@lang('Journal Entries')</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="journal-entries-table">
                                <thead>
                                    <tr>
                                        <th width="40%">@lang('Account')</th>
                                        <th width="25%">@lang('Description')</th>
                                        <th width="15%">@lang('Debit')</th>
                                        <th width="15%">@lang('Credit')</th>
                                        <th width="5%">@lang('Action')</th>
                                    </tr>
                                </thead>
                                <tbody id="journal-entries-body">
                                    <tr class="journal-entry-row">
                                        <td>
                                            <select name="entries[0][account_id]" class="form-control select-picker account-select" data-live-search="true" required>
                                                <option value="">@lang('Select Account')</option>
                                                @foreach($accounts as $account)
                                                    <option value="{{ $account->id }}">{{ $account->account_code }} - {{ $account->account_name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" name="entries[0][description]" class="form-control entry-description" placeholder="@lang('Entry description')" />
                                        </td>
                                        <td>
                                            <input type="number" name="entries[0][debit]" class="form-control debit-input" step="0.01" min="0" placeholder="0.00" />
                                        </td>
                                        <td>
                                            <input type="number" name="entries[0][credit]" class="form-control credit-input" step="0.01" min="0" placeholder="0.00" />
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-danger remove-entry" disabled>
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr class="journal-entry-row">
                                        <td>
                                            <select name="entries[1][account_id]" class="form-control select-picker account-select" data-live-search="true" required>
                                                <option value="">@lang('Select Account')</option>
                                                @foreach($accounts as $account)
                                                    <option value="{{ $account->id }}">{{ $account->account_code }} - {{ $account->account_name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" name="entries[1][description]" class="form-control entry-description" placeholder="@lang('Entry description')" />
                                        </td>
                                        <td>
                                            <input type="number" name="entries[1][debit]" class="form-control debit-input" step="0.01" min="0" placeholder="0.00" />
                                        </td>
                                        <td>
                                            <input type="number" name="entries[1][credit]" class="form-control credit-input" step="0.01" min="0" placeholder="0.00" />
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-danger remove-entry">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2" class="text-right"><strong>@lang('Total')</strong></td>
                                        <td><strong id="total-debit">0.00</strong></td>
                                        <td><strong id="total-credit">0.00</strong></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="5">
                                            <button type="button" class="btn btn-sm btn-success" id="add-entry">
                                                <i class="fa fa-plus"></i> @lang('Add Entry')
                                            </button>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="alert alert-warning mt-3" id="balance-warning" style="display: none;">
                            <i class="fa fa-exclamation-triangle"></i> @lang('Journal entry is not balanced. Total debits must equal total credits.')
                        </div>
                    </div>
                </div>

                <x-form-actions>
                    <x-forms.button-primary id="save-journal" class="mr-3" icon="check">@lang('app.save')
                    </x-forms.button-primary>
                    <x-forms.button-cancel :link="route('accounting.journals.index')" class="border-0">@lang('app.cancel')
                    </x-forms.button-cancel>
                </x-form-actions>
            </div>
        </x-form>
    </div>
</div>

<script>
$(document).ready(function() {
    let entryIndex = 2;

    // Add new entry row
    $('#add-entry').click(function() {
        const newRow = `
            <tr class="journal-entry-row">
                <td>
                    <select name="entries[${entryIndex}][account_id]" class="form-control select-picker account-select" data-live-search="true" required>
                        <option value="">@lang('Select Account')</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}">{{ $account->account_code }} - {{ $account->account_name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="text" name="entries[${entryIndex}][description]" class="form-control entry-description" placeholder="@lang('Entry description')" />
                </td>
                <td>
                    <input type="number" name="entries[${entryIndex}][debit]" class="form-control debit-input" step="0.01" min="0" placeholder="0.00" />
                </td>
                <td>
                    <input type="number" name="entries[${entryIndex}][credit]" class="form-control credit-input" step="0.01" min="0" placeholder="0.00" />
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger remove-entry">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;

        $('#journal-entries-body').append(newRow);
        $('.select-picker').selectpicker('refresh');
        entryIndex++;
        updateRemoveButtons();
    });

    // Remove entry row
    $(document).on('click', '.remove-entry', function() {
        $(this).closest('tr').remove();
        updateRemoveButtons();
        calculateTotals();
    });

    // Update remove button states
    function updateRemoveButtons() {
        const rows = $('.journal-entry-row').length;
        $('.remove-entry').prop('disabled', rows <= 2);
    }

    // Calculate totals
    $(document).on('input', '.debit-input, .credit-input', function() {
        // Ensure only one of debit or credit is filled
        const row = $(this).closest('tr');
        if ($(this).hasClass('debit-input') && $(this).val()) {
            row.find('.credit-input').val('');
        } else if ($(this).hasClass('credit-input') && $(this).val()) {
            row.find('.debit-input').val('');
        }

        calculateTotals();
    });

    function calculateTotals() {
        let totalDebit = 0;
        let totalCredit = 0;

        $('.debit-input').each(function() {
            totalDebit += parseFloat($(this).val()) || 0;
        });

        $('.credit-input').each(function() {
            totalCredit += parseFloat($(this).val()) || 0;
        });

        $('#total-debit').text(totalDebit.toFixed(2));
        $('#total-credit').text(totalCredit.toFixed(2));

        // Show/hide balance warning
        if (totalDebit !== totalCredit && (totalDebit > 0 || totalCredit > 0)) {
            $('#balance-warning').show();
        } else {
            $('#balance-warning').hide();
        }
    }

    // Save journal entry
    $('#save-journal').click(function() {
        const url = "{{ route('accounting.journals.store') }}";

        $.easyAjax({
            url: url,
            container: '#save-journal-form',
            type: "POST",
            disableButton: true,
            blockUI: true,
            buttonSelector: "#save-journal",
            data: $('#save-journal-form').serialize(),
            success: function(response) {
                if (response.status == 'success') {
                    window.location.href = response.redirectUrl;
                }
            }
        });
    });

    $('.select-picker').selectpicker();
});
</script>
