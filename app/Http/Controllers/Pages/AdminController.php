<?php

namespace App\Http\Controllers\Pages;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Vendor;
use App\Models\Transaction;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function unauthorized()
    {
        return view('unauthorized');
    }

    public function index()
    {
        $currentUser = Auth::user();
        return view('dashboard.admin.index', [
            'adminName' => $currentUser->name
        ]);
    }

    public function products()
    {
        // Get all products with pagination
        $products = Product::with(['category', 'brand'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('dashboard.admin.products.index', [
            'products' => $products
        ]);
    }

    public function createProduct()
    {
        $categories = Category::all();
        $brands = Brand::all();

        return view('dashboard.admin.products.create', [
            'categories' => $categories,
            'brands' => $brands
        ]);
    }
    public function editProduct(Product $product)
    {
        // Product sudah ter-load dengan category dan brand otomatis
        // Tidak perlu findOrFail() lagi

        $categories = Category::all();
        $brands = Brand::all();

        return view('dashboard.admin.products.edit', [
            'product' => $product,
            'categories' => $categories,
            'brands' => $brands
        ]);
    }

   

    public function transactions(Request $request)
    {
        // Default filter ke status 'paid' agar admin tahu mana yang harus dikirim
        $status = $request->get('status', 'paid');

        $query = Transaction::with(['user', 'transaction_items.product', 'delivery', 'address'])
            ->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $transactions = $query->paginate(15);

        $statusCounts = [
            'all' => Transaction::count(),
            'pending_payment' => Transaction::where('status', Transaction::STATUS_PENDING_PAYMENT)->count(),
            'paid' => Transaction::where('status', Transaction::STATUS_PAID)->count(),
            'processing' => Transaction::where('status', Transaction::STATUS_PROCESSING)->count(),
            'shipped' => Transaction::where('status', Transaction::STATUS_SHIPPED)->count(),
            'delivered' => Transaction::where('status', Transaction::STATUS_DELIVERED)->count(),
            'completed' => Transaction::where('status', Transaction::STATUS_COMPLETED)->count(),
        ];

        return view('dashboard.admin.transactions.index', [
            'transactions' => $transactions,
            'statusCounts' => $statusCounts,
            'currentStatus' => $status,
        ]);
    }

    public function showTransaction(Transaction $transaction)
    {
        $transaction->load(['user', 'transaction_items.product', 'delivery', 'address']);
        return view('dashboard.admin.transactions.detail', compact('transaction'));
    }
    public function updateTransactionStatus(Request $request, Transaction $transaction)
    {
        $request->validate([
            'status' => 'required|in:pending_payment,paid,processing,shipped,delivered,completed,cancelled,failed',
            'no_resi' => 'nullable|string|max:255',
        ]);

        $updateData = ['status' => $request->status];

        if ($request->no_resi) {
            $updateData['no_resi'] = $request->no_resi;
            $updateData['status'] = Transaction::STATUS_SHIPPED;
        }

        $transaction->update($updateData);

        return redirect()->route('admin.transactions.detail', $transaction)
            ->with('success', 'Transaction updated successfully!');
    }

    public function editTransaction(Transaction $transaction)
    {
        $transaction->load(['user', 'transaction_items.product', 'delivery', 'address']);
        return view('dashboard.admin.transactions.edit', compact('transaction'));
    }

    public function vendors()
    {
        $vendors = Vendor::orderBy('id', 'asc')->paginate(15);

        return view('dashboard.admin.vendors.index', [
            'vendors' => $vendors
        ]);
    }

    public function createVendor()
    {
        return view('dashboard.admin.vendors.create');
    }

    public function editVendor(Vendor $vendor)
    {
        return view('dashboard.admin.vendors.edit', [
            'vendor' => $vendor
        ]);
    }
}
