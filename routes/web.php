<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProductGroupController;
use App\Http\Controllers\userController;
use App\Http\Controllers\ProductStoreController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Accounts\AccountsController;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Http\Controllers\WarrantyClaimController;

// Change this route to redirect to login
Route::get('/', function () {
    return redirect()->route('login');
});
Route::middleware(['auth', 'verified'])->group(function () {
    // Standard Dashboarduse Carbon\Carbon;
    Route::get('/dashboard', function () {
    $managerId = auth()->user()->manager_id;

    // Time boundaries (Current Month vs Last Month)
    $startOfMonth   = Carbon::now()->startOfMonth()->toDateString();
    $endOfMonth     = Carbon::now()->endOfMonth()->toDateString();
    $lastMonthStart = Carbon::now()->subMonth()->startOfMonth()->toDateString();
    $lastMonthEnd   = Carbon::now()->subMonth()->endOfMonth()->toDateString();



    // ------------------------------------------------------------------------
// RETURNS (approved only) — This Month & All-Time, manager scoped
// ------------------------------------------------------------------------
$returnMetrics = DB::table('return_invoice_rc')
    ->where('manager_id', $managerId)
    ->where('status', 'approve')
    ->whereBetween('invoice_date', [$lastMonthStart, $endOfMonth])
    ->selectRaw("
        COALESCE(SUM(CASE WHEN invoice_date BETWEEN '{$startOfMonth}' AND '{$endOfMonth}' THEN amount END), 0) as this_return,
        COALESCE(SUM(CASE WHEN invoice_date BETWEEN '{$startOfMonth}' AND '{$endOfMonth}' THEN cost END), 0) as this_return_cost,
        COALESCE(SUM(CASE WHEN invoice_date BETWEEN '{$startOfMonth}' AND '{$endOfMonth}' THEN qty END), 0) as this_return_qty,

        COALESCE(SUM(CASE WHEN invoice_date BETWEEN '{$lastMonthStart}' AND '{$lastMonthEnd}' THEN amount END), 0) as last_return,
        COALESCE(SUM(CASE WHEN invoice_date BETWEEN '{$lastMonthStart}' AND '{$lastMonthEnd}' THEN cost END), 0) as last_return_cost,
        COALESCE(SUM(CASE WHEN invoice_date BETWEEN '{$lastMonthStart}' AND '{$lastMonthEnd}' THEN qty END), 0) as last_return_qty
    ")->first();

// ------------------------------------------------------------------------
// COMPANY PAYABLE vs PAID (journal_type: 7 = Due to me, 8 = Paid by me to SR)
// ------------------------------------------------------------------------


$paidMetrics = DB::table('journal_book')
    ->where('manager_id', $managerId)
    ->where('journal_type', 8)
    ->whereBetween('transaction_date', [$lastMonthStart, $endOfMonth])
    ->selectRaw("
        COALESCE(SUM(CASE WHEN transaction_date BETWEEN '{$startOfMonth}' AND '{$endOfMonth}' THEN amount END), 0) as this_paid,
        COALESCE(SUM(CASE WHEN transaction_date BETWEEN '{$lastMonthStart}' AND '{$lastMonthEnd}' THEN amount END), 0) as last_paid
    ")->first();

 

$allTimePaid = DB::table('journal_book')
    ->where('manager_id', $managerId)
    ->where('journal_type', 8)
    ->sum('amount');

// ------------------------------------------------------------------------
// DUE FROM COMPANY (This Month, Last Month, All-Time)
// ------------------------------------------------------------------------
$thisMonthComppayed = max(0,$paidMetrics->this_paid);
$allTimeComppayed   = max(0, $allTimePaid);

$allTimeReturn = DB::table('return_invoice_rc')
    ->where('manager_id', $managerId)
    ->where('status', 'approve')
    ->sum('amount');

$allTimeReturnCost = DB::table('return_invoice_rc')
    ->where('manager_id', $managerId)
    ->where('status', 'approve')
    ->sum('cost');
    $thisMonthReturnProfit = round($returnMetrics->this_return - $returnMetrics->this_return_cost, 2);
$lastMonthReturnProfit = round($returnMetrics->last_return - $returnMetrics->last_return_cost, 2);
$allTimeReturnProfit   = round($allTimeReturn - $allTimeReturnCost, 2);
    // ------------------------------------------------------------------------
    // 1. SINGLE-HIT INVOICE METRICS (Combined This Month & Last Month)
    // ------------------------------------------------------------------------
    $invoiceMetrics = DB::table('invoice_rc')
        ->where('manager_id', $managerId)
        ->whereBetween('invoice_date', [$lastMonthStart, $endOfMonth])
        ->selectRaw("
            /* This Month Aggregates */
            COALESCE(SUM(CASE WHEN invoice_date BETWEEN '{$startOfMonth}' AND '{$endOfMonth}' THEN amount END), 0) as this_sales,
            COALESCE(SUM(CASE WHEN invoice_date BETWEEN '{$startOfMonth}' AND '{$endOfMonth}' THEN cost END), 0) as this_cost,
            COALESCE(SUM(CASE WHEN invoice_date BETWEEN '{$startOfMonth}' AND '{$endOfMonth}' THEN discount END), 0) as this_discount,

            /* Last Month Aggregates */
            COALESCE(SUM(CASE WHEN invoice_date BETWEEN '{$lastMonthStart}' AND '{$lastMonthEnd}' THEN amount END), 0) as last_sales,
            COALESCE(SUM(CASE WHEN invoice_date BETWEEN '{$lastMonthStart}' AND '{$lastMonthEnd}' THEN cost END), 0) as last_cost,
            COALESCE(SUM(CASE WHEN invoice_date BETWEEN '{$lastMonthStart}' AND '{$lastMonthEnd}' THEN discount END), 0) as last_discount
        ")->first();

    // ------------------------------------------------------------------------
    // 2. SINGLE-HIT JOURNAL RECEIVABLES (Combined This Month & Last Month)
    // ------------------------------------------------------------------------
    $journalMetrics = DB::table('journal_book')
        ->where('manager_id', $managerId)
        ->where('dr_ledger', '3')
        ->where('journal_type', '3') /* It is type id of receive entry */
        ->whereBetween('transaction_date', [$lastMonthStart, $endOfMonth])
        ->selectRaw("
            COALESCE(SUM(CASE WHEN transaction_date BETWEEN '{$startOfMonth}' AND '{$endOfMonth}' THEN amount END), 0) as this_receive,
            COALESCE(SUM(CASE WHEN transaction_date BETWEEN '{$lastMonthStart}' AND '{$lastMonthEnd}' THEN amount END), 0) as last_receive
        ")->first();
      
    // ------------------------------------------------------------------------
    // 3. PENNY-PERFECT PROFIT CALCULATIONS
    // ------------------------------------------------------------------------
    
    // --- THIS MONTH MATH ENGINE ---
// --- THIS MONTH MATH ENGINE (FIXED) ---
// --- THIS MONTH MATH ENGINE (now return-aware) ---
$thisMonthNetSales    = $invoiceMetrics->this_sales - $returnMetrics->this_return;
$thisMonthNetCost     = $invoiceMetrics->this_cost - $returnMetrics->this_return_cost;
$thisMonthTotalProfit = $thisMonthNetSales - $thisMonthNetCost;
$thisMonthMarginRatio = $thisMonthNetSales > 0 ? ($thisMonthTotalProfit / $thisMonthNetSales) : 0;

$thisMonthCashAppliedToCurrentInvoices = min($journalMetrics->this_receive, $thisMonthNetSales);
$thisMonthCollectedProfit = round($thisMonthCashAppliedToCurrentInvoices * $thisMonthMarginRatio, 2);
$thisMonthRemainingProfit = round($thisMonthTotalProfit - $thisMonthCollectedProfit, 2);
$thisMonthExtraCashReceived = max(0, $journalMetrics->this_receive - $thisMonthNetSales);

// Due now nets out approved returns too
$thisMonthDue = max(0, $thisMonthNetSales - $journalMetrics->this_receive);


// --- LAST MONTH MATH ENGINE (now return-aware) ---
$lastMonthNetSales    = $invoiceMetrics->last_sales - $returnMetrics->last_return;
$lastMonthNetCost     = $invoiceMetrics->last_cost - $returnMetrics->last_return_cost;
$lastMonthTotalProfit = $lastMonthNetSales - $lastMonthNetCost;
$lastMonthMarginRatio = $lastMonthNetSales > 0 ? ($lastMonthTotalProfit / $lastMonthNetSales) : 0;

$lastMonthCashAppliedToInvoices = min($journalMetrics->last_receive, $lastMonthNetSales);
$lastMonthCollectedProfit       = round($lastMonthCashAppliedToInvoices * $lastMonthMarginRatio, 2);
$lastMonthRemainingProfit       = round($lastMonthTotalProfit - $lastMonthCollectedProfit, 2);
$lastMonthExtraCashReceived     = max(0, $journalMetrics->last_receive - $lastMonthNetSales);

    // ------------------------------------------------------------------------
    // RETAIN REMAINING CODE (Previous Stock / Group / Product Metrics)
    // ------------------------------------------------------------------------
    $thisMonthSold = DB::table('invoices')
        ->where('manager_id', $managerId)
        ->where('status', 'complete')
        ->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
        ->selectRaw('COALESCE(SUM(qty),0) as qty, COALESCE(SUM((qty*price)-(qty*discount)),0) as value')
        ->first();

    $thisMonthStockIn = DB::table('product_store_logs')
        ->where('manager_id', $managerId)
        ->whereBetween('buydate', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
        ->selectRaw('COALESCE(SUM(qty),0) as qty, COALESCE(SUM(qty*cost),0) as value')
        ->first();
    $thismonthVal  =($thisMonthSold->value)-
    $buySoldTotal = ($thisMonthStockIn->value + $thisMonthSold->value) ?: 1;
    $buyPercent   = round(($thisMonthStockIn->value / $buySoldTotal) * 100, 1);
    $soldPercent  = round(($thisMonthSold->value / $buySoldTotal) * 100, 1);

  $allTimeInvoice = DB::table('invoice_rc')->where('manager_id', $managerId)->sum('amount');
$allTimeReceive = DB::table('journal_book')->where('manager_id', $managerId)->where('dr_ledger', '3')->where('journal_type', '3')->sum('amount');
$allTimeDue     =  max(0,$allTimeInvoice- $allTimeReturn - $allTimeReceive+$allTimePaid);
$allTimeAdvance = $allTimeInvoice - $allTimeReturn - $allTimeReceive + $allTimePaid;

// Display-friendly positive version
$allTimeAdvanceReceived = abs($allTimeAdvance);

    // Top 10 Products
    $topProducts = DB::table('invoices')
        ->select('product_id', DB::raw('SUM(qty) as total_qty'), DB::raw('SUM((qty*price)-discount) as total_sales'))
        ->where('manager_id', $managerId)->where('status', 'complete')->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
        ->groupBy('product_id')->orderByDesc('total_qty')->take(10)->get();

    $topProductIds = $topProducts->pluck('product_id');
    $productNames = DB::table('products')->whereIn('id', $topProductIds)->pluck('product_name', 'id');
    
    $lastMonthProductStats = DB::table('invoices')
        ->select('product_id', DB::raw('SUM(qty) as total_qty'), DB::raw('SUM((qty*price)-discount) as total_sales'))
        ->where('manager_id', $managerId)->where('status', 'complete')->whereIn('product_id', $topProductIds)->whereBetween('created_at', [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()])
        ->groupBy('product_id')->get()->keyBy('product_id');

    $productComparison = $topProducts->map(function ($p) use ($lastMonthProductStats, $productNames) {
        $last = $lastMonthProductStats->get($p->product_id);
        return [
            'product_id' => $p->product_id,
            'name'       => $productNames[$p->product_id] ?? ('#' . $p->product_id),
            'this_qty'   => $p->total_qty,
            'this_sales' => $p->total_sales,
            'last_qty'   => $last->total_qty ?? 0,
            'last_sales' => $last->total_sales ?? 0,
        ];
    });

    // Top 10 Groups
    $topGroups = DB::table('invoices')
        ->select('group_id', DB::raw('SUM(qty) as total_qty'), DB::raw('SUM((qty*price)-discount) as total_sales'))
        ->where('manager_id', $managerId)->where('status', 'complete')->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
        ->groupBy('group_id')->orderByDesc('total_qty')->take(10)->get();

    $topGroupIds = $topGroups->pluck('group_id');
    $groupNames = DB::table('products_group')->whereIn('id', $topGroupIds)->pluck('product_group', 'id');
    
    $lastMonthGroupStats = DB::table('invoices')
        ->select('group_id', DB::raw('SUM(qty) as total_qty'), DB::raw('SUM((qty*price)-discount) as total_sales'))
        ->where('manager_id', $managerId)->where('status', 'complete')->whereIn('group_id', $topGroupIds)->whereBetween('created_at', [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()])
        ->groupBy('group_id')->get()->keyBy('group_id');

    $groupComparison = $topGroups->map(function ($g) use ($lastMonthGroupStats, $groupNames) {
        $last = $lastMonthGroupStats->get($g->group_id);
        return [
            'group_id'   => $g->group_id,
            'name'       => $groupNames[$g->group_id] ?? ('#' . $g->group_id),
            'this_qty'   => $g->total_qty,
            'this_sales' => $g->total_sales,
            'last_qty'   => $last->total_qty ?? 0,
            'last_sales' => $last->total_sales ?? 0,
        ];
    });

    // Stock Remaining Configuration Matrices
    $stockInByProduct = DB::table('product_store_logs')->where('manager_id', $managerId)->select('product_id', DB::raw('SUM(qty) as bought_qty'))->groupBy('product_id')->pluck('bought_qty', 'product_id');
    $soldByProduct = DB::table('invoices')->where('manager_id', $managerId)->where('status', 'complete')->select('product_id', DB::raw('SUM(qty) as sold_qty'))->groupBy('product_id')->pluck('sold_qty', 'product_id');
    $allProductIds = collect($stockInByProduct->keys())->merge($soldByProduct->keys())->unique()->values();
    $stockProductNames = DB::table('products')->whereIn('id', $allProductIds)->pluck('product_name', 'id');

    $stockRemaining = $allProductIds->map(function ($pid) use ($stockInByProduct, $soldByProduct, $stockProductNames) {
        $bought = $stockInByProduct[$pid] ?? 0;
        $sold   = $soldByProduct[$pid] ?? 0;
        return [
            'product_id' => $pid,
            'name'       => $stockProductNames[$pid] ?? ('#' . $pid),
            'bought'     => $bought,
            'sold'       => $sold,
            'remaining'  => $bought - $sold,
        ];
    })->sortBy('remaining')->take(10)->values();

    // Return view with all profit and extra cash collection metrics safely included
$thisMonthActualCashReceived = $journalMetrics->this_receive;

    // Update your compact statement at the very bottom to include it:
return view('dashboard', compact(
    'thisMonthSold', 'thisMonthStockIn',
    'buyPercent', 'soldPercent',
    'thisMonthDue', 'allTimeDue',
    'stockRemaining', 'productComparison', 'groupComparison',
    'thisMonthCollectedProfit', 'thisMonthRemainingProfit',
    'lastMonthCollectedProfit', 'lastMonthRemainingProfit',
    'thisMonthExtraCashReceived', 'lastMonthExtraCashReceived',
    'thisMonthActualCashReceived','allTimeAdvanceReceived',
    'returnMetrics', 'allTimeReturn', 'allTimeReturnCost',
    'thisMonthReturnProfit', 'lastMonthReturnProfit', 'allTimeReturnProfit','thisMonthNetSales',
    'thisMonthComppayed' ,
'allTimeComppayed'
));

})->name('dashboard');

    // FIX: Keep ONLY this one for the User List
    Route::get('/userlist', [UserController::class, 'index'])->name('userlist');
// Route for the Group Form
Route::get('/product-group/add', [ProductGroupController::class, 'createGroup'])->name('productgroupadd');
Route::get('/product-store/add', [ProductStoreController::class, 'productStore'])->name('productstore');
Route::get('/get-store-details', [ProductGroupController::class, 'getStoreDetails']);
Route::post('/product/store/save', [ProductGroupController::class, 'storeSave'])->name('product.store.save');
// Route for the Product Form
Route::post('/update-inventory-stock', [ProductStoreController::class, 'updateStockBuy_sell'])->name('stock_sell_cost.update');
Route::post('/stock/bulk-update', [ProductStoreController::class, 'bulkUpdateStock'])->name('stock.bulkUpdate');

Route::get('/product/add', [ProductGroupController::class, 'createProduct'])->name('productadd');
Route::get('/product/return', [ProductStoreController::class, 'returnproductStore'])->name('productreturn');
Route::get('/returns/fetch-invoices', [ProductStoreController::class, 'getreturnInvoicesRC'])->name('returns.fetch-invoices');
Route::get('/returns/fetch-log-items', [ProductStoreController::class, 'getReturnLogItems'])->name('returns.fetch-log-items');
Route::post('/returns/approve-invoice', [ProductStoreController::class, 'approveReturnInvoice'])->name('returns.approve-invoice');
Route::get('/Get/fetch-invoices', [ProductStoreController::class, 'getInvoicesByLedger'])->name('fetch-invoices');
Route::get('/returns/fetch-items', [ProductStoreController::class, 'getInvoiceItems'])->name('returns.fetch-items');

Route::get('/product/Attribute', [ProductGroupController::class, 'sizevariantlist'])->name('productsattribute');
Route::post('/attribute/size/store', [ProductGroupController::class, 'storeSize'])->name('attribute.size.store');
Route::post('/attribute/variant/store', [ProductGroupController::class, 'storeVariant'])->name('attribute.variant.store');
Route::post('/stock/bulk-Return', [ProductStoreController::class, 'ReturnStoreLog'])->name('returnstock.bulkUpdate');
// Use 'storeProduct' if that is what you named your function
Route::post('/invoice/add-item', [ProductGroupController::class, 'addItem'])->name('invoice.addItem');
Route::delete('/invoice/remove-item/{id}', [ProductGroupController::class, 'removeItem'])->name('invoice.removeItem');
Route::post('/product/store', [ProductGroupController::class, 'storeProduct'])->name('product.store');
Route::post('/user-store', [UserController::class, 'store'])->name('users.storedata');
Route::get('/product', [ProductGroupController::class, 'showProduct'])->name('product.view');
Route::get('/products/datatable', [ProductGroupController::class, 'datatable'])->name('products.datatable');
Route::get('/product/{id}/sizes', [ProductGroupController::class, 'sizes'])->name('product.sizes');
Route::post('/product-group/store', [ProductGroupController::class, 'storeGroup'])->name('productgroup.store');
Route::get('/invoice/get-pending', [ProductGroupController::class, 'getPendingItems'])->name('invoice.getPending');
/* Process invoice */
Route::post('/invoice/store', [ProductGroupController::class, 'storeInvoice'])->name('invoice.store');

/* Sr data route */
Route::get('/get-sr-list', [ProductGroupController::class, 'getSrList'])->name('get.sr.list');
Route::get('/get-ledger-list', [ProductGroupController::class, 'getLedgerList'])->name('get.ledger.list');
Route::get('/get-sr-details/{id}', [ProductGroupController::class, 'getSrDetails'])->name('sr.getDetails');
Route::get('/get-ledgers-by-type', [AccountsController::class, 'getLedgersByType']);
// GET: To open the blade form
Route::get('/accounts/receive', [AccountsController::class, 'receiveEntry'])->name('receiveentry');
Route::get('/accounts/Payment', [AccountsController::class, 'paymententry'])->name('paymententry');
Route::get('/accounts/JournalBook',[AccountsController::class, 'journalview'])->name('journalview');
Route::get('/accounts/get-ledgers-by-group', [AccountsController::class, 'getLedgersByGroup'])->name('accounts.getLedgers');

// POST: To process and save data when form is submitted

Route::post('/accounts/receive/store', [AccountsController::class, 'storeReceivePayment'])->name('receive.payment.store');
Route::post('/accounts/Payment/store', [AccountsController::class, 'storePayment'])->name('payment_seller.store');
Route::get('/users/managers', [UserController::class, 'getManagers'])->name('users.managers');

Route::get('/accounts/sr-management', [AccountsController::class, 'index'])->name('accounts.sr.index');
Route::get('/accounts/seller-management', [AccountsController::class, 'index_seller'])->name('accounts.seller.index');
    
    // Add form data submission routing target
Route::post('/accounts/sr-management/store', [AccountsController::class, 'storeStoreRep'])->name('accounts.sr.store');
Route::post('/accounts/seller-management/store', [AccountsController::class, 'storeSeller'])->name('accounts.seller.store');
Route::get('/warranty/index', [WarrantyClaimController::class, 'index'])->name('warranty.index');
Route::post('/warranty/{id}/approve', [WarrantyClaimController::class, 'approve'])->name('warranty.approve');
Route::post('/warranty/{id}/cancel', [WarrantyClaimController::class, 'cancel'])->name('warranty.cancel');
Route::post('/warranty/store', [WarrantyClaimController::class, 'store'])->name('warranty.store');
   /*  // Admin Specific Routes
    Route::get('/admin/users', [AdminController::class, 'index'])->name('admin.users'); */
});
// Add this line for your OLT Devices
/* Route::get('/olts', function () {
    return view('olts.index'); // Make sure this view exists later!
})->middleware(['auth'])->name('olt.index'); */


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';