<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function download(Request $request)
    {


        $pdf = Pdf::loadView('pdf.plan', ['plan' => $request->input('plan'), 'settings' => $request->input('settings')])
                  ->setPaper('a4', 'portrait');

        return $pdf->download("plan-{$request->input('plan')['id']}.pdf");
    }
}
