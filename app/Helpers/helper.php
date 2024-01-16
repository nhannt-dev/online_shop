<?php

use App\Mail\OrderEmail;
use App\Models\Category;
use App\Models\Country;
use App\Models\Order;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Mail;

function getCategories()
{
    return Category::orderBy('name', 'ASC')->with('sub_category')->orderBy('id', 'DESC')->where('status', 1)->where('showHome', 'Yes')->get();
}

function getProductImage($prodId)
{
    return ProductImage::where('product_id', $prodId)->first();
}

function orderEmail($id)
{
    $order = Order::where('id', $id)->with('items')->first();
    $data = [
        'subject' => 'Thankyou',
        'order' => $order
    ];
    Mail::to($order->email)->send(new OrderEmail($data));
}

function getCountry($id)
{
    return Country::where('id', $id)->first();
}
