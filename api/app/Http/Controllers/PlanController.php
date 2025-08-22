<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Jobs\GoogleDriveRaw;
use Illuminate\Support\Facades\Storage;

class PlanController extends Controller
{
    public function download(Request $request)
    {

        $plan = $request->input('plan');
        $settings = $request->input('settings');

        $pdf = Pdf::loadView('pdf.plan', ['plan' => $plan, 'settings' => $settings])
                  ->setPaper('a4', 'portrait');
        
        $pdfContent = $pdf->download();
        $fileName = "plan-{$settings['po_nr']}.pdf";
        $filePath = "plans/{$settings['po_nr']}/{$fileName}";     
        
        Storage::disk('public')->put($filePath, $pdfContent);

        $google_path = ('plans/'.$settings['po_nr'].'/'.$fileName);

        GoogleDriveRaw::dispatch($filePath, $google_path);

        return $pdfContent;
    }
}
