@extends('layouts.app')
@section('title', 'Product Details - ' . $groupt->product_group)
@section('content')


<div>

<div class="">
    <div class="">
        
        @if($groupt)
            <div class="full graph_head">
                <div class="heading1 margin_0">
                    <h2>Group: {{ $groupt->product_group }}</h2>
                </div>
            </div>

            <div class="table_section padding_infor_info">
                <div class="table-responsive-sm">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Product Name</th>
                                <th>Created By</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                                <tr>
                                    <td>{{ $product->id }}</td>
                                    <td>{{ $product->product_name }}</td>
                                    <td>{{ $product->createdBy }}</td>
                              
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-warning">
                                        No products found available for this group.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="alert alert-danger m-4">
                <h4><i class="fa fa-exclamation-triangle"></i> Group Not Available</h4>
                <p>The ID provided in the URL does not match any valid product group.</p>
               
            </div>
        @endif

    </div>
</div>

</div>
 @endsection