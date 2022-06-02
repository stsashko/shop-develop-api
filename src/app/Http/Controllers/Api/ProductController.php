<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Products;
use App\Models\Deliveries;
use App\Models\Purchase_items;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
//use Symfony\Component\Console\Input\Input;

class ProductController extends Controller
{

    public function index(Request $request, $page = null)
    {

        if (!$page)
            return response()->json([
                'success' => true,
                'data' => ProductResource::collection(Products::all())
            ], 200);
        else {
//            $limit = !in_array($limit, [30, 50, 100]) ? 30 : $limit;

            $limit = (int)$request->input('rowsPerPage');

            $limit = !in_array($limit, [30, 50, 100]) ? 30 : $limit;

            Paginator::currentPageResolver(function () use ($page) {
                return $page;
            });


            $products = Products::select('products.*', 'categories.category_name', 'manufacturers.manufacturer_name')
                ->orderBy((in_array($request->input('orderBy'), ['product_name', 'category_name', 'price', 'updated_at']) ? $request->input('orderBy') : 'id'), (in_array($request->input('order'), ['asc', 'desc']) ? $request->input('order') : 'desc'));

            if ($request->input('category_id'))
                $products->where('category_id', '=', (int) $request->input('category_id'));

            if ($request->input('manufacturer_id'))
                $products->where('manufacturer_id', '=', (int) $request->input('manufacturer_id'));

            if ($request->input('search'))
                $products->where('product_name', 'LIKE',  "%{$request->input('search')}%");

            $products->leftJoin('categories', 'categories.id', '=', 'products.category_id');
            $products->leftJoin('manufacturers', 'manufacturers.id', '=', 'products.manufacturer_id');

            return response()->json([
                'success' => true,
                'data' => $products->paginate($limit)
            ], 200);
        }
    }

    public function products_find(Request $request)
    {
        if ($request->input('search') && strlen($request->input('search')) >= 3)
        {
            $products = Products::query();
            $products->select('products.product_name', 'products.id as product_id');

            $search = $request->input('search');

            $products->Where(function ($query) use ($search) {
                $query->where('product_name', 'LIKE', "%{$search}%");
                $query->orWhere('id', '=', "%{$search}%");
            });

//            $products->where('product_name', 'LIKE',  "%{$request->input('search')}%");

            return response()->json([
                'success' => true,
                'data' => $products->get()
            ], 200);
        }
        else {
            return response()->json([
                'success' => false,
                'errors' =>  ['Not found']
            ], 201);
        }
    }

    public function show($id)
    {
        try {
            $product = Products::select('products.*', 'categories.category_name', 'manufacturers.manufacturer_name');
            $product->leftJoin('categories', 'categories.id', '=', 'products.category_id');
            $product->leftJoin('manufacturers', 'manufacturers.id', '=', 'products.manufacturer_id');

            return response()->json([
                'success' => true,
                'data' => new ProductResource($product->findOrFail($id))
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response([
                'success' => false,
                'errors' => ['404 not found']
            ], 404);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_name' => 'required',
            'price' => 'required',
            'category_id' => 'required',
            'manufacturer_id' => 'required',
            'image'  =>  ($request->hasFile('image') ? 'required|mimes:jpg,jpeg,png,gif|max:2048' : ''),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->messages()
            ], 201);
        } else {
            $create = [
                'product_name' => $request->product_name,
                'price' => $request->price,
                'category_id' => $request->category_id,
                'manufacturer_id' => $request->manufacturer_id,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ];

            if($request->hasFile('image') && $request->file()) {
                $fileName = Str::random(15) . '.' . $request->image->extension();
                $request->image->move(public_path('product'), $fileName);
                $filePath = url('product/' . $fileName);
                $create['image'] = $filePath;
            }

            $id = Products::insertGetId($create);

            $product = Products::select('products.*', 'categories.category_name', 'manufacturers.manufacturer_name');
            $product->leftJoin('categories', 'categories.id', '=', 'products.category_id');
            $product->leftJoin('manufacturers', 'manufacturers.id', '=', 'products.manufacturer_id');
            $product = $product->findOrFail((int)$id);

            return response()->json([
                'success' => true,
                'data' => new ProductResource($product)
            ], 201);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'product_name' => 'required',
            'price' => 'required',
            'category_id' => 'required',
            'manufacturer_id' => 'required',
            'image'  =>  ($request->hasFile('image') ? 'required|mimes:jpg,jpeg,png,gif|max:2048' : ''),
        ]);

        if (Products::where('id', (int)$id)->count() <= 0) {
            return response()->json([
                'success' => false,
                'errors' => ['Id not found']
            ], 201);
        }

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->messages()
            ], 201);
        } else {
            $insert = [
                'product_name' => $request->product_name,
                'price' => $request->price,
                'category_id' => $request->category_id,
                'manufacturer_id' => $request->manufacturer_id
            ];

            if($request->hasFile('image') && $request->file()) {
                $fileName = Str::random(15) . '.' . $request->image->extension();
                $request->image->move(public_path('product'), $fileName);
                $filePath = url('product/' . $fileName);
                $insert['image'] = $filePath;
            }

            $product = Products::findOrFail((int)$id);
            $product->update($insert);

            $product = Products::select('products.*', 'categories.category_name', 'manufacturers.manufacturer_name');
            $product->leftJoin('categories', 'categories.id', '=', 'products.category_id');
            $product->leftJoin('manufacturers', 'manufacturers.id', '=', 'products.manufacturer_id');
            $product = $product->findOrFail((int)$id);

            return response()->json([
                'success' => true,
                'data' => new ProductResource($product)
            ], 201);
        }
    }


    public function destroy($id)
    {
        if (Products::where('id', (int)$id)->count() <= 0) {
            return response()->json([
                'success' => false,
                'errors' => ['Id not found']
            ], 201);
        }

        Deliveries::where('product_id', '=', (int) $id)->delete();
        Purchase_items::where('product_id', '=', (int) $id)->delete();

        $product = Products::findOrFail($id);
        $product->delete();

        return response()->json([
            'success' => true,
        ], 201);
    }


}
