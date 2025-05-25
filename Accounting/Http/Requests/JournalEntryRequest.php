<?php
// =======================
// FILE: Accounting/Http/Requests/JournalEntryRequest.php
// =======================

namespace Modules\Accounting\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Accounting\Rules\BalancedJournalRule;

class JournalEntryRequest extends FormRequest
{
    public function authorize()
    {
        return in_array('accounting', user()->modules ?? []);
    }

    public function rules()
    {
        return [
            'date' => 'required|date',
            'description' => 'required|string|max:255',
            'entries' => ['required', 'array', 'min:2', new BalancedJournalRule],
            'entries.*.account_id' => 'required|exists:chart_of_accounts,id',
            'entries.*.debit' => 'nullable|numeric|min:0',
            'entries.*.credit' => 'nullable|numeric|min:0',
            'entries.*.description' => 'nullable|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'entries.min' => 'Journal entry must have at least 2 entries',
            'entries.*.account_id.required' => 'Account is required for each entry',
            'entries.*.account_id.exists' => 'Selected account does not exist',
        ];
    }
}
