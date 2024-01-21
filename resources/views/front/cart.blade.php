@extends('front.layouts.app')
@section('content')
<section class="section-5 pt-3 pb-3 mb-3 bg-white">
    <div class="container">
        <div class="light-font">
            <ol class="breadcrumb primary-color mb-0">
                <li class="breadcrumb-item"><a class="white-text" href="{{route('front.home')}}"><i class="fa fa-home"></i></a></li>
                <li class="breadcrumb-item"><a class="white-text" href="{{route('front.shop')}}">Shop</a></li>
                <li class="breadcrumb-item">Cart</li>
            </ol>
        </div>
    </div>
</section>

<section class=" section-9 pt-4">
    <div class="container">
        <div class="row">
            @if (Session::has('error'))
            <div class="col-md-12">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{Session::get('error')}}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
            @endif
            <div class="col-md-8">
                <div class="table-responsive">
                    <table class="table" id="cart">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th>Remove</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($cartContent->isNotEmpty())
                            @foreach ($cartContent as $item)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if (!empty($item->options->productImage->image))
                                        <img src="{{asset('uploads/product/small/' . $item->options->productImage->image)}}" width="" height="">
                                        @else
                                        <img src="{{asset('admin-assets/img/default-150x150.png')}}" width="" height="">
                                        @endif
                                        <h2>{{$item->name}}</h2>
                                    </div>
                                </td>
                                <td>${{$item->price}}</td>
                                <td>
                                    <div class="input-group quantity mx-auto" style="width: 100px">
                                        <div class="input-group-btn">
                                            <button class="btn btn-sm btn-dark btn-minus p-2 pt-1 pb-1 sub" data-id="{{$item->rowId}}">
                                                <i class="fa fa-minus"></i>
                                            </button>
                                        </div>
                                        <input type="text" readonly class="form-control form-control-sm  border-0 text-center" value="{{$item->qty}}">
                                        <div class="input-group-btn">
                                            <button class="btn btn-sm btn-dark btn-plus p-2 pt-1 pb-1 add" data-id="{{$item->rowId}}">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    ${{$item->price * $item->qty}}
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-danger" onclick="deleteCart('{{$item->rowId}}')"><i class="fa fa-times"></i></button>
                                </td>
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td colspan="5">There are no products in the cart</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card cart-summery">
                    <div class="card-body">
                        <div class="sub-title">
                            <h2 class="bg-white">Cart Summary</h3>
                        </div>
                        <div class="d-flex justify-content-between pb-2">
                            <div>Subtotal</div>
                            <div>${{Cart::subtotal()}}</div>
                        </div>
                        <div class="d-flex justify-content-between pb-2">
                            <div>Shipping</div>
                            <div>$0</div>
                        </div>
                        <div class="d-flex justify-content-between summery-end">
                            <div>Total</div>
                            <div>${{Cart::subtotal()}}</div>
                        </div>
                        @if (Cart::subtotal() > 0)
                        <div class="pt-2 mt-2">
                            <a href="{{route('front.checkout')}}" class="btn-dark btn btn-block w-100">Proceed to Checkout</a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@section('customJS')
<script>
    $('.add').click(function() {
        var qtyElement = $(this).parent().prev() // Qty Input
        var qtyValue = parseInt(qtyElement.val())
        if (+qtyValue < 10) {
            qtyElement.val(qtyValue + 1)
            updateCart($(this).data('id'), qtyElement.val())
        }
    })

    $('.sub').click(function() {
        var qtyElement = $(this).parent().next()
        var qtyValue = parseInt(qtyElement.val())
        if (+qtyValue > 1) {
            qtyElement.val(qtyValue - 1)
            updateCart($(this).data('id'), qtyElement.val())
        }
    })

    function updateCart(id, qty) {
        $.ajax({
            url: '{{route("front.updateCart")}}',
            type: 'post',
            data: {
                rowId: id,
                qty: qty
            },
            dataType: 'json',
            success: function(response) {
                window.location.reload()
            }
        })
    }

    function deleteCart(id) {
        if (confirm('Are you sure you want to delete?')) {
            $.ajax({
                url: '{{route("front.deleteCart")}}',
                type: 'post',
                data: {
                    rowId: id,
                },
                dataType: 'json',
                success: function(response) {
                    window.location.reload()
                }
            })
        }
    }
</script>
@endsection