<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ManufacturerResource;
use App\Models\Manufacturers;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Validator;

class ManufacturerController extends Controller
{
    public function index(Request $request, $page = null)
    {
        if (!$page)
            return response()->json([
                'success' => true,
                'data' => ManufacturerResource::collection(Manufacturers::all())
            ], 200);
        else {
            $limit = (int)$request->input('rowsPerPage');
            $limit = !in_array($limit, [30, 50, 100]) ? 30 : $limit;

            Paginator::currentPageResolver(function () use ($page) {
                return $page;
            });

            $manufacturers = Manufacturers::query();
            if ($request->input('search')) {
                $manufacturers->where('manufacturer_name', 'LIKE', "%{$request->input('search')}%");
            }
            $manufacturers->orderBy('created_at', 'desc');

            return response()->json([
                'success' => true,
                'data' => $manufacturers->paginate($limit)
            ], 200);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'manufacturer_name' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->messages()
            ], 201);
        } else {
            return response()->json([
                'success' => true,
                'data' => Manufacturers::create($request->all())
            ], 201);
        }
    }

    public function show($id)
    {
        try {
            return response()->json([
                'success' => true,
                'data' => new ManufacturerResource(Manufacturers::findOrFail($id))
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response([
                'success' => false,
                'errors' => ['404 not found']
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'manufacturer_name' => 'required'
        ]);

        if(Manufacturers::where('id', (int) $id)->count() <=0)
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
            $manufacturer = Manufacturers::findOrFail($id);
            $manufacturer->update($request->all());

            return response()->json([
                'success' => true,
                'data' => new ManufacturerResource($manufacturer)
            ], 200);
        }
    }

    public function destroy($id)
    {
        if(Manufacturers::where('id', (int) $id)->count() <=0)
        {
            return response()->json([
                'success' => false,
                'errors' => ['Id not found']
            ], 400);
        }

        $manufacturer = Manufacturers::findOrFail($id);
        $manufacturer->delete();

        return response()->json([
            'success' => true,
        ], 201);
    }
}
