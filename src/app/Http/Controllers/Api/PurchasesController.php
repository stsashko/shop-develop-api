<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PurchasesResource;
use Illuminate\Http\Request;
use App\Models\Purchases;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Validator;


class PurchasesController extends Controller
{

    public function index(Request $request, $page = null)
    {
        if(!$page)
            return response()->json([
                'success' => true,
                'data' => PurchasesResource::collection(Purchases::all())
            ], 200);
        else {
            $limit = (int)$request->input('rowsPerPage');
            $limit = !in_array($limit, [30, 50, 100]) ? 30 : $limit;

            Paginator::currentPageResolver(function () use ($page) {
                return $page;
            });

            $purchases = Purchases::query();
            $purchases->select('purchases.*', DB::raw('CONCAT(customers.customer_fname, " ",  customers.customer_lname) as customer_name'), 'stores.store_name');
            $purchases->leftJoin('customers', 'customers.id', '=', 'purchases.customer_id');
            $purchases->leftJoin('stores', 'stores.id', '=', 'purchases.store_id');

            if ($request->input('customer_id'))
                $purchases->where('purchases.customer_id', '=', (int)$request->input('customer_id'));

            if ($request->input('store_id'))
                $purchases->where('purchases.store_id', '=', (int)$request->input('store_id'));

            if ($request->input('date')) {
                $date = explode(' - ', $request->input('date'));

                $date = array_map(function ($item) {
                    return trim(str_replace('/', '-', $item));
                }, $date);

                if (!empty($date[0]))
                    $purchases->where('purchases.purchase_date', '>=', $date[0]);

                if (isset($date[1]) && $date[1] != '')
                    $purchases->where('purchases.purchase_date', '<=', $date[1]);
            }

            $purchases->orderBy('purchases.created_at', 'desc');

            return response()->json([
                'success' => true,
                'data' => $purchases->paginate($limit)
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
            'customer_id' => 'required|integer',
            'store_id' => 'required|integer',
            'purchase_date' => 'required|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->messages()
            ], 201);
        }
        else {
            $create = [
                'customer_id' => (int)$request->customer_id,
                'store_id' => (int)$request->store_id,
                'purchase_date' => $request->input('purchase_date'),
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ];

            $id = Purchases::insertGetId($create);

            $purchase = Purchases::select('purchases.*', DB::raw('CONCAT(customers.customer_fname, " ",  customers.customer_lname) as customer_name'), 'stores.store_name');
            $purchase->leftJoin('customers', 'customers.id', '=', 'purchases.customer_id');
            $purchase->leftJoin('stores', 'stores.id', '=', 'purchases.store_id');
            $purchase = $purchase->findOrFail((int)$id);

            return response()->json([
                'success' => true,
                'data' => new PurchasesResource($purchase)
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
            $purchase = Purchases::select('purchases.*', DB::raw('CONCAT(customers.customer_fname, " ",  customers.customer_lname) as customer_name'), 'stores.store_name');
            $purchase->leftJoin('customers', 'customers.id', '=', 'purchases.customer_id');
            $purchase->leftJoin('stores', 'stores.id', '=', 'purchases.store_id');

            return response()->json([
                'success' => true,
                'data' => new PurchasesResource($purchase->findOrFail($id))
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
            'customer_id' => 'required|integer',
            'store_id' => 'required|integer',
//            'purchase_date' => 'required|date_format:Y-m',
            'purchase_date' => 'required|date_format:Y-m-d',
        ]);

        if(Purchases::where('id', (int) $id)->count() <=0)
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
            $insert = [
                'customer_id' => (int)$request->customer_id,
                'store_id' => (int)$request->store_id,
                'purchase_date' => $request->input('purchase_date'),
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ];

            $purchase = Purchases::findOrFail((int)$id);
            $purchase->update($insert);

            $purchase = Purchases::select('purchases.*', DB::raw('CONCAT(customers.customer_fname, " ",  customers.customer_lname) as customer_name'), 'stores.store_name');
            $purchase->leftJoin('customers', 'customers.id', '=', 'purchases.customer_id');
            $purchase->leftJoin('stores', 'stores.id', '=', 'purchases.store_id');
            $purchase = $purchase->findOrFail((int)$id);

            return response()->json([
                'success' => true,
                'data' => new PurchasesResource($purchase)
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
        if(Purchases::where('id', (int) $id)->count() <=0)
        {
            return response()->json([
                'success' => false,
                'errors' => ['Id not found']
            ], 201);
        }

        $delivery = Purchases::findOrFail($id);
        $delivery->delete();

        return response()->json([
            'success' => true,
        ], 201);
    }


    public function chart()
    {
        $purchases = Purchases::select(
            DB::raw('from_unixtime(UNIX_TIMESTAMP(purchases.purchase_date),"%b %y") as `date`'),
            DB::raw('SUM(purchase_items.product_count) as `Purchases`'),
            DB::raw('ROUND(SUM(purchase_items.product_price), 2) as `Total`')
        );
        $purchases->leftJoin('purchase_items', 'purchase_items.purchase_id', '=', 'purchases.id');
        $purchases->whereRaw('purchases.purchase_date > DATE_SUB(now(), INTERVAL 11 MONTH)');
        $purchases->groupByRaw('`date`');
        $purchases->orderByRaw('purchases.purchase_date ASC');
        $purchases = $purchases->get();

        $purchases = array_map(function ($item){
            return [
                'date' => $item['date'],
                'Purchases' => (int) $item['Purchases'],
                'Total' => (float) $item['Total']
            ];
        }, (array) $purchases->toArray());

//        PurchasesResource::collection($purchases)

        return response()->json([
            'success' => true,
            'data' =>  $purchases
        ], 200);
    }


}
