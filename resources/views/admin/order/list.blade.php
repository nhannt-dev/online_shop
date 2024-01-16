@extends('admin.layouts.app')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid my-2">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Orders</h1>
            </div>
            <div class="col-sm-6 text-right">
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
        <div class="card">
            <form action="" method="get">
                <div class="card-header">
                    <div class="card-title">
                        <a href="{{route('orders.index')}}" class="btn btn-default btn-sm">Reset</a>
                    </div>
                    <div class="card-tools">
                        <div class="input-group input-group" style="width: 250px;">
                            <input type="text" autocomplete="off" name="keyword" value="{{Request::get('keyword')}}" class="form-control float-right" placeholder="Search">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-default">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>Orders #</th>
                            <th>Customer</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Amount</th>
                            <th>Date Purchased</th>
                            <th>Shipped Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($orders->isNotEmpty())
                        @php($count=0)
                        @foreach ($orders as $order)
                        @php($count++)
                        <tr>
                            <td><a href="{{route('orders.detail', $order->id)}}"><strong>{{$count}}</strong></a></td>
                            <td>{{$order->first_name}} {{$order->last_name}}</td>
                            <td>{{$order->email}}</td>
                            <td>{{$order->mobile}}</td>
                            <td>
                                @if ($order->status == 'pending')
                                <span class="badge bg-warning">Pending</span>
                                @elseif ($order->status == 'shipped')
                                <span class="badge bg-info">Shipped</span>
                                @elseif ($order->status == 'delivered')
                                <span class="badge bg-success">Delivered</span>
                                @else
                                <span class="badge bg-danger">Canceled</span>
                                @endif
                            </td>
                            <td>${{number_format($order->grand_total)}}</td>
                            <td>{{Carbon::parse($order->created_at)->format('d M, Y')}}</td>
                            <td>
                                @if (!empty($order->shipped_date))
                                {{Carbon::parse($order->shipped_date)->format('d M, Y')}}
                                @else
                                N/A
                                @endif
                            </td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="card-footer clearfix">
                {{$orders->links()}}
            </div>
        </div>
    </div>
    <!-- /.card -->
</section>
<!-- /.content -->

@endsection