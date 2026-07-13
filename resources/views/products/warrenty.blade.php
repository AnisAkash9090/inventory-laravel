@extends('layouts.app')
@section('title', 'Warranty Claims')
@section('content')

<div class="container-fluid">
    <div class="card shadow-sm">
  <div class="card-header bg-white d-flex justify-content-between align-items-center">
  <ul class="nav nav-tabs card-header-tabs" id="warrantyTabs" role="tablist">
    <li class="nav-item">
        <button class="nav-link active" data-toggle="tab" data-target="#tab-claimed" type="button">
            Claimed by Client
            <span class="badge badge-warning ms-1">{{ $claims->where('status', 'claimed_by_client')->count() }}</span>
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-toggle="tab" data-target="#tab-approved" type="button">
            Claim Approved
            <span class="badge badge-primary ms-1">{{ $claims->where('status', 'claimed_approve')->count() }}</span>
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-toggle="tab" data-target="#tab-store" type="button">
            Kept in Store
            <span class="badge badge-info ms-1">{{ $claims->where('status', 'kept_in_store')->count() }}</span>
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-toggle="tab" data-target="#tab-returned-final" type="button">
            Returned to Client
            <span class="badge badge-success ms-1">{{ $claims->where('status', 'returned_to_client')->count() }}</span>
        </button>
    </li>
</ul>

    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addClaimModal">
        <i class="fa fa-plus"></i> Add Warranty Claim
    </button>

</div>
       <div class="row g-2 align-items-center ml-1">
        <div class="col-md-4 ">
            <select id="srFilter" class="form-control form-control-sm">
                <option value="">-- All Sellers (SR) --</option>
                @foreach($returnSR as $sr)
                    <option value="{{ $sr->ledger }} - {{ $sr->name }}">{{ $sr->ledger }} - {{ $sr->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
        <div class="card-body">
         <div class="tab-content" id="warrantyTabsContent">

    {{-- TAB 1: Claimed by Client (new claims, awaiting approval) --}}
    <div class="tab-pane fade show active" id="tab-claimed">
        <div class="table-responsive">
         <table class="table table-hover align-middle table-claimed" id="table-claimed">
    <thead>
        <tr>
            <th>#</th><th>SR</th><th>Product</th><th>Size</th><th>Qty</th>
            <th>Claim Date</th><th>Remarks</th><th class="text-center">Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($claims->where('status', 'claimed_by_client') as $claim)
        <tr id="claim-row-{{ $claim->id }}">
            <td>{{ $claim->id }}</td>
            <td>{{ $claim->seller_ledger_id }} - {{ $claim->sr_name }}</td>
            <td>{{ $claim->product_name }}</td>
            <td>{{ $claim->size_name }}</td>
            <td>{{ $claim->qty }}</td>
            <td>{{ $claim->client_claim_date }}</td>
            <td>{{ $claim->client_claim_remarks }}</td>
            <td class="text-center">
                <button class="btn btn-sm btn-success btn-approve" data-id="{{ $claim->id }}">Approve</button>
                <button class="btn btn-sm btn-danger btn-cancel" data-id="{{ $claim->id }}">Cancel</button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
        </div>
    </div>

    {{-- TAB 2: Claim Approved (waiting to be physically received into store) --}}
    <div class="tab-pane fade" id="tab-approved">
        <div class="table-responsive">
            <table class="table table-hover align-middle table-approved" id="table-approved">
                <thead>
                    <tr>
                        <th>#</th><th>SR</th><th>Product</th><th>Size</th><th>Qty</th> <th>Client Remarks</th>
                        <th>Claim Date</th><th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($claims->where('status', 'claimed_approve') as $claim)
                    <tr id="claim-row-{{ $claim->id }}">
                        <td>{{ $claim->id }}</td> 
                        <td>{{ $claim->seller_ledger_id }} - {{ $claim->sr_name }}</td>
                    <td>{{ $claim->product_name }}</td>
<td>{{ $claim->size_name }}</td>
                        <td>{{ $claim->qty }}</td>
                         <td>{{ $claim->client_claim_remarks }}</td>
                        <td>{{ $claim->client_claim_date }}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-success btn-approve" data-id="{{ $claim->id }}">Receive into Store</button>
                            <button class="btn btn-sm btn-warning btn-cancel" data-id="{{ $claim->id }}">Revert</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- TAB 3: Kept in Store (ready to return to client) --}}
    <div class="tab-pane fade" id="tab-store">
        <div class="table-responsive">
            <table class="table table-hover align-middle table-store" id="table-store">
                <thead>
                    <tr>
                        <th>#</th> <th>SR</th><th>Product</th><th>Size</th><th>Qty</th><th>Client Remarks</th>
                        <th>Store Receive Date</th><th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($claims->where('status', 'kept_in_store') as $claim)
                    <tr id="claim-row-{{ $claim->id }}">
                        <td>{{ $claim->id }}</td>
                        
                        <td>{{ $claim->sr_name }}</td>
                     <td>{{ $claim->product_name }}</td>
<td>{{ $claim->size_name }}</td>
                        <td>{{ $claim->qty }}</td>      <td>{{ $claim->client_claim_remarks }}</td>
                        <td>{{ $claim->store_receive_date }}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-success btn-approve" data-id="{{ $claim->id }}">Return to Client</button>
                            <button class="btn btn-sm btn-warning btn-cancel" data-id="{{ $claim->id }}">Revert</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- TAB 4: Returned to Client (final, view only) --}}
    <div class="tab-pane fade" id="tab-returned-final">
        <div class="table-responsive">
                    <table class="table table-hover align-middle table-returned-final" id="table-returned-final">
    <thead>
        <tr>
            <th>#</th><th>SR</th><th>Product</th><th>Size</th><th>Qty</th>
            <th>Claim Date</th><th>Remarks</th><th class="text-center">resolution  Remarks </th>
        </tr>
    </thead>
    <tbody>
        @foreach($claims->where('status', 'returned_to_client') as $claim)
        <tr id="claim-row-{{ $claim->id }}">
            <td>{{ $claim->id }}</td>
            <td>{{ $claim->seller_ledger_id }} - {{ $claim->sr_name }}</td>
            <td>{{ $claim->product_name }}</td>
            <td>{{ $claim->size_name }}</td>
            <td>{{ $claim->qty }}</td>
            <td>{{ $claim->client_claim_date }}</td>
            <td>{{ $claim->client_claim_remarks }}</td>
            <td>{{ $claim->resolution_remarks }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
        </div>
    </div>

</div>
        </div>
    </div>
</div>
{{-- ADD WARRANTY CLAIM MODAL --}}
<div class="modal fade" id="addClaimModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="addClaimForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Warranty Claim</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label">Seller / SR <span class="text-danger">*</span></label>
                            <select name="seller_ledger_id" id="seller_ledger_id" class="form-select" required>
                                <option value="">-- Select Seller --</option>
                            @foreach($returnSR as $SR)
                    <option value="{{ $SR->ledger }}">
                        #{{ $SR->ledger }} - {{ $SR->name }} 
                    </option>
                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Invoice No</label>
                            <input type="text" name="invoice_no" class="form-control" placeholder="Optional">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Product <span class="text-danger">*</span></label>
                       <select name="product_id" id="product_id" class="form-select" required>
    <option value="">-- Select Product --</option>
    @foreach($products as $product)
        <option value="{{ $product->id }}" data-sizes="{{ json_encode($product->size) }}">
            {{ $product->product_name }}
        </option>
    @endforeach
</select>


                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Size <span class="text-danger">*</span></label>
                       <select name="size" id="size" class="form-select" required disabled>
    <option value="">-- Select Product First --</option>
</select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Qty <span class="text-danger">*</span></label>
                            <input type="number" name="qty" class="form-control" min="1" value="1" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Claim Date <span class="text-danger">*</span></label>
                            <input type="date" name="client_claim_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Claim Remarks</label>
                            <textarea name="client_claim_remarks" class="form-control" rows="3" placeholder="Describe the issue..."></textarea>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Claim</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="storeReceiveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Receive into Store</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Store Receive Date <span class="text-danger">*</span></label>
                    <input type="date" id="storeReceiveDate" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="form-group mt-2">
                    <label class="form-label">Remarks</label>
                    <textarea id="storeReceiveRemarks" class="form-control" rows="3" placeholder="Optional remarks..."></textarea>
                </div>
                <input type="hidden" id="storeReceiveClaimId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="confirmStoreReceive">Confirm Receive</button>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const masterSizesArray = @json($allSizes); // [{id, name}, ...]

    const productSelect = document.getElementById('product_id');
    const sizeSelect = document.getElementById('size');

    productSelect.addEventListener('change', function () {
        sizeSelect.innerHTML = '<option value="">-- Choose Size --</option>';

        if (!this.value) {
            sizeSelect.disabled = true;
            return;
        }

        const selectedOption = this.options[this.selectedIndex];
        let sizeIds = [];

        try {
            sizeIds = JSON.parse(selectedOption.getAttribute('data-sizes') || "[]");
        } catch (e) {
            sizeIds = [];
        }

        if (!sizeIds.length) {
            sizeSelect.innerHTML = '<option value="">No sizes setup</option>';
            sizeSelect.disabled = true;
            return;
        }

        sizeIds.forEach(sizeId => {
            const matchedSize = masterSizesArray.find(s => String(s.id) === String(sizeId));
            if (matchedSize) {
                const opt = document.createElement('option');
                opt.value = matchedSize.id;
                opt.textContent = matchedSize.name;
                sizeSelect.appendChild(opt);
            }
        });

        sizeSelect.disabled = false;
    });
});
</script>
<script> 

    $(function () {

 // Submit new claim
        $('#addClaimForm').on('submit', function (e) {
            e.preventDefault();

            $.post("{{ route('warranty.store') }}", $(this).serialize())
                .done(function (res) {
                    Swal.fire('Success', res.message, 'success').then(() => location.reload());
                })
                .fail(function (xhr) {
                    let msg = 'Something went wrong';
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        msg = Object.values(errors).flat().join('<br>');
                    }
                    Swal.fire('Error', msg, 'error');
                });
        });

    });
</script>
{{-- Resolution Remarks Modal (used when approving from Kept in Store -> Returned) --}}
<div class="modal fade" id="resolutionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Return to Client</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Vendor Ledger <span class="text-danger">*</span></label>
                    <select id="vendorLedgerId" class="form-control" required>
                        <option value="">-- Select Vendor --</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->ledger }}">{{ $vendor->ledger }} - {{ $vendor->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mt-2">
                    <label class="form-label">Client Return Date <span class="text-danger">*</span></label>
                    <input type="date" id="clientReturnDate" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="form-group mt-2">
                    <label class="form-label">Resolution Remarks</label>
                    <textarea id="resolutionRemarks" class="form-control" rows="4" placeholder="Enter resolution remarks..."></textarea>
                </div>
                <input type="hidden" id="resolutionClaimId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="confirmResolution">Confirm & Return</button>
            </div>
        </div>
    </div>
</div>
<script>
$(function () {
 $.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
});
$('#confirmStoreReceive').on('click', function () {
    const id = $('#storeReceiveClaimId').val();
    const receiveDate = $('#storeReceiveDate').val();
    const remarks = $('#storeReceiveRemarks').val();

    if (!receiveDate) {
        Swal.fire('Missing Date', 'Please select a store receive date.', 'warning');
        return;
    }

    $('#storeReceiveModal').modal('hide');
    approveClaim(id, null, null, receiveDate);
});
    // APPROVE
$(document).on('click', '.btn-approve', function () {
    const id = $(this).data('id');
    const row = $(this).closest('tr');

    if (row.closest('table').hasClass('table-claimed')) {
        // Tab 1 -> 2: plain approve, no modal needed
        approveClaim(id);
        return;
    }

    if (row.closest('table').hasClass('table-approved')) {
        // Tab 2 -> 3: ask for store receive date
        $('#storeReceiveClaimId').val(id);
        $('#storeReceiveDate').val(new Date().toISOString().split('T')[0]);
        $('#storeReceiveRemarks').val('');
        $('#storeReceiveModal').modal('show');
        return;
    }

    if (row.closest('table').hasClass('table-store')) {
        // Tab 3 -> 4: ask for return date + vendor + resolution remarks
        $('#resolutionClaimId').val(id);
        $('#clientReturnDate').val(new Date().toISOString().split('T')[0]);
        $('#resolutionRemarks').val('');
        $('#vendorLedgerId').val('');
        $('#resolutionModal').modal('show');
        return;
    }
});

$('#confirmResolution').on('click', function () {
    const id = $('#resolutionClaimId').val();
    const remarks = $('#resolutionRemarks').val();
    const returnDate = $('#clientReturnDate').val();
    const vendorLedgerId = $('#vendorLedgerId').val();

    if (!returnDate) {
        Swal.fire('Missing Date', 'Please select a client return date.', 'warning');
        return;
    }

    if (!vendorLedgerId) {
        Swal.fire('Missing Vendor', 'Please select a vendor ledger.', 'warning');
        return;
    }

    $('#resolutionModal').modal('hide');
    approveClaim(id, remarks, returnDate, null, vendorLedgerId);
});

// approveClaim signature: now accepts vendorLedgerId as 5th param
function approveClaim(id, resolutionRemarks = null, clientReturnDate = null, storeReceiveDate = null, vendorLedgerId = null) {
    $.post(`/warranty/${id}/approve`, {
        resolution_remarks: resolutionRemarks,
        client_return_date: clientReturnDate,
        store_receive_date: storeReceiveDate,
        vendor_ledger_id: vendorLedgerId
    })
        .done(function (res) {
            Swal.fire('Success', res.message, 'success').then(() => location.reload());
        })
        .fail(function (xhr) {
            Swal.fire('Error', xhr.responseJSON?.message || 'Something went wrong', 'error');
        });
}

    // CANCEL
    $(document).on('click', '.btn-cancel', function () {
        const id = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: 'This will cancel/revert this claim.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, do it'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(`/warranty/${id}/cancel`)
                    .done(function (res) {
                        Swal.fire('Done', res.message, 'success').then(() => location.reload());
                    })
                    .fail(function (xhr) {
                        Swal.fire('Error', xhr.responseJSON?.message || 'Something went wrong', 'error');
                    });
            }
        });
    });
});
$(function () {
    const tableIds = ['#table-claimed', '#table-approved', '#table-store', '#table-returned-final'];
    const dtInstances = {};

    tableIds.forEach(id => {
        dtInstances[id] = $(id).DataTable({
            order: [[0, 'desc']],
            pageLength: 10,
            autoWidth: false,
            language: { search: "Search:" }
        });
    });

    // Fix column alignment when switching Bootstrap tabs (DataTables quirk)
    $('#warrantyTabs button[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        const targetId = $(e.target).data('target'); // e.g. "#tab-store"
        const tableSelector = '#' + $(targetId).find('table').attr('id');
        if (dtInstances[tableSelector]) {
            dtInstances[tableSelector].columns.adjust();
        }
    });

    // SR filter — applies to ALL 4 tables at once
    $('#srFilter').on('change', function () {
        const srValue = $(this).val();
        tableIds.forEach(id => {
            dtInstances[id].column(1).search(srValue).draw(); // column index 1 = SR column
        });
    });
});
</script>
@endsection

