<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\TempImage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class HomeController extends Controller
{
    public function index()
    {
        $totalOrders = Order::where('status', '!=', 'canceled')->count();
        $totalProducts = Product::count();
        $totalUsers = User::where('role', 1)->count();
        $totalRevenue = Order::where('status', '!=', 'canceled')->sum('grand_total');
        $startOfMonth = Carbon::now()->startOfMonth()->format('Y-m-d');
        $current = Carbon::now()->format('Y-m-d');
        $startLastMonth = Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d');
        $endLastMonth = Carbon::now()->subMonth()->endOfMonth()->format('Y-m-d');
        $revenueByMonth = Order::where('status', '!=', 'canceled')->whereDate('created_at', '>=', $startOfMonth)->whereDate('created_at', '<=', $current)->sum('grand_total');
        $revenueLastMonth = Order::where('status', '!=', 'canceled')->whereDate('created_at', '>=', $startLastMonth)->whereDate('created_at', '<=', $endLastMonth)->sum('grand_total');
        // Within the last 30 days
        $date30 = Carbon::now()->subDays(30)->format('Y-m-d');
        $revenueWithin30 = Order::where('status', '!=', 'canceled')->whereDate('created_at', '>=', $date30)->whereDate('created_at', '<=', $current)->sum('grand_total');

        $dayBeforeToday = Carbon::now()->subDays(1)->format('Y-m-d H:i:m');
        $tempImages = TempImage::where('created_at', '<=' . $dayBeforeToday)->get();
        foreach ($tempImages as $tempImage) {
            $path = public_path('/temp' . $tempImage->name);
            $thumbPath = public_path('/temp/thumb/' . $tempImage->name);
            // Delete main image
            if (File::exists($path)) {
                File::delete($path);
            }
            // Delete thumb image
            if (File::exists($thumbPath)) {
                File::delete($thumbPath);
            }

            TempImage::where('id', $tempImage->id)->delete();
        }

        return view('admin.dashboard', compact('totalOrders', 'totalProducts', 'totalUsers', 'totalRevenue', 'revenueByMonth', 'revenueLastMonth', 'revenueWithin30'));
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }
}
