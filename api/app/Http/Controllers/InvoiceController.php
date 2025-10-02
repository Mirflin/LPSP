<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\lpsp_credentials;
use App\Models\Plan;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Client;
use App\Models\ActionHistory;

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

        $query->where('statuss', 5);

        $plans = $query->get();

        return response()->json($plans);
    }

    public function print(Request $request){

        $client = Client::find($request->input('client_id'));

        $creds = lpsp_credentials::first();

        $pdf = Pdf::loadView('pdf.invoice', [
            'rows' => $request->input('info'), 'creds' => $creds, 'client' => $client, 'options' => $request->input('options'),
            'discountEur' => $request->input('discountEur'), 'sumWithoutVAT' => $request->input('sumWithoutVAT'),
            'vatSum' => $request->input('vatSum'), 'sumWithVAT' => $request->input('sumWithVAT'), 'words' => $request->input('words')
            ])
                  ->setPaper('a4', 'portrait');

        $pdfContent = $pdf->download();

        return $pdf->download('invoice.pdf');
    }
}
