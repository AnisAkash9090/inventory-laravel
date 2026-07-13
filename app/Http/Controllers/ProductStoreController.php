<?php

namespace App\Http\Controllers;
use App\Models\Product; // Add this
use \App\Models\Size;
use App\Models\ProductStore;
use Illuminate\Http\Request;
use App\Models\ProductStoreLog;
use Illuminate\Support\Facades\DB;
use App\Models\JournalBook;
use App\Models\Seller;
use App\Models\SR;
use \App\Models\ProductReturnLog;
use \App\Models\ReturnInvoiceRc;
use \App\Models\InvoiceRc;
use \App\Models\invoice;

class ProductStoreController extends Controller
{
public function productStore()
{
    // 1. Fetch the groups so the dropdown in the modal has data
    $allGroups = \App\Models\ProductGroup::all();
   
    // 2. Fetch the products to show in your table
    // (Filtering by managerId as we discussed before)
    $managerId = auth()->user()->manager_id  ;
    $products = Product::where('manager', $managerId)
                ->orderBy('id', 'desc')
                ->get();
$availableSizes = Size::forManager($managerId)->get();
  /*   $availableVariants = \App\Models\Variant::forManager($managerId)->get(); */
 $sellers = Seller::where('manager_id', $managerId)
                ->orderBy('name', 'asc')
                ->get();

    // 3. PASS BOTH VARIABLES TO THE VIEW
    return view('products.productstoreupdate', compact('allGroups', 'products','availableSizes', 'sellers'));
}
public function returnproductStore()
{
    // 1. Fetch the groups so the dropdown in the modal has data
    $allGroups = \App\Models\ProductGroup::all();
   
    // 2. Fetch the products to show in your table
    // (Filtering by managerId as we discussed before)
    $managerId = auth()->user()->manager_id ;
    $products = \App\Models\Product::where('manager', $managerId)
                ->orderBy('id', 'desc')
                ->get();
$availableSizes = Size::forManager($managerId)->get();
  /*   $availableVariants = \App\Models\Variant::forManager($managerId)->get(); */
 $returnSR = SR::where('manager_id', $managerId)
                ->orderBy('name', 'asc')
                ->get();

    // 3. PASS BOTH VARIABLES TO THE VIEW
    return view('products.returnProduct', compact('allGroups','availableSizes', 'returnSR','products'));
}
 public function getInvoicesByLedger(Request $request)
    {
        $ledgerId = $request->get('ledger_id');
        
        if (empty($ledgerId)) {
            return response()->json(['success' => false, 'data' => []]);
        }

        $invoices = invoiceRC::where('ledger_id', $ledgerId)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $invoices
        ]);
    }
    public function getReturnLogItems(Request $request)
{
    $invoiceNo = $request->get('invoice_no');
    $ledgerId  = $request->get('ledger_id');

    $items = \DB::table('product_return_logs as prl')
        ->leftJoin('products as p', 'p.id', '=', 'prl.product_id')
        ->leftJoin('sizes as pg', 'pg.id', '=', 'prl.size')
        ->select([
            'prl.id as log_item_id',
            'prl.invoice_no',
            'prl.seller_ledger',
            'prl.manager_id',
            'prl.created_by',
            'prl.approved_by',
            'pg.name as size',
            'prl.qty',
            'prl.price',
            'prl.type',
            'prl.return_date',
            'prl.approve_date',
            'prl.created_at',
            'p.product_name'
        ])
        ->where('prl.invoice_no', $invoiceNo)
        ->orderBy('prl.id', 'asc')
        ->get();

    return response()->json(['success' => true, 'data' => $items]);
}
public function approveReturnInvoice(Request $request)
{    $generatePayment = (int) $request->get('generate_payment', 0);
$paymentDate     = $request->get('payment_date');
    $invoiceId = $request->get('invoice_id');
    $ledgerId  = $request->get('ledger_id');

    if (empty($invoiceId)) {
        return response()->json(['success' => false, 'message' => 'Missing target invoice identifier parameter matrix.'], 400);
    }

    // Begin Database Core Isolation Transaction Block
    \DB::beginTransaction();

    try {
        // 1. Double check current target parent ledger record status configuration to avoid duplicate submissions
        $parentInvoice = \DB::table('return_invoice_rc')
            ->where('invoice_id', $invoiceId)
            ->where('ledger_id', $ledgerId)
            ->first();

        if (!$parentInvoice) {
            return response()->json(['success' => false, 'message' => 'Target invoice matrix not verified in registry.'], 404);
        }

        if ($parentInvoice->status === 'approve') {
            return response()->json(['success' => false, 'message' => 'This return invoice transaction has already been validated and processed.'], 422);
        }

        // 2. Query and collect all child product log items matching this specific invoice context
        $logItems = \DB::table('product_return_logs')
            ->where('invoice_no', $invoiceId)
            ->where('seller_ledger', $ledgerId)
            ->get();

        if ($logItems->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No item nodes verified within this logging layout container.'], 422);
        }

        // 3. Increment individual inventory storage arrays
        foreach ($logItems as $item) {
            
            // Check if product storage row configuration exists
            $storeQuery = \DB::table('product_store')
                ->where('product_id', $item->product_id)
                ->where('size', $item->size);

            if (!$storeQuery->exists()) {
                throw new \Exception("Warehouse Storage Exception: Product ID #{$item->product_id} with size '{$item->size}' does not have an active matching row footprint established inside the product_store matrix table.");
            }

            // Increment the inventory quantity directly inside SQL execution
            $storeQuery->increment('qty', (int)$item->qty);
        }

        // 4. Update the structural statuses inside parent and child tables
        \DB::table('return_invoice_rc')
            ->where('invoice_id', $invoiceId)
            ->where('ledger_id', $ledgerId)
            ->update([
                'status' => 'approve',
                'updated_at' => now()
            ]);

        \DB::table('product_return_logs')
            ->where('invoice_no', $invoiceId)
            ->where('seller_ledger', $ledgerId)
            ->update([
                
            'invoice_date' => $paymentDate,
                'approved_by' =>  auth()->user()->id ?? 1,
                'approve_date' => now(),
                'updated_at' => now()
            ]);

        // Commit execution block transactions cleanly to physical hardware layers
        \DB::commit();
        
        $summaryRemarks = "Sales Return: - Inv #{$invoiceId}";
/* First making sales return  */
              JournalBook::create([
            'dr_ledger'        => 15, // Default Purchase Account ID
            'cr_ledger'        => $ledgerId,
            'journal_type'    => 7,
            'amount'           => $parentInvoice->amount,
            'remarks'          => $summaryRemarks,
            'invoice_id'       => $invoiceId,
            'transaction_date' => $paymentDate,
            'manager_id'       => auth()->user()->manager_id,
            'created_by'       => auth()->user()->id
        ]);

        /* Restoring the inventory  */
        
        $summaryRemarksrs = "Sales Return Restock: - Inv #{$invoiceId} Cost of profuct {$parentInvoice->cost}";
              JournalBook::create([
            'dr_ledger'        => 10, // Default Purchase Account ID
            'cr_ledger'        => 14,
            'journal_type'    => 6,
            'amount'           => $parentInvoice->cost,
            'remarks'          => $summaryRemarksrs,
            'invoice_id'       => $invoiceId,
            'transaction_date' => $paymentDate,
            'manager_id'       => auth()->user()->manager_id,
            'created_by'       => auth()->user()->id
        ]);

if ($generatePayment === 1 && !empty($paymentDate)) {
     $summaryRemarkspaid = "Bulk Return: - Inv #{$invoiceId} > payment Done";
             JournalBook::create([
            'dr_ledger'        => $ledgerId,
            'cr_ledger'        => 3,
            'journal_type'    => 4,
            'amount'           => $parentInvoice->amount,
            'remarks'          => $summaryRemarkspaid,
            'invoice_id'       => $invoiceId,
            'transaction_date' => $paymentDate,
            'manager_id'       => auth()->user()->manager_id,
            'created_by'       => auth()->user()->id
        ]);
}
        return response()->json([
            'success' => true,
            'message' => "Invoice #{$invoiceId} status marked 'Approved' and item quantities successfully returned to structural warehouse store listings!"
        ]);

    } catch (\Exception $e) {
        // Rollback instantly on any data constraint exceptions
        \DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Database Processing Error: ' . $e->getMessage()
        ], 500);
    }
}
 public function getreturnInvoicesRC(Request $request)
    {
        $ledgerId = $request->get('ledger_id');
        
        if (empty($ledgerId)) {
            return response()->json(['success' => false, 'data' => []]);
        }

        $invoices = ReturnInvoiceRc::where('ledger_id', $ledgerId)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $invoices
        ]);
    }
    /**
     * Step 2: Get all sub-items inside 'invoices' table once a specific invoice number is selected
     */
public function getInvoiceItems(Request $request)
{
    $invoiceNo = $request->get('invoice_no');

    if (empty($invoiceNo)) {
        return response()->json(['success' => false, 'message' => 'No active invoice target identifier selected'], 400);
    }

    try {
        // Explicitly structural aliases prevent database runtime property collisions
        $items = \DB::table('invoices as i')
            ->leftJoin('products as p', 'p.id', '=', 'i.product_id')
            ->leftJoin('sizes as pg', 'pg.id', '=', 'i.size')
            ->select([
                'i.id as invoice_item_id',
                'i.invoice_no',
                'i.product_id as product_raw_id',
                  'p.product_name as product_name',
                'pg.name as size',
                'i.qty',
                'i.price',
                'i.cost',
                'i.manager_id',
               
               
            ])
            ->where('i.invoice_no', $invoiceNo)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $items
        ]);

    } catch (\Exception $e) {
        // Sends structural tracking context back downstream to your jQuery error window
        return response()->json([
            'success' => false,
            'message' => 'Database Query Engine Fault: ' . $e->getMessage()
        ], 500);
    }
}
    /**
     * Store item inside product_return_logs array mapping via single AJAX action fire
     */
 
    public function store(Request $request)
    {
        // 1. Validation
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'costprice'  => 'required|numeric|min:0',
            'sellprice'  => 'required|numeric|min:0.1',
            'size'       => 'required|string',
            'qty'        => 'required|integer|min:0',
            'img'        => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // 2. Duplicate Check (Product + Size)
        $exists = ProductStore::where('product_id', $request->product_id)
                    ->where('size', $request->size)
                    ->exists();

        if ($exists) {
            return redirect()->back()
                ->with('error_message', 'This product size already exists in the store inventory.');
        }

        // 3. Handle Image Upload
        $imageName = null;
        if ($request->hasFile('img')) {
            $imageName = time() . '.' . $request->img->extension();
            $request->img->move(public_path('images/product_store'), $imageName);
        }

        // 4. Create Record
        $store = new ProductStore();
        $store->product_id = $request->product_id;
        $store->size       = $request->size;
        $store->price      = $request->costprice; // Buying price
        $store->sell_price = $request->sellprice; // Selling price
        $store->qty        = $request->qty;
  
        $store->img        = $imageName;
        $store->save();

        return redirect()->back()->with('success_message', 'Product added to inventory successfully!');
    }

    /**
     * AJAX Method: Get details for specific product and size
     */
    public function getDetails(Request $request)
    {
        $details = ProductStore::where('product_id', $request->product_id)
                    ->where('size', $request->size)
                    ->first();

        if ($details) {
            return response()->json([
                'qty'   => $details->qty,
                'price' => $details->sell_price, // Return selling price for invoice
                'cost'  => $details->price       // Return cost for reference
            ]);
        }

        return response()->json(null, 404);
    }

public function updateStockBuy_sell(Request $request)
{
    $request->validate([
        'product_id' => 'required|integer',
        'size_id'    => 'required|integer',
        'price'      => 'required|numeric|min:0',
        'sell_price' => 'required|numeric|min:0',
        'sold_total' => 'required|integer',
    ]);

    $costPrice    = (float)$request->price;
    $sellingPrice = (float)$request->sell_price;

    if ($costPrice > $sellingPrice) {
        return redirect()->back()
            ->withInput() 
            ->with('error_message', 'Validation Error: Cost Price cannot be higher than the Retail Selling Price!');
    }

    $currentStoreRow = DB::table('product_store') 
        ->where('product_id', $request->product_id)
        ->where('size', $request->size_id)
        ->first();

    if (!$currentStoreRow) {
        return redirect()->back()->with('error_message', 'Target product stock record context missing!');
    }

    $previousCost        = (float)($currentStoreRow->price ?? 0);
    $previousSellingRate = (float)($currentStoreRow->sell_price ?? 0);

    if ($costPrice === $previousCost && $sellingPrice === $previousSellingRate) {
        return redirect()->back()
            ->withInput()
            ->with('error_message', 'Action Canceled: This exact configuration already exists.');
    }

    $managerId = auth()->user()->manager_id ?? 1;
    $userId    = auth()->user()->id ?? 1;

    $logMessage = "Base rates modified. Cost shifted from ৳{$previousCost} to ৳{$costPrice}. Retail Sale Rate shifted from ৳{$previousSellingRate} to ৳{$sellingPrice}.";

    DB::beginTransaction();
    try {
        DB::table('product_store') 
            ->where('product_id', $request->product_id)
            ->where('size', $request->size_id)
            ->update([
                'price'      => $costPrice,      
                'sell_price' => $sellingPrice, 
                'updated_at' => now(),
            ]);

        // Fix: Log if EITHER the cost or selling rate changed, ensuring consistency with your duplicate check
        if ($previousCost !== $costPrice || $previousSellingRate !== $sellingPrice) {
            DB::table('product_rate_logs')->insert([
                'product_id' => $request->product_id,
                'manager_id' => $managerId,
                'created_by' => $userId,
                'prev_rate'  => $previousSellingRate,
                'new_rate'   => $sellingPrice,
                'sold_total' => $request->sold_total, 
                'sale_rate'  => $sellingPrice,
                'log'        => $logMessage,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::commit();

        // FIXED: Explicitly pass back the success string AND the active product ID context
        return redirect()->back()->with([
            'success_message'   => 'Rates re-configured and logged into system archives successfully!',
            'active_product_id' => $request->product_id
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error_message', 'Execution error: ' . $e->getMessage());
    }
}
public function bulkUpdateStock(Request $request)
{
    // Validate master tracking variables along with the repeated item fields
    $request->validate([
        'invoiceno'          => 'required|string',
        'seller_id'          => 'required',
        'buydate'            => 'required|date',
        'items'              => 'required|array|min:1',
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.size'       => 'required',
        'items.*.qty'        => 'required|integer|min:1',
        'items.*.costprice'  => 'required|numeric|min:0',
    ]);

    DB::beginTransaction();
    try {
        $totalInvoiceAmount = 0;
        $itemsSummaryStrings = [];

        foreach ($request->items as $item) {
            // 1. Verify existence in Store setup records
            $store = ProductStore::where('product_id', $item['product_id'])
                ->where('size', $item['size'])
                ->first();

            if (!$store) {
                DB::rollback();
                return redirect()->back()->with('error_message', "Match missing: Initialise product parameters for selected size variants first.");
            }

            // 2. Create the standalone individual Product Log Entry
            ProductStoreLog::create([
                'product_id'   => $item['product_id'],
                'size'         => $item['size'],
                'manager_id'   => auth()->user()->manager_id,
                'createdBy'    => auth()->user()->id,
                'qty'          => $item['qty'],
                'cost'         => $item['costprice'],
                'sellerledger' => $request->seller_id,
                'buydate'      => $request->buydate,
                'invoiceno'    => $request->invoiceno,
            ]);

            // 3. Increment variant quantities 
   $store->increment('qty', $item['qty'], [
    'price' => $item['costprice']
]);

            // 4. Increment standard product table scope sums
            Product::where('id', $item['product_id'])->increment('quantity', $item['qty']);

            // Track item properties to create a combined invoice summary string
            $lineTotal = $item['qty'] * $item['costprice'];
            $totalInvoiceAmount += $lineTotal;

            $productName = Product::find($item['product_id'])->product_name ?? 'Product';
            $itemsSummaryStrings[] = "{$productName} (x{$item['qty']})";
        }

        // Build string listing contents under the single invoice summary
        $summaryRemarks = "Bulk Purchase: " . implode(', ', $itemsSummaryStrings) . " - Inv #{$request->invoiceno}";

        // 5. Generate exactly ONE journal entry containing aggregate summary parameters
        JournalBook::create([
            'dr_ledger'        => 10, // Default Purchase Account ID
            'cr_ledger'        => $request->seller_id,
            'journal_type'    => 5,
            'amount'           => $totalInvoiceAmount,
            'remarks'          => $summaryRemarks,
            'invoice_id'       => $request->invoiceno,
            'transaction_date' => $request->buydate,
            'manager_id'       => auth()->user()->manager_id,
            'created_by'       => auth()->user()->id
        ]);

        DB::commit();
        return redirect()->back()->with('success_message', 'Invoice entry processed and store inventory successfully updated!');

    } catch (\Exception $e) {
        DB::rollback();
        return redirect()->back()->with('error_message', 'System Processing Error: ' . $e->getMessage());
    }
}public function ReturnStoreLog(Request $request)
{
    // Validate master tracking variables along with the repeated item fields
    $request->validate([
        'invoiceno'          => 'required|string',
        'seller_id'          => 'required',
        'buydate'            => 'required|date',
        'items'              => 'required|array|min:1',
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.size'       => 'required',
        'items.*.qty'        => 'required|integer|min:1',
        'items.*.costprice'  => 'required|numeric|min:0',
        'items.*.sell_price'  => 'required|numeric|min:0',
        'items.*.type'       => 'required|string|in:damage,solid',
    ]);
 

    DB::beginTransaction();
    try {
        $totalAmount = 0;
        $totalCost   = 0;
        $totalQTY  =0;
        // 1. Fetch vendor Name & Address from your SR/Ledger table dynamically
        // Replace 'AccountLedger' with your actual SR/Ledger model name
        $srAccount = \DB::table('ledger')->where('id', $request->seller_id)->first();
        
        $vendorName    = $srAccount->name ?? 'Unknown Vendor';
        $vendorAddress = $srAccount->address ?? null;

        // 2. Loop through items to validate and calculate financial metrics
        foreach ($request->items as $item) {
            // Verify existence in Store setup records
            $store = ProductStore::where('product_id', $item['product_id'])
                ->where('size', $item['size'])
                ->first();

            if (!$store) {
                DB::rollback();
                return redirect()->back()->with('error_message', "Match missing: Initialize product parameters for selected size variants first.");
            }

            // Create individual Product Log Entry
            ProductReturnLog::create([
                'product_id'    => $item['product_id'],
                'manager_id'    => auth()->user()->manager_id ?? 1,
                'created_by'    => auth()->user()->id,
                'approved_by'   => null,
                'size'          => $item['size'],
                'qty'           => $item['qty'],
                'price'         => $item['sell_price'],
                'cost'          => $item['costprice'], 
                'invoice_no'    => $request->invoiceno,
                'seller_ledger' => $request->seller_id,
                'type'          => $item['type'],
                'return_date'   => $request->buydate,
                'approve_date'  => null,
            ]);

            // Calculate operational summaries
            $lineTotal   = $item['qty'] * $item['costprice'];
            $lineTotalsell_price   = $item['qty'] * $item['sell_price'];
            $totalAmount += $lineTotalsell_price;
            
            // If you track distinct unit cost separate from selling price, calculate here. 
            // Otherwise, mapping line total to both works as a default financial baseline.
            $totalCost   += $lineTotal; 
            $totalQTY += $item['qty'];
        }

        // 3. Create the Parent Master Invoice Return Tracker Record
        // Maps exactly to your return_invoice_rc schema
        \DB::table('return_invoice_rc')->insert([
            'invoice_id'   => $request->invoiceno,
            'ledger_id'    => $request->seller_id,
            'manager_id'   => auth()->user()->manager_id ,
            'created_by'   => auth()->user()->id,
            'name'         => $vendorName,
            'address'      => $vendorAddress,
            'amount'       => $totalAmount,
            'cost'         => $totalCost,
            'qty'         => $totalQTY,
            'remarks'      => $request->get('remarks', 'Bulk product return session initialized.'),
            'status'       => 'pending',
            'invoice_date' => $request->buydate,
            'approve_date' => null,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        DB::commit();
        return redirect()->back()->with('success_message', 'Invoice data and individual product return logs successfully stored.');

    } catch (\Exception $e) {
        DB::rollback();
        return redirect()->back()->with('error_message', 'System Processing Error: ' . $e->getMessage());
    }
}
}