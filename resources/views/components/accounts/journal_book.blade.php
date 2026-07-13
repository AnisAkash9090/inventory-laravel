@extends('layouts.app')

@section('title', 'Product Add ')

@section('content')
<div class="card bg-white border-light shadow-sm mb-4">
    <div class="card-header border-bottom bg-light d-flex justify-content-between align-items-center py-3" 
         data-bs-toggle="collapse" 
         href="#collapseFilterPanel" 
         role="button" 
         aria-expanded="{{ !empty($selectedLedgerId) ? 'true' : 'false' }}" 
         aria-controls="collapseFilterPanel"
         style="cursor: pointer;">
         
        <h5 class="card-title mb-0 text-uppercase tracking-wider fw-bold text-dark">
            <i class="fas fa-sliders-h me-2 text-primary"></i> Ledger Filter Configuration
        </h5>
        
        <div>
            @if(!empty($selectedLedgerId))
                <span class="badge bg-success px-2 py-1 me-2 small text-white">Parameters Active</span>
            @endif
            <i class="fas fa-chevron-down text-secondary toggle-icon-rotate"></i>
        </div>
    </div>

    <div class="collapse show"  id="collapseFilterPanel">
        <div class="card-body bg-light bg-opacity-25">
            <form method="GET" action="{{ route('journalview') }}" class="row g-3">
           <div class="col-lg-4 col-md-6">
    <label class="form-label text-dark small fw-bold">Account Group Filter</label>
    <div class="input-group mb-3">
        <span class="input-group-text bg-white border-secondary text-secondary"><i class="fas fa-layer-group"></i></span>
       <select class="form-control bg-white text-dark border-secondary" id="accountGroupSelect" name="account_group_id" required>
    <option value="">-- Select Account Group --</option>
    <option value="all" {{ $selectedGroupId === 'all' ? 'selected' : '' }}>All Groups (Show All Ledgers)</option>
    @foreach($accountGroups as $group)
        <option value="{{ $group->id }}" {{ $selectedGroupId == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
    @endforeach
</select>
    </div>
</div>

<div class="col-lg-4 col-md-6">
    <label class="form-label text-dark small fw-bold">Account Ledger</label>
    <div class="input-group mb-3">
        <span class="input-group-text bg-white border-secondary text-secondary"><i class="fas fa-user"></i></span>
      <select class="form-control bg-white text-dark border-secondary" id="ledgerSelect" name="ledger_id" data-selected="{{ $selectedLedgerId }}" required>
    <option value="">-- Select Ledger --</option>
</select>
    </div>
</div>
                <div class="col-lg-3 col-md-3 col-sm-6">
                    <label class="form-label text-dark small fw-bold">From Date</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-secondary text-secondary"><i class="fas fa-calendar-alt"></i></span>
                        <input type="date" class="form-control bg-white text-dark border-secondary" name="from_date" value="{{ $fromDate }}">
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6">
                    <label class="form-label text-dark small fw-bold">To Date</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-secondary text-secondary"><i class="fas fa-calendar-check"></i></span>
                        <input type="date" class="form-control bg-white text-dark border-secondary" name="to_date" value="{{ $toDate }}">
                    </div>
                </div>
                <div class="col-lg-2 col-md-12 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100 fw-bold py-2 shadow-sm text-uppercase">
                        <i class="fas fa-sync-alt me-1"></i> Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@if(!empty($selectedLedgerId) || $selectedGroupId === 'all')
<div class="card bg-white border-light shadow-sm">
    <div class="card-header border-bottom bg-light d-flex justify-content-between align-items-center py-3">
        <h5 class="card-title mb-0 text-uppercase tracking-wider fw-bold text-dark">
            <i class="fas fa-book text-success me-2"></i>Statement Registry
        </h5>
        <div id="tableSearchContainer"></div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive" style="max-height: 550px; overflow-y: auto;">
            <table class="table table-hover table-striped align-middle mb-0" id="ledgerBookGrid" style="width:100%">
                <thead class="table-light position-sticky top-0 style-sticky-header">
                    <tr class="border-bottom">
                        <th class="text-dark" style="width: 9%;">Date</th>
                        <th class="text-dark" style="width: 15%;">Journal ID</th>
                        <th class="text-dark" style="width: 33%;">Description / Remarks</th>
                        <th class="text-end text-dark" style="width: 13%;">Debit (TK)</th>
                        <th class="text-end text-dark" style="width: 13%;">Credit (TK)</th>
                        <th class="text-end pe-3 text-dark" style="width: 14%;">Running Bal (TK)</th>
                    </tr>
                </thead>
                <tbody class="text-dark">
                    <tr class="bg-light fw-bold text-dark">
                        <td class="ps-3">{{ \Carbon\Carbon::parse($fromDate)->format('Y-m-d') }}</td>
                        <td><span class="badge bg-dark text-white text-uppercase tracking-wide">Opening</span></td>
                        <td class="text-secondary font-monospace">Initial Balance Brought Forward (BF)</td>
                        <td class="text-end text-muted">-</td>
                        <td class="text-end text-muted">-</td>
                        <td class="text-end pe-3 fw-bolder">{{ number_format($openingBalance, 2) }}</td>
                    </tr>

                   @php $runningBal = $openingBalance; @endphp

                    @forelse($journals as $journal)
                        @php
                            // Check if we are viewing the consolidated ledger framework
                            $isAllView = ($selectedGroupId === 'all' || empty($selectedLedgerId));
                            
                            if ($isAllView) {
                                // In a master list view, display raw entry amounts clearly
                                $drAmt = $journal->dr_ledger ? $journal->amount : 0;
                                $crAmt = !$journal->dr_ledger ? $journal->amount : 0;
                            } else {
                                // Traditional mapping for single ledger view styles
                                $isDr = ($journal->dr_ledger == $selectedLedgerId);
                                $drAmt = $isDr ? $journal->amount : 0;
                                $crAmt = !$isDr ? $journal->amount : 0;
                            }
                            
                            $runningBal += ($drAmt - $crAmt);
                        @endphp
                        <tr class="border-bottom border-light">
                            <td class="ps-3 text-secondary">{{ $journal->transaction_date }}</td>
                            <td><span class="text-center">{{ $journal->id }}</span></td>
                            <td class="text-dark small">{{ $journal->remarks }}</td>
                            <td class="text-end text-success fw-bold">{{ $drAmt > 0 ? number_format($drAmt, 2) : '-' }}</td>
                            <td class="text-end text-danger fw-bold">{{ $crAmt > 0 ? number_format($crAmt, 2) : '-' }}</td>
                            <td class="text-end pe-3 fw-bold text-dark">{{ number_format($runningBal, 2) }}</td>
                        </tr>
                    @empty
                        @endforelse
                </tbody>
                <tfoot class="table-light border-top border-secondary position-sticky bottom-0 style-sticky-footer">
                    <tr class="fw-bold bg-light text-dark">
                        <td colspan="3" class="text-end text-secondary ps-3">Total Changes Summary:</td>
                        <td class="text-end text-success fs-6">{{ number_format($totalDebit, 2) }}</td>
                        <td class="text-end text-danger fs-6">{{ number_format($totalCredit, 2) }}</td>
                        <td class="text-end pe-3 text-dark fs-6 bg-secondary bg-opacity-10">{{ number_format($closingBalance, 2) }}</td>
                    </tr>
                    <tr class="fw-bold align-middle bg-light">
                        <td colspan="5" class="text-end text-uppercase border-0 text-muted small tracking-wider">Final Account Standing:</td>
                        <td class="text-end p-2 border-0 pe-3">
                            @if($closingBalance > 0)
                                <div class="badge bg-danger text-white w-100 p-2 text-wrap shadow-sm rounded-1">
                                    <i class="fas fa-exclamation-circle me-1"></i> Due: {{ number_format($closingBalance, 2) }} TK
                                </div>
                            @elseif($closingBalance < 0)
                                <div class="badge bg-info text-dark w-100 p-2 text-wrap shadow-sm rounded-1">
                                    <i class="fas fa-hand-holding-usd me-1"></i> Advance: {{ number_format(abs($closingBalance), 2) }} TK
                                </div>
                            @else
                                <div class="badge bg-success text-white w-100 p-2 text-wrap shadow-sm rounded-1">
                                    <i class="fas fa-check-circle me-1"></i> Settled / Clear
                                </div>
                            @endif
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@else
<div class="alert alert-light border border-secondary text-center text-muted py-4 shadow-sm">
    <i class="fas fa-info-circle fs-4 mb-2 text-primary d-block"></i>
    Please select a functional Customer/SR ledger from the controller parameter panel above to run transaction computations.
</div>
@endif

<style>
    .style-sticky-header th { background: #f8f9fa !important; z-index: 10; box-shadow: inset 0 -2px 0 #dee2e6; }
    .style-sticky-footer td { background: #f8f9fa !important; z-index: 10; box-shadow: inset 0 2px 0 #dee2e6; }
    
    /* Smooth Chevron Animation */
    [aria-expanded="true"] .toggle-icon-rotate { transform: rotate(180deg); transition: transform 0.2s ease; }
    [aria-expanded="false"] .toggle-icon-rotate { transform: rotate(0deg); transition: transform 0.2s ease; }

    /* Custom Input Configurations */
    .dataTables_filter { display: none; }
    .custom-search-input { width: 260px !important; border-radius: 4px !important; background-color: #ffffff !important; color: #212529 !important; border: 1px solid #ced4da !important; }
    .custom-search-input::placeholder { color: #adb5bd; }


</style>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    if ($('#ledgerBookGrid').length) {
        let table = $('#ledgerBookGrid').DataTable({
            "paging": false,
            "info": false,
            "searching": true,
            "ordering": true,
            "order": [[0, 'asc']], // Orders initially by date ascending
            "columnDefs": [
                { "targets": [1, 2, 3, 4], "orderable": true },
                { "targets": [5], "orderable": false } // Lock running balance tracking sequence
            ],
            "language": {
                "zeroRecords": "No matching rows found."
            }
        });

        // Setup the localized search window inside header markup smoothly
        $('#tableSearchContainer').html('<input type="search" class="form-control custom-search-input form-control-sm" placeholder="🔍 Filter rows instantly...">');
        $('#tableSearchContainer input').on('keyup change clear', function() {
            table.search(this.value).draw();
        });
    }
});
$(document).ready(function() {
    $('#accountGroupSelect').on('change', function() {
        let groupId = $(this).val();
        let ledgerDropdown = $('#ledgerSelect');
        
        // Check if there is an initial value saved from the server side
        let preSelectedLedger = ledgerDropdown.data('selected');

        ledgerDropdown.html('<option value="">-- Loading Ledgers... --</option>');

        if (!groupId) {
            ledgerDropdown.html('<option value="all">-- Select Ledger --</option>');
            ledgerDropdown.prop('required', true);
            return;
        }

        $.ajax({
            url: "{{ route('accounts.getLedgers') }}",
            type: "GET",
            data: { group_id: groupId },
            dataType: "json",
            success: function(data) {
                let options = '';
                
                if (groupId === 'all') {
                    options += '<option value="all">All Ledgers</option>';
                    ledgerDropdown.prop('required', false); 
                } else {
                    options += '<option value="">-- Select Ledger --</option>';
                    ledgerDropdown.prop('required', true);
                }
                
                $.each(data, function(key, ledger) {
                    // Check if this iteration matches our active URL string parameter match rule
                    let isSelected = (preSelectedLedger && preSelectedLedger == ledger.id) ? 'selected' : '';
                    options += `<option value="${ledger.id}" ${isSelected}>${ledger.name}</option>`;
                });
                
                ledgerDropdown.html(options);
                
                // Clear the data attribute once it's set so subsequent manual changes work cleanly
                ledgerDropdown.data('selected', '');
            },
            error: function(xhr) {
                console.error("Ledger fetching failed:", xhr);
                ledgerDropdown.html('<option value="">-- Error Loading Data --</option>');
            }
        });
    });

    // =============================================================
    // CRITICAL TRIGGER CODE: If a group is selected in the URL on load, 
    // force-trigger the change event to fetch ledgers and auto-select
    // =============================================================
    if ($('#accountGroupSelect').val()) {
        $('#accountGroupSelect').trigger('change');
    }
});
</script>



@endsection