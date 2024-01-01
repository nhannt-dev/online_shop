<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Gloudemans\Shoppingcart\Facades\Cart as Cart;

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
}
