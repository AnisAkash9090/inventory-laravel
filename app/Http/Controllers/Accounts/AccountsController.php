<?php

namespace App\Http\Controllers\Accounts;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// 1. Import your SR model here
use App\Models\SR;
use App\Models\Seller; 
use App\Models\Ledger; 
use Illuminate\Support\Facades\DB;
use App\Models\JournalBook; // Or however you import your Journal model
use Illuminate\Support\Facades\Auth;
class AccountsController extends Controller
{

public function getLedgersByType(Request $request)
    {
        $type = $request->get('type');
        $data = [];

        if ($type === '8') {
            // Fetch from your 'sr' table (adjust table name if needed)
            $data = SR::select('ledger', 'name')
                ->whereNotNull('ledger')
                ->get();
        } elseif ($type === '4') {
            // Fetch from your 'seller' table
            $data = Seller::select('ledger', 'name')
                ->whereNotNull('ledger')
                ->get();
        }

        // Return data as JSON response for jQuery
        return response()->json($data);
    }
    public function receiveEntry()
    {
        // 2. Get the current user's manager ID
        $managerId = auth()->user()->manager_id;

        // 3. Fetch SRs along with their ledger details scoped by manager_id
        $customers = SR::with('ledgerDetails')
            ->where('manager_id', $managerId)
            ->where('status', 'active') // Optional: only show active accounts
            ->get();
            

        // 4. Send the $customers collection into your Blade template
        return view('components.accounts.receive_create', compact('customers'));
    }
        public function paymententry()
    {
        // 2. Get the current user's manager ID
        $managerId = auth()->user()->manager_id;


            

        // 4. Send the $customers collection into your Blade template
        return view('components.accounts.payment_create');
    }
public function getLedgersByGroup(Request $request)
{
    $managerId = auth()->user()->manager_id;
    $groupId = $request->get('group_id');

    // Start with the base query for the active manager
$query = \App\Models\Ledger::where(function($q) use ($managerId) {
        $q->where('manager_id', $managerId)
          ->orWhere('manager_id', 0);
    });

    // If a specific group is selected (and it's not "all"), filter by that group
    if (!empty($groupId) && $groupId !== 'all') {
        $query->where('account_group', $groupId);
    }

    $ledgers = $query->orderBy('name', 'asc')->get(['id', 'name']);

    return response()->json($ledgers);
}
public function storeReceivePayment(Request $request)
    {
        // 1. Validate Form Inputs
        $request->validate([
            'ledger_id'                 => 'required|integer', // Customer Ledger
            'amount'                    => 'required|numeric|min:0.01',
            'payment_date'              => 'required|date',
            'invoice_id'                => 'nullable|string',
            'remarks'                   => 'nullable|string',
        ]);

        DB::beginTransaction();
try {
        // Save the created record to a variable
        $journal = JournalBook::create([
            'dr_ledger'        => 3, 
            'cr_ledger'        => $request->ledger_id, 
            'journal_type'     =>3,
            'amount'           => $request->amount,
            'remarks'          => $request->remarks,
            'invoice_id'       => 'Receive', 
            'transaction_date' => $request->payment_date,
            'manager_id'       => auth()->user()->manager_id,
            'created_by'       => auth()->id() 
        ]);

        DB::commit();

        return response()->json([
            'success' => true, // Added this for your JS 'if(response.success)' check
            'message' => 'Receive entry posted successfully into the ledger books.',
            'data'    => $journal // Send the created data back
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}

public function storePayment(Request $request)
    {
        // 1. Validate Form Inputs
        $request->validate([
            'ledger_id'                 => 'required|integer', // Customer Ledger
            'amount'                    => 'required|numeric|min:0.01',
            'payment_date'              => 'required|date',
            'invoice_id'                => 'nullable|string',
            'remarks'                   => 'nullable|string',
            'journalType'               => 'required|integer'
        ]);

        DB::beginTransaction();
try {
        // Save the created record to a variable
 $journal = JournalBook::create([
    'dr_ledger'        => $request->ledger_id, // DEBIT: The Vendor's Ledger (decreases liability)
    'cr_ledger'        => 3,    
    'journal_type'     =>$request->journalType,              // CREDIT: Cash/Bank Ledger (decreases asset)
    'amount'           => $request->amount,
  'remarks' => "Payment made against Bulk Inv #{$request->invoice_id}" . ($request->remarks ? " - " . $request->remarks : ""),
    'invoice_id'       => $request->invoice_id ?? 'Payment', 
    'transaction_date' => $request->payment_date,
    'manager_id'       => auth()->user()->manager_id,
    'created_by'       => auth()->id() 
]);

        DB::commit();

        return response()->json([
            'success' => true, // Added this for your JS 'if(response.success)' check
            'message' => 'Receive entry posted successfully into the ledger books.',
            'data'    => $journal // Send the created data back
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}
public function journalView(Request $request)
{
    $managerId = auth()->user()->manager_id;
    $fromDate = $request->query('from_date', date('Y-m-01'));
    $toDate = $request->query('to_date', date('Y-m-d'));
    
    $selectedGroupId = $request->query('account_group_id');
    $selectedLedgerId = $request->query('ledger_id');

    // 1. Determine if we are looking at everything
    $isAllView = ($selectedGroupId === 'all' || empty($selectedLedgerId));

$accountGroups = \App\Models\AccountGroup::orderBy('name', 'asc')
        ->get();
    // 2. --- COMPUTING HISTORICAL OPENING BALANCE ---
    $historicalDrQuery = DB::table('journal_book')
        ->where('manager_id', $managerId)
        ->where('transaction_date', '<', $fromDate);

    $historicalCrQuery = DB::table('journal_book')
        ->where('manager_id', $managerId)
        ->where('transaction_date', '<', $fromDate);

    if (!$isAllView) {
        $historicalDrQuery->where('dr_ledger', $selectedLedgerId);
        $historicalCrQuery->where('cr_ledger', $selectedLedgerId);
    } else if (!empty($selectedGroupId) && $selectedGroupId !== 'all') {
        // Optional: If a group is selected but ledger is "all"
        $ledgerIds = \App\Models\Ledger::where('account_group_id', $selectedGroupId)->pluck('id');
        $historicalDrQuery->whereIn('dr_ledger', $ledgerIds);
        $historicalCrQuery->whereIn('cr_ledger', $ledgerIds);
    }

    $openingBalance = $historicalDrQuery->sum('amount') - $historicalCrQuery->sum('amount');

    // 3. --- FETCH STATEMENT PERIOD TRANSACTIONS ---
    $journalsQuery = JournalBook::where('manager_id', $managerId)
        ->whereBetween('transaction_date', [$fromDate, $toDate]);

    if (!$isAllView) {
        $journalsQuery->where(function($q) use ($selectedLedgerId) {
            $q->where('dr_ledger', $selectedLedgerId)
              ->orWhere('cr_ledger', $selectedLedgerId);
        });
    } else if (!empty($selectedGroupId) && $selectedGroupId !== 'all') {
        $ledgerIds = \App\Models\Ledger::where('account_group_id', $selectedGroupId)->pluck('id');
        $journalsQuery->where(function($q) use ($ledgerIds) {
            $q->whereIn('dr_ledger', $ledgerIds)
              ->orWhere('In', $ledgerIds);
        });
    }

    $journals = $journalsQuery->orderBy('transaction_date', 'asc')->orderBy('id', 'asc')->get();

    // 4. --- CALC SUMMARY METRICS FOR THE FOOTER ---
    $totalDebit = 0;
    $totalCredit = 0;

    foreach ($journals as $journal) {
        // In an "All" view, everything listed is a transaction to calculate
        if ($isAllView) {
            $totalDebit += $journal->amount; // Or define your custom mapping logic here
        } else {
            if ($journal->dr_ledger == $selectedLedgerId) { $totalDebit += $journal->amount; }
            if ($journal->cr_ledger == $selectedLedgerId) { $totalCredit += $journal->amount; }
        }
    }
    
    $closingBalance = $openingBalance + ($totalDebit - $totalCredit);

    return view('components.accounts.journal_book', compact(
        'journals', 'openingBalance', 'totalDebit', 'totalCredit', 'closingBalance',
        'fromDate', 'toDate', 'selectedGroupId', 'selectedLedgerId', 'isAllView' ,'accountGroups', 'selectedGroupId'
    ));
}
public function index()
    {
        // Fetch all SRs to display in the view table
        $srs = DB::table('sr')->orderBy('id', 'desc')->get();
        return view('components.accounts.sr_manage', compact('srs'));
    }
    public function index_seller()
    {
        // Fetch all SRs to display in the view table
       $sellers = DB::table('sellers')->orderBy('id', 'desc')->get();
        return view('components.accounts.seller_manage', compact('sellers'));
    }

    // 2. Automated double-entry creation logic (SR + Ledger)
    public function storeStoreRep(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact' => 'required|string|max:20',
            'address' => 'nullable|string',
            'company' => 'nullable|string',
            'branch' => 'nullable|string',
            'compaddress' => 'nullable|string'     
        ]);


        $managerId = auth()->user()->manager_id ; // Fallback or auth configuration
        $userId = auth()->user()->id ;

        // Wrap in a database Transaction to guarantee safety
        DB::beginTransaction();
        try {
            
            // Step A: Create the Ledger entry first to get the ledger_id
            $ledgerId = DB::table('ledger')->insertGetId([
                'name' => $request->name,
                'manager_id'  => $managerId,
                'created_by'  => $userId,
                'account_group'=> 1,  
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            // Step B: Create the SR profile linking back to that newly created ledger account
            DB::table('sr')->insert([
                'ledger'          => $ledgerId, // Store the ledger identifier map here
                'name'            => $request->name,
                'contact'         => $request->contact,
                'address'         => $request->address,
                'status'          => 'Active',
                'manager_id'      => $managerId,
                'created_by'      => $userId,
                'company'         => $request->company,
                'branch'          => $request->branch ?? null,
                'company_address' => $request->compaddress ?? null,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);

            DB::commit();
            return redirect()->back()->with('success_message', 'SR profile and corresponding general ledger created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error_message', 'Transaction failed: ' . $e->getMessage());
        }
    }
// Add form data submission routing target for Sellers
public function storeSeller(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'contact' => 'required|string|max:20',
        'address' => 'nullable|string',
    ]);

    $managerId = auth()->user()->manager_id ?? 1;
    $userId = auth()->user()->id ?? 1;

    // Wrap in a database Transaction to guarantee data integrity
    DB::beginTransaction();
    try {
        
        // Step A: Create the Ledger entry first to generate the ledger_id
        $ledgerId = DB::table('ledger')->insertGetId([
            'name' => $request->name . ' (Seller Ledger)',
            'manager_id'  => $managerId,
            'account_group'=> 6,  
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        // Step B: Create the Seller profile linking back to that newly created ledger account
        DB::table('sellers')->insert([
            'name'        => $request->name,
            'address'     => $request->address,
            'branch'      => $request->branch ?? null,
            'contact'     => $request->contact,
            'ledger'      => $ledgerId, // Mapped General Ledger ID reference
            'manager_id'  => $managerId,
            'created_by'  => $userId,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        DB::commit();
        return redirect()->back()->with('success_message', 'Seller profile and corresponding general ledger created successfully!');

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error_message', 'Transaction failed: ' . $e->getMessage());
    }
}

}