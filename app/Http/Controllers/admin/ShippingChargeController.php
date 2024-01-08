<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\ShippingCharge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShippingChargeController extends Controller
{
    public function index(Request $request)
    {
        $shippingCharges = ShippingCharge::select('shipping_charges.*', 'countries.name as countryName')->latest('shipping_charges.id')->leftJoin('countries', 'countries.id', 'shipping_charges.country_id');
        if (!empty($request->get('keyword'))) {
            $shippingCharges = $shippingCharges->where('shipping_charges.amount', 'like', '%' . $request->get('keyword') . '%');
            $shippingCharges = $shippingCharges->orWhere('countries.name', 'like', '%' . $request->get('keyword') . '%');
        }
        $shippingCharges = $shippingCharges->paginate(10);
        return view('admin.shipping.list', compact('shippingCharges'));
    }

    public function create()
    {
        $countries = Country::get();
        return view('admin.shipping.create', compact('countries'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'country' => 'required',
            'amount' => 'required|numeric'
        ]);

        if ($validator->passes()) {
            $shippingCharge = new ShippingCharge();
            $shippingCharge->country_id = $request->country;
            $shippingCharge->amount = $request->amount;
            $shippingCharge->save();

            session()->flash('success', 'Shipping Charge added successfully!');

            return response()->json([
                'status' => true,
                'message' => 'Shipping Charge added successfully!'
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
        $shippingCharge = ShippingCharge::find($id);
        $countries = Country::get();
        if (empty($shippingCharge)) {
            session()->flash('error', 'Shipping Charge not found!');
            return response([
                'status' => false,
                'notFound' => true
            ]);
        }
        return view('admin.shipping.edit', compact('shippingCharge', 'countries'));
    }

    public function update($id, Request $request)
    {
        $shippingCharge = ShippingCharge::find($id);
        if (empty($shippingCharge)) {
            session()->flash('error', 'Shipping Charge not found!');
            return response([
                'status' => false,
                'notFound' => true
            ]);
        }

        $validator = Validator::make($request->all(), [
            'country' => 'required',
            'amount' => 'required|numeric'
        ]);

        if ($validator->passes()) {
            $shippingCharge->country_id = $request->country;
            $shippingCharge->amount = $request->amount;
            $shippingCharge->save();

            session()->flash('success', 'Shipping Charge updated successfully!');

            return response()->json([
                'status' => true,
                'message' => 'Shipping Charge updated successfully!'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function destroy($id, Request $request)
    {
        $shippingCharge = ShippingCharge::find($id);
        if (empty($shippingCharge)) {
            session()->flash('error', 'Shipping Charge not found!');
            return response([
                'status' => false,
                'notFound' => true
            ]);
        }

        $shippingCharge->delete();
        session()->flash('success', 'Shipping Charge deleted successfully!');
        return response()->json([
            'status' => true,
            'message' => 'Shipping Charge deleted successfully!'
        ]);
    }
}
