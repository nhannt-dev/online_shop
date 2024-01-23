<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordEmail;
use App\Models\Country;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

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
        $user = User::where('id', Auth::user()->id)->first();
        $countries = Country::orderBy('name', 'ASC')->get();
        $customerAddress = CustomerAddress::where('user_id', Auth::user()->id)->first();
        return view('front.auth.profile', compact('user', 'countries', 'customerAddress'));
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
        } else {
            Wishlist::where('user_id', Auth::user()->id)->where('product_id', $request->id)->delete();
            session()->flash('success', 'Product removed successfully!');
            return response()->json([
                'status' => true,
                'message' => 'Product removed successfully!'
            ]);
        }
    }

    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . Auth::user()->id . ',id',
            'phone' => 'required|max:10'
        ]);

        if ($validator->passes()) {
            $user = User::find(Auth::user()->id);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->save();

            session()->flash('success', 'Profile updated successfully!');

            return response()->json([
                'status' => true,
                'message' => 'Profile updated successfully!'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function updateAddress(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'country_id' => 'required',
            'address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zip' => 'required',
            'mobile' => 'required|max:10',
        ]);

        if ($validator->passes()) {
            CustomerAddress::updateOrCreate(
                ['user_id' => Auth::user()->id],
                [
                    'user_id' => Auth::user()->id,
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'mobile' => $request->mobile,
                    'country_id' => $request->country_id,
                    'address' => $request->address,
                    'apartment' => $request->apartment,
                    'city' => $request->city,
                    'state' => $request->state,
                    'zip' => $request->zip
                ]
            );

            session()->flash('success', 'Address updated successfully!');

            return response()->json([
                'status' => true,
                'message' => 'Address updated successfully!'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function changePassword()
    {
        return view('front.auth.change-password');
    }

    public function processChangePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required|min:8|same:confirm_password',
            'confirm_password' => 'required'
        ]);

        if ($validator->passes()) {
            $user = User::select('id', 'password')->where('id', Auth::user()->id)->first();
            if (!Hash::check($request->old_password, $user->password)) {
                session()->flash('error', 'Your old password is incorrect, please try again!');
                return response()->json([
                    'status' => false
                ]);
            }

            User::where('id', $user->id)->update([
                'password' => Hash::make($request->new_password)
            ]);

            session()->flash('success', 'Your password changed successfully!');

            return response()->json([
                'status' => true,
                'message' => 'Your password changed successfully!'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function forgotPassword()
    {
        return view('front.auth.forgot-password');
    }

    public function processForgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email'
        ]);

        if ($validator->passes()) {
            $token = Str::random(60);
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            DB::table('password_reset_tokens')->insert([
                'email' => $request->email,
                'token' => $token,
                'created_at' => now()
            ]);
            $user = User::where('email', $request->email)->first();
            $data = [
                'token' => $token,
                'user' => $user,
                'subject' => 'You have requested to reset password'
            ];
            Mail::to($request->email)->send(new ResetPasswordEmail($data));
            return redirect()->route('front.forgotPassword')->with('success', 'Please check your inbox to reset your password');
        } else {
            return redirect()->route('front.forgotPassword')->withInput()->withErrors($validator);
        }
    }

    public function resetPassword($token)
    {
        $tk = Db::table('password_reset_tokens')->where('token', $token)->first();
        if (empty($tk)) return redirect()->route('front.forgotPassword')->with('error', 'Invalid token');
        return view('front.auth.reset-password', compact('token'));
    }

    public function processResetPassword(Request $request)
    {
        $token = Db::table('password_reset_tokens')->where('token', $request->token)->first();
        $user = User::where('email', $token->email)->first();
        $validator = Validator::make($request->all(), [
            'new_password' => 'required|same:confirm_password',
            'confirm_password' => 'required'
        ]);
        if ($validator->passes()) {
            User::where('id', $user->id)->update([
                'password' => Hash::make($request->new_password)
            ]);
            DB::table('password_reset_tokens')->where('email', $user->email)->delete();
            return redirect()->route('account.login')->with('success', 'You have been updated your password successfully!');
        } else {
            return redirect()->route('front.resetPassword', $request->token)->withInput()->withErrors($validator);
        }
    }
}
