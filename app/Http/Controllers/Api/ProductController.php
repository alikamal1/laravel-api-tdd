<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Product as ResourcesProduct;
use App\Http\Resources\ProductCollection;
use App\Product;

class ProductController extends Controller
{

    public function index()
    {
        return new ProductCollection(Product::paginate());
    }

    public function store(Request $request)
    {
        $product = Product::create([
            'name' => $request->name,
            'slug' => str_slug($request->name),
            'price' => $request->price
        ]);
        return response()->json(new ResourcesProduct($product), 201);
    }

    public function show(int $id)
    {
        $product = Product::findOrFail($id);

        return response()->json(new ResourcesProduct($product), 200);
    }

    public function update(Request $request ,int $id)
    {
        $product = Product::findOrFail($id);
        $product->update([
            'name' => $request->name,
            'slug' => str_slug($request->name),
            'price' => $request->price
        ]);

        return response()->json(new ResourcesProduct($product), 200);
    }

    public function destroy(int $id)
    {
        $product = Product::findOrFail($id);

        $product->delete();

        return response()->json(null, 204);
    }


}
