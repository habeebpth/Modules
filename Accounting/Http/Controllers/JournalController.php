<?php
namespace Modules\Accounting\Http\Controllers;

use App\Http\Controllers\AccountBaseController;
use App\Helper\Reply;
use Illuminate\Http\Request;
use Modules\Accounting\Entities\Journal;
use Modules\Accounting\Entities\JournalEntry;
use Modules\Accounting\Entities\ChartOfAccount;
use Modules\Accounting\Services\AccountingService;
use Modules\Accounting\DataTables\JournalDataTable;
use Illuminate\Support\Facades\DB;

class JournalController extends AccountBaseController
{
    protected $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        parent::__construct();
        $this->accountingService = $accountingService;
        $this->pageTitle = 'Journals';

        $this->middleware(function ($request, $next) {
            abort_403(!in_array('accounting', $this->user->modules));
            return $next($request);
        });
    }

    public function index(JournalDataTable $dataTable)
    {
        return $dataTable->render('accounting::journals.index', $this->data);
    }

    public function create()
    {
        $this->pageTitle = 'Create Journal Entry';
        $this->accounts = ChartOfAccount::where('company_id', user()->company_id)
            ->where('is_active', true)
            ->orderBy('account_code')
            ->get();

        $this->view = 'accounting::journals.ajax.create';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('accounting::journals.create', $this->data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'description' => 'required|string',
            'entries' => 'required|array|min:2',
            'entries.*.account_id' => 'required|exists:chart_of_accounts,id',
            'entries.*.debit' => 'nullable|numeric|min:0',
            'entries.*.credit' => 'nullable|numeric|min:0',
        ]);

        try {
            $journal = $this->accountingService->createJournalEntry(
                $request->entries,
                $request->description,
                null,
                null,
                $request->date
            );

            return Reply::successWithData('Journal entry created successfully', [
                'redirectUrl' => route('accounting.journals.index')
            ]);
        } catch (\Exception $e) {
            return Reply::error($e->getMessage());
        }
    }

    public function show($id)
    {
        $this->journal = Journal::with('entries.account')->findOrFail($id);
        $this->pageTitle = 'Journal Entry #' . $this->journal->journal_number;

        return view('accounting::journals.show', $this->data);
    }

    public function edit($id)
    {
        $this->journal = Journal::with('entries')->findOrFail($id);

        if ($this->journal->status !== Journal::STATUS_DRAFT) {
            return Reply::error('Only draft journal entries can be edited');
        }

        $this->pageTitle = 'Edit Journal Entry';
        $this->accounts = ChartOfAccount::where('company_id', user()->company_id)
            ->where('is_active', true)
            ->orderBy('account_code')
            ->get();

        $this->view = 'accounting::journals.ajax.edit';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('accounting::journals.edit', $this->data);
    }

    public function update(Request $request, $id)
    {
        $journal = Journal::findOrFail($id);

        if ($journal->status !== Journal::STATUS_DRAFT) {
            return Reply::error('Only draft journal entries can be updated');
        }

        $request->validate([
            'date' => 'required|date',
            'description' => 'required|string',
            'entries' => 'required|array|min:2',
            'entries.*.account_id' => 'required|exists:chart_of_accounts,id',
            'entries.*.debit' => 'nullable|numeric|min:0',
            'entries.*.credit' => 'nullable|numeric|min:0',
        ]);

        // Validate balanced entries
        $totalDebit = collect($request->entries)->sum('debit');
        $totalCredit = collect($request->entries)->sum('credit');

        if ($totalDebit != $totalCredit) {
            return Reply::error('Journal entry is not balanced');
        }

        try {
            DB::transaction(function () use ($journal, $request, $totalDebit, $totalCredit) {
                // Update journal
                $journal->update([
                    'date' => $request->date,
                    'description' => $request->description,
                    'total_debit' => $totalDebit,
                    'total_credit' => $totalCredit,
                ]);

                // Delete existing entries
                $journal->entries()->delete();

                // Create new entries
                foreach ($request->entries as $entry) {
                    if (($entry['debit'] ?? 0) > 0 || ($entry['credit'] ?? 0) > 0) {
                        JournalEntry::create([
                            'company_id' => user()->company_id,
                            'journal_id' => $journal->id,
                            'account_id' => $entry['account_id'],
                            'debit' => $entry['debit'] ?? 0,
                            'credit' => $entry['credit'] ?? 0,
                            'description' => $entry['description'] ?? $request->description,
                        ]);
                    }
                }
            });

            return Reply::successWithData('Journal entry updated successfully', [
                'redirectUrl' => route('accounting.journals.index')
            ]);
        } catch (\Exception $e) {
            return Reply::error($e->getMessage());
        }
    }

    public function post($id)
    {
        $journal = Journal::findOrFail($id);

        if ($journal->status !== Journal::STATUS_DRAFT) {
            return Reply::error('Journal entry is already posted');
        }

        if (!$journal->isBalanced()) {
            return Reply::error('Journal entry is not balanced');
        }

        try {
            DB::transaction(function () use ($journal) {
                $journal->update(['status' => Journal::STATUS_POSTED]);

                // Update account balances
                foreach ($journal->entries as $entry) {
                    $this->accountingService->updateAccountBalance($entry->account_id);
                }
            });

            return Reply::success('Journal entry posted successfully');
        } catch (\Exception $e) {
            return Reply::error($e->getMessage());
        }
    }

    public function reverse($id)
    {
        $journal = Journal::findOrFail($id);

        if ($journal->status !== Journal::STATUS_POSTED) {
            return Reply::error('Only posted journal entries can be reversed');
        }

        try {
            // Create reversing entry
            $reversingEntries = [];
            foreach ($journal->entries as $entry) {
                $reversingEntries[] = [
                    'account_id' => $entry->account_id,
                    'debit' => $entry->credit, // Swap debit and credit
                    'credit' => $entry->debit,
                    'description' => 'Reversing entry for JE #' . $journal->journal_number,
                ];
            }

            $reversingJournal = $this->accountingService->createJournalEntry(
                $reversingEntries,
                'Reversing entry for JE #' . $journal->journal_number,
                'journal_reversal',
                $journal->id
            );

            // Mark original as reversed
            $journal->update([
                'status' => Journal::STATUS_REVERSED,
                'reversed_by' => user()->id,
                'reversed_at' => now(),
            ]);

            return Reply::success('Journal entry reversed successfully');
        } catch (\Exception $e) {
            return Reply::error($e->getMessage());
        }
    }
}
