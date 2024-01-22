<?php

namespace App\Http\Controllers;

use App\Mail\ContactEmail;
use App\Models\Page;
use App\Models\Product;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

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

    public function page($slug)
    {
        $page = Page::where('slug', $slug)->first();
        if (empty($page)) abort(404);
        return view('front.page', compact('page'));
    }

    public function sendContactForm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'subject' => 'required'
        ]);

        if ($validator->passes()) {
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'subject' => $request->subject,
                'message' => $request->message,
            ];

            $admin = User::where('id', 1)->first();
            Mail::to($admin->email)->send(new ContactEmail($data));

            session()->flash('success', 'Your content have been sent successfully!');

            return response()->json([
                'status' => true,
                'message' => 'Your content have been sent successfully!'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }
}
