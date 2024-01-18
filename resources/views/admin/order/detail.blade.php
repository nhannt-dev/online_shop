@extends('admin.layouts.app')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid my-2">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Order: #{{$order?->id}}</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{route('orders.index')}}" class="btn btn-primary">Back</a>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
</section>
<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="container-fluid">
        @include('admin.message')
        <div class="row">
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header pt-3">
                        <div class="row invoice-info">
                            <div class="col-sm-4 invoice-col">
                                <h1 class="h5 mb-3">Shipping Address</h1>
                                <address>
                                    <strong>{{$order->first_name}} {{$order->last_name}}</strong><br>
                                    {{$order->address}}<br>
                                    {{$order->city}}, {{$order->zip}}, {{$order->countryName}}<br>
                                    Phone: {{$order->mobile}}<br>
                                    Email: {{$order->email}}
                                </address>
                                <strong>Shipping Date:</strong><br>
                                @if (!empty($order->shipped_date))
                                {{Carbon::parse($order->shipped_date)->format('d M, Y')}}
                                @else
                                N/A
                                @endif
                            </div>

                            <div class="col-sm-4 invoice-col">
                                <b>Invoice #{{$order->id}}</b><br>
                                <br>
                                <b>Order ID:</b> {{$order->id}}<br>
                                <b>Total:</b> ${{number_format($order->grand_total, 2)}}<br>
                                <b>Status:</b>
                                @if ($order->status == 'pending')
                                <span class="badge bg-warning">Pending</span>
                                @elseif ($order->status == 'shipped')
                                <span class="badge bg-info">Shipped</span>
                                @else
                                <span class="badge bg-success">Delivered</span>
                                @endif
                                <br>
                            </div>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-3">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th width="100">Price</th>
                                    <th width="100">Qty</th>
                                    <th width="100">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($orderItems->isNotEmpty())
                                @foreach ($orderItems as $orderItem)
                                <tr>
                                    <td>{{$orderItem->name}}</td>
                                    <td>${{number_format($orderItem->price, 2)}}</td>
                                    <td>{{$orderItem->qty}}</td>
                                    <td>${{number_format($orderItem->total, 2)}}</td>
                                </tr>
                                @endforeach
                                @endif

                                <tr>
                                    <th colspan="3" class="text-right">Subtotal:</th>
                                    <td>${{number_format($order->subtotal, 2)}}</td>
                                </tr>

                                <tr>
                                    <th colspan="3" class="text-right">Discount: {{!empty($order->coupon_code) ? '('.$order->coupon_code.')' : ''}}</th>
                                    <td>${{number_format($order->discount, 2)}}</td>
                                </tr>

                                <tr>
                                    <th colspan="3" class="text-right">Shipping:</th>
                                    <td>${{$order->shipping}}</td>
                                </tr>
                                <tr>
                                    <th colspan="3" class="text-right">Grand Total:</th>
                                    <td>${{$order->grand_total}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <form method="post" name="changeOrderStatus" id="changeOrderStatus">
                        <div class="card-body">
                            <h2 class="h4 mb-3">Order Status</h2>
                            <div class="mb-3">
                                <select name="status" id="status" class="form-control">
                                    <option {{ $order->status == 'pending' ? 'selected' : '' }} value="pending">Pending</option>
                                    <option {{ $order->status == 'shipped' ? 'selected' : '' }} value="shipped">Shipped</option>
                                    <option {{ $order->status == 'delivered' ? 'selected' : '' }} value="delivered">Delivered</option>
                                    <option {{ $order->status == 'canceled' ? 'selected' : '' }} value="canceled">Canceled</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="shipped_date">Shipped Date</label>
                                <input placeholder="Shipped Date" type="text" value="{{ $order->shipped_date }}" name="shipped_date" id="shipped_date" class="form-control">
                            </div>
                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card">
                    <div class="card-body">
                        <form method="post" name="sendInvoiceEmail" id="sendInvoiceEmail">
                            <h2 class="h4 mb-3">Send Inovice Email</h2>
                            <div class="mb-3">
                                <select name="userType" id="userType" class="form-control">
                                    <option value="customer">Customer</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <button id="send" class="btn btn-primary">Send</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.card -->
</section>
<!-- /.content -->

@endsection
@section('customJS')
<script>
    $(document).ready(function() {
        $('#shipped_date').datetimepicker({
            format: 'Y-m-d H:i:s'
        })
    })

    $('#changeOrderStatus').submit(function(event) {
        event.preventDefault()
        $('button[type="submit"]').prop('disabled', true)
        $.ajax({
            url: '{{route("orders.changeOrderStatus", $order->id)}}',
            type: 'post',
            data: $(this).serializeArray(),
            dataType: 'json',
            success: function(response) {
                $('button[type="submit"]').prop('disabled', false)
                window.location.href = '{{ route("orders.index") }}'
            }
        })
    })

    $('#sendInvoiceEmail').submit(function(event) {
        event.preventDefault()
        $('#send').prop('disabled', true)
        $.ajax({
            url: '{{route("orders.sendInvoiceEmail", $order->id)}}',
            type: 'post',
            data: $(this).serializeArray(),
            dataType: 'json',
            success: function(response) {
                $('#send').prop('disabled', false)
                window.location.reload()
            }
        })
    })
</script>
@endsection