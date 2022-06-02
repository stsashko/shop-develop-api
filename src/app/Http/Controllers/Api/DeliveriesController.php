<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DeliveriesResource;
use Illuminate\Http\Request;
use App\Models\Deliveries;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Validator;


class DeliveriesController extends Controller
{

    public function index(Request $request, $page = null)
    {
        if (!$page)
            return response()->json([
                'success' => true,
                'data' => DeliveriesResource::collection(Deliveries::all())
            ], 200);
        else {
            $limit = (int)$request->input('rowsPerPage');
            $limit = !in_array($limit, [30, 50, 100]) ? 30 : $limit;

            Paginator::currentPageResolver(function () use ($page) {
                return $page;
            });

            $deliveries = Deliveries::query();
            $deliveries->select('deliveries.*', 'products.product_name', 'stores.store_name');
            $deliveries->leftJoin('products', 'products.id', '=', 'deliveries.product_id');
            $deliveries->leftJoin('stores', 'stores.id', '=', 'deliveries.store_id');

            if ($request->input('product_name'))
                $deliveries->where('products.product_name', '=', $request->input('product_name'));

            if ($request->input('store_id'))
                $deliveries->where('deliveries.store_id', '=', (int)$request->input('store_id'));

            if ($request->input('date')) {
                $date = explode(' - ', $request->input('date'));

                $date = array_map(function ($item) {
                    return trim(str_replace('/', '-', $item));
                }, $date);

                if (!empty($date[0]))
                    $deliveries->where('deliveries.delivery_date', '>=', $date[0]);

                if (isset($date[1]) && $date[1] != '')
                    $deliveries->where('deliveries.delivery_date', '<=', $date[1]);
            }

            $deliveries->orderBy('deliveries.created_at', 'desc');

            return response()->json([
                'success' => true,
                'data' => $deliveries->paginate($limit)
            ], 200);
        }
    }

    /**
     * Store a newly created resource in storage.
     * Створення сатегорії
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer',
            'store_id' => 'required|integer',
            'delivery_date' => 'required|date_format:Y-m-d',
            'product_count' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->messages()
            ], 201);
        } else {
            $create = [
                'product_id' => (int)$request->product_id,
                'store_id' => (int)$request->store_id,
                'delivery_date' => $request->input('delivery_date'),
                'product_count' => (int)$request->product_count,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ];

            $id = Deliveries::insertGetId($create);

            $delivery = Deliveries::select('deliveries.*', 'products.product_name', 'stores.store_name');
            $delivery->leftJoin('products', 'products.id', '=', 'deliveries.product_id');
            $delivery->leftJoin('stores', 'stores.id', '=', 'deliveries.store_id');
            $delivery = $delivery->findOrFail((int)$id);

            return response()->json([
                'success' => true,
                'data' => $delivery
            ], 201);
        }
    }

    /**
     * Display the specified resource.
     * Показати одну категорію
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $delivery = Deliveries::select('deliveries.*', 'products.product_name', 'stores.store_name');
            $delivery->leftJoin('products', 'products.id', '=', 'deliveries.product_id');
            $delivery->leftJoin('stores', 'stores.id', '=', 'deliveries.store_id');

            return response()->json([
                'success' => true,
                'data' => new  DeliveriesResource($delivery->findOrFail($id))
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
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer',
            'store_id' => 'required|integer',
            'delivery_date' => 'required|date_format:Y-m-d',
            'product_count' => 'required|integer',
        ]);

        if (Deliveries::where('id', (int)$id)->count() <= 0) {
            return response()->json([
                'success' => false,
                'errors' => ['Id not found']
            ], 400);
        }

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->messages()
            ], 400);
        } else {
            $insert = [
                'product_id' => (int)$request->product_id,
                'store_id' => (int)$request->store_id,
                'delivery_date' => $request->input('delivery_date'),
                'product_count' => (int)$request->product_count,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ];

            $delivery = Deliveries::findOrFail((int)$id);
            $delivery->update($insert);

            $delivery = Deliveries::select('deliveries.*', 'products.product_name', 'stores.store_name');
            $delivery->leftJoin('products', 'products.id', '=', 'deliveries.product_id');
            $delivery->leftJoin('stores', 'stores.id', '=', 'deliveries.store_id');
            $delivery = $delivery->findOrFail((int)$id);

            return response()->json([
                'success' => true,
                'data' => new DeliveriesResource($delivery)
            ], 200);
        }

    }

    /**
     * Remove the specified resource from storage.
     * Видалити категорію
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Deliveries::where('id', (int)$id)->count() <= 0) {
            return response()->json([
                'success' => false,
                'errors' => ['Id not found']
            ], 201);
        }

        $delivery = Deliveries::findOrFail($id);
        $delivery->delete();

        return response()->json([
            'success' => true,
        ], 201);
    }


    public function chart()
    {
        $deliveries = Deliveries::select(
            DB::raw('SUM(deliveries.product_count) as `Products`'),
            DB::raw('from_unixtime(UNIX_TIMESTAMP(deliveries.created_at),"%b %y") as `date`')
        );
//        $deliveries->whereRaw('purchases.purchase_date > DATE_SUB(now(), INTERVAL 11 MONTH)');
        $deliveries->groupByRaw('`date`');
        $deliveries->orderByRaw('deliveries.created_at ASC');
        $deliveries = $deliveries->get();

        $deliveries = array_map(function ($item) {
            return [
                'Products' => (int)$item['Products'],
                'date' => $item['date']
            ];
        }, (array)$deliveries->toArray());


        return response()->json([
            'success' => true,
            'data' => $deliveries
        ], 200);
    }


}
