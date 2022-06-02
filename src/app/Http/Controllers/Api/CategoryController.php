<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Categories;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
//use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{

    public function index(Request $request, $page = null)
    {
        if(!$page)
            return response()->json([
                'success' => true,
                'data' => CategoryResource::collection(Categories::all())
            ], 200);
        else {
            $limit = (int)$request->input('rowsPerPage');
            $limit = !in_array($limit, [30, 50, 100]) ? 30 : $limit;

            Paginator::currentPageResolver(function () use ($page) {
                return $page;
            });

            $categories = Categories::query();

            if ($request->input('search')) {
                $categories->where('category_name', 'LIKE', "%{$request->input('search')}%");
            }
            $categories->orderBy('created_at', 'desc');

            return response()->json([
                'success' => true,
                'data' => $categories->paginate($limit)
            ], 200);
        }
    }

    /**
     * Store a newly created resource in storage.
     * Створення сатегорії
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_name' => 'required|unique:categories',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->messages()
            ], 201);
        }
        else {
            return response()->json([
                'success' => true,
                'data' => Categories::create($request->all())
            ], 201);
        }
    }

    /**
     * Display the specified resource.
     * Показати одну категорію
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            return response()->json([
                'success' => true,
                'data' => new CategoryResource(Categories::findOrFail($id))
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response([
                'success' => false,
                'errors' => ['404 not found']
            ], 404);
        }
    }


    /**
     * Update the specified resource in storage.
     * Оновити категорію
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'category_name' => 'required|unique:categories,category_name,'  . (int) $id,
        ]);

        if(Categories::where('id', (int) $id)->count() <=0)
        {
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
        }
        else {
            $category = Categories::findOrFail($id);
            $category->update($request->all());

            return response()->json([
                'success' => true,
                'data' => new CategoryResource($category)
            ], 200);
        }


    }

    /**
     * Remove the specified resource from storage.
     * Видалити категорію
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(Categories::where('id', (int) $id)->count() <=0)
        {
            return response()->json([
                'success' => false,
                'errors' => ['Id not found']
            ], 201);
        }

        Products::where('category_id', '=', (int) $id)->delete();

        $category = Categories::findOrFail($id);
        $category->delete();

        return response()->json([
            'success' => true,
        ], 201);
    }
}
