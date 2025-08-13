<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Material;
use App\Models\Plan;
use App\Models\Product;
use App\Models\Process_list;
use App\Models\Client;
use Illuminate\Support\Facades\Log;
use App\Models\Product_material;
use App\Models\Product_children;
use App\Models\Process;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\UploadedFile;
use Yaza\LaravelGoogleDriveStorage\Gdrive;
use Str;
use App\Jobs\GoogleDrive;
use App\Events\NewProduct;

class ProductionController extends Controller
{
    private function getProductById($id) {
        if(Auth::user()->permission != 1){
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $product = Product::find($id);

        $product->materials = [];
        $product->name = $product->drawing_nr;

        $client = Client::find($product->client_id);
        $product->client = $client;

        $materialRefs = Product_material::where('product_id', $product->id)->get();

        $materials = [];
        foreach ($materialRefs as $ref) {
            $material = Material::find($ref->material_id);
            if ($material) {
                $materials[] = $material;
            }
        }
        $product->setRelation('materials', collect($materials));

        return $product;
    }

    public function createProduct(Request $request) {
        $validated = $request->validate([
            'drawing_nr' => 'required|string',
            'part_nr' => 'required|string',
            'revision' => 'nullable|string',
            'description' => 'nullable|string',
            'additional_info' => 'nullable|string',
            'client' => 'nullable|array',
            'files' => 'nullable|array',
            'materials' => 'required|array',
            'childs' => 'nullable',
            'processes' => 'required|array',
            'weight' => 'nullable|numeric'
        ]);

        $product = Product::create([
            'drawing_nr' => $validated['drawing_nr'],
            'part_nr' => $validated['part_nr'],
            'revision' => $validated['revision'] ?? null,
            'description' => $validated['description'] ?? null,
            'additional_info' => $validated['additional_info'] ?? null,
            'client_id' => $validated['client']['id'] ?? null,
            'weight' => $validated['weight'] ?? null
        ]);

        if(!empty($validated['childs'])){
            foreach($validated['childs'] as $child){
                Product_children::create([
                    'product_id' => $product->id,
                    'children_product_id' => $child['id'],
                ]);
            }
        }

        if (!empty($validated['materials'])) {
            foreach ($validated['materials'] as $material) {

                $materialName = is_array($material) ? $material['material'] : $material;

                $mat = Material::firstOrCreate(['name' => $materialName]);

                Product_material::create([
                    'product_id' => $product->id,
                    'material_id' => $mat->id
                ]);
            }
        }

        if($validated['processes']){
            foreach($validated['processes'] as $process){
                Process::create([
                    'place' => $process['id'],
                    'sub_process' => $process['id'],
                    'product_id' => $product->id,
                    'process_id' => $process['process']['id']
                ]);
            }
        }

        if (!empty($validated['files'])) {
            foreach ($validated['files'] as $file) {
                $base64_string = preg_replace('#^data:.*;base64,#', '', $file['base64']);
                $decoded = base64_decode($base64_string);

                $tmpFilePath = tempnam(sys_get_temp_dir(), 'upload_');

                file_put_contents($tmpFilePath, $decoded);

                $uploadedFile = new UploadedFile(
                    $tmpFilePath,
                    $file['name'],
                    $file['type'],
                    null,
                    true
                );

                $full_path = ('products/'.$validated['client']['name'].'/'.$validated['drawing_nr'].'/');

                if($file['drawing'] == false && $file['bom'] == false){
                    $full_path = ('products/'.$validated['client']['name'].'/'.$validated['drawing_nr'].'/other'.'/');
                }elseif($file['drawing']){
                    $full_path = ('products/'.$validated['client']['name'].'/'.$validated['drawing_nr'].'/drawings'.'/');
                }elseif($file['bom']){
                    $full_path = ('products/'.$validated['client']['name'].'/'.$validated['drawing_nr'].'/BOM'.'/');
                }

                Storage::put($full_path, $uploadedFile);
            }
            GoogleDrive::dispatch($validated['files'], ($validated['client']['name'].'/'.$validated['drawing_nr'].'/'));
        }

        $newProd = $this->getProductById(intval($product->id));

        Log::info($newProd);

        broadcast(new NewProduct($newProd))->toOthers();

        return response()->json(['message'=>'Product created!', 201]);
    }

    public function token(){
        $client_id=\Config('services.google.client_id');
        $client_secret=\Config('services.google.client_secret');
        $refresh_token=\Config('services.google.refresh_token');

        $response = Http::post('https://oauth2.googleapis.com/token', [
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'refresh_token' => $refresh_token,
            'grant_type' => 'refresh_token', 
        ]);

        $accessToken = json_decode((string)$response->getBody(), true)['access_token'];
        return $accessToken;
    }

    public function createMaterial(Request $request) {
        if(Auth::user()->permission != 1){
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'max:255',
        ]);

        Material::create($validated);
        return response()->json(['message'=>'Materail created!', 201]);
    }

    public function updateMaterial(Request $request) {
        if(Auth::user()->permission != 1){
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'id' => 'required|integer',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $material = Material::find($validated['id']);
        if($material){
            $material->name = $validated['name'];
            $material->description = $validated['description'];
            $material->save();
        }else{
            return response()->json(['message' => 'Material not found!'], 404);
        }
    }

    public function getProductionData() {
        if(Auth::user()->permission != 1){
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $materials = Material::all();
        $plans = Plan::all();
        $products = Product::all();
        $process = Process_list::all();

        foreach($products as $product){
            $product->materials = [];
            $product->name = $product->drawing_nr;

            $client = Client::find($product->client_id);
            $product->client = $client;


            $materialRefs = Product_material::where('product_id', $product->id)->get();

            $materials = [];
            foreach ($materialRefs as $ref) {
                $material = Material::find($ref->material_id);
                if ($material) {
                    $materials[] = $material;
                }
            }
            $product->setRelation('materials', collect($materials));
        }

        return response()->json(['message' => 'data fetched!', 'materials' => $materials, 'plans' => $plans, 'products' => $products, 'processes' => $process], 200);
    }

    public function deleteMaterial($id){
        if(Auth::user()->permission != 1){
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $material = Material::find($id);

        if (!$material) {
            return response()->json(['message' => 'Material not found'], 404);
        }

        $material->delete();

        return response()->json(['message' => 'Material deleted successfully'], 204);
    }

}