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

        $search = $request->get('search');
        $action = $request->get('action');

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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'stok' => 'required|integer',
            'price' => 'required|numeric',
            'image' => 'nullable|image|max:2048',
            'category' => 'nullable|string|max:255',
            'edit_reason' => 'required|string'
        ]);

        $product = Product::findOrFail($id);

        if (!auth()->user()->isSuperAdmin() && !auth()->user()->hasPermission('product master') && $product->user_id !== auth()->id()) {
            return redirect()->route('inventory.index')->with('error', 'Anda tidak memiliki akses untuk mengedit produk ini.');
        }

        $oldStok = $product->stok;
        $oldData = $product->getOriginal();

        // Deteksi perubahan nama dan deskripsi sebelum disimpan
        $changedFields = [];
        if ($oldData['name'] !== $request->name) {
            $changedFields[] = 'nama';
        }
        if (($oldData['description'] ?? '') !== ($request->description ?? '')) {
            $changedFields[] = 'deskripsi';
        }

        $product->name = $request->name;
        $product->description = $request->description;

        $stokDifference = $request->stok - $oldStok;
        $product->stok = $request->stok;

        $product->price = $request->price;
        $product->category = $request->category;

        if ($request->hasFile('image')) {
            if ($product->image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($product->image);
            }
            $imagePath = $request->file('image')->store('products', 'public');
            $product->image = $imagePath;
        }

        $product->save();

        InventoryAdjustment::create([
            'product_id' => $product->id,
            'user_id' => auth()->id(),
            'action' => 'adjustment',
            'stok' => $stokDifference,
            'harga_old' => $oldData['price'],
            'harga_new' => $request->price,
            'note' => $request->edit_reason,
            'changed_fields' => !empty($changedFields) ? implode(',', $changedFields) : null,
        ]);

        if (auth()->check()) {
            auth()->user()->notify(new \App\Notifications\ProductActionNotification('update', $product->name));
        }

        return redirect()->route('inventory.index')->with('success', 'Produk dan stok berhasil disesuaikan.');
    }
}
