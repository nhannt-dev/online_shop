@extends('front.layouts.app')
@section('content')
<section class="section-5 pt-3 pb-3 mb-3 bg-white">
    <div class="container">
        <div class="light-font">
            <ol class="breadcrumb primary-color mb-0">
                <li class="breadcrumb-item"><a class="white-text" href="{{route('front.home')}}"><i class="fa fa-home"></i></a></li>
                <li class="breadcrumb-item"><a class="white-text" href="{{route('account.profile')}}">Profile</a></li>
                <li class="breadcrumb-item">Orders</li>
            </ol>
        </div>
    </div>
</section>

<section class=" section-11 ">
    <div class="container  mt-5">
        <div class="row">
            <div class="col-md-3">
                @include('front.auth.sidebar')
            </div>
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">
                        <h2 class="h5 mb-0 pt-2 pb-2">My Orders</h2>
                    </div>
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Orders #</th>
                                        <th>Date Purchased</th>
                                        <th>Shipping Date</th>
                                        <th>Status</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($orders->isNotEmpty())
                                    @php($count=0)
                                    @foreach ($orders as $order)
                                    @php($count++)
                                    <tr>
                                        <td>
                                            <a href="{{route('account.orders.detail', $order->id)}}">{{$count}}</a>
                                        </td>
                                        <td>{{Carbon::parse($order->created_at)->format('d M, Y')}}</td>
                                        <td>{{Carbon::parse($order->shipped_date)->format('d M, Y')}}</td>
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
                                        <td>${{number_format($order->grand_total, 2)}}</td>
                                    </tr>
                                    @endforeach
                                    @else
                                    <tr>
                                        <td colspan="4">Order Not Found</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection