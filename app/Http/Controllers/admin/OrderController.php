<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::latest('orders.created_at')->select('orders.*');
        $orders = $orders->leftJoin('users', 'users.id', 'orders.user_id');
        if ($request->get('keyword') != '') {
            $orders = $orders->where('users.name', 'like', '%' . $request->keyword . '%');
            $orders = $orders->orWhere('users.email', 'like', '%' . $request->keyword . '%');
            $orders = $orders->orWhere('orders.id', 'like', '%' . $request->keyword . '%');
        }
        $orders = $orders->paginate(10);
        return view('admin.order.list', compact('orders'));
    }

    public function detail($id)
    {
        $order = Order::select('orders.*', 'countries.name as countryName')->where('orders.id', $id)->leftJoin('countries', 'countries.id', 'orders.country_id')->first();
        $orderItems = OrderItem::where('order_id', $id)->get();
        return view('admin.order.detail', compact('order', 'orderItems'));
    }
}
