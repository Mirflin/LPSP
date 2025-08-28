<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Jobs\GoogleDriveRaw;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;
use App\Models\Plan;
use App\Models\SubPlan;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use App\Events\PlanUpdate;
use App\Events\NewPlan;

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
        $validated = $request->validate([
            'po_date' => 'required|string',
            'po_nr' => 'required|string',
            'client_id' => 'required|numeric',
            'product_id' => 'required|numeric',
            'price' => 'numeric',
            'total' => 'numeric',
            'childs' => 'array'
        ]);
        
        $extra_process = $request->input('extra_process', '');

        $isOutsourced = false;
        $product = Product::with([            
            'client',                      
            'processes.processList',        
            'materials',
            'children.client',
            'children.processes.processList',
            'children.materials',
            'children.files',                    
            'children',                     
            'files'           
        ])->find($validated['product_id']);
        
        foreach($product['processes'] as $process){
            if($process['processList']['name'] == 'Outsourcing'){
                $isOutsourced = true;
            }
        }

        $plan = Plan::create([
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

        foreach($validated['childs'] as $child){

            foreach($child['processes'] as $process){
                if($process['process_list']['name'] == 'Outsourcing'){
                    $isOutsourced = true;
                }
            }

            SubPlan::create([
                'plan_id' => $plan->id,
                'product_id' => $child['id'],
                'cost' => $child['cost'],
                'outsource_statuss' => $isOutsourced ? 1 : 0
            ]);   
        }

        $query = Plan::with([
            'client',
            'user',
            'subplan.product',
            'product.processes.processList',
        ])->find($plan->id);

        broadcast(new NewPlan($query))->toOthers();
        return response()->json(['message' => 'Plan created successfully!']);
    }

    public function produced(Request $request){
        $produced = $request->input('produced');
        $sended = $request->input('sended');
        $plan_id = $request->input('id');

        $plan = Plan::find($plan_id);

        $plan->produced = $produced;
        $plan->sended = $sended;

        $plan->save();

        return response()->json(['message' => 'Updated successfully']);
    }

    public function downloadFiles(Request $request)
    {
        $product = $request->input('product');
        $allFiles = $product['files'] ?? [];

        if (!empty($product['children'])) {
            foreach ($product['children'] as $child) {
                if (!empty($child['files'])) {
                    $allFiles = array_merge($allFiles, $child['files']);
                }
            }
        }

        $totalPages = 0;
        $lastFilePath = null;
        foreach ($allFiles as $file) {
            $path = Storage::disk('public')->path($file['path']);
            if (!file_exists($path) || filesize($path) === 0) continue;
            if (mime_content_type($path) !== 'application/pdf') continue;

            try {
                $pdfTmp = new Fpdi();
                $count = $pdfTmp->setSourceFile($path);
                $totalPages += $count;
                $lastFilePath = $path;
            } catch (\Exception $e) {
                continue;
            }
        }

        if ($totalPages === 1 && $lastFilePath) {
            return response()->file($lastFilePath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="single.pdf"'
            ]);
        }

        $pdf = new Fpdi();
        $this->mergePdfs($pdf, $allFiles);

        return response($pdf->Output('S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="merged.pdf"');
    }

    public function status(Request $request){
        $plan = Plan::find($request->input('id'));

        $map = [
            0 => 'Pending',
            1 => 'In Production',
            2 => 'Completed',
            3 => 'Cancelled',
            4 => 'Aproved'
        ];

        $reverseMap = array_flip($map);

        $map = [
            0 => 'Not Outsourced',
            1 => 'Waiting Supplier',
            2 => 'Delivered',
            3 => 'Outsource Cancelled'
        ];

        $OutreverseMap = array_flip($map);

        $plan->statuss = $reverseMap[$request->input('status')];
        $plan->outsource_statuss = $OutreverseMap[$request->input('outstatus')];


        $plan->save();

        $plan = Plan::with([
            'client',
            'user',
            'subplan.product',
            'product.processes.processList',
        ])->find($request->input('id'));

        broadcast(new PlanUpdate($plan))->toOthers();
        return response()->json(['message' => 'Plan updated successfully!']);
    }

    public function sub_status(Request $request){
        $subplan = SubPlan::find($request->input('subId'));

        $map = [
            0 => 'Pending',
            1 => 'In Production',
            2 => 'Completed',
            3 => 'Cancelled',
            4 => 'Aproved'
        ];

        $reverseMap = array_flip($map);

        $map = [
            0 => 'Not Outsourced',
            1 => 'Waiting Supplier',
            2 => 'Delivered',
            3 => 'Outsource Cancelled'
        ];

        $OutreverseMap = array_flip($map);

        $subplan->statuss = $reverseMap[$request->input('status')];
        $subplan->outsource_statuss = $OutreverseMap[$request->input('outsource_status')];
        $subplan->produced = $request->input('produced');


        $subplan->save();

        $plan = Plan::with([
            'client',
            'user',
            'subplan.product',
            'product.processes.processList',
        ])->find($request->input('id'));

        broadcast(new PlanUpdate($plan))->toOthers();
        return response()->json(['message' => 'Plan updated successfully!']);
    }

    public function downloadActual(Request $request){
        $plan_nr = $request->input('plan_nr');
        $path = Storage::disk('public')->path('plans/'.$plan_nr.'/plan-'.$plan_nr.'.pdf');

        return response()->download($path);
    }

    private function mergePdfs(Fpdi $pdf, array $files)
    {
        foreach ($files as $file) {
            $path = Storage::disk('public')->path($file['path']);

            // Ensure file is valid
            if (!file_exists($path) || filesize($path) === 0) continue;
            if (mime_content_type($path) !== 'application/pdf') continue;

            try {
                $pageCount = $pdf->setSourceFile($path);
                for ($i = 1; $i <= $pageCount; $i++) {
                    $tplIdx = $pdf->importPage($i);
                    if (!$tplIdx) continue;

                    $size = $pdf->getTemplateSize($tplIdx);
                    $orientation = ($size['width'] > $size['height']) ? 'L' : 'P';
                    $pdf->AddPage($orientation, [$size['width'], $size['height']]);
                    $pdf->useTemplate($tplIdx);
                }
            } catch (\Exception $e) {
                Log::error("PDF merge error: ".$e->getMessage());
            }
        }
    }

}
