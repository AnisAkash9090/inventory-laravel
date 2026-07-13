@extends('layouts.app')

@section('title', 'Product Add ')

@section('content')
<div class="">
    <div class="d-flex justify-content-between mb-3">
        <h3>Product Management</h3>
        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addProductModal">
            <i class="fa fa-plus"></i> Add Product
        </button>
    </div>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Product Name</th>
                <th>Group</th>
                <th>Quantity</th>
                <th>Sold</th>
                <th>Rating</th>
                <th>Created By</th>
                  <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            <tr>
                <td>{{ $product->id }}</td>
                <td><img src="{{ asset('images/product/' . $product->img) }}" width="50"></td>
                <td>{{ $product->product_name }}</td>
                <td>{{ $product->group_id }}</td> {{-- Better to use $product->group->product_group later --}}
                <td>{{ $product->quantity }}</td>
                <td>{{ $product->sold }}</td>
                <td>{{ $product->rating }}/5</td>
                <td>{{ $product->createdBy }}</td>
                <td><button type="button" 
        class="btn btn-primary btn-sm" 
        data-toggle="modal" 
        data-target="#addstoreModal" 
        data-id="{{ $product->id }}">
    <i class="fa fa-plus"></i> Variant
</button>
    </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<!-- Add Product Model -->
<div class="modal fade" id="addProductModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('product.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add New Product</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Product Name</label>
                        <input type="text" name="product_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Product Group</label>
                    <select name="group_id" class="form-control">
    @foreach($allGroups as $g)
        <option value="{{ $g->id }}" >
            {{ $g->product_group }}
        </option>
    @endforeach
</select>
                    </div>
                    <div class="form-group">
                        <label>Product Image</label>
                        <input type="file" name="img" class="form-control">
                    </div>
              
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Save Product</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Store update -->
 <div class="modal fade" id="addstoreModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <form action="{{ route('product.store.save') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Product Variant</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="text" readonly name="product_id" class="d-none" id="modal_product_id">

                    <div class="row">
             <div class="col-md-6 mb-3">
        <label class="form-label">Size</label>
        <select name="size" class="form-control" required>
            <option value="">-- Select Size --</option>
            @foreach($availableSizes as $size)
                <option value="{{ $size->id }}">{{ $size->name }}</option>
            @endforeach
        </select>
    
    </div>


                    
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Cost</label>
                            <input type="number" step="0.01" name="costprice" class="form-control" placeholder="0.00" required>
                        </div>
                           <div class="col-md-6 mb-3">
                            <label class="form-label">Selling Price</label>
                            <input type="number" step="0.01" name="sellprice" class="form-control" placeholder="0.00" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Low Stock Alert</label>
                            <input type="number" step="0" name="stock_alert" class="form-control" placeholder="0" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Image (Optional)</label>
                            <input type="file" name="img" class="form-control" accept="image/*">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save to Store</button>
                </div>
            </div>
        </form>
    </div>
</div><script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
            timer: 3000,
            showConfirmButton: false // Added for a cleaner auto-closing look
        });
    </script>
@endif

<script>
$(document).ready(function() {
    $('#addstoreModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); 
        var productId = button.data('id'); 
        console.log("Product ID detected:", productId);
        $(this).find('#modal_product_id').val(productId);
    });
});
</script>
@endsection