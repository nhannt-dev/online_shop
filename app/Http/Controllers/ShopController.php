<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductRating;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShopController extends Controller
{
    public function index(Request $request, $categorySlug = null, $subCategorySlug = null)
    {
        $categorySelected = '';
        $subCategorySelected = '';
        $brandArr = [];

        $categories = Category::orderBy('name', 'ASC')->with('sub_category')->where('status', 1)->get();
        $brands = Brand::orderBy('name', 'ASC')->where('status', 1)->get();
        $products = Product::where('status', 1);

        if (!empty($categorySlug)) {
            $category = Category::where('slug', $categorySlug)->first();
            $products = $products->where('category_id', $category->id);
            $categorySelected = $category->id;
        }
        if (!empty($subCategorySlug)) {
            $subCategory = SubCategory::where('slug', $subCategorySlug)->first();
            $products = $products->where('sub_category_id', $subCategory->id);
            $subCategorySelected = $subCategory->id;
        }

        if (!empty($request->get('brand'))) {
            $brandArr = explode(',', $request->get('brand'));
            $products = $products->whereIn('brand_id', $brandArr);
        }

        if ($request->get('price_max') != '' && $request->get('price_min') != '') {
            if ($request->get('price_max')) {
                $products = $products->whereBetween('price', [intval($request->get('price_min')), 1000000]);
            } else {
                $products = $products->whereBetween('price', [intval($request->get('price_min')), intval($request->get('price_max'))]);
            }
        }

        if (!empty($request->get('search'))) {
            $products = $products->where('title', 'like', '%' . $request->get('search') . '%');
        }

        $price_min = intval($request->get('price_min'));
        $price_max = (intval($request->get('price_max')) == 0 ? 1000 : $request->get('price_max'));

        if ($request->get('sort') != '') {
            if ($request->get('sort') == 'latest') {
                $products = $products->orderBy('id', 'DESC');
            } elseif ($request->get('sort') == 'price_asc') {
                $products = $products->orderBy('price', 'ASC');
            } else {
                $products = $products->orderBy('price', 'DESC');
            }
        } else {
            $products = $products->orderBy('id', 'DESC');
        }
        $products = $products->paginate(6);
        $sort = $request->get('sort');
        return view('front.shop', compact('categories', 'brands', 'products', 'categorySelected', 'subCategorySelected', 'brandArr', 'price_min', 'price_max', 'sort'));
    }

    public function product($slug)
    {
        $product = Product::where('slug', $slug)->with('product_images')->first();
        if ($product == null) abort(404);
        $relatedProducts = [];
        if ($product->related_products != '') {
            $productArr = explode(',', $product->related_products);
            $relatedProducts = Product::whereIn('id', $productArr)->where('status', 1)->with('product_images')->get();
        }
        return view('front.product', compact('product', 'relatedProducts'));
    }

    public function rating(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'comment' => 'required',
            'rating' => 'required',
        ]);

        if ($validator->passes()) {
            $productRating = new ProductRating();
            $productRating->product_id = $id;
            $productRating->username = $request->name;
            $productRating->email = $request->email;
            $productRating->comment = $request->comment;
            $productRating->rating = $request->rating;
            $productRating->status = 0;
            $productRating->save();

            session()->flash('success', 'Your rating have been sent successfully!');

            return response()->json([
                'status' => true,
                'message' => 'Your rating have been sent successfully!'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }
}
