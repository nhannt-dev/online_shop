<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FrontController extends Controller
{
    public function index()
    {
        $featuredProducts = Product::where('is_featured', 'Yes')->orderBy('id', 'DESC')->take(8)->where('status', 1)->get();
        $latestProducts = Product::orderBy('id', 'DESC')->where('status', 1)->take(8)->get();
        return view('front.home', compact('featuredProducts', 'latestProducts'));
    }

    public function add2Wishlist(Request $request)
    {
        if (!Auth::check()) {
            session(['url.intended' => url()->previous()]);
            return response()->json([
                'status' => false
            ]);
        }

        Wishlist::updateOrCreate(
            [
                'user_id' => Auth::user()->id,
                'product_id' => $request->id
            ],
            [
                'user_id' => Auth::user()->id,
                'product_id' => $request->id
            ]
        );

        $product = Product::where('id', $request->id)->first();
        if ($product == null) {
            return response()->json([
                'status' => false,
                'message' => '<div class="alert alert-danger">Product Not Found</div>'
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => "<div class='alert alert-success'><b>{$product?->title}</b> added in your wishlist</div>"
        ]);
    }
}
