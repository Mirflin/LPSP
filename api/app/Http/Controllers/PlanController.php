<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Jobs\GoogleDriveRaw;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;
use App\Models\Plan;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

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

    private function getFileByName($path){
        if (!Storage::disk('public')->exists($path)) {
            return false;
        }

        return Storage::disk('public')->get($path);
    }

    public function create(Request $request) {
        Log::info($request);
        $validated = $request->validate([
            'po_date' => 'required|string',
            'po_nr' => 'required|string',
            'client_id' => 'required|numeric',
            'product_id' => 'required|numeric',
            'price' => 'numeric',
            'total' => 'numeric',
        ]);
        
        $extra_process = $request->input('extra_process', '');

        $isOutsourced = false;
        $product = Product::with([            
            'processes.processList',                
        ])->find($validated['product_id']);
        
        foreach($product['processes'] as $process){
            if($process['processList']['name'] == 'Outsourcing'){
                $isOutsourced = true;
            }
        }

        Plan::create([
            'po_date' => $validated['po_date'],
            'po_nr' => $validated['po_nr'],
            'client_id' => $validated['client_id'],
            'product_id' => $validated['product_id'],
            'price' => $validated['price'],
            'total' => $validated['total'],
            'extra_process' => $validated['extra_process'] ?? '',
            'user_id' => Auth::user()->id,
            'outsource_statuss' => $isOutsourced ? 1 : 0
        ]);

        return response()->json(['message' => 'Plan created successfully!']);
    }

    public function downloadFiles(Request $request)
    {
        $product = $request->input('product');
        $pdf = new Fpdi();

        $this->mergePdfs($pdf, $product['files'] ?? []);

        if (!empty($product['children'])) {
            foreach ($product['children'] as $child) {
                $this->mergePdfs($pdf, $child['files'] ?? []);
            }
        }

        $pdfContent = $pdf->Output('S');

        return response($pdfContent)
        ->header('Content-Type', 'application/pdf')
        ->header('Content-Disposition', 'inline; filename="merged.pdf"');
    }

    private function mergePdfs(Fpdi $pdf, array $files)
    {
        foreach ($files as $file) {
            $path = Storage::disk('public')->path($file['path']);
            if (!file_exists($path)) continue;
            $mime = mime_content_type($path);
            if ($mime !== 'application/pdf') continue;

            $pageCount = $pdf->setSourceFile($path);
            for ($i = 1; $i <= $pageCount; $i++) {
                $tplIdx = $pdf->importPage($i);
                $size = $pdf->getTemplateSize($tplIdx);
                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($tplIdx);
            }
        }
    }

}
