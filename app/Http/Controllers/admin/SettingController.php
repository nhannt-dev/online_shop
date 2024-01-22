<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    public function changePassword()
    {
        return view('admin.change-password');
    }

    public function processChangePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required|min:8|same:confirm_password',
            'confirm_password' => 'required'
        ]);

        if ($validator->passes()) {
            $admin = User::select('id', 'password')->where('id', Auth::guard('admin')->user()->id)->first();
            if (!Hash::check($request->old_password, $admin->password)) {
                session()->flash('error', 'Your old password is incorrect, please try again!');
                return response()->json([
                    'status' => false
                ]);
            }
            
            User::where('id', Auth::guard('admin')->user()->id)->update([
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
}
