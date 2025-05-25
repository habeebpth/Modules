<?php
// =======================
// FILE: Accounting/Http/Requests/ChartOfAccountRequest.php
// =======================

namespace Modules\Accounting\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Accounting\Entities\ChartOfAccount;

class ChartOfAccountRequest extends FormRequest
{
    public function authorize()
    {
        return in_array('accounting', user()->modules ?? []);
    }

    public function rules()
    {
        $accountId = $this->route('chart_of_account');

        return [
            'account_code' => 'required|string|max:20|unique:chart_of_accounts,account_code,' . $accountId,
            'account_name' => 'required|string|max:255',
            'account_type' => 'required|in:' . implode(',', array_keys(ChartOfAccount::ACCOUNT_TYPES)),
            'account_sub_type' => 'required|string|max:50',
            'parent_id' => 'nullable|exists:chart_of_accounts,id',
            'description' => 'nullable|string',
            'opening_balance' => 'nullable|numeric',
        ];
    }

    public function messages()
    {
        return [
            'account_code.unique' => 'Account code already exists',
            'account_type.in' => 'Invalid account type selected',
            'parent_id.exists' => 'Selected parent account does not exist',
        ];
    }
}
