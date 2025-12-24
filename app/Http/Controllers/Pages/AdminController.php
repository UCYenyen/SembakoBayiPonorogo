<?php

namespace App\Http\Controllers\Pages;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;
use App\Models\BaseVoucher;
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

    public function vouchers()
    {
        $vouchers = BaseVoucher::orderBy('created_at', 'desc')->paginate(15);

        return view('dashboard.admin.vouchers.index', [
            'vouchers' => $vouchers
        ]);
    }

    public function createVoucher()
    {
        return view('dashboard.admin.vouchers.create');
    }

    public function editVoucher(BaseVoucher $baseVoucher)
    {
        return view('dashboard.admin.vouchers.edit', [
            'voucher' => $baseVoucher
        ]);
    }

    public function transactions(Request $request)
    {
        $status = $request->get('status', 'all');
        
        // Base query
        $query = Transaction::with(['user', 'transaction_items.product', 'payment', 'delivery', 'address'])
            ->latest();
        
        // Filter by status
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        
        // Get transactions
        $transactions = $query->paginate(15);
        
        // Count by status for tabs
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
        $transaction->load(['user', 'transaction_items.product', 'payment', 'delivery', 'address']);
        
        return view('dashboard.admin.transactions.show', [
            'transaction' => $transaction,
        ]);
    }

    public function updateTransactionStatus(Request $request, Transaction $transaction)
    {
        $request->validate([
            'status' => 'required|in:pending_payment,paid,processing,shipped,delivered,completed,cancelled,failed',
            'no_resi' => 'nullable|string|max:255',
        ]);
        
        $updateData = ['status' => $request->status];
        
        // Update no_resi if provided and status is shipped
        if ($request->status === Transaction::STATUS_SHIPPED && $request->no_resi) {
            $updateData['no_resi'] = $request->no_resi;
        }
        
        $transaction->update($updateData);
        
        return redirect()->back()->with('success', 'Transaction status updated successfully!');
    }
}
