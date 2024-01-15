<?php

use App\Models\Category;
use App\Models\ProductImage;

function getCategories()
{
    return Category::orderBy('name', 'ASC')->with('sub_category')->orderBy('id', 'DESC')->where('status', 1)->where('showHome', 'Yes')->get();
}

function getProductImage($prodId)
{
    return ProductImage::where('product_id', $prodId)->first();
}
