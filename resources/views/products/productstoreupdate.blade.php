@extends('layouts.app')
@section('title', 'Product Store update')
@section('content')
<div style="width:122%;height:40px;background:white;
margin-left:-44px;
margin-top:-10px;
padding-top:10px;
color:black;
display:flex;
padding-left:30px;
border-top-right-radius:5px;"><h4 class="" style="letter-spacing:0.8px;font-weight:400;">
<i class="fa fa-users blue2_color">&nbsp;</i>Store Upgrade</h4></div>

<div style="width: 100%;margin-top:-30px;padding-bottom:40px;
   position: relative;
">
<button type="button" style="float:right;
    position: relative;z-index:1;" class="btn btn-success mb-3" data-toggle="modal" data-target="#bulkStockModal">
    <i class="fas fa-plus-circle mr-2"></i>Bulk Purchase
</button>
</div>
    <div class="card ">
        <div class="card-header  d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Inventory Manager</h5>
        </div>
<style>
    /* Container for the search box */
.top-toolbar {
    display: flex;
    justify-content: flex-end;
    margin-bottom: 15px;
    padding-right: 5px;
}

/* Styling the actual search input */
.dataTables_filter input {
    background: rgba(255, 255, 255, 0.5) !important;
    backdrop-filter: blur(5px);
    border: 1px solid rgba(0, 0, 0, 0.1) !important;
    border-radius: 8px !important;
    padding: 6px 12px !important;
    outline: none !important;
    transition: all 0.3s ease;
    width: 200px; /* Adjust width as needed */
}

.dataTables_filter input:focus {
    background: rgba(243, 0, 0, 0.8) !important;
    border-color: #667eea !important;
    box-shadow: 0 0 10px rgba(102, 126, 234, 0.2) !important;
    width: 250px; /* Expands slightly on focus */
}

/* Optional: Add a magnifying glass icon via CSS if you didn't use the label */
.dataTables_filter {
    position: relative;
}

.dataTables_filter::after {
    content: "\f002"; /* FontAwesome Search Icon */
    font-family: "Font Awesome 5 Free";
    font-weight: 900;
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #ffffff;
    pointer-events: none;
}
    .glass-search-container {
    position: relative;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 10px;
    padding: 5px 15px;
    display: flex;
    align-items: center;
}

.glass-search-container i {
    color: #718096;
    margin-right: 10px;
}

.glass-search-container input {
    background: transparent;
    border: none;
    box-shadow: none !important;
    color: #2d3748;
}

/* Hide default DataTable search box to use our custom UI */

    .ims-glass-accordion {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 15px;
    }

    .ims-card {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 12px !important;
        margin-bottom: 12px;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }

    .ims-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    .ims-header {
        background: transparent !important;
        padding: 0 !important;
        border: none !important;
    }

    .ims-trigger {
        width: 100%;
        padding: 1.2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        text-decoration: none !important;
        color: #2d3748;
        font-weight: 600;
        transition: background 0.2s;
    }

    .ims-trigger:hover {
        background: rgba(0, 0, 0, 0.02);
    }

    .stock-badge {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        box-shadow: 0 4px 10px rgba(118, 75, 162, 0.3);
    }

    .ims-table {
        border-collapse: separate;
        border-spacing: 0 8px;
    }

    .ims-table thead th {
        background: transparent;
        border: none;
        color: #718096;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
    }

    .ims-table tbody tr {
        background: rgba(255, 255, 255, 0.5);
        transition: transform 0.2s;
    }

    .ims-table td {
        border: none;
        padding: 12px 8px;
    }

    .ims-table td:first-child { border-radius: 8px 0 0 8px; }
    .ims-table td:last-child { border-radius: 0 8px 8px 0; }

    .btn-add-stock {
        background: #4a5568;
        color: white;
        border-radius: 6px;
        font-size: 0.75rem;
        padding: 5px 12px;
        border: none;
        transition: background 0.2s;
    }

    .btn-add-stock:hover {
        background: #2d3748;
        color: #fff;
    }
</style>

<!-- Global Search for Products -->
<div class="mb-4">
    <div class="glass-search-container">
        <i class="fas fa-search"></i>
        <input type="text" id="globalSearch" class="form-control" placeholder="Search product name or stock status...">
    </div>
</div>

<div id="accordion" class="ims-glass-accordion">
    @foreach($products as $product)
    
    {{-- STEP 1: PRE-CALCULATE ACCORDION HEADER WARNINGS --}}
    @php
        $overpricedRowsCount = 0;
        foreach($product->stores as $item) {
            // Check if Cost Price ($item->price) is greater than Selling Price ($item->sell_price)
            if((float)$item->price > (float)$item->sell_price) {
                $overpricedRowsCount++;
            }
        }
    @endphp

    <div class="card ims-card product-item"> 
        <div class="card-header ims-header" id="heading{{ $product->id }}">
            <a href="javascript:void(0)" class="ims-trigger collapsed" 
               data-toggle="collapse" 
               data-target="#collapse{{ $product->id }}">
                
                <span class="d-flex align-items-center">
                    <i class="fas fa-box-open mr-2 text-primary"></i>
                    <span class="product-title">{{ $product->product_name }}</span>
                </span>
                
                <span class="d-flex align-items-center">
                    <!-- ADDED: Show Count if any row inside has cost > selling price -->
                    @if($overpricedRowsCount > 0)
                        <span class="badge bg-danger text-white border-0 mr-2" title="Cost Price exceeds Selling Price!">
                            <i class="fas fa-exclamation-triangle mr-1"></i> {{ $overpricedRowsCount }} Loss Item(s)
                        </span>
                    @endif

                    <span class="stock-badge">
                        {{ $product->quantity }} in stock
                    </span>
                </span>
            </a>
        </div>

        <div id="collapse{{ $product->id }}" class="collapse" data-parent="#accordion">
            <div class="card-body pt-0">
                <div class="table-responsive">
                    <table class="table ims-table datatable-init w-full text-sm " id="table-{{ $product->id }}">
                        <thead class="thead-dark">
                            <tr>
                                <th>Size</th>
                                <th>Available Qty</th>
                                <th>Sold Qty</th>
                                <th>Alert Qty</th>
                                <th>Cost Price</th>
                                <th>Selling Price</th>
                                <th>Profit Ratio </th>
                                <th class="text-right no-sort">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($product->stores as $item)
                                @php 
                                    $sizeObj = $allSizes->firstWhere('id', $item->size); 
                                    // Row Condition evaluation flag
                                    $isLossRow = (float)$item->price > (float)$item->sell_price;
                                @endphp
                                
                                <!-- FIXED: Dynamic Table Row Color Shift via style / conditional classes -->
                                <tr class="{{ $isLossRow ? 'bg-soft-danger table-danger text-danger' : '' }}" 
                                    style="{{ $isLossRow ? 'background-color: rgba(220, 53, 69, 0.1) !important;' : '' }}">
                                    
                                    <td class="font-weight-bold">
                                        {{ $sizeObj->name }}
                                        @if($isLossRow)
                                            <i class="fas fa-exclamation-circle text-danger ms-1" title="Selling price is lower than cost!"></i>
                                        @endif
                                    </td>
                                    <td>{{ $item->qty }}</td>
                                    <td>{{ $item->sold }}</td>
                                    <td>{{ $item->alertqty }}</td>
                                    <td class="{{ $isLossRow ? 'font-weight-bold text-decoration-underline' : '' }}">{{ $item->price }}</td>
                                    <td>{{ $item->sell_price }}</td>
                                    <td> 
                                        @if($isLossRow)
                                            <span class="badge bg-danger text-white">Loss Engine</span>
                                        @else
                                            <span class="badge badge-success badge-sm">
                                                {{ $item->price > 0 ? round((($item->sell_price - $item->price) / $item->price) * 100) : 0 }} %
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                        <button onclick="openUpdateModal('{{ $product->id }}', '{{ $product->product_name }}', '{{ $item->size }}', '{{ $item->price }}', '{{ $item->sell_price }}', '{{ $item->sold }}')" 
        class="btn-add-stock btn btn-sm btn-outline-secondary">
    <i class="fas fa-edit"></i> Update Rates
</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endforeach
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
        <form action="{{ route('stock.bulkUpdate') }}" method="POST">
            @csrf
            <div class="modal-content border-0 shadow-lg">
                
                <div class="modal-header bg-dark text-white d-flex align-items-center justify-content-between">
                    <h5 class="modal-title class="m-0">
                        <i class="fas fa-file-invoice mr-2 text-warning"></i> Bulk Invoice Stock Management
                    </h5>
                    <button type="button" class="close text-white border-0 bg-transparent" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="font-size: 1.5rem;">&times;</span>
                    </button>
                </div>

                <div class="modal-body p-4">
                    
                    <div class="row g-3 mb-4 bg-light p-3 rounded border">
                        <div class="col-md-4">
                            <label class="form-label font-weight-bold">Invoice No *</label>
                            <input type="text" name="invoiceno" class="form-control" placeholder="e.g., INV-2026-99" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label font-weight-bold">Seller / Vendor *</label>
                            <select name="seller_id" class="form-select form-control" required>
                                <option value="">-- Choose Seller Account --</option>
                                @foreach($sellers as $seller)
                                    <option value="{{ $seller->ledger }}">
                                        #{{ $seller->ledger }} - {{ $seller->name }} 
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label font-weight-bold">Purchase Date *</label>
                            <input type="date" name="buydate" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>

                    <div class="card border-warning mb-4 bg-light">
                        <div class="card-body py-3">
                            <h6 class="font-weight-bold text-warning mb-3"><i class="fas fa-plus"></i> Step 1: Configure Line Item</h6>
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
    <label class="small font-weight-bold">Cost Price</label>
    <input type="number" step="0.01" id="modalCostInput" class="form-control" placeholder="0.00">
    <input type="hidden" id="currentSellPrice">
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

                    <h6 class="font-weight-bold text-secondary mb-2"><i class="fas fa-list"></i> Step 2: Invoice Summary List</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered m-0">
                            <thead class="bg-secondary text-white">
                                <tr>
                                    <th>Product Details</th>
                                    <th style="width: 15%;" class="text-center">Size</th>
                                    <th style="width: 15%;">Quantity</th>
                                    <th style="width: 20%;">Cost Price</th>
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
<script>
document.addEventListener('DOMContentLoaded', function () {
    const masterSizesArray = @json($allSizes);
    let itemIndex = 0;
    
    // UI Selectors
    const productSelect = document.getElementById('modalProductSelect');
    const sizeSelect = document.getElementById('modalSizeSelect');
    const qtyInput = document.getElementById('modalQtyInput');
    const costInput = document.getElementById('modalCostInput');
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
                opt.setAttribute('data-price', item.price);
                opt.setAttribute('data-sell-price', item.sell_price); 
                sizeSelect.appendChild(opt);
            }
        });

        sizeSelect.disabled = false;
    });

    // 2. Size selection trigger to pull data parameters
    sizeSelect.addEventListener('change', function () {
        if (!this.value) {
            costInput.value = '';
            currentSellPriceInput.value = '';
            warningText.style.display = 'none';
            return;
        }
        
        const selectedSizeOption = this.options[this.selectedIndex];
        const defaultCostPrice = selectedSizeOption.getAttribute('data-price') || "0.00";
        const sellPrice = selectedSizeOption.getAttribute('data-sell-price') || "0.00";
        
        costInput.value = parseFloat(defaultCostPrice).toFixed(2);
        currentSellPriceInput.value = parseFloat(sellPrice).toFixed(2);
        
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

        // Show active warning badge inside Step 2 under cost price if it's a loss-making entry
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
                    ${priceWarningBadge}
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
    }

    // 5. Delegate row removal deletion listener
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
                title: 'Rates Updated!',
                text: "{{ session('success_message') }}",
                timer: 3500,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        @endif

        // Handle Failure Flashes
        @if(session('error_message'))
             Swal.fire({
                icon: 'error',
                title: ' Updated Block!',
                text: "{{ session('error_message') }}",
                timer: 3500,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
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
});</script>

@endsection