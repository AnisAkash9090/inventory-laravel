@extends('layouts.app')

@section('title', 'Product Add ')

@section('content')
<div class="py-12">
<div style="width:122%;height:40px;background:white;
margin-left:-44px;
margin-top:-10px;
padding-top:10px;
color:black;
display:flex;
padding-left:30px;
border-top-right-radius:5px;"><h4 class="" style="letter-spacing:0.8px;font-weight:400;">
<i class="fa fa-users blue2_color">&nbsp;</i>User Registration</h4></div>

<div style="width: 100%;margin-top:-30px;padding-bottom:40px;
   position: relative;
">


    <button type="button"style="float:right;
    position: relative;z-index:1;" class="btn btn-success btn-sm" data-toggle="modal" data-target="#AddUser">
+ Add User
</button>
</div>


<div class="card border-0 shadow-sm mt-3">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="userListTable">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Photo</th>
                        <th>Name</th>
                        <th>Contact</th>
                        <th>Email</th>
                        <th>Type</th>
                        <th>Manager</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $index => $u)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <img src="{{ $u['img'] ? asset('images/user/' . $u['img']) : asset('images/default-avatar.png') }}"
                                     alt="{{ $u['name'] }}"
                                     class="rounded-circle"
                                     style="width:40px;height:40px;object-fit:cover;">
                            </td>
                            <td>{{ $u['name'] }}</td>
                            <td>{{ $u['contact'] ?? '—' }}</td>
                            <td>{{ $u['email'] }}</td>
                            <td>
                                @if($u['type_manage'] === 'Manager')
                                    <span class="badge rounded-pill" style="background:#e7f1ff;color:#0056b3;">
                                        <i class="mdi mdi-shield-crown-outline me-1"></i> Manager
                                    </span>
                                @else
                                    <span class="badge rounded-pill" style="background:#eafaf1;color:#1cc88a;">
                                        <i class="mdi mdi-account-tie-outline me-1"></i> Sub Manager
                                    </span>
                                @endif
                            </td>
                            <td>{{ $u['manager_name'] }}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill" title="Edit"
                                        data-bs-toggle="modal" data-bs-target="#edituser"
                                        data-id="{{ $u['idU'] }}">
                                    <i class="mdi mdi-pencil-outline"></i>Edit
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill delete-user" title="Delete"
                                        data-id="{{ $u['idU'] }}" data-name="{{ $u['name'] }}">
                                    <i class="mdi mdi-trash-can-outline"></i> Delete
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="mdi mdi-account-off-outline fs-3 d-block mb-2"></i>
                                No users found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
 <br>
 <div class="modal fade" id="AddUser" tabindex="1"    aria-hidden="true"
     data-bs-backdrop="static" 
     data-bs-keyboard="false">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title " id="exampleModalLongTitle ">User Create </h5>
        <button type="button" class="close text-danger" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
         <div class="user-form-card">

    
<form action="{{ route('users.storedata') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <style>
        /* (Keep your CSS here) */
        .user-form-card { background: #ffffff; border-radius: 12px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .form-header { background: linear-gradient(90deg, #007bff, #0056b3); color: white; padding: 15px 20px; border-radius: 10px 10px 0 0; margin: -30px -30px 25px -30px; box-shadow: 0 4px 10px rgba(0, 86, 179, 0.3); }
        .form-control { border: 1px solid #ced4da; transition: all 0.3s ease; padding: 2px 15px; height: 45px; }
        .input-group-section { margin-bottom: 20px; padding: 15px; border: 1px solid #f0f2f5; border-radius: 8px; background-color: #f8f9fa; }
        .btn-primary { background-color: #007bff; border-color: #007bff; padding: 10px 30px; font-weight: 600; }
    </style>

    <div class="user-form-card">
        <div class="form-header">
            <h5 class="mb-0">Create New User</h5>
        </div>

        <div class="input-group-section">
            <h6><i class="mdi mdi-account-details-outline me-2"></i> Personal Details</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="username" class="form-label">User Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="username" id="username" class="form-control rounded-3 shadow-sm" value="{{ old('username') }}" required>
                </div>
                <div class="col-md-6">
                    <label for="contact" class="form-label"> Contact</label>
                    <input type="text" name="contact" id="contact" class="form-control rounded-3 shadow-sm" value="{{ old('contact') }}">
                </div>
                <div class="col-md-12">
                    <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                    <input type="text" name="address" id="address" class="form-control rounded-3 shadow-sm" value="{{ old('address') }}" required>
                </div>
            </div>
        </div>
<div class="input-group-section">
    <h6><i class="mdi mdi-account-supervisor-outline me-2"></i> Account Type</h6>

    <div class="row g-3">
        <div class="col-md-12">
            <div class="btn-group w-100" role="group" aria-label="Account type toggle">
                <input type="radio" class="btn-check" name="type_manage" id="typeManager" value="Manager" autocomplete="off" checked>
                <label class="btn btn-outline-primary" for="typeManager">
                    <i class="mdi mdi-shield-crown-outline me-1"></i> Manager
                </label>

                <input type="radio" class="btn-check" name="type_manage" id="typeSubManager" value="Sub_Manager" autocomplete="off">
                <label class="btn btn-outline-primary" for="typeSubManager">
                    <i class="mdi mdi-account-tie-outline me-1"></i> Sub Manager
                </label>
            </div>
        </div>

        <div class="col-md-12" id="managerSelectWrapper" style="display:none;">
            <label for="manager_id" class="form-label">Assign Under Manager <span class="text-danger">*</span></label>
            <select class="form-control rounded-3 shadow-sm" name="manager_id" id="manager_id">
                <option value="">-- Loading managers... --</option>
            </select>
            <small class="text-muted">Sub manager will be registered under the selected manager.</small>
        </div>
    </div>
</div>


        <div class="input-group-section">
            <h6><i class="mdi mdi-lock-outline me-2"></i> Security & Media</h6>
            <div class="row g-3">
                <div class="row col-12" id="credentialsContainer">
                    <div class="col-md-6">
            <label class="form-label">User Email <span class="text-danger">*</span></label>
            <input type="email" name="user_id" class="form-control rounded-3 shadow-sm" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Password <span class="text-danger">*</span></label>
            <input type="password" name="pass" class="form-control rounded-3 shadow-sm" required>
        </div>
                </div>
                <div class="col-md-12">
                    <label for="image_upload" class="form-label">Profile Image</label>
                    <input type="file" name="img" id="image_upload" class="form-control rounded-3 shadow-sm">
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <button type="submit" class="btn btn-primary btn-lg rounded-pill shadow-sm">
                <i class="mdi mdi-check-circle-outline me-2"></i> Create User
            </button>
        </div>
    </div>
</form>





</div>
                  </div>
                  </div>
                  </div>
                  </div>





<!-- Acess Setup -->
<div class="modal fade" id="accesssetup" tabindex="1"    aria-hidden="true"
     data-bs-backdrop="static" 
     data-bs-keyboard="false">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title " id="exampleModalLongTitle ">Access Setup  </h5>
        <button type="button" class="close text-danger" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
         <div class="user-form-card">
            <div id="accessSetupdata">

            </div>

         </div>
         </div>
         </div>
         </div>
         </div>
         <!-- Edit Users -->
         <div class="modal fade" id="edituser" tabindex="1"    aria-hidden="true"
     data-bs-backdrop="static" 
     data-bs-keyboard="false">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title " id="exampleModalLongTitle ">Edit User </h5>
        <button type="button" class="close text-danger" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
         <div class="user-form-card">

         </div>
         </div>
         </div>
         </div>
         </div>
                  <!-- Roll Users -->
         <div class="modal fade" id="rollsetup" tabindex="1"    aria-hidden="true"
     data-bs-backdrop="static" 
     data-bs-keyboard="false">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title " id="exampleModalLongTitle ">Roll Management </h5>
        <button type="button" class="close text-danger" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
         <div class="user-form-card">

         </div>
         </div>
         </div>
         </div>
         </div>


    </div>
    <!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">

<!-- jQuery (skip if already loaded elsewhere in your layout) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script>
$(document).ready(function () {
    $('#userListTable').DataTable({
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        order: [[0, 'asc']],
        columnDefs: [
            { orderable: false, targets: [1, 7] } // disable sorting on Photo & Actions columns
        ],
        language: {
            search: "",
            searchPlaceholder: "Search users...",
            emptyTable: "No users found.",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ users",
            paginate: {
                previous: "‹",
                next: "›"
            }
        }
    });
});
</script>
    <script>
    const managerWrapper = document.getElementById('managerSelectWrapper');
    const managerSelect  = document.getElementById('manager_id');
    let managersLoaded = false;

    document.querySelectorAll('input[name="type_manage"]').forEach(radio => {
        radio.addEventListener('change', function () {
            if (this.value === 'Sub_Manager') {
                managerWrapper.style.display = 'block';
                managerSelect.required = true;
                if (!managersLoaded) loadManagers();
            } else {
                managerWrapper.style.display = 'none';
                managerSelect.required = false;
                managerSelect.value = '';
            }
        });
    });

    function loadManagers() {
        fetch("{{ route('users.managers') }}")
            .then(res => res.json())
            .then(data => {
                managerSelect.innerHTML = '<option value="">-- Select Manager --</option>';
                if (data.length === 0) {
                    managerSelect.innerHTML = '<option value="">No managers found</option>';
                    return;
                }
                data.forEach(manager => {
                    managerSelect.innerHTML += `<option value="${manager.idU}">${manager.name} -${manager.idU}- (${manager.email})</option>`;
                });
                managersLoaded = true;
            })
            .catch(() => {
                managerSelect.innerHTML = '<option value="">Failed to load managers</option>';
            });
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('error_message'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: "{{ session('error_message') }}",
            confirmButtonColor: '#3085d6',
        });
    </script>
@endif

@if(session('success_message'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: "{{ session('success_message') }}",
            timer: 300000,
            showConfirmButton: true
        });
    </script>
@endif
@endsection