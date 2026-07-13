@extends('layouts.app') {{-- Change to your primary wrapper layout asset --}}

@section('content')<div class="row mt-4">
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-success text-white font-weight-bold">
                Create New Seller Profile
            </div>
            <div class="card-body">
                <form action="{{ route('accounts.seller.store') }}" method="POST">
                    @csrf
                    <div class="form-group mb-3">
                        <label class="form-label font-weight-bold">Seller Name *</label>
                        <input type="text" name="name" class="form-control" required placeholder="Acme Wholesalers">
                        <small class="text-muted">An Accounting Ledger will auto-generate for this vendor.</small>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label font-weight-bold">Contact Number *</label>
                        <input type="text" name="contact" class="form-control" required placeholder="018XXXXXXXX">
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label font-weight-bold">Branch Office Reference</label>
                        <input type="text" name="branch" class="form-control" placeholder="Dhaka Main Branch">
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label font-weight-bold">Address</label>
                        <textarea name="address" class="form-control" rows="2" placeholder="Factory / Office street details..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-success w-100 font-weight-bold shadow-sm">
                        Save Seller & Create Ledger
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white font-weight-bold text-dark">
                Sellers & Vendor Accounts Directory
            </div>
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table id="sellerTable" class="table table-hover align-middle mb-0 w-100">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Linked Ledger ID</th>
                                <th>Contact</th>
                                <th>Branch</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sellers as $seller)
                                <tr>
                                    <td><strong>#{{ $seller->id }}</strong></td>
                                    <td>{{ $seller->name }}</td>
                                    <td><span class="badge bg-dark text-light">Ledger #{{ $seller->ledger }}</span></td>
                                    <td>{{ $seller->contact }}</td>
                                    <td>{{ $seller->branch ?? 'N/A' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No seller records configured yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
    if ($('#sellerTable tbody tr').length > 0 && !$('#sellerTable tbody tr td').hasClass('text-center')) {
        $('#sellerTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "pageLength": 10,
            "lengthMenu": [5, 10, 25, 50, 100],
            "language": {
                "search": "_INPUT_",
                "searchPlaceholder": "Search seller name, branch, contact..."
            }
        });
        
        $('.dataTables_filter input').addClass('form-control form-control-sm');
        $('.dataTables_length select').addClass('form-select form-select-sm');
    }
});
</script>
@if(session('success_message'))
    <script>
        Swal.fire({ icon: 'success', title: 'Success!', text: "{{ session('success_message') }}", timer: 3000, showConfirmButton: false });
    </script>
@endif
@if(session('error_message'))
    <script>
        Swal.fire({ icon: 'error', title: 'Error Encountered', text: "{{ session('error_message') }}", confirmButtonColor: '#3085d6' });
    </script>

@endif

@endsection