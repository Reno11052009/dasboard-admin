<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Notifications\ProductActionNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->check() || (!auth()->user()->isSuperAdmin() && !auth()->user()->hasPermission('product.view'))) {
            return redirect('/')->with('error', 'Silakan login terlebih dahulu atau Anda tidak memiliki akses');
        }

        $search = $request->get('search');

        $limit = $request->get('limit', 10);
        $category = $request->get('category');
        
        $query = Product::query();

        if ($search) {
            $query->where(function($q) use ($search) {
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
        if (!auth()->check() || (!auth()->user()->isSuperAdmin() && !auth()->user()->hasPermission('product.create'))) {
            return redirect('/')->with('error', 'Silakan login terlebih dahulu atau Anda tidak memiliki akses');
        }

        return view('admin.product.create');
    }

    public function store(Request $request)
    {
        if (!auth()->check() || (!auth()->user()->isSuperAdmin() && !auth()->user()->hasPermission('product.create'))) {
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
        $product->name = $request->name;
        $product->description = $request->description;
        $product->stok = $request->stok;
        $product->price = $request->price;
        $product->category = $request->category;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $product->image = $imagePath;
        }

        $product->save();

        if (auth()->check()) {
            auth()->user()->notify(new ProductActionNotification('create', $product->name));
        }

        return redirect()->route('products')->with('success', 'Product created successfully.');
    }

    public function show($id)
    {
        if (!auth()->check() || (!auth()->user()->isSuperAdmin() && !auth()->user()->hasPermission('product.view'))) {
            return redirect('/')->with('error', 'Silakan login terlebih dahulu atau Anda tidak memiliki akses');
        }

        $product = Product::findOrFail($id);
        return view('admin.product.show', compact('product'));
    }

    public function edit($id)
    {
        if (!auth()->check() || (!auth()->user()->isSuperAdmin() && !auth()->user()->hasPermission('product.edit'))) {
            return redirect('/')->with('error', 'Silakan login terlebih dahulu atau Anda tidak memiliki akses');
        }

        $product = Product::findOrFail($id);
        return view('admin.product.edit', compact('product'));
    }

    public function update(Request $request, $id)
    {
        if (!auth()->check() || (!auth()->user()->isSuperAdmin() && !auth()->user()->hasPermission('product.edit'))) {
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

        $product = Product::findOrFail($id);
        $product->name = $request->name;
        $product->description = $request->description;
        $product->stok = $request->stok;
        $product->price = $request->price;
        $product->category = $request->category;

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $imagePath = $request->file('image')->store('products', 'public');
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
        if (!auth()->check() || (!auth()->user()->isSuperAdmin() && !auth()->user()->hasPermission('product.delete'))) {
            return redirect('/')->with('error', 'Silakan login terlebih dahulu atau Anda tidak memiliki akses');
        }

        $product = Product::findOrFail($id);
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();

        return redirect()->route('products')->with('success', 'Product deleted successfully.');
    }
}
