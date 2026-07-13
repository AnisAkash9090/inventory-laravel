@extends('layouts.app')
@section('title', 'Product Attribute')
@section('content')

<div class="">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Manage Sizes</h5>
                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addSizeModal">
                        <i class="fa fa-plus"></i> Add Size
                    </button>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($availableSizes as $size)
                            <tr>
                                <td>{{ $size->name }}</td>
                                <td>{{ $size->manager_id ? 'Private' : 'Public' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

       
    </div>
</div>

<div class="modal fade" id="addSizeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form action="{{ route('attribute.size.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Size</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Size Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. XL, 10kg, 42" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Size</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="addVariantModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form action="{{ route('attribute.variant.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Variant</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Variant Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Red, Cotton, Glossy" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Save Variant</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection