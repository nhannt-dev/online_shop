<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\CustomerAddress;
use App\Models\DiscountCoupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ShippingCharge;
use Illuminate\Http\Request;
use Gloudemans\Shoppingcart\Facades\Cart as Cart;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    public function add2Cart(Request $request)
    {
        $product = Product::with('product_images')->find($request->id);
        if ($product == null) {
            return response()->json([
                'status' => false,
                'message' => 'Product Not Found'
            ]);
        }

        if (Cart::count() > 0) {
            $cartContent = Cart::content();
            $productAlreadyExist = false;
            foreach ($cartContent as $item) {
                if ($item->id == $product->id) {
                    $productAlreadyExist = true;
                }
            }

            if (!$productAlreadyExist) {
                Cart::add($product->id, $product->title, 1, $product->price, ['productImages' => !empty($product->product_images) ? $product->product_images->first() : '']);
                $status = true;
                $message = $product->title . ' added in cart';
            } else {
                $status = false;
                $message = $product->title . ' already added in cart';
            }
        } else {
            Cart::add($product->id, $product->title, 1, $product->price, ['productImages' => !empty($product->product_images) ? $product->product_images->first() : '']);
            $status = true;
            $message = $product->title . ' added in cart';
        }

        return response()->json([
            'status' => $status,
            'message' => $message
        ]);
    }

    public function cart()
    {
        $cartContent = Cart::content();
        return view('front.cart', compact('cartContent'));
    }

    public function updateCart(Request $request)
    {
        $rowId = $request->rowId;
        $qty = $request->qty;
        $itemIn4 = Cart::get($rowId);
        $product = Product::find($itemIn4->id);
        if ($product->track_qty == 'Yes') {
            if ($qty <= $product->qty) {
                Cart::update($rowId, $qty);
                $message = 'Cart updated successfully!';
                $status = true;
            } else {
                $message = $product->title . ' exceeding ' . $qty . ' will be out of stock';
                $status = false;
            }
        }

        session()->flash('error', $product->title . ' exceeding ' . $qty . ' will be out of stock');

        return response()->json([
            'status' => $status,
            'message' => $message
        ]);
    }

    public function deleteCart(Request $request)
    {
        $rowId = $request->rowId;
        $itemIn4 = Cart::get($rowId);
        if ($itemIn4 == null) {
            session()->flash('error', 'Item Not Found');
            return response()->json([
                'status' => false,
                'message' => 'Item Not Found'
            ]);
        }
        Cart::remove($rowId);
        session()->flash('success', 'Successfully removed product from cart');
        return response()->json([
            'status' => true,
            'message' => 'Successfully removed product from cart'
        ]);
    }

    public function checkout()
    {
        $discount = 0;
        if (Cart::count() == 0) return redirect()->route('front.cart');
        if (!Auth::check()) {
            if (!session()->has('url.intended')) session(['url.intended' => url()->current()]);
            return redirect()->route('account.login');
        }
        session()->forget('url.intended');
        $countries = Country::orderBy('name', 'ASC')->get();
        $subTotal = Cart::subtotal(2, '.', '');
        if (session()->has('code')) {
            $code = session()->get('code');
            if ($code->type == 'percent') {
                $discount = ($code->discount_amount / 100) * $subTotal;
            } else {
                $discount = $code->discount_amount;
            }
        }
        $customerAddress = CustomerAddress::where('user_id', Auth::user()->id)->first();
        $userCountry = $customerAddress?->country_id;
        $shippingIn4 = ShippingCharge::where('country_id', $userCountry)->first();
        $totalShippingCharge = 0;
        $totalQty = 0;
        foreach (Cart::content() as $item) {
            $totalQty += $item->qty;
        }
        $totalShippingCharge = $totalQty * $shippingIn4?->amount;
        $grandTotal = ($subTotal - $discount) + $totalShippingCharge;
        return view('front.checkout', compact('countries', 'customerAddress', 'totalShippingCharge', 'grandTotal', 'discount'));
    }

    public function processCheckout(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'country' => 'required',
            'address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zip' => 'required',
            'mobile' => 'required|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Please fix your errors',
                'errors' => $validator->errors()
            ]);
        }

        // Save user address
        $user = Auth::user()->id;

        CustomerAddress::updateOrCreate(
            ['user_id' => $user],
            [
                'user_id' => $user,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'country_id' => $request->country,
                'address' => $request->address,
                'apartment' => $request->apartment,
                'city' => $request->city,
                'state' => $request->state,
                'zip' => $request->zip
            ]
        );

        // Store data in orders table
        if ($request->payment_method == 'cod') {
            $coupon_code = '';
            $promo_code = '';
            $shipping = 0;
            $discount = 0;
            $subTotal = Cart::subtotal(2, '.', '');
            if (session()->has('code')) {
                $code = session()->get('code');
                if ($code->type == 'percent') {
                    $discount = ($code->discount_amount / 100) * $subTotal;
                } else {
                    $discount = $code->discount_amount;
                }
                $coupon_code = $code->id;
                $promo_code = $code->code;
            }
            $grandTotal = $subTotal + $shipping;

            $shippingIn4 = ShippingCharge::where('country_id', $request->country)->first();
            $totalQty = 0;
            foreach (Cart::content() as $item) {
                $totalQty += $item->qty;
            }
            if ($shippingIn4 != null) {
                $shipping = $totalQty * $shippingIn4->amount;
                $grandTotal = ($subTotal - $discount) + $shipping;
            }


            $order = new Order();
            $order->subtotal = $subTotal;
            $order->shipping = $shipping;
            $order->grand_total = $grandTotal;
            $order->discount = $discount;
            $order->coupon_code = $promo_code;
            $order->coupon_code_id = $coupon_code;
            $order->payment_status = 'not paid';
            $order->status = 'pending';
            $order->user_id = $user;
            $order->first_name = $request->first_name;
            $order->last_name = $request->last_name;
            $order->email = $request->email;
            $order->mobile = $request->mobile;
            $order->country_id = $request->country;
            $order->address = $request->address;
            $order->apartment = $request->apartment;
            $order->apartment = $request->apartment;
            $order->city = $request->city;
            $order->state = $request->state;
            $order->zip = $request->zip;
            $order->notes = $request->order_notes;
            $order->save();

            // Store order items in order items table
            foreach (Cart::content() as $item) {
                $orderItem = new OrderItem();
                $orderItem->order_id = $order->id;
                $orderItem->product_id = $item->id;
                $orderItem->name = $item->name;
                $orderItem->qty = $item->qty;
                $orderItem->price = $item->price;
                $orderItem->total = $item->qty * $item->price;
                $orderItem->save();
            }
            session()->flash('success', 'Order saved successfully!');
            Cart::destroy();
            session()->forget('code');
            return response()->json([
                'status' => true,
                'orderId' => $order->id,
                'message' => 'Order saved successfully!'
            ]);
        }
    }

    public function thankyou($id)
    {
        if ($id) return view('front.thankyou');
        else return view('front.checkout');
    }

    public function getOrderSummary(Request $request)
    {
        $subTotal = Cart::subtotal(2, '.', '');
        $discount = 0;
        $discountStr = '';
        if (session()->has('code')) {
            $code = session()->get('code');
            if ($code->type == 'percent') {
                $discount = ($code->discount_amount / 100) * $subTotal;
            } else {
                $discount = $code->discount_amount;
            }
            $discountStr = '<div class="mt-4" id="discount_row">
            <strong>' . session()->get('code')->code . '</strong>
            <a class="btn btn-sm btn-danger" id="remove_discount"><i class="fa fa-times"></i></a>
            </div>';
        }


        if ($request->country_id > 0) {
            $shippingIn4 = ShippingCharge::where('country_id', $request->country_id)->first();
            $totalQty = 0;
            foreach (Cart::content() as $item) {
                $totalQty += $item->qty;
            }
            if ($shippingIn4 != null) {
                $shippingCharge = $totalQty * $shippingIn4->amount;
                $grandTotal = ($subTotal - $discount) + $shippingCharge;
                return response()->json([
                    'status' => true,
                    'grandTotal' => number_format($grandTotal, 2),
                    'discount' => number_format($discount, 2),
                    'discountStr' => $discountStr,
                    'shippingCharge' => number_format($shippingCharge, 2),
                ]);
            }
        } else {
            return response()->json([
                'status' => true,
                'grandTotal' => number_format($subTotal - $discount, 2),
                'discount' => number_format($discount, 2),
                'discountStr' => $discountStr,
                'shippingCharge' => number_format(0, 2),
            ]);
        }
    }

    public function applyDiscount(Request $request)
    {
        $discount_code = DiscountCoupon::where('code', $request->code)->first();
        if ($discount_code == null) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid discount coupon'
            ]);
        }

        $now = Carbon::now();
        if ($discount_code->starts_at != '') {
            $starts_at = Carbon::createFromFormat('Y-m-d H:i:s', $discount_code->starts_at);
            if ($now->lt($starts_at)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid starts at discount coupon'
                ]);
            }
        }

        if ($discount_code->expires_at != '') {
            $expires_at = Carbon::createFromFormat('Y-m-d H:i:s', $discount_code->expires_at);
            if ($now->gt($expires_at)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid expires at discount coupon'
                ]);
            }
        }

        if ($discount_code->max_uses > 0) {
            $couponUsed = Order::where('coupon_code_id', $discount_code->id)->count();
            if ($couponUsed >= $discount_code->max_uses) {
                return response()->json([
                    'status' => false,
                    'message' => 'The discount code has exceeded the number of uses'
                ]);
            }
        }

        if ($discount_code->max_uses_user > 0) {
            $couponUsedByUser = Order::where(['coupon_code_id' => $discount_code->id, 'user_id' => Auth::user()->id])->count();
            if ($couponUsedByUser >= $discount_code->max_uses_user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Discount code has been used'
                ]);
            }
        }
        $subTotal = Cart::subtotal(2, '.', '');
        if ($discount_code->min_amount > 0) {
            if ($subTotal < $discount_code->min_amount) {
                return response()->json([
                    'status' => false,
                    'message' => 'Only applies to total order value of $' . $discount_code->min_amount . ' or more'
                ]);
            }
        }
        session()->flash('code', $discount_code);
        return $this->getOrderSummary($request);
    }

    public function removeDiscount(Request $request)
    {
        session()->forget('code');
        return $this->getOrderSummary($request);
    }
}
