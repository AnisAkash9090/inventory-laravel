<?php

namespace App\Http\Controllers;

use App\Models\Product; // Add this
use Illuminate\Http\Request;
use App\Models\SR;
use \App\Models\Size;
use App\Models\Seller;
class WarrantyClaimController extends Controller
{public function index()
{  $managerId = auth()->user()->manager_id  ;
  $claims = \DB::table('warranty_claims')
    ->leftJoin('products', 'products.id', '=', 'warranty_claims.product_id')
    ->leftJoin('sizes', 'sizes.id', '=', 'warranty_claims.size')
    ->leftJoin('sr as sr', 'sr.ledger', '=', 'warranty_claims.seller_ledger_id')
    ->leftJoin('sellers as seller', 'seller.ledger', '=', 'warranty_claims.vendor_ledger_id')
    ->select(
        'warranty_claims.*',
        'products.product_name',
        'sizes.name as size_name',
        'sr.name as sr_name',
        'seller_ledger_id',
        'seller.name as sellers_name'
    )
    ->orderBy('warranty_claims.id', 'desc')
    ->get();
 $vendors = Seller::where('manager_id', $managerId)
                ->orderBy('name', 'asc')
                ->get();
    $products =   Product::where('manager', $managerId)
                ->orderBy('id', 'desc')
                ->get();
    $allSizes     =  Size:: orderBy('name')->get(); // keyBy for quick lookup in JS
    $returnSR = SR::where('manager_id', $managerId)
                ->orderBy('name', 'asc')
                ->get();

    return view('products.warrenty', compact('claims', 'products', 'allSizes', 'returnSR','vendors'));
}

public function store(Request $request)
{
    $request->validate([
        'product_id'          => 'required|integer',
        'size'                => 'required|string',
        'seller_ledger_id'    => 'required|integer',
        'qty'                 => 'required|integer|min:1',
        'invoice_no'          => 'nullable|string|max:50',
        'client_claim_remarks'=> 'nullable|string',
        'client_claim_date'   => 'required|date',
    ]);

    \DB::table('warranty_claims')->insert([
        'invoice_no'           => $request->invoice_no,
        'seller_ledger_id'     => $request->seller_ledger_id,
        'product_id'           => $request->product_id,
        'size'                 => $request->size,
        'qty'                  => $request->qty,
        'status'               => 'claimed_by_client',
        'client_claim_remarks' => $request->client_claim_remarks,
        'client_claim_date'    => $request->client_claim_date,
        'create_by'            => auth()->id(),
        'created_at'           => now(),
        'updated_at'           => now(),
    ]);

    return response()->json(['status' => 'success', 'message' => 'Warranty claim added successfully']);
}

public function approve(Request $request, $id)
{
    $claim = \DB::table('warranty_claims')->where('id', $id)->first();

    if (!$claim) {
        return response()->json(['status' => 'error', 'message' => 'Claim not found'], 404);
    }

    if ($claim->status === 'claimed_by_client') {
        // Step 1 -> 2: simple approval, no extra data needed
        \DB::table('warranty_claims')->where('id', $id)->update([
            'status' => 'claimed_approve',
            'updated_at' => now(),
        ]);

    } elseif ($claim->status === 'claimed_approve') {
        // Step 2 -> 3: receive into store, needs date
        $request->validate([
            'store_receive_date' => 'required|date',
        ]);

        \DB::table('warranty_claims')->where('id', $id)->update([
            'status' => 'kept_in_store',
            'store_receive_date' => $request->store_receive_date,
            'updated_at' => now(),
        ]);

   } elseif ($claim->status === 'kept_in_store') {
    $request->validate([
        'client_return_date' => 'required|date',
        'vendor_ledger_id'   => 'required|integer',
    ]);

    \DB::table('warranty_claims')->where('id', $id)->update([
        'status' => 'returned_to_client',
        'client_return_date' => $request->client_return_date,
        'resolution_remarks' => $request->input('resolution_remarks'),
        'vendor_ledger_id' => $request->vendor_ledger_id,
        'provide_by' => auth()->id(),
        'updated_at' => now(),
    ]);
} else {
        return response()->json(['status' => 'error', 'message' => 'This claim is already completed'], 422);
    }

    return response()->json(['status' => 'success', 'message' => 'Claim moved to next step']);
}

public function cancel($id)
{
    $claim = \DB::table('warranty_claims')->where('id', $id)->first();

    if (!$claim) {
        return response()->json(['status' => 'error', 'message' => 'Claim not found'], 404);
    }

    if ($claim->status === 'kept_in_store') {
        \DB::table('warranty_claims')->where('id', $id)->update([
            'status' => 'claimed_approve',
            'store_receive_date' => null,
            'updated_at' => now(),
        ]);
    } elseif ($claim->status === 'claimed_approve') {
        \DB::table('warranty_claims')->where('id', $id)->update([
            'status' => 'claimed_by_client',
            'updated_at' => now(),
        ]);
    } elseif ($claim->status === 'claimed_by_client') {
        \DB::table('warranty_claims')->where('id', $id)->delete();
    } else {
        return response()->json(['status' => 'error', 'message' => 'Cannot cancel a completed claim'], 422);
    }

    return response()->json(['status' => 'success', 'message' => 'Action completed']);
}
}