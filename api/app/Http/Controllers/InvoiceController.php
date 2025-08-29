<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\lpsp_credentials;
use App\Models\Plan;

class InvoiceController extends Controller
{
    public function getData(Request $request)
    {
        $client = Client::find($request->input('client_id'));
        $creds = lpsp_credentials::where('client_id', $client->id)->first();

        $plan = Plan::with([
            'client',
            'subplan.product',
            'product.processes.processList',
        ])->where('po_nr', $request->input('po_nr'))->first();

        return response()->json([
            'client' => $client,
            'creds' => $creds,
            'plan' => $plan
        ]);
    }

    public function getPlan(Request $request)
    {

        $search = $request->input('search');

        $query = Plan::with([
            'client',
            'subplan.product',
            'product.processes.processList',
        ]);

        if ($search != '') {
            $query->where('po_nr', 'like', '%' . $search . '%');
        }

        $plans = $query->get();

        return response()->json($plans);
    }
}
