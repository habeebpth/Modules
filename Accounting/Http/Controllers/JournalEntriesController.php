<?php

namespace Modules\Accounting\Http\Controllers;

use App\Helper\Reply;
use App\Http\Controllers\AccountBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Accounting\DataTables\JournalEntriesDataTable;
use Modules\Accounting\Entities\Account;
use Modules\Accounting\Entities\JournalEntry;
use Modules\Accounting\Entities\JournalEntryItem;

class JournalEntriesController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.journalEntries';
        $this->middleware(function ($request, $next) {
            // You can add middleware here if needed
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     */
    public function index(JournalEntriesDataTable $dataTable)
    {
        return $dataTable->render('accounting::journal-entries.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $this->pageTitle = __('app.addJournalEntry');
        $this->accounts = Account::orderBy('name')->get();
        $this->referenceNumber = 'JE-' . now()->format('Ymd') . '-' . str_pad(JournalEntry::count() + 1, 4, '0', STR_PAD_LEFT);

        $this->view = 'accounting::journal-entries.ajax.create';

        if ($request->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('accounting::journal-entries.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'entry_date' => 'required|date',
            'reference_number' => 'required|string|unique:journal_entries,reference_number',
            'description' => 'nullable|string',
            'account_id' => 'required|array|min:2',
            'account_id.*' => 'required|exists:accounts,id',
            'debit' => 'required|array',
            'debit.*' => 'nullable|numeric|min:0',
            'credit' => 'required|array',
            'credit.*' => 'nullable|numeric|min:0',
            'item_description' => 'nullable|array',
            'item_description.*' => 'nullable|string',
        ]);

        // Check if total debits equals total credits
        $totalDebits = array_sum($request->debit);
        $totalCredits = array_sum($request->credit);

        if ($totalDebits != $totalCredits) {
            return Reply::error(__('messages.journalEntryNotBalanced'));
        }

        // Start transaction
        DB::beginTransaction();
        try {
            // Create journal entry
            $journalEntry = new JournalEntry();
            $journalEntry->company_id = user()->company_id;
            $journalEntry->reference_number = $request->reference_number;
            $journalEntry->entry_date = $request->entry_date;
            $journalEntry->description = $request->description;
            $journalEntry->status = 'draft';
            $journalEntry->created_by = user()->id;
            $journalEntry->save();

            // Create journal entry items
            for ($i = 0; $i < count($request->account_id); $i++) {
                if (empty($request->account_id[$i]) || ($request->debit[$i] == 0 && $request->credit[$i] == 0)) {
                    continue;
                }

                $journalEntryItem = new JournalEntryItem();
                $journalEntryItem->journal_entry_id = $journalEntry->id;
                $journalEntryItem->account_id = $request->account_id[$i];
                $journalEntryItem->description = $request->item_description[$i] ?? null;
                $journalEntryItem->debit = $request->debit[$i] ?? 0;
                $journalEntryItem->credit = $request->credit[$i] ?? 0;
                $journalEntryItem->save();
            }

            DB::commit();
            return Reply::successWithData(__('messages.journalEntryAddedSuccessfully'), ['redirectUrl' => route('journal-entries.index')]);
        } catch (\Exception $e) {
            DB::rollback();
            return Reply::error(__('messages.errorOccurred') . ': ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $this->pageTitle = __('app.viewJournalEntry');
        $this->journalEntry = JournalEntry::with(['items.account', 'creator', 'poster'])->findOrFail($id);

        return view('accounting::journal-entries.show', $this->data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $this->pageTitle = __('app.editJournalEntry');

        $this->journalEntry = JournalEntry::with('items.account')->findOrFail($id);

        // Only allow editing draft journal entries
        if ($this->journalEntry->status != 'draft') {
            return Reply::error(__('messages.cannotEditPostedJournalEntry'));
        }

        $this->accounts = Account::orderBy('name')->get();
        $this->view = 'accounting::journal-entries.ajax.edit';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('accounting::journal-entries.create', $this->data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $journalEntry = JournalEntry::findOrFail($id);

        // Only allow updating draft journal entries
        if ($journalEntry->status != 'draft') {
            return Reply::error(__('messages.cannotEditPostedJournalEntry'));
        }

        $request->validate([
            'entry_date' => 'required|date',
            'description' => 'nullable|string',
            'account_id' => 'required|array|min:2',
            'account_id.*' => 'required|exists:accounts,id',
            'debit' => 'required|array',
            'debit.*' => 'nullable|numeric|min:0',
            'credit' => 'required|array',
            'credit.*' => 'nullable|numeric|min:0',
            'item_description' => 'nullable|array',
            'item_description.*' => 'nullable|string',
        ]);

        // Check if total debits equals total credits
        $totalDebits = array_sum($request->debit);
        $totalCredits = array_sum($request->credit);

        if ($totalDebits != $totalCredits) {
            return Reply::error(__('messages.journalEntryNotBalanced'));
        }

        // Start transaction
        DB::beginTransaction();
        try {
            // Update journal entry
            $journalEntry->entry_date = $request->entry_date;
            $journalEntry->description = $request->description;
            $journalEntry->save();

            // Delete existing journal entry items
            $journalEntry->items()->delete();

            // Create updated journal entry items
            for ($i = 0; $i < count($request->account_id); $i++) {
                if (empty($request->account_id[$i]) || ($request->debit[$i] == 0 && $request->credit[$i] == 0)) {
                    continue;
                }

                $journalEntryItem = new JournalEntryItem();
                $journalEntryItem->journal_entry_id = $journalEntry->id;
                $journalEntryItem->account_id = $request->account_id[$i];
                $journalEntryItem->description = $request->item_description[$i] ?? null;
                $journalEntryItem->debit = $request->debit[$i] ?? 0;
                $journalEntryItem->credit = $request->credit[$i] ?? 0;
                $journalEntryItem->save();
            }

            DB::commit();
            return Reply::successWithData(__('messages.journalEntryUpdatedSuccessfully'), ['redirectUrl' => route('journal-entries.index')]);
        } catch (\Exception $e) {
            DB::rollback();
            return Reply::error(__('messages.errorOccurred') . ': ' . $e->getMessage());
        }
    }

    /**
     * Post a journal entry.
     */
    public function post(Request $request)
    {
        $journalEntry = JournalEntry::findOrFail($request->id);

        if (!$journalEntry->canBePosted()) {
            return Reply::error(__('messages.journalEntryCannotBePosted'));
        }

        if ($journalEntry->post(user()->id)) {
            return Reply::success(__('messages.journalEntryPostedSuccessfully'));
        }

        return Reply::error(__('messages.errorOccurred'));
    }

    /**
     * Void a journal entry.
     */
    public function void(Request $request)
    {
        $journalEntry = JournalEntry::findOrFail($request->id);

        if ($journalEntry->void()) {
            return Reply::success(__('messages.journalEntryVoidedSuccessfully'));
        }

        return Reply::error(__('messages.journalEntryCannotBeVoided'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $journalEntry = JournalEntry::findOrFail($id);
        $journalEntry->delete();

        return Reply::success(__('messages.journalEntryDeletedSuccessfully'));
    }
}
