@extends('layouts.app')

@section('title', 'Vendor Payment Voucher')

@section('content')
<div class="d-flex justify-content-center mt-5">
    <div class="card shadow-lg border-0" style="width: 100%; max-width: 650px; border-radius: 12px; overflow: hidden;">
        <div class="card-header bg-dark text-white p-4 border-0">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0 fw-bold"><i class="fas fa-money-check-alt me-2 text-warning"></i>Vendor Payment Voucher</h4>
                    <small class="text-white-50">Debit Account Payable Ledger Entry</small>
                </div>
                <span class="badge bg-danger px-3 py-2 text-uppercase">Debit Outflow</span>
            </div>
        </div>
        
        <div class="card-body p-4 bg-light">
            <form id="vendor-payment-form" action="{{ route('payment_seller.store') }}" method="POST">
                @csrf

                <div class="row g-4">
                    <div class="col-md-12">
                        <label class="form-label text-uppercase fw-bold text-muted small">Purchase Invoice No <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg border-secondary shadow-sm" name="invoice_id" id="payment_invoice_id" value="{{ $invoiceNo ?? '' }}" placeholder="e.g., INV-2026-001" required>
                    </div>
<div class="form-group mb-3 col-md-6">
    <label for="ledger_type">Select Type</label>
    <select class="form-control border-secondary shadow-sm" name="journalType" id="ledger_type">
        <option value="">-- Choose Type --</option>
        <option value="4">Vendor</option>
        <option value="8">Seller</option>
    </select>
</div>

<div class="form-group mb-3 col-md-6">
    <label for="seller_ledger_id">Ledger</label>
    <select class="form-control form-select-lg border-secondary shadow-sm" name="ledger_id" id="seller_ledger_id" required>
        <option value="">Select Vendor/Seller Ledger...</option>
    </select>
</div>
                 <script>
                    $(document).ready(function() {
    $('#ledger_type').on('change', function() {
        var type = $(this).val();
        var $ledgerSelect = $('#seller_ledger_id');

        // Reset the ledger dropdown
        $ledgerSelect.html('<option value="">Select Vendor/Seller Ledger...</option>');

        if (type) {
            $.ajax({
                url: '/get-ledgers-by-type', // The Laravel route
                type: 'GET',
                data: { type: type },
                dataType: 'json',
                success: function(data) {
                    // Loop through the data and append options
                    $.each(data, function(key, ledger) {
                        $ledgerSelect.append(
                            $('<option>', {
                                value: ledger.ledger, // 'ledger' field for the value
                                text: ledger.name    // 'aname' field for the display text
                            })
                        );
                    });
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error: ", error);
                }
            });
        }
    });
});
                 </script>

                    <div class="col-md-6">
                        <label class="form-label text-uppercase fw-bold text-muted small">Paid Amount (TK) <span class="text-danger">*</span></label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-danger text-white border-0">৳</span>
                            <input type="number" step="0.01" min="0.01" class="form-control border-secondary shadow-sm" name="amount" id="payment_amount" placeholder="0.00" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-uppercase fw-bold text-muted small">Payment Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control form-control-lg border-secondary shadow-sm" name="payment_date" id="payment_date" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="col-12">
                        <label class="form-label text-uppercase fw-bold text-muted small">Transaction Remarks / Internal Notes</label>
                        <textarea class="form-control border-secondary shadow-sm" name="remarks" id="payment_remarks" rows="2" placeholder="e.g., Paid via Bank Transfer / Cheque No..."></textarea>
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-grid">
                    <button type="submit" class="btn btn-danger btn-lg fw-bold shadow-sm" id="btnSubmitPayment">
                        <i class="fas fa-paper-plane me-2"></i>Execute & Post Payment
                    </button>
                </div>
            </form>
        </div>
        
        <div class="card-footer bg-white text-center py-3 border-top-0">
            <p class="text-muted small mb-0">Double-Entry Ledger Authorization Document</p>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    $('#vendor-payment-form').on('submit', function(e) {
        e.preventDefault(); 

        let submitBtn = $('#btnSubmitPayment');
        submitBtn.prop('disabled', true).text('Processing Payment...'); 

        let formData = $(this).serialize();
        let formAction = $(this).attr('action') || "{{ route('payment_seller.store') }}";

        $.ajax({
            url: formAction,
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                submitBtn.prop('disabled', false).text('Execute & Post Payment');
                
                if(response.success) {
                    let journalId = response.data && response.data.id ? response.data.id : 'N/A';
                    let amountPaid = response.data && response.data.amount ? response.data.amount : $('#payment_amount').val();

                    Swal.fire({
                        title: 'Payment Posted Successfully!',
                        html: `
                            <div class="text-start p-2 border rounded bg-white">
                                <p class="mb-1"><strong>Journal Reference:</strong> <span class="badge bg-dark text-light">#${journalId}</span></p>
                                <p class="mb-1"><strong>Amount Paid:</strong> <span class="text-danger fw-bold">${amountPaid} TK</span></p>
                                <p class="mb-0"><strong>Invoice Reference:</strong> <span class="text-muted fw-bold">#${$('#payment_invoice_id').val()}</span></p>
                            </div>
                            <p class="mt-3 text-muted small mb-0">${response.message}</p>
                        `,
                        icon: 'success',
                        confirmButtonText: 'Finish Document',
                        confirmButtonColor: '#212529' 
                    }).then((result) => {
                        location.reload(); 
                    });

                } else {
                    Swal.fire({
                        title: 'Transaction Error!',
                        text: response.message || 'Could not post this payment to ledger records.',
                        icon: 'error',
                        confirmButtonText: 'Review Fields',
                        confirmButtonColor: '#dc3545'
                    });
                }
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false).text('Execute & Post Payment');
                
                let errorMessage = 'Something went wrong.';
                if(xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                Swal.fire({
                    title: 'Submission Failed!',
                    text: errorMessage,
                    icon: 'error',
                    confirmButtonText: 'Try Again',
                    confirmButtonColor: '#dc3545'
                });
                
                console.error(xhr.responseText);
            }
        });
    });
});
</script>
@endsection