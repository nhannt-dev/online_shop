<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\DiscountCoupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;

class DiscountCouponController extends Controller
{
    public function index(Request $request)
    {
        $discountCoupons = DiscountCoupon::latest();
        if (!empty($request->get('keyword'))) {
            $discountCoupons = $discountCoupons->where('name', 'like', '%' . $request->get('keyword') . '%');
            $discountCoupons = $discountCoupons->orWhere('code', 'like', '%' . $request->get('keyword') . '%');
        }
        $discountCoupons = $discountCoupons->paginate(10);
        return view('admin.coupon.list', compact('discountCoupons'));
    }

    public function create()
    {
        return view('admin.coupon.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|unique:discount_coupons',
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

            if (!empty($request->starts_at) && !empty($request->expires_at)) {
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

    public function edit($id, Request $request)
    {
        $discountCoupon = DiscountCoupon::find($id);
        if (empty($discountCoupon)) {
            return redirect()->route('coupons.index');
        }
        return view('admin.coupon.edit', compact('discountCoupon'));
    }

    public function update($id, Request $request)
    {
        $discountCoupon = DiscountCoupon::find($id);
        if (empty($discountCoupon)) {
            if (empty($discountCoupon)) {
                return redirect()->route('coupons.index');
            }
        }

        $validator = Validator::make($request->all(), [
            'code' => 'required|unique:discount_coupons,code,' . $discountCoupon->id . ',id',
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

            if (!empty($request->starts_at) && !empty($request->expires_at)) {
                $expires_at = Carbon::createFromFormat('Y-m-d H:i:s', $request->expires_at);
                $starts_at = Carbon::createFromFormat('Y-m-d H:i:s', $request->starts_at);
                if (!$expires_at->gt($starts_at)) {
                    return response()->json([
                        'status' => false,
                        'errors' => ['expires_at' => 'Expiry date can not be greator than start date!']
                    ]);
                }
            }

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

            session()->flash('success', 'Discount Coupon updated successfully!');

            return response()->json([
                'status' => true,
                'message' => 'Discount Coupon updated successfully!'
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
        $discountCoupon = DiscountCoupon::find($id);
        if (empty($discountCoupon)) {
            session()->flash('error', 'Discount Coupon not found!');
            return response([
                'status' => false,
                'notFound' => true
            ]);
        }

        $discountCoupon->delete();
        session()->flash('success', 'Discount Coupon deleted successfully!');
        return response()->json([
            'status' => true,
            'message' => 'Discount Coupon deleted successfully!'
        ]);
    }
}
