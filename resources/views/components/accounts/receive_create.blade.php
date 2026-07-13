@extends('layouts.app')

@section('title', 'Product Add ')

@section('content')
<div class="d-flex justify-content-center mt-5">
    <div class="card shadow-lg border-0" style="width: 100%; max-width: 650px; border-radius: 12px; overflow: hidden;">
        <div class="card-header bg-primary text-white p-4 border-0">
            <h4 class="mb-0 fw-bold"><i class="fas fa-file-invoice-dollar me-2"></i>Payment Receipt Voucher</h4>
            <small class="text-white-50">Official Accounting Entry</small>
        </div>
        
        <div class="card-body p-4 bg-light">
            <form id="receive-payment-form" action="{{ route('receive.payment.store') }}" method="POST">
                @csrf
                <input type="hidden" name="invoice_id" id="receive_invoice_id" value="{{ $invoiceNo ?? '' }}">

                <div class="row g-4">
                    <div class="col-md-12">
                        <label class="form-label text-uppercase fw-bold text-muted small">Customer / Account</label>
                        <select class="form-control form-select-lg border-secondary shadow-sm" name="ledger_id" id="customer_ledger_id" required>
                            <option value="">Select Account...</option>
                            @foreach($customers as $customer)
                                @if($customer->ledgerDetails)
                                    <option value="{{ $customer->ledgerDetails->id }}">{{ $customer->ledgerDetails->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-uppercase fw-bold text-muted small">Amount (TK)</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-success text-white border-0">৳</span>
                            <input type="number" step="0.01" class="form-control border-secondary" name="amount" id="receive_amount" placeholder="0.00" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-uppercase fw-bold text-muted small">Date</label>
                        <input type="date" class="form-control form-control-lg border-secondary" name="payment_date" id="payment_date" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="col-12">
                        <label class="form-label text-uppercase fw-bold text-muted small">Remarks</label>
                        <textarea class="form-control border-secondary" name="remarks" id="receive_remarks" rows="2" placeholder="Document the transaction details..."></textarea>
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-grid">
                    <button type="submit" class="btn btn-success btn-lg fw-bold shadow" id="btnSubmitReceive">
                        <i class="fas fa-check-circle me-2"></i>Post to Ledger
                    </button>
                </div>
            </form>
        </div>
        <div class="card-footer bg-white text-center py-3">
            <p class="text-muted small mb-0">System Generated Accounting Entry</p>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    $('#receive-payment-form').on('submit', function(e) {
        e.preventDefault(); // Stop standard form refresh redirect

        let submitBtn = $('#btnSubmitReceive');
        submitBtn.prop('disabled', true).text('Saving...'); // Prevent double clicking

        // Gather token and fields
        let formData = $(this).serialize();
     let formAction = $(this).attr('action') || "{{ route('receive.payment.store') }}";// Fallback if action attribute is missing

        $.ajax({
            url: formAction,
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                submitBtn.prop('disabled', false).text('Save Receipt');
                
                // Check for response.success (boolean true) based on controller standard
                if(response.success) {
                    
                    // Extracting details safely from response.data matching the JournalBook save
                    let journalId = response.data && response.data.id ? response.data.id : 'N/A';
                    let amountPaid = response.data && response.data.amount ? response.data.amount : $('#receive_amount').val();

                    Swal.fire({
                        title: 'Receipt Saved!',
                        html: `
                            <div class="text-start p-2">
                                <p class="mb-1"><strong>Journal ID:</strong> <span class="badge bg-primary text-light">#${journalId}</span></p>
                                <p class="mb-1"><strong>Amount Received:</strong> <span class="text-success fw-bold">${amountPaid} TK</span></p>
                            </div>
                            <p class="mt-2 text-muted small">${response.message}</p>
                        `,
                        icon: 'success',
                        confirmButtonText: 'Done',
                        confirmButtonColor: '#198754' // Green match for success receipt
                    }).then((result) => {
                        // Refresh to update main ledger UI values after they read the alert
                        location.reload(); 
                    });

                } else {
                    // Handled if controller passes success: false explicitly
                    Swal.fire({
                        title: 'Error!',
                        text: response.message || 'Could not process the payment transaction.',
                        icon: 'error',
                        confirmButtonText: 'Review Fields'
                    });
                }
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false).text('Save Receipt');
                
                // Read exact Laravel validation exception notes if failed
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