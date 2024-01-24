<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::latest();
        if (!empty($request->get('keyword'))) {
            $users = $users->where('name', 'like', '%' . $request->get('keyword') . '%');
            $users = $users->orWhere('email', 'like', '%' . $request->get('keyword') . '%');
            $users = $users->orWhere('phone', 'like', '%' . $request->get('keyword') . '%');
        }
        $users = $users->where('role', 1)->paginate(10);
        return view('admin.user.list', compact('users'));
    }

    public function create()
    {
        return view('admin.user.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'phone' => 'required|max:10',
            'password' => 'required'
        ]);

        if ($validator->passes()) {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->phone = $request->phone;
            $user->status = $request->status;
            $user->save();

            session()->flash('success', 'User added successfully!');

            return response()->json([
                'status' => true,
                'message' => 'User added successfully!'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function edit($id, Request $request)
    {
        $user = User::find($id);
        if (empty($user)) {
            session()->flash('error', 'User Not Found!');
            return redirect()->route('users.index');
        }
        return view('admin.user.edit', compact('user'));
    }

    public function update($id, Request $request)
    {
        $user = User::find($id);
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id . ',id',
            'phone' => 'required|max:10',
            'password' => 'required'
        ]);

        if ($validator->passes()) {
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->phone = $request->phone;
            $user->status = $request->status;
            $user->save();

            session()->flash('success', 'User updated successfully!');

            return response()->json([
                'status' => true,
                'message' => 'User updated successfully!'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if (empty($user)) {
            session()->flash('error', 'User Not Found!');
            return redirect()->route('users.index');
        }

        $user->delete();

        session()->flash('success', 'User deleted successfully!');

        return response()->json([
            'status' => true,
            'message' => 'User deleted successfully!'
        ]);
    }
}
