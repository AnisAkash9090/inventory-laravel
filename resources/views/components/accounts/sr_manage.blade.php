@extends('layouts.app') {{-- Change to your primary wrapper layout asset --}}

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white font-weight-bold">
                    Create New SR Profile
                </div>
                <div class="card-body">
                    <form action="{{ route('accounts.sr.store') }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label font-weight-bold">SR Full Name *</label>
                            <input type="text" name="name" class="form-control" required placeholder="John Doe">
                            <small class="text-muted">A matching Accounting Ledger account will auto-generate.</small>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label font-weight-bold">Contact Number *</label>
                            <input type="text" name="contact" class="form-control" required placeholder="017XXXXXXXX">
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label font-weight-bold">SR Address</label>
                            <textarea name="address" class="form-control" rows="2" placeholder="Residential details..."></textarea>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label font-weight-bold">Company / Distribution Channel</label>
                            <input type="text" name="company" class="form-control" placeholder="Acme Logistics">
                        </div>
                          <div class="form-group mb-3">
                            <label class="form-label font-weight-bold">Branch</label>
                            <input type="text" name="branch" class="form-control" placeholder="Branch">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label font-weight-bold">Company Address</label>
                            <input type="text" name="compaddress" class="form-control" placeholder="Company Address">
                        </div>
                        <button type="submit" class="btn btn-primary w-100 font-weight-bold shadow-sm">
                            Save SR & Create Ledger
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
          <div class="card shadow-sm border-0">
    <div class="card-header bg-white font-weight-bold text-dark d-flex justify-content-between align-items-center">
        <span>Active Sales Representatives Directory</span>
    </div>
    <div class="card-body p-3"> <div class="table-responsive">
            <table id="srTable" class="table table-hover align-middle mb-0 w-100">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Linked Ledger ID</th>
                        <th>Contact</th>
                        <th>Company</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($srs as $sr)
                        <tr>
                            <td><strong>#{{ $sr->id }}</strong></td>
                            <td>{{ $sr->name }}</td>
                            <td><span class="badge bg-secondary text-light">Ledger #{{ $sr->ledger }}</span></td>
                            <td>{{ $sr->contact }}</td>
                            <td>{{ $sr->company ?? 'N/A' }}</td>
                            <td>
                                <span class="badge {{ strtolower($sr->status) === 'active' ? 'bg-success' : 'bg-danger' }}">
                                    {{ ucfirst($sr->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No Sales Representatives profiles configured yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Check if there are rows before applying DataTables to prevent overriding empty state bugs
    if ($('#srTable tbody tr').length > 0 && !$('#srTable tbody tr td').hasClass('text-center')) {
        $('#srTable').DataTable({
            "paging": true,         // Enable pagination elements
            "searching": true,      // Enable real-time dynamic search bar filtering
            "ordering": true,       // Enable multi-column order sorting capabilities
            "info": true,           // Shows "Showing 1 to 10 of X entries" description text
            "pageLength": 10,       // Default records per viewing page pane split
            "lengthMenu": [5, 10, 25, 50, 100], // Options for row count display selector
            "language": {
                "search": "_INPUT_", // Displaces the word "Search:" text to save grid layout whitespace
                "searchPlaceholder": "Search SR name, contact, company..."
            }
        });

        // Optional styling optimization to make the search input look native Bootstrap 
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