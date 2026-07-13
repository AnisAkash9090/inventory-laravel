<?php
namespace App\Http\Controllers;
use App\Models\JournalBook;
use App\Models\Product; // Add this
use App\Models\ProductGroup; 
use \App\Models\Size;
use Illuminate\Http\Request;
use App\Models\Invoice;
use \App\Models\InvoiceRc;

use Illuminate\Support\Facades\DB;
class ProductGroupController extends Controller 
{
    public function index(Request $request)
    {
        // 1. Get the group_id from the URL (if it exists)
        $groupId = $request->query('group_id');

        // 2. Query Products: returnProduct
        // If $groupId is present, filter by it. If not, get all.
        $products = Product::when($groupId, function ($query, $groupId) {
            return $query->where('group_id', $groupId);
        })
        ->orderBy('create_date', 'desc')
        ->get();

        // 3. Return your main dashboard/index view (NOT the sidebar component)
        // We pass 'products' to the main page.
        return view('dashboard', compact('products'));
    }
    // Inside ProductGroupController.php

public function create()
{
    // Fetch groups so the user can select one in a dropdown
    $groups = ProductGroup::all();
    
    return view('products.add', compact('groups'));
}

public function showProduct(Request $request)
{
    $groupId = $request->query('product');
    $managerId = auth()->user()->manager_id ;

    if (empty($groupId) || $groupId == '0') {
        $groupt = null; 
        $products = \App\Models\Product::where('manager', $managerId)
                        ->orderBy('product_name', 'asc')
                        ->get();
                        
        return view('products.product', compact('groupt', 'products'));
    }

    // CASE 2: Flat list with Group Context
    if ($groupId === 'all') {
        // Fetch all products for this manager and eager load their groups
        $products = \App\Models\Product::with('group') // assuming relation name is 'group'
                        ->where('manager', $managerId)
                        ->orderBy('product_name', 'asc')
                        ->get();

        return view('products.product', [
            'view_mode' => 'all_flat',
            'groupt'    => null,
            'products'  => $products
        ]);
    }

    // CASE 3: Single Group
    $groupt = \App\Models\ProductGroup::with(['products' => function($query) use ($managerId) {
        $query->where('manager', $managerId)->orderBy('product_name', 'asc'); 
    }])->find($groupId);

    $products = $groupt ? $groupt->products : collect([]);

    return view('products.product', [
        'view_mode' => 'single',
        'groupt'    => $groupt,
        'products'  => $products
    ]);
}

public function createGroup()
{
    $managerId = auth()->user()->manager_id;

    // Fetch groups where manager_id matches
    $groups = \App\Models\ProductGroup::where('manager_id', $managerId)
                ->orderBy('id', 'desc')
                ->get();

    return view('products.add_group', compact('groups'));
}

    // VIEW 2: Show the form to add a PRODUCT
// app/Http/Controllers/ProductGroupController.php
// Controller
public function datatable(Request $request)
{
    $viewMode = $request->input('view_mode');
    $groupId  = $request->input('group_id');

    $query = Product::with(['stores', 'group']);

    if ($groupId) {
        $query->where('group_id', $groupId);
    }

    // No search / sort / skip / take here — fetch everything matching the scope
    $products = $query->orderBy('product_name', 'asc')->get();

    $data = $products->map(function ($product) use ($viewMode) {

        $img = $product->img ? asset('images/product/'.$product->img) : asset('images/default-product.png');
        $photoHtml = '<img src="'.$img.'" loading="lazy" class="product-img shadow-sm" alt="img" style="width:50px;height:50px;object-fit:cover;border-radius:8px;">';

        $groupBadge = '';
        if ($viewMode === 'all_flat' && $product->group) {
            $groupBadge = '<span class="badge bg-info text-white ms-2 px-2 py-0.5" style="font-size:10px;">
                <i class="fa fa-folder-open me-1"></i>'.e($product->group->product_group).'</span>';
        }
        $productHtml = '<div class="d-flex flex-column">
            <div class="fw-bold text-dark">'.e($product->product_name).'</div>
            <div class="d-flex align-items-center gap-2 mt-1">
                <small class="text-muted" style="margin-right:20px;">ID: #'.$product->id.'</small>'.$groupBadge.'
            </div>
        </div>';

        $inventoryHtml = '<select class="form-control form-control-sm size-selector mb-2 shadow-none" data-product-id="'.$product->id.'">
                <option value="">Select Size</option>
            </select>
            <div class="d-flex gap-2">
                <div class="badge rounded-pill bg-light border text-primary flex-fill py-2">
                    <span class="text-uppercase opacity-75" style="font-size:10px;">Stock:</span>
                    <span id="stock-'.$product->id.'" class="fw-bold">0</span>
                </div>
                <div class="badge rounded-pill bg-light border text-success flex-fill py-2 cost-reveal">
                    <span class="text-uppercase opacity-75" style="font-size:10px;">Cost:</span>
                    <span id="cost-'.$product->id.'" class="fw-bold cost-value">0.00</span>
                </div>
            </div>';

        $actionHtml = '<div class="input-group input-group-sm align-items-stretch">
            <div class="d-flex flex-column flex-grow-1 me-1">
                <div class="d-flex mb-1">
                    <input type="hidden" id="price_get-'.$product->id.'">
                    <input type="number" id="price-'.$product->id.'" class="form-control me-1" placeholder="Price per QTY.[TK]" step="0.01">
                    <input type="number" id="qty-'.$product->id.'" class="form-control text-center" min="0.1" style="max-width:85px;" placeholder="Qty" step="0.01">
                </div>
                <div class="input-group input-group-sm">
                    <input type="number" id="discount-'.$product->id.'" class="form-control text-center" min="0" max="100" placeholder="Discount per QTY.[TK]">
                    <button class="btn btn-secondary add-to-invoice d-flex align-items-center justify-content-center px-3" data-id="'.$product->id.'" disabled style="border-radius:0 4px 4px 0;">
                        <i class="fa fa-plus"></i>
                    </button>
                </div>
            </div>
        </div>';

        return [
            'photo' => $photoHtml,
            'product' => $productHtml,
            'inventory' => $inventoryHtml,
            'action' => $actionHtml,
        ];
    });

    // Plain array response — no draw/recordsTotal/recordsFiltered needed
    return response()->json([
        'data' => $data,
    ]);
}
public function sizes($id)
{
    $product = Product::with('stores')->findOrFail($id);
    $allSizes = Size::whereIn('id', $product->size ?? [])->get();

    $options = $allSizes->map(function ($size) use ($product) {
        $store = $product->stores->firstWhere('size', $size->id);
        return [
            'id' => $size->id,
            'name' => $size->name,
            'cost' => $store->price ?? 0,
            'sell' => $store->sell_price ?? 0,
        ];
    });

    return response()->json($options);
}
public function createProduct()
{
    // 1. Fetch the groups so the dropdown in the modal has data
    $allGroups = \App\Models\ProductGroup::all();
    
    // 2. Fetch the products to show in your table
    // (Filtering by managerId as we discussed before)
    $managerId = auth()->user()->manager_id ?? 0;
    $products = \App\Models\Product::where('manager', $managerId)
                ->orderBy('id', 'desc')
                ->get();
$availableSizes = Size::forManager($managerId)->get();
    $availableVariants = \App\Models\Variant::forManager($managerId)->get();


    // 3. PASS BOTH VARIABLES TO THE VIEW
    return view('products.add_product', compact('allGroups', 'products','availableSizes', 'availableVariants'));
}
public function productsizeadd()
{
    // 1. Fetch the groups so the dropdown in the modal has data
    $allGroups = \App\Models\ProductGroup::all();
    
    // 2. Fetch the products to show in your table
    // (Filtering by managerId as we discussed before)
    $managerId = auth()->user()->manager_id ?? 0;
    $products = \App\Models\Product::where('manager', $managerId)
                ->orderBy('id', 'desc')
                ->get();

    // 3. PASS BOTH VARIABLES TO THE VIEW
    return view('products.add_product', compact('allGroups', 'products'));
}
public function storeProduct(Request $request) 
{
    // 1. Basic Validation
    $request->validate([
        'product_name' => 'required',
        'img' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    $product = new \App\Models\Product();
    $product->product_name = $request->product_name;
    $product->group_id     = $request->group_id;
  
    $product->createdBy    = auth()->user()->id ?? 0;
    $product->manager    = auth()->user()->manager_id ?? 0;
    // 2. Handle Image Upload to Public Folder
    if ($request->hasFile('img')) {
        $image = $request->file('img');
        
        // Create a unique filename
        $imageName = time() . '_' . $image->getClientOriginalName();
        
        // Move the file to public/images/product
        $image->move(public_path('images/product'), $imageName);
        
        // Save only the filename in the DB
        $product->img = $imageName;
    }

    $product->save();
    
    return redirect()->back()->with('success', 'Product added successfully!');
}
public function storeGroup(Request $request) 
{
    // 1. Validation
    $request->validate([
        'product_group' => 'required|unique:products_group,product_group',
    ]);

    // 2. Create the Group
    $group = new \App\Models\ProductGroup();
    $group->product_group = $request->product_group;
    
    // 3. Track the Managers/Users
    $group->manager_id = auth()->user()->manager_id ?? 0;
    $group->created_by = auth()->user()->id; // Store the id of who created it
    
    $group->save();
    
    return redirect()->back()->with('success', 'New Group created successfully!');
}

public function storeSave(Request $request) 
{
    $request->validate([
        'product_id' => 'required|exists:products,id',
        'costprice'  => 'required|numeric',
        'sellprice'      => 'required|numeric',
        'img'        => 'nullable|image|max:2048'
    ]);
    
    // Check if the combination of Product ID and Size already exists
    $exists = \App\Models\ProductStore::where('product_id', $request->product_id)
                ->where('size', $request->size)
                ->exists();

    if ($exists) {
        return redirect()->back()->with('error_message', 'This product size already exists in the store!');
    }
    
    $store = new \App\Models\ProductStore();
    $store->product_id = $request->product_id;
    $store->size       = $request->size;
    $store->alertqty   = $request->stock_alert;
    $store->sell_price = $request->sellprice;
    $store->price      = $request->costprice;

    if ($request->hasFile('img')) {
        $imageName = time() . '_store_' . $request->file('img')->getClientOriginalName();
        $request->file('img')->move(public_path('images/product/store'), $imageName);
        $store->img = $imageName;
    }

    $store->save();
  $product = \App\Models\Product::find($request->product_id);

    // Get existing arrays or start new ones if they are empty
    $currentSizes = $product->size ?? [];
    $currentVariants = $product->variant ?? [];

    // Add the new data to the arrays (if not already there)
    if (!in_array($request->size, $currentSizes)) {
        $currentSizes[] = $request->size;
    }
    if (!in_array($request->variant, $currentVariants)) {
        $currentVariants[] = $request->variant;
    }

    // Update the product: Increment quantity AND save the new JSON
    $product->update([
        'quantity' => $product->quantity + $request->qty,
        'size'     => $currentSizes, // Laravel casts this to JSON automatically
        'variant'  => $currentVariants // Laravel casts this to JSON automatically
    ]);
    return redirect()->back()->with('success_message', 'Product added to store and total quantity updated!');

}

public function sizevariantlist()
{
    $managerId = auth()->user()->manager_id;

    // This uses the scope we defined in the Models
    $availableSizes = Size::forManager($managerId)->get();
    $availableVariants = \App\Models\Variant::forManager($managerId)->get();

    return view('products.sizevariant', compact('availableSizes', 'availableVariants'));
}
public function storeSize(Request $request)
{
    $request->validate(['name' => 'required']);

   Size::create([
        'name' => $request->name,
        'manager_id' => auth()->user()->manager_id // Auto-tag to the manager
    ]);

    return redirect()->back()->with('success', 'Size added!');
}

public function storeVariant(Request $request)
{
    $request->validate(['name' => 'required']);

    \App\Models\Variant::create([
        'name' => $request->name,
        'manager_id' => auth()->user()->manager_id
    ]);

    return redirect()->back()->with('success', 'Variant added!');
}
public function addItem(Request $request) 
{
    \DB::beginTransaction();

    try {
        if (!session()->has('active_invoice_no')) {
            session(['active_invoice_no' => 'pending']);
        }

        $invoiceNo = session('active_invoice_no');
        $product = \App\Models\Product::findOrFail($request->product_id);

        // 1. Get the actual Store record using first()
        $storeRecord = \App\Models\ProductStore::where('product_id', $request->product_id)
            ->where('size', $request->size)
            ->first();

        if (!$storeRecord) {
            throw new \Exception("Product size not found in store.");
        }
$requestedQty = (float) $request->qty;
        $availableStock = (float) $storeRecord->qty; // Variant specific stock

        if ($requestedQty <= 0) {
            throw new \Exception("Please enter a valid quantity greater than 0.");
        }

        if ($requestedQty > $availableStock) {
            // Throwing an exception triggers the catch block below, 
            // which safely rolls back the transaction and returns a 500/422 status
            throw new \Exception("Insufficient stock! Available quantity for this size is: " + $availableStock);
        }
        // 2. Create the Invoice Item
        $invoiceItem = Invoice::create([
            'invoice_no' => $invoiceNo,
            'product_id' => $request->product_id,
            'size'       => $request->size,
            'qty'        => $request->qty,
            'price'      => $request->price,
            'discount'   => $request->discount ?? 0,
            // Use the cost from the record we just fetched
            'cost'       => $storeRecord->price ?? 0, // Ensure 'price' is the cost column in ProductStore
            'status'     => $request->status, 
            'createdBy'  => auth()->id(),
            'manager_id' => auth()->user()->manager_id,
            'group_id'   => $product->group_id,
        ]);

        // 3. Update Main Product Table
        $product->decrement('quantity', $request->qty);

        // 4. Update Product Store 
        // We can reuse the $storeRecord object to decrement
        $storeRecord->decrement('qty', $request->qty);
       $storeRecord->increment('sold', $request->qty);
        \DB::commit(); 
      return response()->json([
            'success'      => true, 
            'invoice_no'   => $invoiceNo,
            'product_name' => $product->product_name // <-- ADDED
        ]);

    } catch (\Exception $e) {
        \DB::rollBack(); 
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}
public function storeInvoice(Request $request)
{
    $managerId = auth()->user()->manager_id;
    $userId = auth()->user()->id;
    
    // Generate the unique Invoice Number
    $invoiceNo = $managerId . $userId . substr(time(), -6);

    // Use a transaction to guarantee data integrity across all 3 tables
    return DB::transaction(function () use ($request, $managerId, $userId, $invoiceNo) {
        
        // 1. Update the original pending items to this final generated invoice number
        $updated = Invoice::where('manager_id', $managerId)
            ->where('createdBy', $userId)
            ->where('status', 'pending')
            ->update([
                'invoice_no' => $invoiceNo,
                'status'     => 'complete',
                'created_at' =>  $request->invoice_date
            ]);

        if ($updated) {
            // 2. Create the main customer invoice master record
            $invoicercd = InvoiceRc::create([
                'invoice_id'   => $invoiceNo,
                'name'         => $request->customer_name,
                'address'      => $request->address,
                'ledger_id'    => $request->ledger_id,
                'invoice_date' => $request->invoice_date,
                'amount'       => $request->amount,
                'manager_id'   => $managerId,
                'createdBy'    => $userId,
                'discount'     => $request->discount,
                'cost'         => $request->cost,
                'create_date'  => now()
            ]);

            // 3. Create the double-entry accounting ledger entry (Accrual Rule)

            JournalBook::create([
                // DEBIT: The Customer's Ledger ID (Increases Asset / Accounts Receivable)
                'dr_ledger'        => $request->ledger_id, 
                'cr_ledger'        => 5, 
                'journal_type'     =>1,
                'amount'           => $request->amount,
                // FIXED: Changed $request->invoiceno to the freshly generated $invoiceNo variable
                'remarks'          => "Unpaid Invoice #{$invoiceNo} - for Customer: {$request->customer_name} [Net Amount: {$request->net_amount} - Discount:{$request->discount}  Grand Total: {$request->amount}]",
                'invoice_id'       => $invoiceNo, 
                'transaction_date' => $request->invoice_date,
                'manager_id'       => $managerId,
                'created_by'       => $userId
            ]);
    JournalBook::create([
            'dr_ledger'        => 14, // Default Purchase Account ID
            'cr_ledger'        => 10,
            'journal_type'     =>2,
            'amount'           => $request->cost,
            'remarks'          => "Invoice #{$invoiceNo} - for Customer: {$request->customer_name} [Cost: {$request->cost}  ]",
            'invoice_id'       => $invoiceNo, 
            'transaction_date' => $request->invoice_date,
            'manager_id'       => $managerId,
            'created_by'       => $userId
        ]);

        DB::commit();
            return response()->json([
                'success'         => true,
                'invoice_no'      => $invoiceNo,
                'amount'          => $request->amount,
                'invoice_details' => $invoicercd
            ]);
        }

        return response()->json([
            'success' => false, 
            'message' => 'No pending invoice found.'
        ], 404);
    });
}
public function getPendingItems() // Match this to your route
{
    $invoiceNo = 'pending';
    
    $items = \App\Models\Invoice::with('product')
                ->where('invoice_no', $invoiceNo)
                ->where('manager_id', auth()->user()->manager_id)
                ->where('createdBy', auth()->user()->id)
                ->orderByDesc('id')
                ->get();
 
    return response()->json(['items' => $items]);
}
public function removeItem($id)
{
    \DB::beginTransaction();
    try {
        $item = \App\Models\Invoice::findOrFail($id);
        $productId = $item->product_id; 
        $itemQty   = $item->qty; // <-- CAPTURED: Grab the item quantity before deleting

        // Fetch the product name first before modifying inventory layout data
        $product = \App\Models\Product::find($productId);
        $productName = $product ? $product->product_name : 'Product';

        // Increment stock back
        \App\Models\Product::where('id', $productId)->increment('quantity', $itemQty);
        
        $storeRecord = \App\Models\ProductStore::where('product_id', $productId)
            ->where('size', $item->size)
            ->first();
            
        if ($storeRecord) {
            $storeRecord->increment('qty', $itemQty);
            $storeRecord->decrement('sold', $itemQty);
        }

        // Complete the deletion operation
        $item->delete();

        \DB::commit();
        
        // FIXED: Returning quantity, product_name, and product_id together
        return response()->json([
            'success'      => true, 
            'product_id'   => $productId,
            'product_name' => $productName,
            'qty'          => $itemQty // <-- ADDED TO RESPONSE
        ]);

    } catch (\Exception $e) {
        \DB::rollBack();
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}
public function getStoreDetails(Request $request){
    $details =\App\Models\ProductStore::Where('product_id',$request->product_id)
              ->where('size',$request->size)
              ->first();
            return response()->json($details);
}
/* SR information  */
// In ProductGroupController.php
public function getSrList()
{
    // Debug: Check if auth user exists
    if (!auth()->check()) {
        return response()->json(['error' => 'Unauthenticated'], 401);
    }

    $srs = \App\Models\SR::where('manager_id', auth()->user()->manager_id)
                         ->where('status', 'Active')
                         ->get();
                         
    return response()->json($srs);
}

public function getLedgerList(Request $request)
{
    if (!auth()->check()) {
        return response()->json(['error' => 'Unauthenticated'], 401);
    }

    $managerId = auth()->user()->manager_id;
    $account = $request->account;

   $ledger = \App\Models\Ledger::where('account_group', $account)
    ->where(function ($query) use ($managerId) {
        $query->where('manager_id', $managerId)
              ->orWhere(function ($q) {
                  $q->where('manager_id', 0)
                    ->where('status', 1);
              });
    })
    ->get();
   

    return response()->json($ledger);
}
public function getSrDetails($id)
{
    $sr = \App\Models\SR::where('manager_id', auth()->user()->manager_id)
                        ->where('ledger', $id)
                        ->first();
    
    return response()->json($sr);
}
}