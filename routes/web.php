<?php

use App\Http\Controllers\admin\AdminLoginController;
use App\Http\Controllers\admin\BrandController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\DiscountCouponController;
use App\Http\Controllers\admin\HomeController;
use App\Http\Controllers\admin\OrderController;
use App\Http\Controllers\admin\PageController;
use App\Http\Controllers\admin\ProductController;
use App\Http\Controllers\admin\ProductImageController;
use App\Http\Controllers\admin\ProductSubCategoryController;
use App\Http\Controllers\admin\ShippingChargeController;
use App\Http\Controllers\admin\SubCategoryController;
use App\Http\Controllers\admin\TempImagesController;
use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\ShopController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [FrontController::class, 'index'])->name('front.home');
Route::get('/shop/{categorySlug?}/{subCategorySlug?}', [ShopController::class, 'index'])->name('front.shop');
Route::get('/product/{slug}', [ShopController::class, 'product'])->name('front.product');
Route::get('/cart', [CartController::class, 'cart'])->name('front.cart');
Route::post('/add-to-cart', [CartController::class, 'add2cart'])->name('front.add2cart');
Route::post('/update-cart', [CartController::class, 'updateCart'])->name('front.updateCart');
Route::post('/delete-cart', [CartController::class, 'deleteCart'])->name('front.deleteCart');
Route::get('/checkout', [CartController::class, 'checkout'])->name('front.checkout');
Route::post('/process-checkout', [CartController::class, 'processCheckout'])->name('front.processCheckout');
Route::post('/get-order-summary', [CartController::class, 'getOrderSummary'])->name('shipping.getOrderSummary');
Route::post('/apply-discount', [CartController::class, 'applyDiscount'])->name('shipping.applyDiscount');
Route::post('/remove-discount', [CartController::class, 'removeDiscount'])->name('shipping.removeDiscount');
Route::get('/thankyou/{orderId}', [CartController::class, 'thankyou'])->name('front.thankyou');
Route::post('/add-to-wishlist', [FrontController::class, 'add2Wishlist'])->name('front.add2Wishlist');
Route::get('/page/{slug}', [FrontController::class, 'page'])->name('front.page');

Route::group(['prefix' => 'account'], function () {
    Route::group(['middleware' => 'guest'], function () { // See more at Kernel.php file
        Route::get('/register', [AuthController::class, 'register'])->name('account.register');
        Route::post('/process-register', [AuthController::class, 'registerProcess'])->name('account.process.register');
        Route::get('/login', [AuthController::class, 'login'])->name('account.login');
        Route::post('/login', [AuthController::class, 'authenticate'])->name('account.authenticate');
    });

    Route::group(['middleware' => 'auth'], function () { // See more at Kernel.php file
        Route::get('/profile', [AuthController::class, 'profile'])->name('account.profile');
        Route::post('/update-profile', [AuthController::class, 'updateProfile'])->name('account.updateProfile');
        Route::post('/update-address', [AuthController::class, 'updateAddress'])->name('account.updateAddress');
        Route::get('/orders', [AuthController::class, 'orders'])->name('account.orders');
        Route::get('/order-detail/{orderId}', [AuthController::class, 'orderDetail'])->name('account.orders.detail');
        Route::get('/wishlist', [AuthController::class, 'wishlist'])->name('account.wishlist');
        Route::post('/wishlist-remove', [AuthController::class, 'removeProd'])->name('account.removeProd');
        Route::get('/change-password', [AuthController::class, 'changePassword'])->name('account.changePassword');
        Route::post('/process-change-password', [AuthController::class, 'processChangePassword'])->name('account.processChangePassword');
        Route::get('/logout', [AuthController::class, 'logout'])->name('account.logout');
    });
});

Route::group(['prefix' => 'admin'], function () {
    Route::group(['middleware' => 'admin.guest'], function () {
        Route::get('/login', [AdminLoginController::class, 'index'])->name('admin.login');
        Route::post('/authenticate', [AdminLoginController::class, 'authenticate'])->name('admin.authenticate');
    });

    Route::group(['middleware' => 'admin.auth'], function () {
        Route::get('/dashboard', [HomeController::class, 'index'])->name('admin.dashboard');
        Route::get('/logout', [HomeController::class, 'logout'])->name('admin.logout');

        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.delete');

        Route::get('/sub-categories', [SubCategoryController::class, 'index'])->name('sub-categories.index');
        Route::get('/sub-categories/create', [SubCategoryController::class, 'create'])->name('sub-categories.create');
        Route::post('/sub-categories', [SubCategoryController::class, 'store'])->name('sub-categories.store');
        Route::get('/sub-categories/{subCategory}/edit', [SubCategoryController::class, 'edit'])->name('sub-categories.edit');
        Route::put('/sub-categories/{subCategory}', [SubCategoryController::class, 'update'])->name('sub-categories.update');
        Route::delete('/sub-categories/{subCategory}', [SubCategoryController::class, 'destroy'])->name('sub-categories.delete');

        Route::get('/brands', [BrandController::class, 'index'])->name('brands.index');
        Route::get('/brands/create', [BrandController::class, 'create'])->name('brands.create');
        Route::post('/brands', [BrandController::class, 'store'])->name('brands.store');
        Route::get('/brands/{subCategory}/edit', [BrandController::class, 'edit'])->name('brands.edit');
        Route::put('/brands/{subCategory}', [BrandController::class, 'update'])->name('brands.update');
        Route::delete('/brands/{subCategory}', [BrandController::class, 'destroy'])->name('brands.delete');

        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::get('/product-subcategories', [ProductSubCategoryController::class, 'index'])->name('product-subcategories.index');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.delete');
        Route::get('/get-products', [ProductController::class, 'getProducts'])->name('products.getProducts');

        Route::post('/product-images/update', [ProductImageController::class, 'update'])->name('product-images.update');
        Route::delete('/product-images', [ProductImageController::class, 'destroy'])->name('product-images.destroy');

        Route::get('/shipping/create', [ShippingChargeController::class, 'create'])->name('shipping.create');
        Route::post('/shipping', [ShippingChargeController::class, 'store'])->name('shipping.store');
        Route::get('/shipping', [ShippingChargeController::class, 'index'])->name('shipping.index');
        Route::get('/shipping/{shipping}/edit', [ShippingChargeController::class, 'edit'])->name('shipping.edit');
        Route::put('/shipping/{shipping}', [ShippingChargeController::class, 'update'])->name('shipping.update');
        Route::delete('/shipping/{shipping}', [ShippingChargeController::class, 'destroy'])->name('shipping.delete');

        Route::get('/coupons/create', [DiscountCouponController::class, 'create'])->name('coupons.create');
        Route::post('/coupons', [DiscountCouponController::class, 'store'])->name('coupons.store');
        Route::get('/coupons', [DiscountCouponController::class, 'index'])->name('coupons.index');
        Route::get('/coupons/{coupon}/edit', [DiscountCouponController::class, 'edit'])->name('coupons.edit');
        Route::put('/coupons/{coupon}', [DiscountCouponController::class, 'update'])->name('coupons.update');
        Route::delete('/coupons/{coupon}', [DiscountCouponController::class, 'destroy'])->name('coupons.delete');

        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'detail'])->name('orders.detail');
        Route::post('/orders/change-status/{order}', [OrderController::class, 'changeOrderStatus'])->name('orders.changeOrderStatus');
        Route::post('/orders/send-invoice-email/{order}', [OrderController::class, 'sendInvoiceEmail'])->name('orders.sendInvoiceEmail');

        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.delete');

        Route::get('/pages/create', [PageController::class, 'create'])->name('pages.create');
        Route::post('/pages', [PageController::class, 'store'])->name('pages.store');
        Route::get('/pages', [PageController::class, 'index'])->name('pages.index');
        Route::get('/pages/{page}/edit', [PageController::class, 'edit'])->name('pages.edit');
        Route::put('/pages/{page}', [PageController::class, 'update'])->name('pages.update');
        Route::delete('/pages/{page}', [PageController::class, 'destroy'])->name('pages.delete');

        Route::post('/upload-temp-image', [TempImagesController::class, 'create'])->name('temp-images.create');

        Route::get('/getSlug', function (Request $request) {
            $slug = '';
            if (!empty($request->title)) {
                $slug = Str::slug($request->title);
            }
            return response()->json([
                'status' => true,
                'slug' => $slug
            ]);
        })->name('getSlug');
    });
});
