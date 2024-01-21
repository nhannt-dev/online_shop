<?php

use App\Mail\OrderEmail;
use App\Models\Category;
use App\Models\Country;
use App\Models\Order;
use App\Models\Page;
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

function orderEmail($id, $userType = 'customer')
{
    $order = Order::where('id', $id)->with('items')->first();
    $subject = '';
    $email = '';
    if ($userType == 'customer') {
        $subject = 'Thanks for your order';
        $email = $order->email;
    } else {
        $subject = 'You have received an order';
        $email = env('ADMIN_EMAIL');
    }

    $data = [
        'subject' => $subject,
        'order' => $order,
        'userType' => $userType
    ];
    Mail::to($email)->send(new OrderEmail($data));
}

function getCountry($id)
{
    return Country::where('id', $id)->first();
}

function getStaticPages()
{
    return Page::orderBy('name', 'ASC')->get();
}
