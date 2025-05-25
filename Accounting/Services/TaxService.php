<?php
namespace Modules\Accounting\Services;

use Modules\Accounting\Entities\TaxCode;

class TaxService
{
    public function calculateTax($amount, $taxCodeId)
    {
        $taxCode = TaxCode::findOrFail($taxCodeId);
        return ($amount * $taxCode->rate) / 100;
    }

    public function getApplicableTaxCodes($type = 'both')
    {
        return TaxCode::where('company_id', user()->company_id)
            ->where('is_active', true)
            ->where(function($query) use ($type) {
                $query->where('type', $type)
                      ->orWhere('type', 'both');
            })
            ->get();
    }

    public function createTaxJournalEntry($amount, $taxCodeId, $description, $referenceType = null, $referenceId = null)
    {
        $taxCode = TaxCode::findOrFail($taxCodeId);
        $taxAmount = $this->calculateTax($amount, $taxCodeId);

        if (!$taxCode->tax_account_id) {
            throw new \Exception('Tax account not configured for tax code: ' . $taxCode->code);
        }

        // This would typically be called from AccountingService to create the tax portion of entries
        return [
            'account_id' => $taxCode->tax_account_id,
            'credit' => $taxAmount,
            'debit' => 0,
            'description' => $description . ' - ' . $taxCode->name
        ];
    }
}
