<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\DiscountCoupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;

class DiscountCouponController extends Controller
{
    public function index()
    {
        return view('admin.coupon.list');
    }

    public function create()
    {
        return view('admin.coupon.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required',
            'discount_amount' => 'required|numeric'
        ]);

        if ($validator->passes()) {
            if (!empty($request->starts_at)) {
                $now = Carbon::now();
                $starts_at = Carbon::createFromFormat('Y-m-d H:i:s', $request->starts_at);
                if ($starts_at->lte($now)) {
                    return response()->json([
                        'status' => false,
                        'errors' => ['starts_at' => 'Start date can not be less than current time!']
                    ]);
                }
            }

            if (!empty($request->starts_at)&&!empty($request->expires_at)) {
                $expires_at = Carbon::createFromFormat('Y-m-d H:i:s', $request->expires_at);
                $starts_at = Carbon::createFromFormat('Y-m-d H:i:s', $request->starts_at);
                if (!$expires_at->gt($starts_at)) {
                    return response()->json([
                        'status' => false,
                        'errors' => ['expires_at' => 'Expiry date can not be greator than start date!']
                    ]);
                }
            }

            $discountCoupon = new DiscountCoupon();
            $discountCoupon->code = $request->code;
            $discountCoupon->name = $request->name;
            $discountCoupon->description = $request->description;
            $discountCoupon->max_uses = $request->max_uses;
            $discountCoupon->max_uses_user = $request->max_uses_user;
            $discountCoupon->type = $request->type;
            $discountCoupon->discount_amount = $request->discount_amount;
            $discountCoupon->min_amount = $request->min_amount;
            $discountCoupon->status = $request->status;
            $discountCoupon->starts_at = $request->starts_at;
            $discountCoupon->expires_at = $request->expires_at;
            $discountCoupon->save();

            session()->flash('success', 'Discount Coupon added successfully!');

            return response()->json([
                'status' => true,
                'message' => 'Discount Coupon added successfully!'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function edit()
    {
        return view('admin.coupon.edit');
    }

    public function update()
    {
    }

    public function destroy()
    {
    }
}
