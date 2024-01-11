<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        $brands = Brand::latest('id');
        if (!empty($request->get('keyword'))) {
            $brands = $brands->where('name', 'like', '%' . $request->get('keyword') . '%');
        }
        $brands = $brands->paginate(10);
        return view('admin.brand.list', compact('brands'));
    }

    public function create()
    {
        return view('admin.brand.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:brands'
        ]);

        if ($validator->passes()) {
            $brand = new Brand();
            $brand->name = $request->name;
            $brand->slug = $request->slug;
            $brand->status = $request->status;
            $brand->save();
            session()->flash('success', 'Brand updated successfully!');
            return response()->json([
                'status' => true,
                'message' => 'Brand updated successfully!'
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
        $brand = Brand::find($id);
        if (empty($brand)) {
            return redirect()->route('brands.index');
        }
        return view('admin.brand.edit', compact('brand'));
    }

    public function update($id, Request $request)
    {
        $brand = Brand::find($id);
        if (empty($brand)) {
            session()->flash('error', 'Brand Not Found');
            return response()->json([
                'status' => false,
                'notFound' => true,
                'message' => 'Brand Not Found'
            ]);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:brands,slug,' . $brand->id . ',id'
        ]);

        if ($validator->passes()) {
            $brand = new Brand();
            $brand->name = $request->name;
            $brand->slug = $request->slug;
            $brand->status = $request->status;
            $brand->save();
            session()->flash('success', 'Brand updated successfully!');
            return response()->json([
                'status' => true,
                'message' => 'Brand updated successfully!'
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
        $brand = Brand::find($id);
        if (empty($brand)) {
            session()->flash('error', 'Brand not found!');
            return response([
                'status' => false,
                'notFound' => true
            ]);
        }
        $brand->delete();
        session()->flash('success', 'Brand deleted successfully!');
        return response()->json([
            'status' => true,
            'message' => 'Brand deleted successfully!'
        ]);
    }
}
