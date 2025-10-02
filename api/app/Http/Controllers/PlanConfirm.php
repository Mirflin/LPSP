<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\lpsp_credentials;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Plan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\Client;
use App\Models\ActionHistory;

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
        })->where('statuss', '!=', 4)->get();

        $totalPrice = 0;

        foreach($query as $plan){

            if(isset($plan->subplan)){
                foreach($plan->subplan as $sub){

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

    public function completedPlans(Request $request){

        if(Auth::user()->permission != 1){
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $perPage = $request->input('perPage', 10);
        $page = $request->input('current_page', 1);
        $search = $request->input('search', '');

        $query = Plan::with([
            'client',
            'subplan.product',
            'product.processes.processList',
        ])->where('statuss', 2);

        if(!empty($search)){
            $query->where(function($q) use ($search){
                $q->where('po_nr', 'like', '%'.$search.'%')
                  ->orWhereHas('client', function($q2) use ($search){
                      $q2->where('name', 'like', '%'.$search.'%');
                  });
            });
        }

        $history = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json($history);
    }

    public function print(Request $request)
    {
        $client_id = $request->input('client_id');

        $client = Client::find($client_id);

        if (!$client) {
            return response()->json(['error' => 'Client not found'], 404);
        }

        $query = Plan::with([
            'client',
            'subplan.product',
            'product.processes.processList',
        ])->whereHas('client', function ($q) use ($client_id) {
            $q->where('id', $client_id);
        });

        

        $plans = $query->where('statuss', 0)
               ->take(25)
               ->get();

        foreach($plans as $plan){
            $plan->statuss = 6;
            $plan->save();
        }

        $rows = [];
        $totalPrice = 0;

        foreach ($plans as $plan) {

            $totalPrice += $plan->total;

            foreach($plan->subplan as $child){
                $rows[] = [
                    'po_nr' => $plan->po_nr,
                    'customer' => $plan->client->name,
                    'ship_date' => $plan->po_date,
                    'desc' => $child->product->drawing_nr,
                    'rev' => $child->product->revision,
                    'ammount' => $child->cost,
                    'price' => $child->cost,
                    'total' => $child->cost*($child->product->count*round($plan->total/$plan->price)),
                ];

                $totalPrice += $child->cost*($child->product->count*round($plan->total/$plan->price));
            }
            
            $rows[] = [
                'po_nr' => $plan->po_nr,
                'customer' => $plan->client->name,
                'ship_date' => $plan->po_date,
                'desc' => $plan->product->drawing_nr,
                'rev' => $plan->product->revision,
                'ammount' => round($plan->total / $plan->price),
                'price' => $plan->price,
                'total' => $plan->total
            ];
        }

        $creds = lpsp_credentials::first();

        if (!$creds) {
            return response()->json(['error' => 'Credentials not found'], 404);
        }

        $pdf = Pdf::loadView('pdf.plan_confirmation', ['rows' => $rows, 'creds' => $creds, 'client' => $client, 'totalPrice' => $totalPrice])
                  ->setPaper('a4', 'portrait');
        
        $pdf->getDomPDF()->set_option("isRemoteEnabled", true);
        $pdf->getDomPDF()->set_option("enable_font_subsetting", true);
        
        $pdfContent = $pdf->download();

        ActionHistory::create([
            'user_id' => Auth::user()->id,
            'action' => 'Printed plan confirmation pdf'
        ]);

        Storage::put('public/pdf/plan_confirmation.pdf', $pdfContent);

        return $pdf->download('plan_confirmation.pdf');
    }
}
