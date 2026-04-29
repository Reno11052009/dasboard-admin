<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use App\Models\InventoryAdjustment;
use App\Notifications\ProductApprovalRequest;
use App\Notifications\ProductActionNotification;
use App\Notifications\ProductStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->check() || (!auth()->user()->isSuperAdmin() && !auth()->user()->hasPermission('product view'))) {
            return redirect('/')->with('error', 'Silakan login terlebih dahulu atau Anda tidak memiliki akses');
        }

        $search = $request->get('search');

        $limit = $request->get('limit', 10);
        $category = $request->get('category');

        $query = Product::query();

        // Filter status: if user has master access, they can see all, otherwise only approved or their own products
        if (!auth()->user()->isSuperAdmin() && !auth()->user()->hasPermission('product master')) {
            $query->where(function ($q) {
                $q->where('status', 'approved')
                    ->orWhere('user_id', auth()->id());
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('category', 'like', "%$search%");
            });
        }

        if ($category) {
            $query->where('category', $category);
        }

        if ($limit === 'all') {
            $perPage = $query->count() > 0 ? $query->count() : 1;
        } else {
            $perPage = (int) $limit;
        }

        $products = $query->latest()->paginate($perPage)->withQueryString();

        $categories = Product::select('category')->distinct()->whereNotNull('category')->where('category', '!=', '')->pluck('category');

        return view('admin.product.product', compact('products', 'categories'));
    }

    public function create()
    {
        if (!auth()->check() || (!auth()->user()->isSuperAdmin() && !auth()->user()->hasPermission('product create'))) {
            return redirect('/')->with('error', 'Silakan login terlebih dahulu atau Anda tidak memiliki akses');
        }

        return view('admin.product.create');
    }

    public function store(Request $request)
    {
        if (!auth()->check() || (!auth()->user()->isSuperAdmin() && !auth()->user()->hasPermission('product create'))) {
            return redirect('/')->with('error', 'Silakan login terlebih dahulu atau Anda tidak memiliki akses');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'stok' => 'required|integer',
            'price' => 'required|numeric',
            'image' => 'nullable|image|max:2048',
            'category' => 'nullable|string|max:255',
        ]);

        $product = new Product();
        $product->user_id = auth()->id();
        $product->name = $request->name;
        $product->description = $request->description;
        $product->stok = $request->stok;
        $product->price = $request->price;
        $product->category = $request->category;

        $isMaster = auth()->user()->isSuperAdmin() || auth()->user()->hasPermission('product master');
        $product->status = $isMaster ? 'approved' : 'pending';

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $product->image = $imagePath;
        }

        $product->save();

        InventoryAdjustment::create([
            'product_id' => $product->id,
            'user_id' => auth()->id(),
            'action' => 'created',
            'stok' => $product->stok,
            'stok_total' => $product->stok,
            'note' => 'Penambahan produk baru'
        ]);

        if ($isMaster) {
            auth()->user()->notify(new ProductActionNotification('create', $product->name));
        } else {
            $masterUsers = User::whereHas('role', function ($q) {
                $q->where('name', 'Super Admin')
                    ->orWhereJsonContains('permissions', 'product.master')
                    ->orWhereJsonContains('permissions', 'product master');
            })->get();

            $legacySuperAdmins = User::where('role_id', null)->get()->filter->isSuperAdmin();
            $allMasterUsers = $masterUsers->merge($legacySuperAdmins);

            foreach ($allMasterUsers as $masterUser) {
                $masterUser->notify(new ProductApprovalRequest($product, auth()->user()));
            }
        }

        $message = $isMaster ? 'Product created successfully.' : 'Product submitted and waiting for approval.';
        return redirect()->route('products')->with('success', $message);
    }

    public function updateStatus(Request $request, $id)
    {
        if (!auth()->check() || (!auth()->user()->isSuperAdmin() && !auth()->user()->hasPermission('product master'))) {
            return redirect('/')->with('error', 'Silakan login terlebih dahulu atau Anda tidak memiliki akses');
        }

        $request->validate([
            'status' => 'required|in:approved,rejected'
        ]);

        $product = Product::findOrFail($id);
        $product->status = $request->status;
        $product->save();

        if ($product->user_id) {
            $creator = User::find($product->user_id);
            if ($creator) {
                $creator->notify(new ProductStatusUpdated($product, $request->status));
            }
        }


        \Illuminate\Support\Facades\DB::table('notifications')
            ->where('type', 'App\Notifications\ProductApprovalRequest')
            ->where('data', 'like', '%"product_id":' . $id . '%')
            ->update(['read_at' => now()]);

        return redirect()->back()->with('success', 'Status produk berhasil diperbarui.');
    }

    public function show($id)
    {
        if (!auth()->check() || (!auth()->user()->isSuperAdmin() && !auth()->user()->hasPermission('product view'))) {
            return redirect('/')->with('error', 'Silakan login terlebih dahulu atau Anda tidak memiliki akses');
        }

        $product = Product::findOrFail($id);
        $adjustments = \App\Models\InventoryAdjustment::with('user')->where('product_id', $product->id)->latest()->get();
        return view('admin.product.show', compact('product', 'adjustments'));
    }

    public function edit($id)
    {
        if (!auth()->check() || (!auth()->user()->isSuperAdmin() && !auth()->user()->hasPermission('product edit'))) {
            return redirect('/')->with('error', 'Silakan login terlebih dahulu atau Anda tidak memiliki akses');
        }

        $product = Product::findOrFail($id);
        return view('admin.product.edit', compact('product'));
    }

    public function update(Request $request, $id)
    {
        if (!auth()->check() || (!auth()->user()->isSuperAdmin() && !auth()->user()->hasPermission('product edit'))) {
            return redirect('/')->with('error', 'Silakan login terlebih dahulu atau Anda tidak memiliki akses');
        }

        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric',
            'image'       => 'nullable|image|max:2048',
            'category'    => 'nullable|string|max:255',
        ]);

        $product = Product::findOrFail($id);

        $product->name        = $request->name;
        $product->description = $request->description;
        $product->price       = $request->price;
        $product->category    = $request->category;

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $imagePath    = $request->file('image')->store('products', 'public');
            $product->image = $imagePath;
        }

        $product->save();

        if (auth()->check()) {
            auth()->user()->notify(new ProductActionNotification('update', $product->name));
        }

        return redirect()->route('products')->with('success', 'Product updated successfully.');
    }

    public function destroy($id)
    {
        if (!auth()->check() || (!auth()->user()->isSuperAdmin() && !auth()->user()->hasPermission('product delete'))) {
            return redirect('/')->with('error', 'Silakan login terlebih dahulu atau Anda tidak memiliki akses');
        }

        $product = Product::findOrFail($id);

        InventoryAdjustment::create([
            'product_id' => null,
            'user_id' => auth()->id(),
            'action' => 'deleted',
            'stok' => 0,
            'stok_total' => 0,
            'note' => 'Produk dihapus: ' . $product->name
        ]);

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();

        return redirect()->route('products')->with('success', 'Product deleted successfully.');
    }
}
