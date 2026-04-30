<?php

namespace App\Http\Controllers;

use App\Models\InventoryAdjustment;
use App\Models\Product;
use Illuminate\Http\Request;

class InventoryAdjustmentController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->check() || (!auth()->user()->isSuperAdmin() && !auth()->user()->hasPermission('Inventory Adjustments'))) {
            return redirect('/')->with('error', 'Silakan login terlebih dahulu atau Anda tidak memiliki akses');
        }

        $search    = $request->get('search');
        $action    = $request->get('action');
        $dateFrom  = $request->get('date_from');
        $dateTo    = $request->get('date_to');

        $query = InventoryAdjustment::with(['product', 'user']);

        if (!auth()->user()->isSuperAdmin() && !auth()->user()->hasPermission('product master')) {
            $query->whereHas('product', function($q) {
                $q->where('user_id', auth()->id());
            });
        }

        if ($search) {
            $query->whereHas('product', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if ($action && $action !== 'all') {
            $query->where('action', $action);
        }

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $adjustments = $query->latest()->paginate(15)->withQueryString();
        $productsList = Product::select('id', 'name')->orderBy('name')->get();

        return view('admin.inventory.index', compact('adjustments', 'productsList'));
    }

    public function editProduct($id)
    {
        if (!auth()->check() || (!auth()->user()->isSuperAdmin() && !auth()->user()->hasPermission('product edit'))) {
            return redirect('/')->with('error', 'Silakan login terlebih dahulu atau Anda tidak memiliki akses');
        }

        $product = Product::findOrFail($id);

        if (!auth()->user()->isSuperAdmin() && !auth()->user()->hasPermission('product master') && $product->user_id !== auth()->id()) {
            return redirect()->route('inventory.index')->with('error', 'Anda tidak memiliki akses untuk mengedit produk ini.');
        }

        return view('admin.inventory.edit', compact('product'));
    }

    public function updateProduct(Request $request, $id)
    {
        if (!auth()->check() || (!auth()->user()->isSuperAdmin() && !auth()->user()->hasPermission('product edit'))) {
            return redirect('/')->with('error', 'Silakan login terlebih dahulu atau Anda tidak memiliki akses');
        }

        $request->validate([
            'tipe'        => 'required|in:tambah,kurang',
            'jumlah'      => 'required|integer|min:1',
            'edit_reason' => 'required|string|max:500',
        ]);

        $product = Product::findOrFail($id);

        if (!auth()->user()->isSuperAdmin() && !auth()->user()->hasPermission('product master') && $product->user_id !== auth()->id()) {
            return redirect()->route('inventory.index')->with('error', 'Anda tidak memiliki akses untuk mengedit produk ini.');
        }

        $jumlah       = (int) $request->jumlah;
        $delta        = $request->tipe === 'tambah' ? $jumlah : -$jumlah;
        $stokBaru     = $product->stok + $delta;

        $product->stok = $stokBaru;
        $product->save();

        InventoryAdjustment::create([
            'product_id' => $product->id,
            'user_id'    => auth()->id(),
            'action'     => 'adjustment',
            'stok'       => $delta,
            'stok_total' => $stokBaru,
            'note'       => $request->edit_reason,
        ]);

        if (auth()->check()) {
            auth()->user()->notify(new \App\Notifications\ProductActionNotification('update', $product->name));
        }

        return redirect()->route('inventory.index')->with('success', 'Stok berhasil disesuaikan.');
    }
}
