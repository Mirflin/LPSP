<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\lpsp_credentials;
use Illuminate\Support\Facades\Log;
use App\Models\Plan;
use App\Models\Client;

class PlanConfirm extends Controller
{
    public function getData(Request $request){

        $client_id = $request->input('client_id');

        $data = [];

        $query = Plan::with([
            'client',
            'subplan.product',
            'product.processes.processList',
        ])->whereHas('client', function ($q) use ($client_id) {
            $q->where('id', $client_id);
        })->get();

        $totalPrice = 0;

        foreach($query as $plan){

            if(isset($plan->subplan)){
                foreach($plan->subplan as $sub){
                    Log::info($sub);
                    $temp = [
                        'po_nr' => $plan->po_nr,
                        'customer' => $plan->client->name,
                        'ship_date' => $plan->po_date,
                        'desc' => $sub->product->drawing_nr,
                        'rev' => $sub->product->revision,
                        'ammount' => ($sub->product->count*round($plan->total/$plan->price)),
                        'price' => $sub->cost,
                        'total' => $sub->cost*($sub->product->count*round($plan->total/$plan->price)),
                    ];
                    $totalPrice = $totalPrice + floatval($temp['total']);
                    array_push($data, $temp);
                }
            }

            $temp = [
                'po_nr' => $plan->po_nr,
                'customer' => $plan->client->name,
                'ship_date' => $plan->po_date,
                'desc' => $plan->product->drawing_nr,
                'rev' => $plan->product->revision,
                'ammount' => round($plan->total/$plan->price),
                'price' => $plan->price,
                'total' => $plan->total
            ];

            $totalPrice = $totalPrice + floatval($temp['total']);

            array_push($data, $temp);

        }

        return response()->json(['data' => $data, 'total' => $totalPrice]);
    }
}
