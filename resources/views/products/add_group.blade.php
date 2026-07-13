@extends('layouts.app')

@section('title', 'Group Management')

@section('content')
<div class="">
    <div class="d-flex justify-content-between mb-3">
        <h3>Product Group Management</h3>
        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#addGroupModal">
            <i class="fa fa-plus"></i> Add New Group
        </button>
    </div>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Group Name</th>
                <th>Manager ID</th>
                <th>Created By (Name)</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($groups as $group)
            <tr>
                <td>{{ $group->id }}</td>
                <td>{{ $group->product_group }}</td>
                <td>{{ $group->manager_id }}</td>
                <td>{{ $group->created_by }}</td> {{-- This is the name column we added --}}
           
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="modal fade" id="addGroupModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('productgroup.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Create Product Group</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Group Name</label>
                        <input type="text" name="product_group" class="form-control" placeholder="Enter group name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Group</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection