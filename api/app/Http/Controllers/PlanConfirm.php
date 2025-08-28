<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\lpsp_credentials;
use App\Models\Plan;
use App\Models\Client;

class PlanConfirm extends Controller
{
    public function getData($client_id){

        $data = [];

        $query = Plan::with([
            'client',
            'subplan.product',
            'product.processes.processList',
        ])->where('client.id', $client_id)->get();

        foreach($query as $plan){
            $temp = [
                'po_nr' => $plan->po_nr,
                'customer' => $plan->client->name,
                'ship_date' => $plan->po_date,
                'desc' => $plan->product->description,
                'rev' => $plan->product->revision,
                'ammount' => ($plan->total/$plan->price),
                'price' => $plan->price,
                'total' => $plan->total
            ];

            array_push($data, $temp);

            foreach($plan->subplan as $sub){
                $temp = [
                    'po_nr' => $plan->po_nr,
                    'customer' => $sub->client->name,
                    'ship_date' => $plan->po_date,
                    'desc' => $sub->product->description,
                    'rev' => $sub->product->revision,
                    'ammount' => ($sub->total/$sub->price),
                    'price' => $sub->price,
                    'total' => $sub->total
                ];

                array_push($data, $temp);
            }
        }

        return response()->json(['data' => $data]);
    }
}
