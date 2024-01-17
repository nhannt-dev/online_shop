<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register()
    {
        return view('front.auth.register');
    }

    public function registerProcess(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'phone' => 'required|max:10',
            'password' => 'required|min:5|confirmed',
        ]);

        if ($validator->passes()) {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->password = Hash::make($request->password);
            $user->save();

            session()->flash('success', 'Register successfully! Please login to your account.');

            return response()->json([
                'status' => true,
                'message' => 'Register successfully! Please login to your account.'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }

    public function login()
    {
        return view('front.auth.login');
    }

    public function authenticate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->passes()) {
            if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->get('remember'))) {
                return redirect()->route('account.profile');
            } else {
                session()->flash('error', 'Either Email/Password is incorrect');
                return redirect()->route('account.login')->withInput($request->only('email'))->with('error', 'Either Email/Password is incorrect');
            }
        } else {
            return redirect()->route('account.login')->withErrors($validator)->withInput($request->only('email'));
        }
    }

    public function profile()
    {
        return view('front.auth.profile');
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('account.login')->with('success', 'You logged out successfully!');
    }

    public function orders()
    {
        $user = Auth::user()->id;
        $orders = Order::where('user_id', $user)->orderBy('created_at', 'DESC')->get();
        return view('front.auth.order', compact('orders'));
    }

    public function orderDetail($id)
    {
        $user = Auth::user()->id;
        $order = Order::where('user_id', $user)->where('id', $id)->first();
        $orderItems = OrderItem::where('order_id', $id)->get();
        return view('front.auth.order-detail', compact('order', 'orderItems'));
    }

    public function wishlist()
    {
        $wishlists = Wishlist::where('user_id', Auth::user()->id)->with('product')->get();
        return view('front.auth.wishlist', compact('wishlists'));
    }

    public function removeProd(Request $request)
    {
        $wishlist = Wishlist::where('user_id', Auth::user()->id)->where('product_id', $request->id)->first();
        if ($wishlist == null) {
            session()->flash('error', 'Product already removed!');
            return response()->json([
                'status' => false,
                'message' => 'Product already removed!'
            ]);
        }else {
            Wishlist::where('user_id', Auth::user()->id)->where('product_id', $request->id)->delete();
            session()->flash('success', 'Product removed successfully!');
            return response()->json([
                'status' => true,
                'message' => 'Product removed successfully!'
            ]);
        }
    }
}
