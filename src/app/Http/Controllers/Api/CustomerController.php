<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Models\Customers;
use App\Models\Purchases;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class CustomerController extends Controller
{
    public function index(Request $request, $page = null)
    {
        if (!$page)
            return response()->json([
                'success' => true,
                'data' => CustomerResource::collection(Customers::all())
            ], 200);
        else {
            $limit = (int)$request->input('rowsPerPage');
            $limit = !in_array($limit, [30, 50, 100]) ? 30 : $limit;

            Paginator::currentPageResolver(function () use ($page) {
                return $page;
            });

            $customers = Customers::query();
            if ($request->input('search')) {
                $search = $request->input('search');
                $customers->Where(function ($query) use ($search) {
                    $query->where('customer_fname', 'LIKE', "%{$search}%");
                    $query->orWhere('customer_lname', 'LIKE', "%{$search}%");
                    $query->orWhere(DB::raw('CONCAT(customer_fname, " ", customer_lname)'), 'LIKE', "%{$search}%");
                });
            }

            $customers->orderBy('created_at', 'desc');

            return response()->json([
                'success' => true,
                'data' => $customers->paginate($limit)
            ], 200);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_fname' => 'required',
            'customer_lname' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->messages()
            ], 400);
        } else {
            return response()->json([
                'success' => true,
                'data' => Customers::create($request->all())
            ], 201);
        }
    }

    public function show($id)
    {
        try {
            return response()->json([
                'success' => true,
                'data' => new CustomerResource(Customers::findOrFail($id))
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
            'customer_fname' => 'required',
            'customer_lname' => 'required',
        ]);

        if (Customers::where('id', (int)$id)->count() <= 0) {
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
            $customer = Customers::findOrFail($id);
            $customer->update($request->all());

            return response()->json([
                'success' => true,
                'data' => new CustomerResource($customer)
            ], 200);
        }
    }

    public function destroy($id)
    {
        if (Customers::where('id', (int)$id)->count() <= 0) {
            return response()->json([
                'success' => false,
                'errors' => ['Id not found']
            ], 201);
        }

        Purchases::where('customer_id', '=', (int) $id)->delete();

        $customer = Customers::findOrFail($id);
        $customer->delete();

        return response()->json([
            'success' => true,
        ], 201);
    }


    public function customers_find(Request $request)
    {
        if ($request->input('search') && strlen($request->input('search')) >= 3)
        {
            $customers = Customers::query();
            $customers->select(DB::raw('CONCAT(customers.customer_fname, " ",  customers.customer_lname) as customer_name'), 'customers.id as customer_id');

            if($request->input('search'))
            {
                $customers->where(DB::raw('CONCAT(customers.customer_fname, " ",  customers.customer_lname)'), 'LIKE', "%{$request->input('search')}%");
            }

            return response()->json([
                'success' => true,
                'data' => $customers->get()
            ], 200);
        }
        else {
            return response()->json([
                'success' => false,
                'errors' =>  ['Not found']
            ], 201);
        }
    }

}
