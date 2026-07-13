@extends('layouts.app')
@section('title', 'Product Details - ')
@section('content')
<style>#product-panel-col,
#cart-panel-col {
    transition: none; /* let slideUp/slideDown handle the animation, avoid CSS transition conflicts */
}

#cart-panel-col.d-none {
    display: none !important;
}#productTable {
    width: 100% !important;
}
#productTable_wrapper {
    width: 100% !important;
}</style>
<!-- Add this once, near the top of your page (e.g. right under the navbar) -->                        <div id="alert-toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 1055; min-width: 300px;"></div>
<div   id="invoice-layout-row">
    <!-- Left Side: Product Table (8 columns) -->
<div >
    <div class="white_shd full margin_bottom_30 shadow-sm" style="border-radius: 15px;">
        
 <div class="full graph_head p-3 border-bottom d-flex justify-content-between align-items-center">
    <div class="heading1 margin_0">
        <h5 class="text-primary">
            <i class="fa fa-boxes mr-2"></i>
            @if(isset($view_mode) && $view_mode === 'all_flat')
                All Products Master Registry
            @else
                {{ $groupt ? $groupt->product_group : 'All Unsorted Products' }}
            @endif
        </h5>
    </div>
<button class="btn btn-success shadow" id="floating-cart-btn"
        data-toggle="modal" data-target="#cartPreviewModal"
        style="position:fixed; bottom:20px; right:20px; z-index:1050; border-radius:50px; padding:12px 20px;">
    <i class="fa fa-shopping-cart"></i>
    <span id="floating-cart-count" class="badge badge-light ml-1">0</span>
</button>

</div>
        
        <div class="table_section padding_infor_info p-3">
            <div class="table-responsive-sm">
                <table id="productTable" class="table table-hover align-middle productTableClass">
                    <thead class="table-light">
                        <tr class="text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                            <th style="width: 80px;">Photo</th>
                            
                            {{-- Modifies header dynamically if view_mode is all_flat --}}
                            <th>Product @if(isset($view_mode) && $view_mode === 'all_flat') - Group @endif</th>
                            
                            <th>Store & Pricing </th>
                            <th class="text-center" style="width: 250px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
<style>
    /* Modern Scrollbar & Sticky Header */
    .fixTableHead { 
        overflow-y: auto; 
        height: 60vh; 
        border-radius: 8px;
        border: 1px solid #eee;
    }
    
    .fixTableHead thead th { 
        position: sticky; 
        top: 0; 
        z-index: 10;
        background: #212529; /* Deep dark header */
        color: white;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 1px;
    }

    /* Table Styling */
    .table-custom { margin-bottom: 0; }
    .table-custom td { vertical-align: middle; padding: 12px 8px; border-bottom: 1px solid #f8f9fa; }

    /* Grand Total Card Styling */
/* Compact Summary Card */
.summary-card-compact {
    background: linear-gradient(135deg, #1d2127 0%, #2e343b 100%);
    color: white;
    padding: 12px 18px; /* Reduced padding */
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
}

.total-info {
    line-height: 1.1;
}

.total-label-sm { 
    font-size: 0.75rem; 
    opacity: 0.7; 
    text-transform: uppercase; 
    letter-spacing: 0.5px;
}

.total-amount-sm { 
    font-size: 1.4rem; 
    font-weight: 700; 
    color: #15ccec; 
}
.total-amount-smrow { 
    font-size: 1.1rem; 
    font-weight: 700; 
    color: #ec6b15; 
}

.proceed-btn-compact {
    max-width: 150px; /* Limits width so it doesn't stretch too far */
    height: 45px;
    font-size: 0.9rem;
    white-space: nowrap;
}

    .total-label { font-size: 0.9rem; opacity: 0.8; }
    .total-amount { font-size: 1.8rem; font-weight: 700; color: #28a745; }

    /* Responsive Mobile View: Table to Cards */
    @media (max-width: 768px) {
        .fixTableHead thead { display: none; } /* Hide headers on mobile */
        
        .fixTableHead table, 
        .fixTableHead tbody, 
        .fixTableHead tr, 
        .fixTableHead td { display: block; width: 100%; }
        
        .fixTableHead tr {
            margin-bottom: 15px;
            background: #fff;
            border: 1px solid #eee;
            border-radius: 10px;
            padding: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        .fixTableHead td {
            text-align: right;
            padding-left: 50%;
            position: relative;
            border: none;
        }

        .fixTableHead td::before {
            content: attr(data-label);
            position: absolute;
            left: 15px;
            width: 45%;
            font-weight: bold;
            text-align: left;
            color: #666;
        }
    }




    /* Align wrapper layout structure gracefully */
.dataTables_wrapper .dataTables_length {
    float: left;
    margin-bottom: 15px;
}

.dataTables_wrapper .dataTables_filter {
    float: right;
    text-align: right;
    margin-bottom: 15px;
}

/* Style the custom raw search bar container spacing inputs */
.dataTables_wrapper .dataTables_filter input {
    border: 1px solid #ced4da;
    border-radius: 4px;
    padding: 5px 10px;
    margin-left: 0.5em;
    display: inline-block;
    width: auto;
    outline: none;
}

/* Fix pagination stacking rules */
.dataTables_wrapper .dataTables_info {
    clear: both;
    float: left;
    padding-top: 0.755em;
}

.dataTables_wrapper .dataTables_paginate {
    float: right;
    padding-top: 0.25em;
}
</style>
 
        <!-- Modal of process invoice -->

        <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
     <div class="modal-content">
  <form id="payment-form">
    <div class="modal-header">
        <h5 class="modal-title">Genarate Invoice</h5>
           <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
    </div>
    
    <div class="modal-body">
        <div class="mb-3">
            <label class="form-label">Select SR / Customer</label>
            <select id="sr-select" class="form-control" name="sr_id">
                <option value="">-- Select SR --</option>
                @if(isset($allSrs))
                    @foreach($allSrs as $sr)
                        <option value="{{ $sr->id }}">{{ $sr->name }}</option>
                    @endforeach
                @endif
            </select>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Customer Name</label>
                <input type="text" id="cust-name" class="form-control" required name="customer_name">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Ledger ID</label>
                <input type="text" id="ledger-id" class="form-control" name="ledger_id" required readonly>
            </div>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Address</label>
            <input type="text" id="cust-address" class="form-control" required name="address">
        </div>

        <div class="mb-3">
            <label class="form-label">Invoice Date</label>
            <input type="date" id="cust-date" class="form-control" required name="invoice_date" value="2026-07-07">
        </div>
        <hr>

        <div class="bg-light p-3 rounded mb-3 small">
            <div class="d-flex justify-content-between mb-1 text-muted">
                <span>Gross Amount:</span>
                <span>৳<span id="modal-gross-amount-text">0.00</span></span>
            </div>
            <div class="d-flex justify-content-between mb-1 text-danger">
                <span>Total Discount Allowance:</span>
                <span>-৳<span id="modal-total-discount-text">0.00</span></span>
            </div>
            <div class="d-flex justify-content-between fw-bold text-primary pt-2 border-top fs-6">
                <span>Grand Total (Net Payable):</span>
                <span>৳<span id="modal-grand-total-text">0.00</span></span>
            </div>
        </div>

        <input type="hidden" id="modal-hidden-gross" name="total_gross_amount">
        <input type="hidden" id="modal-hidden-discount" name="total_discount_amount">
        <input type="hidden" id="modal-hidden-net" name="total_net_amount">
        <input type="hidden" id="modal-hidden-cost" name="total_cost_amount" value="0.00">
    </div>

    <div class="modal-footer">
        <button type="submit" class="btn btn-primary" id="confirm-payment">Confirm Payment</button>
    </div>
</form>
</div>
        </div>
    </div>
    </div>
</div>
<!-- DataTables CSS -->
<div class="modal fade" id="cartPreviewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 480px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-shopping-cart mr-2"></i>Cart Preview</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-3">
                <div class="table-responsive">
                    <table class="table table-sm" style="font-size:12px;">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th class="text-right">Amount*Qty</th>
                                <th class="text-right">Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="modal-invoice-items-list"></tbody>
                    </table>
                </div>
                <hr>
                <div class="d-flex justify-content-between small text-muted mb-1">
                    <span>Total Amount:</span>
                    <span>৳<span id="modal-invoice-total-amount">0.00</span></span>
                </div>
                <div class="d-flex justify-content-between small text-danger mb-1" id="modal-discount-row" style="display:none;">
                    <span>Total Disc.:</span>
                    <span>-৳<span id="modal-invoice-total-discount">0.00</span></span>
                </div>
                <div class="d-flex justify-content-between fw-bold pt-2 border-top">
                    <span>Grand Total:</span>
                    <span>৳<span id="modal-invoice-grand-total">0.00</span></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="modal-proceed-btn">
                    Proceed <i class="fa fa-arrow-right ml-1"></i>
                </button>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function () {

const urlParams = new URLSearchParams(window.location.search);
    const productParam = urlParams.get('product') || '';
$('.productTableClass').DataTable({
       
        serverSide: false,        // client-side search/sort/paginate now
        ajax: {
            url: "{{ route('products.datatable') }}",
            data: function (d) {
                d.view_mode = "{{ $view_mode ?? '' }}";
                d.group_id  = productParam;   // <-- add thi
            },
            dataSrc: 'data'
        },
        columns: [
            { data: 'photo', orderable: false },
            { data: 'product', orderable: true },
            { data: 'inventory', orderable: false },
            { data: 'action', orderable: false }
        ],
        order: [[1, 'asc']],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search products...",
           
        },
        responsive: true,
        pageLength: 25
    });
});


// Add this instead, to delay the ajax call until typing pauses:
$('.dataTables_filter input').off('keyup search input').on('keyup', function () {
    clearTimeout(window.dtSearchTimer);
    var table = $('.productTableClass').DataTable();
    window.dtSearchTimer = setTimeout(function () {
        table.search(this.value).draw();
    }.bind(this), 400);
});
function showBootstrapToast(message, type = 'success') {
    // Unique ID generation to manage concurrent notifications safely
    let alertId = 'toast-' + Date.now();
    
    // Theme configuration mapping
    let config = {
        success: { bg: '#28a745', icon: 'fas fa-check-circle', title: 'Success' },
        warning: { bg: '#ffc107', icon: 'fas fa-exclamation-triangle', title: 'Warning' },
        danger:  { bg: '#dc3545', icon: 'fas fa-times-circle', title: 'Error' }
    };

    // Fallback protection for unknown types
    let theme = config[type] || config.success;
    
    // Style adjustments: set white text for success/danger, dark text for warning readability
    let textColor = type === 'warning' ? '#212529' : '#ffffff';

    // Dynamic Bootstrap Alert markup template
    let alertHtml = `
        <div id="${alertId}" class="alert alert-dismissible fade show shadow-sm border-0 d-flex align-items-center mb-2" 
             role="alert" 
             style="background-color: ${theme.bg}; color: ${textColor};">
            <div class="me-2">
                <i class="${theme.icon} mr-2"></i>
                <strong>${theme.title}:</strong> ${message}
            </div>
            <button type="button" class="close ml-auto text-current border-0 bg-transparent" 
                    data-dismiss="alert" 
                    aria-label="Close" 
                    style="outline: none; color: ${textColor}; opacity: 0.8;">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    `;
    
    // Append the newly generated element to our fixed notification viewport
    $('#alert-toast-container').append(alertHtml);
    
    // Self-destruct sequence tracking timer (2000ms = 2 Seconds)
    setTimeout(function() {
        $(`#${alertId}`).fadeTo(500, 0, function() {
            $(this).remove(); // Cleans up the DOM footprint completely
        });
    }, 2000);
}
    /* Remove js */
$(document).on('click', '.remove-item', function() {
    let clickedButton = $(this);
    let itemId = clickedButton.data('id');
    
    // =========================================================================
    // CRITICAL FIX: Extract tracking context BEFORE ajax execution starts
    // Make sure your delete buttons have these HTML attributes: data-product-id="..." and data-size="..."
    // If they aren't directly on the button, read them from the row: clickedButton.closest('tr').data('product-id')
    // =========================================================================
    let productId = clickedButton.data('product-id') || clickedButton.closest('tr').data('product-id');
    let previouslySelectedSize = clickedButton.data('size') || clickedButton.closest('tr').data('size') || '';

    $.ajax({
        url: '/invoice/remove-item/' + itemId,
        method: 'DELETE',
        data: { _token: "{{ csrf_token() }}" },
        success: function(response) {
            if (response.success) {
                // 1. Refresh the side invoice layout preview metrics
                loadInvoicePreview();

                // Reads real quantity and product details returned dynamically by the controller
                showBootstrapToast(`Removed: ${response.qty}x ${response.product_name} dropped from invoice.`, 'warning');

                // 2. CRITICAL: Refresh the product stock 
                if (response.product_id) {
                    $('.size-selector[data-product-id="' + response.product_id + '"]').trigger('change');
                }
            } else {
                showBootstrapToast(response.message || "Could not drop item from invoice context tracker.", "danger");
                
                if (productId) {
                    // Force the dropdown visually back to the default placeholder option
                    $('.size-selector[data-product-id="' + productId + '"]').val('');
                    
                    // Manually clear out the visual stock and cost numbers since no size is selected anymore
                    $('#stock-' + productId).text('0');
                    $('#cost-' + productId).text('0.00');
                    $(`#price-${productId}`).val('');
                    $(`.add-to-invoice[data-id="${productId}"]`).prop('disabled', true);
                }
            }
        },
        error: function(xhr) {
            showBootstrapToast("Execution error: Could not connect to server to drop item.", "danger");
            
            if (productId) {
                let selector = $('.size-selector[data-product-id="' + productId + '"]');
                
                // 1. Restore the dropdown element back to its size state if you want it kept, 
                // OR leave it blank based on your current preference logic:
                selector.val(''); 
                
                // 2. Trigger the layout engine change tracker
                selector.trigger('change');
                
                // 3. Hard reset adjacent HTML numeric text spans so they don't display old data
                $('#stock-' + productId).text('0');
                $('#cost-' + productId).text('0.00');
                $('#price-' + productId).val('');
                $('#qty-' + productId).val('');
                
                // 4. Disable the insertion action button safely
                $(`.add-to-invoice[data-id="${productId}"]`).prop('disabled', true);
            }
            
            loadInvoicePreview();
        }
    });
});
    $(document).ready(function() {
        loadInvoicePreview();
    });
    /* Size select for get count of stock */
    $(document).on('focusin', '.size-selector', function () {
    const $select = $(this);
    if ($select.data('loaded')) return; // only fetch once per dropdown

    const productId = $select.data('product-id');
    $select.html('<option value="">Loading sizes...</option>');

    $.get(`/product/${productId}/sizes`, function (sizes) {
        let opts = '<option value="">Select Size</option>';
        sizes.forEach(s => {
            const disabledAttr = s.disabled ? 'disabled' : '';
            const warningText = s.disabled
                ? ` (Disabled: Sell ${parseFloat(s.sell).toFixed(2)} < Cost ${parseFloat(s.cost).toFixed(2)})`
                : '';
            opts += `<option value="${s.id}" ${disabledAttr} data-cost="${s.cost}" data-sell="${s.sell}">${s.name}${warningText}</option>`;
        });
        $select.html(opts);
        $select.data('loaded', true);
    }).fail(function () {
        $select.html('<option value="">Failed to load sizes</option>');
    });
});
   $(document).on('change', '.size-selector', function() {
    var productId = $(this).data('product-id');
    var size = $(this).val();
    var addButton = $('.add-to-invoice[data-id="' + productId + '"]');

    if (!size) return;

    $.ajax({
        url: '/get-store-details',
        method: 'GET',
        data: { product_id: productId, size: size },
        success: function(data) {
            if (data) {
                $('#stock-' + productId).text(data.qty);
                $('#cost-' + productId).text(data.price);

                $('#qty-' + productId).attr('max', data.qty);

                $('#price-' + productId).val((data.sell_price * 1).toFixed(2));
                $('#price_get-' + productId).val((data.sell_price * 1).toFixed(2));

                if (parseInt(data.qty) > 0) {
                    addButton.prop('disabled', false).removeClass('btn-secondary').addClass('btn-primary');
                    $('#qty-' + productId).focus().select();
                } else {
                    addButton.prop('disabled', true).removeClass('btn-primary').addClass('btn-secondary');
                }
            }
        },
        error: function(xhr) {
            console.error('get-store-details failed:', xhr.responseText);
        }
    });
});

$(document).on('keypress', 'input[id^="qty-"]', function(e) {
        if (e.which == 13) { // 13 is the Enter key code
            e.preventDefault();
            var productId = $(this).attr('id').split('-')[1];
            $('.add-to-invoice[data-id="' + productId + '"]').click();
        }
    });

$(document).on('click', '.add-to-invoice', function(e) {
    e.preventDefault();

    // 1. Declare productId FIRST so everything below can access it
    let productId = $(this).data('id');
    
    // 2. Collect the remaining fields safely
    let discount = $(`#discount-${productId}`).val() || 0; // Defaults to 0 if empty
    let size = $(this).closest('tr').find('.size-selector').val();
    let stock = parseInt($('#stock-' + productId).text()) || 0;
    let qty = parseFloat($('#qty-' + productId).val()) || 0; // Changed to parseFloat in case you allow decimal qty
    let price = $('#price-' + productId).val();
    let price_db = $('#price_get-' + productId).val();

    // 1. Validation
    // Validate Price (Must exist and be >= 0.1)
// 1. Validate Price
// 1. Validate Base Minimum Price Entry// 1. Validate basic Price parameters
if (!price || price < 0.1) {
    showBootstrapToast("Please enter a valid selling price (minimum 0.1)", "warning");
    $('#price-' + productId).focus();
    return;
}

// 2. Validate against Database Floor Price
if (price < price_db) {
    showBootstrapToast("Price cannot be lower than the present selling price (minimum " + price_db + " TK)", "danger");
    $('#price-' + productId).focus();
    return;
}

// 3. Validate Quantity
if (!qty || qty <= 0) {
    showBootstrapToast("Please enter a valid quantity", "warning");
    $('#qty-' + productId).focus();
    return;
}

// 4. Prevent selling more than stock
if (qty > stock) {
    showBootstrapToast("Insufficient stock! Available: " + stock, "danger");
    $('#qty-' + productId).focus();
    return;
}

    // 2. AJAX Submission
    $.ajax({
        url: "{{ route('invoice.addItem') }}", 
        method: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            product_id: productId,
            size: size,
            qty: qty,
            price: price,
            discount: discount,
            status: 'pending' 
        },
success: function(response) {
    if(response.success) {
        // Read directly from your updated JSON payload parameters
        let qtyAdded    = $('#qty-' + productId).val() || 1;
        let productName = response.product_name; // <-- READS CLEANLY FROM CONTROLLER NOW

        // Trigger your custom toast notification alert engine
    showBootstrapToast(`Added: ${qtyAdded}x ${productName} successfully!`, 'success');

        // Standard system interface resets
        loadInvoicePreview();
        
        let sizeSelector = $('.size-selector[data-product-id="' + productId + '"]');
        sizeSelector.trigger('change');
        
        $('#qty-' + productId).val('');
        $(`#discount-${productId}`).val(''); 
    }
},
        error: function(err) {
            alert("Error adding item. Please check your inputs.");
        }
    });
});
// Function to fetch and display pending invoice items
// 4. Your existing Preview function
 function loadInvoicePreview() {
    $.ajax({
        url: "{{ route('invoice.getPending') }}",
        method: "GET",
        success: function (data) {
            let html = '';
            let grandTotal = 0, grandrowamount = 0, granddiscount = 0, rowcount = 0, itemscount = 0, grandCost = 0;

            data.items.forEach(item => {
                let itemDiscount = (parseFloat(item.discount) || 0) * (parseFloat(item.qty) || 0);
                let itemRowAmount = (parseFloat(item.qty) || 0) * (parseFloat(item.price) || 0);
                let rowTotal = itemRowAmount - itemDiscount;
                let itemscost = (parseFloat(item.cost) || 0) * (parseFloat(item.qty));

                grandTotal += rowTotal;
                grandrowamount += itemRowAmount;
                granddiscount += itemDiscount;
                grandCost += itemscost;
                rowcount += 1;
                itemscount += Number(item.qty) || 0;

                html += `<tr>
                    <td>${item.product.product_name} <br><small style='font-size:10px!important;'>(${item.size_name})</small></td>
                    <td class="text-right" style="line-height:1.3;">
                        <div><span class="text-muted" style="font-size:10px;">Qty:</span> ${item.qty} × ${parseFloat(item.price).toFixed(2)}</div>
                        ${parseFloat(item.discount) > 0 ? `<div class="text-danger" style="font-size:10px;">Disc: -${parseFloat(item.discount).toFixed(2)}</div>` : ''}
                    </td>
                    <td class="text-right fw-bold">${rowTotal.toFixed(2)}</td>
                    <td>
                        <button class="btn btn-sm btn-danger remove-item" 
                                data-id="${item.id}" 
                                data-product-id="${item.product_id}" 
                                data-size="${item.size}">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>`;
            });

            // Modal only — sidebar no longer exists
            $('#modal-invoice-items-list').html(html);
            $('#modal-invoice-grand-total').text(grandTotal.toFixed(2));
            $('#modal-invoice-total-amount').text(grandrowamount.toFixed(2));
            $('#modal-invoice-total-discount').text(granddiscount.toFixed(2));
            $('#modal-discount-row').toggle(granddiscount > 0);
            $('#modal-hidden-cost').val(grandCost.toFixed(2));

            // Floating button badge count
            $('#floating-cart-count').text(itemscount);
        }
    });
}
/* Ajax sr details */
// 1. Load SR list when page is ready
$(document).ready(function() {
    $.ajax({
        url: "{{ route('get.ledger.list') }}", // Create this route
        method: "GET",
      data: {
            account: 1
        },
        success: function(data) {
            let options = '<option value="">-- Select SR --</option>';
            data.forEach(ledger => {
                options += `<option value="${ledger.id}">${ledger.name}</option>`;
            });
            $('#sr-select').html(options);
        }
    });
});

$('#sr-select').on('change', function() {
    let srId = $(this).val();
    if (!srId) return;
  
    // Use the route() helper to generate the correct URL
    $.ajax({
        url: "{{ route('sr.getDetails', ':id') }}".replace(':id', srId),
        method: 'GET',
        success: function(sr) {
          
            console.log(sr);
            $('#cust-name').val(sr.name);
            $('#ledger-id').val(sr.ledger);
            $('#cust-address').val(sr.address);
            if(sr.id != 1){
               $('#cust-name,  #cust-address')
    .prop('readonly', true)
    .css('background-color', '#67a2dd'); 
            }else{
                $('#cust-name,  #cust-address')
    .prop('readonly', false)
  .css('background-color', '#ffffff'); 
            }
            
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error: " + error);
            console.error(xhr.responseText); // This will show you exactly what's wrong
        }
    });
});
</script>

<script>   function proceedToPayment() {
    let totalNet = $('#modal-invoice-grand-total').text() || '0.00';
    let totalDiscount = $('#modal-invoice-total-discount').text() || '0.00';
    let totalGross = $('#modal-invoice-total-amount').text() || '0.00';
    let totalGrosscost = $('#modal-hidden-cost').val() || '0.00';

    $('#modal-gross-amount-text').text(parseFloat(totalGross).toFixed(2));
    $('#modal-total-discount-text').text(parseFloat(totalDiscount).toFixed(2));
    $('#modal-grand-total-text').text(parseFloat(totalNet).toFixed(2));

    $('#modal-hidden-gross').val(parseFloat(totalGross).toFixed(2));
    $('#modal-hidden-discount').val(parseFloat(totalDiscount).toFixed(2));
    $('#modal-hidden-net').val(parseFloat(totalNet).toFixed(2));
    $('#modal-hidden-cost').val(parseFloat(totalGrosscost).toFixed(2));

    $('#paymentModal').modal('show');
}

$('#modal-proceed-btn').on('click', function () {
    $('#cartPreviewModal').modal('hide');
    setTimeout(proceedToPayment, 300);
});

// Sidebar proceed button
$('#proceed-btn').on('click', function () {
    proceedToPayment();
});

// Cart Preview Modal proceed button — close preview modal first, then run same logic
$('#modal-proceed-btn').on('click', function () {
    $('#cartPreviewModal').modal('hide');
    // slight delay so BS4 doesn't fight itself opening one modal while closing another
    setTimeout(proceedToPayment, 300);
});

    /* Payment process */

    $('#payment-form').on('submit', function(e) {
    e.preventDefault(); // Stop page refresh
        let total = $('#invoice-grand-total').text();
        console.log(total);
    let formData = {
        sr_id: $('#sr-select').val(),
        customer_name: $('#cust-name').val(),
        ledger_id: $('#ledger-id').val(),
        address: $('#cust-address').val(),
        invoice_date: $('#cust-date').val(),
        amount: $('#modal-hidden-net').val(),
        discount: $('#modal-hidden-discount').val(),
        net_amount: $('#modal-hidden-gross').val(),
        cost: $('#modal-hidden-cost').val() || '0.00',
        _token: "{{ csrf_token() }}" // Laravel CSRF requirement
    };

$.ajax({
    url: "{{ route('invoice.store') }}",
    method: "POST",
    data: formData,
    dataType: "json", // Explicitly expect JSON from the controller
    success: function(response) {
        // FIXED: Changed response.invoice_id to response.invoice_no
        // FIXED: Ensuring response.amount matches the lowercase key 'amount' from controller response
        Swal.fire({
            title: 'Success!',
            html: `
                <div class="text-start">
                    <p><strong>Invoice No:</strong> ${response.invoice_no}</p>
                    <p><strong>Total Amount:</strong> <span class="text-success">${response.amount}</span></p>
                </div>
                <p>Invoice has been processed successfully.</p>
            `,
            icon: 'success',
            confirmButtonText: 'Great!',
            confirmButtonColor: '#3085d6',
                toast: true,
                position: 'top-end'
        }).then((result) => {
                loadInvoicePreview();
            $('#paymentModal').modal('hide');
            $('#payment-form')[0].reset();
            // location.reload(); 
        });
    },
    error: function(xhr) {
        // Get the real error message from Laravel if validation fails
        let errorMsg = 'Failed to save invoice. Please check your inputs.';
        if (xhr.responseJSON && xhr.responseJSON.message) {
            errorMsg = xhr.responseJSON.message;
        }

        Swal.fire({
            title: 'Error!',
            text: errorMsg,
            icon: 'error',
            confirmButtonText: 'Try Again',
            toast: true,
                position: 'top-end'
        });
        console.error(xhr.responseText);
    }
});
});

/* Search and Shorting filter product table  */

</script>
 @endsection