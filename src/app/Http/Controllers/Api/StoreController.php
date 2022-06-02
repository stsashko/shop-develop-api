<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StoreResource;
use App\Models\Deliveries;
use App\Models\Stores;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Validator;


class StoreController extends Controller
{
    public function index(Request $request, $page = null)
    {
        if(!$page)
            return response()->json([
                'success' => true,
                'data' => StoreResource::collection(Stores::all())
            ], 200);
        else {
            $limit = (int)$request->input('rowsPerPage');
            $limit = !in_array($limit, [30, 50, 100]) ? 30 : $limit;

            Paginator::currentPageResolver(function () use ($page) {
                return $page;
            });

            $stores = Stores::query();

            if ($request->input('search')) {
                $stores->where('store_name', 'LIKE', "%{$request->input('search')}%");
            }

            $stores->orderBy('stores.created_at', 'desc');
//            $stores->select('stores.*');

            return response()->json([
                'success' => true,
                'data' => $stores->paginate($limit)
            ], 200);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'store_name' => 'required'
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
                'data' => Stores::create($request->all())
            ], 201);
        }
    }

    public function show($id)
    {
        try {
            return response()->json([
                'success' => true,
                'data' => new StoreResource(Stores::findOrFail($id))
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
            'store_name' => 'required'
        ]);

        if(Stores::where('id', (int) $id)->count() <=0)
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
            $stores = Stores::findOrFail($id);
            $stores->update($request->all());
            return response()->json([
                'success' => true,
                'data' => new StoreResource($stores)
            ], 200);
        }
    }

    public function destroy($id)
    {
        if(Stores::where('id', (int) $id)->count() <=0)
        {
            return response()->json([
                'success' => false,
                'errors' => ['Id not found']
            ], 201);
        }

        Deliveries::where('store_id', '=', (int) $id)->delete();

        $stores = Stores::findOrFail($id);
        $stores->delete();

        return response()->json([
            'success' => true,
        ], 201);
    }

}
