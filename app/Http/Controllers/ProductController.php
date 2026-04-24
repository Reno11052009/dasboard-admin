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
        if (!auth()->check()) {
            return redirect('/')->with('error', 'Silakan login terlebih dahulu');
        }

        $search = $request->get('search');

        $query = Product::query();

        if ($search) {
            $query->where('name', 'like', "%$search%")
                ->orWhere('category', 'like', "%$search%");
        }

        $products = $query->latest()->get();
        return view('admin.product.product', compact('products'));
    }

    public function create()
    {
        if (!auth()->check()) {
            return redirect('/')->with('error', 'Silakan login terlebih dahulu');
        }

        return view('admin.product.create');
    }

    public function store(Request $request)
    {
        if (!auth()->check()) {
            return redirect('/')->with('error', 'Silakan login terlebih dahulu');
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

    public function edit($id)
    {
        if (!auth()->check()) {
            return redirect('/')->with('error', 'Silakan login terlebih dahulu');
        }

        $product = Product::findOrFail($id);
        return view('admin.product.edit', compact('product'));
    }

    public function update(Request $request, $id)
    {
        if (!auth()->check()) {
            return redirect('/')->with('error', 'Silakan login terlebih dahulu');
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
        if (!auth()->check()) {
            return redirect('/')->with('error', 'Silakan login terlebih dahulu');
        }

        $product = Product::findOrFail($id);
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();

        return redirect()->route('products')->with('success', 'Product deleted successfully.');
    }
}
