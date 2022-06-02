<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Deliveries;
use App\Models\Purchases;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;


class StatisticsController extends Controller
{
    public function basic()
    {
        $deliveries = Deliveries::select(
            DB::raw('SUM(product_count) as `deliveries_total`')
        );
        $deliveries->whereRaw('created_at > DATE_SUB(now(), INTERVAL 2 MONTH)');
        $deliveries = $deliveries->first()->toArray();

        $purchases = Purchases::select(
            DB::raw('SUM(purchase_items.product_count) as `purchases_total`')
        );
        $purchases->leftJoin('purchase_items', 'purchase_items.purchase_id', '=', 'purchases.id');
        $purchases->whereRaw('purchases.purchase_date > DATE_SUB(now(), INTERVAL 2 MONTH)');
        $purchases = $purchases->first()->toArray();

        return response()->json([
            'success' => true,
            'data' => array_merge((isset($deliveries['deliveries_total']) ? $deliveries : ['deliveries_total' => 0]), (isset($purchases['purchases_total']) ? $purchases : ['purchases_total' => 0]))
        ], 200);
    }
}
