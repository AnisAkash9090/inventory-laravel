@extends('layouts.app')
@section('title', 'Product Store update')
@section('content')

<style>
/* Lock all text, forms, and tables inside the workspace wrapper to 11px */
#invoice-results-wrapper,
#dynamic_returns_table,
.dataTables_wrapper,
.dataTables_wrapper input,
.dataTables_wrapper select {
    font-size: 11px !important;
}

/* Custom Table Header Color */
#dynamic_returns_table thead tr {
    background-color: #212529 !important; /* Deep dark/charcoal color */
    color: #ffffff !important;
}

#dynamic_returns_table th {
    padding: 6px 8px !important;
    vertical-align: middle !important;
}

/* Match form control sizes to 11px layout standards */
#dynamic_returns_table .form-control-sm,
#dynamic_returns_table .form-select-sm,
#dynamic_returns_table .btn-sm {
    font-size: 11px !important;
    padding: 2px 4px !important;
}

/* Align and space out search bar/length controls row */
.dataTables_wrapper .top-controls-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}
</style>
<div style="width:122%;height:40px;background:white;
margin-left:-44px;
margin-top:-10px;
padding-top:10px;
color:black;
display:flex;
padding-left:30px;
border-top-right-radius:5px;"><h4 class="" style="letter-spacing:0.8px;font-weight:400;">
<i class="fa fa-users blue2_color">&nbsp;</i>Store Return </h4></div>


<div class="custom-tab-container mb-4">
    <button type="button" class="btn btn-primary custom-tab-btn active" data-target="#tab-return-form">
        <i class="fa fa-plus-circle me-1"></i> SR Sale's invoice 
    </button>
    <button type="button" class="btn btn-outline-primary custom-tab-btn" data-target="#tab-invoice-history">
        <i class="fa fa-table me-1"></i>Sale's Return Invoice  
    </button>
</div>

<hr>

<div id="tab-return-form" class="custom-tab-panel">
    <div class="row">
        <div class="col-md-4">
            <label class="form-label font-weight-bold">Seller / Vendor *</label>
            <select id="seller_select" name="seller_id" class="form-select form-control" required>
                <option value="">-- Choose Seller Account --</option>
                @foreach($returnSR as $SR)
                    <option value="{{ $SR->ledger }}">
                        #{{ $SR->ledger }} - {{ $SR->name }} 
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label font-weight-bold">Select Invoice *</label>
            <select id="invoice_select" name="invoice_id" class="form-select form-control" disabled required>
                <option value="">-- Choose Invoice (Select Seller First) --</option>
            </select>
        </div>
    </div>

<div id="invoice-results-wrapper" class="row mt-4" style="display: none;">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-body p-2"> <div class="table-responsive">
                    <table id="dynamic_returns_table" class="table table-bordered table-striped w-100 m-0">
                        <thead>
                            <tr>
                                <th>SN</th>
                                <th>Product ID</th>
                                <th>Size</th>
                                <th>Available Qty</th>
                                <th>Unit Price</th>
                                <th>Cost</th>
                            </tr>
                        </thead>
                        <tbody id="invoice_items_tbody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<div id="tab-invoice-history" class="custom-tab-panel" style="display: none;">
    <div class="card shadow-sm">
   <div class="card-header bg-dark text-white font-weight-bold d-flex justify-content-between align-items-center" style="padding: 10px 15px;">
    <span style="font-size: 12px;">All Return SR Product Invoices</span>

    <div>
        <button type="button" class="btn btn-danger btn-sm m-0" data-toggle="modal" data-target="#bulkStockModal" style="font-size: 11px; padding: 4px 8px;">
            <i class="fas fa-plus-circle me-1"></i> SR Return Bulk
        </button>
    </div>
</div>
    <div class="row mb-3">
        <div class="col-md-4">
            <label class="form-label font-weight-bold" style="font-size: 11px;">Filter History by Seller / Vendor *</label>
            <select id="seller_select_2" class="form-select form-control" style="font-size: 11px;">
                <option value="">-- View All Sellers History --</option>
                @foreach($returnSR as $SR)
                    <option value="{{ $SR->ledger }}">#{{ $SR->ledger }} - {{ $SR->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="card shadow-sm">
   
        <div class="card-body p-2">
            <div class="table-responsive">
                <table class="table table-bordered table-striped w-100" id="return_history_master_table">
                    <thead>
                        <tr class="table-secondary">
                            <th>ID</th>
                            <th>Invoice ID</th> 
                            <th>Vendor Name</th>
                            <th>Total Amount</th>
                            <th>Cost</th>
                            <th>Invoice Date</th>
                            <th>Status</th>
                             <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="return_history_tbody">
                        </tbody>
                </table>
            </div>
        </div>
    </div>
<div class="modal fade" id="returnLogDetailsModal" tabindex="-1" aria-labelledby="returnLogDetailsModalLabel" aria-hidden="true" style="font-size: 11px;">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" style="max-width: 95%; width: 95%;">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-dark text-light p-2">
                <h6 class="modal-title fw-bold m-0" id="returnLogDetailsModalLabel">
                    <i class="fas fa-clipboard-list me-1 text-danger"></i> Detailed Return Log Breakdown
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="font-size: 9px;"></button>
            </div>
            <div class="modal-body p-2 bg-light">
                
                <div id="modal_table_wrapper" class="table-responsive">
                    <table class="table table-sm table-bordered table-hover bg-white m-0 w-100" id="modal_log_items_table">
                        <thead>
                            <tr class="table-secondary">
                                <th>Log ID</th>
                                <th>Product Details</th>
                                
                                <th>Staff (Created / Approved)</th>
                                <th>Size</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>Total</th>
                                <th>Type</th>
                                <th>Dates (Return / Approve)</th>
                            </tr>
                        </thead>
                        <tbody id="modal_log_items_tbody">
                            </tbody>
                    </table>
                </div>

                <div id="modal_error_wrapper" style="display: none;">
                    </div>

            </div>
            <div class="modal-footer p-1 bg-light justify-content-end border-top">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" style="font-size: 11px; padding: 2px 8px;">Close Window</button>
            </div>
        </div>
    </div>
</div>
    <script>
$(document).ready(function() {
    $('#seller_select_2').on('change', function() {
        let ledgerId = $(this).val();
        let historyTbody = $('#return_history_tbody');

        $.ajax({
            // FIX 1: Point to the correct history endpoint route, NOT fetch-invoices
            url: "{{ route('returns.fetch-invoices') }}",
            type: "GET",
            data: { ledger_id: ledgerId }, 
            dataType: "json",
            success: function(response) {
                
                // Safely clear old DataTables instance
                if ($.fn.DataTable.isDataTable('#return_history_master_table')) {
                    $('#return_history_master_table').DataTable().destroy();
                }

                historyTbody.empty();

                if (response.success && response.data.length > 0) {
                    response.data.forEach(function(history) {
                        let statusBadge = (history.status === 'approve') 
                            ? `<span class="badge bg-success text-light ">Approved</span>`
                            : `<span class="badge bg-warning text-dark">Pending</span>`;
 let buttonap = (history.status === 'approve')
 ?` ` :
`<button type="button" class="btn btn-success btn-sm btn-approve-invoice" 
        data-invoice="${history.invoice_id}" 
        data-ledger="${history.ledger_id}"
        style="font-size:11px; padding:2px 6px;">
    <i class="fas fa-check-circle me-1"></i> Approve
</button>`;
                        // Ensure proper fallbacks for numbers to prevent NaN layout breaks
                        let amount = history.amount ? parseFloat(history.amount).toFixed(2) : '0.00';
                        let cost = history.cost ? parseFloat(history.cost).toFixed(2) : '0.00';
                        let vendorName = history.name ? history.name : 'N/A';

                   // Splits '2026-07-10T00:00:00.000000Z' into ['2026-07-10', '00:00:00...'] and grabs index 0
let cleanDate = history.invoice_date ? history.invoice_date.split('T')[0] : 'N/A';

historyTbody.append(`
    <tr>
        <td>${history.id}</td>
        <td><span class="fw-bold text-primary">${history.invoice_id}</span></td>
        <td>${vendorName}</td>
        <td>${amount}</td>
        <td>${cost}</td>
        <td>${cleanDate}</td> <td>${statusBadge}</td><td class="text-center">
    <button type="button" class="btn btn-info btn-sm btn-view-logs-modal" 
            data-invoice="${history.invoice_id}" 
            data-ledger="${history.ledger_id}"
            style="font-size:11px; padding:2px 6px;">
        <i class="fa fa-eye me-1"></i> View Details
    </button>
${buttonap}
</td>
    </tr>
`);
                    });
                } else {
                    // FIX 2: Supply exactly 8 distinct <td> elements instead of a colspan="8" property
                    historyTbody.append(`
                        <tr>
                            <td class="text-center text-muted">No records</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>  
                        </tr>
                    `);
                }

                // Re-initialize with crisp settings
                $('#return_history_master_table').DataTable({
                    "paging": true,
                    "lengthChange": true,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "autoWidth": false,
                    "responsive": true,
                    "order": [[0, "desc"]],
                    "dom": "<'top-controls-row'lf>rt<'bottom-controls-row'ip>"
                });
            },
            error: function(xhr) {
                console.error("History logging AJAX pipeline route error trace:", xhr);
            }
        });
    });

    // Run layout render instantly on page initialization load
    $('#seller_select_2').trigger('change');
});


$('#return_history_tbody').on('click', '.btn-view-logs-modal', function(e) {
    e.preventDefault();
    
    let btn = $(this);
    let invoiceNo = btn.data('invoice');
    let ledgerId = btn.data('ledger');
    let modalTbody = $('#modal_log_items_tbody');

    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');

    $.ajax({
        url: "{{ route('returns.fetch-log-items') }}",
        type: "GET",
        data: { invoice_no: invoiceNo, ledger_id: ledgerId },
        dataType: "json",
        success: function(res) {
            btn.prop('disabled', false).html('<i class="fa fa-eye me-1"></i> View Details');

            // Toggle view containers
            $('#modal_error_wrapper').hide();
            $('#modal_table_wrapper').show();

            $('#returnLogDetailsModalLabel').html(`<i class="fas fa-clipboard-list me-1 text-danger"></i> Detailed Return Log Breakdown — Invoice #${invoiceNo}`);
            modalTbody.empty();

            if (res.success && res.data.length > 0) {
                res.data.forEach(function(item) {
                    let typePill = (item.type === 'damage') ? '<span class="badge bg-danger">Damage</span>' : '<span class="badge bg-info text-dark">Solid</span>';
                    let approvedDate = item.approve_date ? item.approve_date.split('T')[0] : 'N/A';
                    let price = item.price/item.qty;
                    modalTbody.append(`
                        <tr style="font-size: 11px;">
                            <td><strong>${item.log_item_id}</strong></td>
                            <td>
                                <span class="fw-bold text-dark">${item.product_name}</span><br>
                               
                            </td>
                            <td>
                                <span class="text-muted">By:</span> ${item.created_by ?? 'System'}<br>
                                <span class="text-muted">Appr:</span> ${item.approved_by ?? 'Pending'}
                            </td>
                            <td class="text-center"><span class="badge bg-light text-dark border">${item.size ? item.size : 'N/A'}</span></td>
                            <td class="fw-bold text-center text-primary">${item.qty}</td>
                            <td class="fw-bold text-center text-primary">${price}</td>
                            <td>${parseFloat(item.price).toFixed(2)}</td>
                            <td class="text-center">${typePill}</td>
                            <td>
                                <small class="text-muted">Ret:</small> ${item.return_date ?? 'N/A'}<br>
                                <small class="text-muted">Appr:</small> ${approvedDate}
                            </td>
                        </tr>
                    `);
                });
            } else {
                modalTbody.append('<tr><td colspan="9" class="text-center text-muted p-3">No child log records found for this selection profile.</td></tr>');
            }

            $('#returnLogDetailsModal').modal('show');
        },
        error: function(xhr) {
            btn.prop('disabled', false).html('<i class="fa fa-eye me-1"></i> View Details');
            
            // Toggle view containers
            $('#modal_table_wrapper').hide();
            
            let backendFault = "Unknown backend routing pipeline processing error.";
            if (xhr.responseJSON && xhr.responseJSON.message) {
                backendFault = xhr.responseJSON.message;
            } else if (xhr.responseText) {
                backendFault = xhr.responseText.substring(0, 400).replace(/<\/?[^>]+(>|$)/g, "");
            }

            $('#returnLogDetailsModalLabel').html(`
                <span class="text-danger"><i class="fas fa-exclamation-triangle me-1"></i> Internal Query Fault — Invoice #${invoiceNo}</span>
            `);

            let errorOutputHtml = `
                <div class="alert alert-danger border-left-danger shadow-sm m-2 p-3">
                    <h6 class="fw-bold text-danger mb-1"><i class="fas fa-bug me-1"></i> HTTP 500: Query Failure Exception Traced</h6>
                    <p class="mb-2 text-dark">The application crashed while fetching logs details from database tables.</p>
                    <div class="bg-dark text-warning p-2 rounded font-monospace" style="font-size: 10px; word-break: break-all; white-space: pre-wrap;">${backendFault}</div>
                </div>
            `;

            $('#modal_error_wrapper').html(errorOutputHtml).show();
            $('#returnLogDetailsModal').modal('show');
        }
    });
});

$(document).ready(function() {

$('#return_history_tbody').on('click', '.btn-approve-invoice', function(e) {
    e.preventDefault();

    let btn       = $(this);
    let invoiceId = btn.data('invoice');
    let ledgerId  = btn.data('ledger');

    Swal.fire({
        title: 'Confirm Inventory Restock?',
        icon: 'warning',
        html: `
            <div class="text-start mb-3 text-muted" style="font-size: 12px; line-height: 1.5;">
                Are you sure you want to approve Invoice #<strong>${invoiceId}</strong>? This action permanently bumps master warehouse items list configurations!
            </div>
            <hr class="my-2">
            
            <div class="form-check text-start d-flex align-items-center mt-2 p-0" style="gap: 8px;">
                <input class="form-check-input m-0" type="checkbox" id="swal_generate_payment" value="1" style="cursor: pointer; width: 16px; height: 16px;">
                <label class="form-check-label fw-bold text-dark m-0" for="swal_generate_payment" style="cursor: pointer; font-size: 12px; user-select: none;">
                    Do you want to generate a payment record?
                </label>
            </div>

            <div id="swal_payment_date_wrapper" class="text-start mt-3">
                <label class="fw-bold text-secondary mb-1" style="font-size: 11px;">Payment Settlement Date *</label>
                <input type="date" id="swal_payment_date" class="form-control" style="font-size: 12px; height: 32px;" value="${new Date().toISOString().split('T')[0]}">
            </div>
        `,
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Approve & Restock',
        cancelButtonText: 'Cancel',
        
        // Validation Hook: Runs when "Yes, Approve & Restock" is clicked
        preConfirm: () => {
            let isChecked = $('#swal_generate_payment').is(':checked');
            let dateValue = $('#swal_payment_date').val();

            // Enforces date selection regardless of checkbox state, or change logic as needed
            if (!dateValue) {
                Swal.showValidationMessage('Please specify a valid payment settlement date.');
                return false;
            }
            
            return {
                generatePayment: isChecked ? 1 : 0,
                paymentDate: dateValue
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            let payloadData = result.value;

            Swal.fire({
                title: 'Processing Ledger...',
                text: 'Updating records and incrementing product store metrics.',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            $.ajax({
                url: "{{ route('returns.approve-invoice') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    invoice_id: invoiceId,
                    ledger_id: ledgerId,
                    generate_payment: payloadData.generatePayment,
                    payment_date: payloadData.paymentDate
                },
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Approved successfully',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });

                        $('#seller_select_2').trigger('change');
                    } else {
                        Swal.fire({ icon: 'error', title: 'Execution Rejected', text: response.message });
                    }
                },
                error: function(xhr) {
                    let errorMsg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : "System runtime error.";
                    Swal.fire({ icon: 'error', title: 'Process Aborted', text: errorMsg });
                }
            });
        }
    });
});});
</script>
    </div>
</div>

<!-- Modal remains outside the loop (only need one modal) -->
<div class="modal fade" id="updateStockModal" tabindex="-1" aria-labelledby="updateStockModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="updateStockModalLabel">Update Rate Configuration</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('stock_sell_cost.update') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="product_id" id="modal_product_id">
                    <input type="hidden" name="size_id" id="modal_size_id">
                    <input type="hidden" name="sold_total" id="modal_sold_total">

                    <div class="form-group mb-3">
                        <label class="font-weight-bold">Product Name</label>
                        <input type="text" id="modal_product_name" class="form-control-plaintext font-weight-bold text-primary" readonly>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label class="form-label font-weight-bold">Cost Price</label>
                            <input type="number" step="0.01" name="price" id="modal_cost_price" class="form-control" required>
                            <small class="text-muted">Previous cost price tracking reference.</small>
                        </div>

                        <div class="col-md-6 form-group mb-3">
                            <label class="form-label font-weight-bold">Selling Price</label>
                            <input type="number" step="0.01" name="sell_price" id="modal_selling_price" class="form-control" required>
                            <small class="text-muted">New retail selling layout rate.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary font-weight-bold">Save Changes & Log Rates</button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="bulkStockModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog " role="document" style="max-width: 90%;">
        <form action="{{ route('returnstock.bulkUpdate') }}" method="POST">
            @csrf
            <div class="modal-content border-0 shadow-lg">
                
                <div class="modal-header bg-dark text-white d-flex align-items-center justify-content-between">
                    <h5 class="modal-title class="m-0">
                        <i class="fas fa-file-invoice mr-2 text-warning"></i> Bulk Return From SR
                    </h5>
                    <button type="button" class="close text-white border-0 bg-transparent" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="font-size: 1.5rem;">&times;</span>
                    </button>
                </div>

                <div class="modal-body p-4">
                    
                    <div class="row g-3 mb-4 bg-light p-3 rounded border">
     <div class="col-md-4">
    <label class="form-label font-weight-bold">Invoice No *</label>
    <input type="text" 
           id="automated_invoice_no" 
           name="invoiceno" 
           class="form-control bg-light" 
           readonly 
           required>
</div>
                        <div class="col-md-4">
                            <label class="form-label font-weight-bold">SR *</label>
                            <select name="seller_id" class="form-select form-control" required>
                                <option value="">-- Choose SR Account --</option>
                                @foreach($returnSR as $SR)
                                    <option value="{{ $SR->ledger }}">
                                       {{ $SR->name }} - {{ $SR->ledger }} 
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label font-weight-bold">Require Date *</label>
                            <input type="date" name="buydate" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>

                    <div class="card border-danger mb-4 bg-light">
                        <div class="card-body py-3">
                            <h6 class="font-weight-bold text-danger mb-3"><i class="fas fa-plus"></i> Step 1: Add return Item</h6>
                            <div class="row g-2 align-items-end">
                                <div class="col-md-4">
                                    <label class="small font-weight-bold">Select Product</label>
       <select id="modalProductSelect" class="form-control">
                <option value="">-- Select Product --</option>
            @foreach($products as $product)
                <option value="{{ $product->id }}" 
                        data-store-map="{{ json_encode($product->stores->map(function($store) {
                            return [
                                'size_id'    => $store->size,
                                'price'      => $store->price ?? 0.00,
                                'sell_price' => $store->sell_price ?? 0.00 // Added selling price
                            ];
                        })) }}">
                    {{ $product->product_name }}
                </option>
            @endforeach
        </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="small font-weight-bold">Select Size/ Varient</label>
                                    <select id="modalSizeSelect" class="form-control" disabled>
                                        <option value="">-- Choose Size --</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="small font-weight-bold">Quantity</label>
                                    <input type="number" id="modalQtyInput" class="form-control" placeholder="0" min="1">
                                </div>
                       <div class="col-md-2">
    <label class="small font-weight-bold">Sold Price</label>
    <input type="number" step="0.01" id="modalCostInput" class="form-control" placeholder="0.00">
    <input type="number" id="currentSellPrice">

    <small id="sellPriceWarningText" class="text-danger fw-bold d-block mt-1" style="display:none; font-size: 11px;"></small>
</div>
                                <div class="col-md-2">
                                    <button type="button" id="addBuiltRowBtn" class="btn btn-warning w-100 font-weight-bold">
                                        <i class="fas fa-arrow-down"></i> Add to List
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h6 class="font-weight-bold text-secondary mb-2"><i class="fas fa-list"></i> Step 2: Return Summary List</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered m-0">
                            <thead class="bg-secondary text-white">
                                <tr>
                                    <th>Product Details</th>
                                    <th style="width: 15%;" class="text-center">Size</th>
                                    <th style="width: 15%;">Quantity</th>
                                    <th style="width: 20%;">Sold Price</th>
                                      <th style="width: 20%;">Return Type</th>
                                    <th style="width: 10%;" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="invoiceTableBody">
                                </tbody>
                        </table>
                    </div>

                    <div id="emptyInvoiceState" class="text-center py-4 border border-top-0 rounded-bottom bg-white">
                        <p class="text-muted m-0">Use the configuration builder above to attach line items to this invoice.</p>
                    </div>

                </div>

                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success px-4">Save Entire Invoice</button>
                </div>

            </div>
        </form>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {

    // ==========================================================
    // 1. CUSTOM TAB SWITCH CONTROLLER ENGINE
    // ==========================================================
    $('.custom-tab-btn').on('click', function(e) {
        e.preventDefault();
        
        let targetPanel = $(this).data('target');

        // Toggle Active Button Layout States
        $('.custom-tab-btn').removeClass('btn-primary active').addClass('btn-outline-primary');
        $(this).removeClass('btn-outline-primary').addClass('btn-primary active');

        // Switch Display Visibility on Custom Content Panels
        $('.custom-tab-panel').hide();
        $(targetPanel).fadeIn(200);
    });

    // ==========================================================
    // 2. DETECT URL PARAMS FOR AUTO-NAVIGATION
    // ==========================================================
    const urlParams = new URLSearchParams(window.location.search);
    const incomingLedgerId = urlParams.get('id');

    if (incomingLedgerId) {
        // If query parameters exist, focus instantly on the return form panel tab layout
        $('.custom-tab-btn[data-target="#tab-return-form"]').trigger('click');
        
        // Auto select incoming ledger ID parameters and fire active AJAX cascades
        $('#seller_select').val(incomingLedgerId).trigger('change');
    }
});
$(document).ready(function() {
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // ==========================================
    // DROPDOWN EVENT 1: Seller Changes -> Load Invoices
    // ==========================================
    $('#seller_select').on('change', function() {
        let ledgerId = $(this).val();
        let invoiceDropdown = $('#invoice_select');
        let wrapper = $('#invoice-results-wrapper');

        // Reset elements
        invoiceDropdown.html('<option value="">-- Choose Invoice --</option>').prop('disabled', true);
        wrapper.hide();

        if (!ledgerId) return;

        $.ajax({
            url: "{{ route('fetch-invoices') }}",
            type: "GET",
            data: { ledger_id: ledgerId },
            dataType: "json",
            success: function(response) {
                if (response.success && response.data.length > 0) {
                    response.data.forEach(function(inv) {
                        invoiceDropdown.append(`<option value="${inv.invoice_id}">Invoice #${inv.invoice_id} (${inv.invoice_date})</option>`);
                    });
                    invoiceDropdown.prop('disabled', false);
                } else {
                    invoiceDropdown.html('<option value="">No Invoices Found for this Ledger</option>');
                }
            }
        });
    });

    // ==========================================
    // DROPDOWN EVENT 2: Invoice Changes -> Load Items
    // ==========================================
$('#invoice_select').on('change', function() {
    let invoiceNo = $(this).val();
    let ledgerId = $('#seller_select').val();
    let tbody = $('#invoice_items_tbody');
    let wrapper = $('#invoice-results-wrapper');

    if (!invoiceNo) {
        wrapper.hide();
        return;
    }

    $.ajax({
        url: "{{ route('returns.fetch-items') }}",
        type: "GET",
        data: { invoice_no: invoiceNo },
        dataType: "json",
        success: function(response) {
            
            // 1. If a DataTable already exists on this DOM node, destroy it completely
            if ($.fn.DataTable.isDataTable('#dynamic_returns_table')) {
                $('#dynamic_returns_table').DataTable().destroy();
            }

            tbody.empty();

            if (response.success && response.data.length > 0) {
                response.data.forEach(function(item) {
                    let row = `
                        <tr  >
                          <td><strong>${item.invoice_item_id}</strong></td>
            
            <td>
                <span class="fw-bold">${item.product_name}</span> 
            </td>
            
            <td>${item.size ? item.size : 'N/A'}</td>
            <td>${item.qty}
            </td>
            <td>${parseFloat(item.price).toFixed(2)}</td>
            <td>${parseFloat(item.cost).toFixed(2)}</td>
                    
                        </tr>
                    `;
                    tbody.append(row);
                });

                wrapper.fadeIn(300);

                // 2. Re-initialize the layout as a dynamic corporate DataTable
   // Re-initialize the layout as a beautifully aligned 11px DataTable
$('#dynamic_returns_table').DataTable({
    "paging": true,
    "lengthChange": true,
    "searching": true,
    "ordering": true,
    "info": true,
    "autoWidth": false,
    "responsive": true,
    "order": [[0, "desc"]],
    "columnDefs": [
        { "orderable": false, "targets": 5 },
        { "className": "text-center", "targets": [2, 3, 5] } // Centers Size, Qty, and Action alignments
    ],
    // 'l' = length changing input, 'f' = filtering input, 't' = table, 'i' = information summary, 'p' = pagination
    // This wraps 'l' and 'f' together inside our flex CSS selector row:
    "dom": "<'top-controls-row'lf>rt<'bottom-controls-row'ip>",
    "language": {
        "search": "Search Matrix:",
        "lengthMenu": "Show _MENU_ entries"
    }
});

            } else {
                tbody.append('<tr><td colspan="7" class="text-center text-danger">No product items found inside this invoice sequence mapping.</td></tr>');
                wrapper.fadeIn(300);
            }
        },
     error: function(xhr, status, error) {
    console.error("Full Error Object Payload Matrix:", xhr);
    
    let runtimeMsg = "HTTP Status Code: " + xhr.status + " (" + error + ")\n\n";
    
    if (xhr.responseJSON && xhr.responseJSON.message) {
        runtimeMsg += "Server Error Trace:\n" + xhr.responseJSON.message;
    } else {
        runtimeMsg += "Raw Response Trace (First 250 chars):\n" + xhr.responseText.substring(0, 250);
    }
    
    alert("--- DATATABLE LOOKUP FAULT DETECTED ---\n" + runtimeMsg);
}
    });
});
  
});
</script>


<script>
document.addEventListener('DOMContentLoaded', function () {
   const masterSizesArray = @json($allSizes);
let itemIndex = 0;

// UI Selectors
const productSelect = document.getElementById('modalProductSelect');
const sizeSelect = document.getElementById('modalSizeSelect');
const qtyInput = document.getElementById('modalQtyInput');
const costInput = document.getElementById('modalCostInput'); // Cost Price field
const currentSellPriceInput = document.getElementById('currentSellPrice');
const warningText = document.getElementById('sellPriceWarningText');
const addRowBtn = document.getElementById('addBuiltRowBtn');
const tableBody = document.getElementById('invoiceTableBody');
const emptyState = document.getElementById('emptyInvoiceState');

function checkEmptyState() {
    emptyState.style.display = (tableBody.children.length === 0) ? 'block' : 'none';
}

// 1. Listen for Product Selection to update Sizes
productSelect.addEventListener('change', function () {
    sizeSelect.innerHTML = '<option value="">-- Choose Size --</option>'; 
    costInput.value = ''; 
    currentSellPriceInput.value = '';
    warningText.style.display = 'none';
    
    if (!this.value) {
        sizeSelect.disabled = true;
        return;
    }

    const selectedOption = this.options[this.selectedIndex];
    const storeMap = JSON.parse(selectedOption.getAttribute('data-store-map') || "[]");

    if (storeMap.length === 0) {
        sizeSelect.innerHTML = '<option value="">No sizes setup</option>';
        sizeSelect.disabled = true;
        return;
    }

    storeMap.forEach(item => {
        const matchedSize = masterSizesArray.find(s => String(s.id) === String(item.size_id));
        if (matchedSize) {
            const opt = document.createElement('option');
            opt.value = matchedSize.id;
            opt.textContent = matchedSize.name;
            opt.setAttribute('data-price', item.price);       // cost
opt.setAttribute('data-sell-price', item.sell_price); // sell
            sizeSelect.appendChild(opt);
        }
    });

    sizeSelect.disabled = false;
});

// ==========================================================
// NEW: 2. Listen for Size Selection to populate Selling Price into Cost Field
// ==========================================================
sizeSelect.addEventListener('change', function () {
    if (!this.value) {
        costInput.value = '';
        currentSellPriceInput.value = '';
        return;
    }

    const selectedSizeOption = this.options[this.selectedIndex];

    // Pull both values from the option's data attributes
    const costPrice = selectedSizeOption.getAttribute('data-price') || '0';
    const sellPrice = selectedSizeOption.getAttribute('data-sell-price') || '0';

    // Auto-fill Cost Price field
    costInput.value = costPrice;

    // Auto-fill Selling Price field
    currentSellPriceInput.value = sellPrice;

    // Re-run the warning check immediately since values just changed
    validatePricing();
});
    // 3. Real-time warning check as user types/modifies the Cost Input field
    costInput.addEventListener('input', validatePricing);

    function validatePricing() {
        const enteredCost = parseFloat(costInput.value) || 0;
        const currentSellPrice = parseFloat(currentSellPriceInput.value) || 0;

        if (currentSellPrice > 0 && enteredCost > currentSellPrice) {
            warningText.innerHTML = `<i class="fas fa-exclamation-triangle"></i> Cost (${enteredCost.toFixed(2)}) exceeds Sale Price (${currentSellPrice.toFixed(2)})! `;
            warningText.style.setProperty('display', 'block', 'important');
        } else {
            warningText.style.setProperty('display', 'none', 'important');
        }
    }

    // 4. Handle processing row addition with SweetAlert2 confirmation
    addRowBtn.addEventListener('click', function () {
        const productId = productSelect.value;
        const productName = productSelect.options[productSelect.selectedIndex]?.text;
        const sizeId = sizeSelect.value;
        const sizeName = sizeSelect.options[sizeSelect.selectedIndex]?.text;
        const qty = qtyInput.value;
        const cost = parseFloat(costInput.value) || 0;
        const sellPrice = parseFloat(currentSellPriceInput.value) || 0;

        // Base input structural validations checked first
        if (!productId || !sizeId || !qty || qty < 1 || cost <= 0) {
            alert('Please select a Product, Size, valid Quantity, and Cost Price before adding line entries.');
            return;
        }

        // PRICE WARNING CHECK: Confirms before adding to Step 2 summary list
        if (sellPrice > 0 && cost > sellPrice) {
            Swal.fire({
                title: 'Suspicious Pricing Detected!',
                html: `
                    <div class="text-start">
                        <p>You are assigning a purchase <strong>Cost Price (${cost.toFixed(2)} TK)</strong> that is greater than your system's current registered <strong>Selling Price (${sellPrice.toFixed(2)} TK)</strong>.</p>
                        <span class="text-danger fw-bold"><i class="fas fa-chart-line"></i> Price Gap Loss Alert!</span>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545', // Danger theme color
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, force add anyway',
                cancelButtonText: 'No, correct cost'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Added to list upon confirmation click
                    appendRowToList(productId, productName, sizeId, sizeName, qty, cost, sellPrice);
                }
            });
        } else {
            // Smooth natural pass-through if margin is profitable
            appendRowToList(productId, productName, sizeId, sizeName, qty, cost, sellPrice);
        }
    });
function appendRowToList(productId, productName, sizeId, sizeName, qty, cost, sellPrice) {
    const rowUid = `row-${productId}-${sizeId}`;
    if (document.getElementById(rowUid)) {
        alert(`"${productName}" in variant [${sizeName}] already lives on this worksheet item row line queue.`);
        return;
    }

    const priceWarningBadge = (sellPrice > 0 && cost > sellPrice) 
        ? `<div class="text-danger small fw-bold mt-1" style="font-size: 11px;">
                <i class="fas fa-exclamation-triangle"></i> Exceeds Sale Price (${sellPrice.toFixed(2)})
           , It will not show at add cart Please Increase sell amount</div>` 
        : '';

    const lineRowHtml = `
        <tr id="${rowUid}">
            <td class="align-middle">
                <span class="font-weight-bold text-dark">${productName}</span>
                <input type="hidden" name="items[${itemIndex}][product_id]" value="${productId}">
            </td>
            <td class="text-center align-middle">
                <span class="badge bg-info text-white px-2 py-1">${sizeName}</span>
                <input type="hidden" name="items[${itemIndex}][size]" value="${sizeId}">
            </td>
            <td class="align-middle">
                <input type="number" name="items[${itemIndex}][qty]" class="form-control form-control-sm" value="${qty}" required min="1">
            </td>
            <td class="align-middle">
                <input type="number" step="0.01" name="items[${itemIndex}][costprice]" class="form-control form-control-sm" value="${cost.toFixed(2)}" required>
                <input type="number" name="items[${itemIndex}][sell_price]" value="${sellPrice.toFixed(2)}">
                ${priceWarningBadge}
            </td>
              <td>
               <select class="form-control form-select-sm "  name="items[${itemIndex}][type]" >
                                     
               <option value="solid">Solid</option>
               <option value="damage">Damage</option>
                                    </select>
              </td>
            <td class="text-center align-middle">
                <button type="button" class="btn btn-danger btn-sm remove-added-row"><i class="fas fa-trash-alt"></i></button>
            </td>
        </tr>
    `;

    tableBody.insertAdjacentHTML('beforeend', lineRowHtml);
    itemIndex++;

    // Reset builder context elements
    productSelect.value = '';
    sizeSelect.innerHTML = '<option value="">-- Choose Size --</option>';
    sizeSelect.disabled = true;
    qtyInput.value = '';
    costInput.value = '';
    currentSellPriceInput.value = '';
    warningText.style.display = 'none';

    checkEmptyState();
}  // 5. Delegate row removal deletion listener
    tableBody.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-added-row') || e.target.closest('.remove-added-row')) {
            e.target.closest('tr').remove();
            checkEmptyState();
        }
    });
});
</script>


<script>
    $(document).ready(function() {
        // Handle Success Flashes
        @if(session('success_message'))
            Swal.fire({
                icon: 'success',
                title: 'Return Bul Submitted',
                text: "{{ session('success_message') }}",
                timer: 3500,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
      // AUTOMATICALLY SWITCH TO HISTORY LOGS TAB
            $('.custom-tab-btn[data-target="#tab-invoice-history"]').trigger('click');
        @endif

        // Handle Failure Flashes
        @if(session('error_message'))
             Swal.fire({
                icon: 'error',
                title: ' Return Product Blocked!',
                text: "{{ session('error_message') }}",
                timer: 3500,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
      // AUTOMATICALLY SWITCH TO HISTORY LOGS TAB
            $('.custom-tab-btn[data-target="#tab-invoice-history"]').trigger('click');
        @endif
    });
</script>

<script>function openUpdateModal(productId, productName, sizeId, currentCost, currentSell, totalSold) {
    // Populate raw context identifiers
    $('#modal_product_id').val(productId);
    $('#modal_product_name').val(productName);
    $('#modal_size_id').val(sizeId);
    
    // Pass current data references into mutable input targets
    $('#modal_cost_price').val(currentCost);
    $('#modal_selling_price').val(currentSell);
    $('#modal_sold_total').val(totalSold); // Captures current cumulative sold total for auditing

    // Toggle view visibility 
    $('#updateStockModal').modal('show');
}
$(document).ready(function() {
    $('.datatable-init').DataTable({
        "paging": false,
        "info": false,
        "dom": '<"top-toolbar"f>rt', // 'f' is the filter/search box, 'rt' is the table
        "language": {
            "search": "", // Remove the "Search:" label for a cleaner look
            "searchPlaceholder": "Filter sizes..." // Add a placeholder instead
        },
        "columnDefs": [{
            "targets": 'no-sort',
            "orderable": false
        }]
    });

    // Fix header alignment when accordion opens
    $('a[data-toggle="collapse"]').on('shown.bs.collapse', function () {
        $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
    });
        $("#globalSearch").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $(".product-item").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });

});

$(document).ready(function() {
    // Inject auth parameters directly into JavaScript via Blade tags
    const managerId = "{{ auth()->user()->manager_id   }}";
    const userId    = "{{ auth()->user()->id   }}";
    
    // Generate components for uniqueness
    const dateObj   = new Date();
    const timeStamp = String(dateObj.getTime()).slice(-6); // Last 6 digits of current millisecond timestamp
    
    // Create the unique string (e.g., RET-M1U5-20260710-458122)
    const uniqueInvoiceNo = `RET-M${managerId}U${userId}-${timeStamp}`;
    
    // Set the value into the input field
    $('#automated_invoice_no').val(uniqueInvoiceNo);
});
</script>

@endsection