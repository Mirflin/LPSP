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
use App\Models\File_list;
use App\Models\File;
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

        $client = Client::find($product->client_id);

        $materials = Product_material::where('product_id', $product->id)->get()->map(function($ref){
            return Material::find($ref->material_id);
        });

        $files = File_list::where('product_id', $product->id)->get()->map(function($ref){
            return File::find($ref->file_id);
        });

        $childs = Product_children::where('product_id', $product->id)->get()->map(function($ref){
            return Product::find($ref->children_product_id);
        });

        $processes = Process::where('product_id', $product->id)->get()->map(function($ref){
            $ref['name'] = Process_list::find($ref->process_id)->name;
            return $ref;
        });

        $newVal = [
            'id' => $product->id,
            'drawing_nr' => $product->drawing_nr,
            'part_nr' => $product->part_nr,
            'revision' => $product->revision,
            'description' => $product->description,
            'additional_info' => $product->additional_info,
            'client_id' => $product->client_id,
            'weight' => $product->weight,
            'created_at' => $product->created_at,
            'updated_at' => $product->updated_at,
            'materials' => $materials,
            'name' => $product->drawing_nr,
            'processes' => $processes,
            'client' => $client,
            'files' => $files,
            'childs' => $childs
        ];

        return $newVal;
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
            'count' => 'numeric',
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
            'count' => $validated['count'] ?? 1,
            'weight' => $validated['weight'] ?? null
        ]);

        if(!empty($validated['childs'])){
            foreach($validated['childs'] as $child){
                $childProduct = Product::find($child['children_product']['id']);
                if($childProduct){
                    Product_children::create([
                        'product_id' => $product->id,
                        'children_product_id' => $child['children_product']['id'],
                    ]);
                } else {
                    Log::warning("Child product ID {$child['children_product']['id']} does not exist.");
                }
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
                    'sub_process' => $process['subprocess'],
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
                $fType = 3;
                if($file['drawing'] == false && $file['bom'] == false){
                    $full_path = ('products/'.$validated['client']['name'].'/'.$validated['drawing_nr'].'/other'.'/');
                }elseif($file['drawing']){
                    $fType = 2;
                    $full_path = ('products/'.$validated['client']['name'].'/'.$validated['drawing_nr'].'/drawings'.'/');
                }elseif($file['bom']){
                    $fType = 1;
                    $full_path = ('products/'.$validated['client']['name'].'/'.$validated['drawing_nr'].'/BOM'.'/');
                }

                $filename = $uploadedFile->getClientOriginalName();

                Storage::disk('public')->putFileAs($full_path, $uploadedFile, $filename);

                //Storage::put($full_path, $uploadedFile);

                $fileDB = File::create([
                    'name' => $file['name'],
                    'type' => $fType,
                    'path' => ($full_path.$file['name'])
                ]);

                File_list::create([
                    'product_id' => $product->id,
                    'file_id' => $fileDB->id
                ]);

            }
            GoogleDrive::dispatch($validated['files'], ('orders/'.$validated['client']['name'].'/'.$validated['drawing_nr'].'/'));
        }

        $newProd = $this->getProductById($product->id);

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
        $process = Process_list::all();

        return response()->json(['message' => 'data fetched!', 'materials' => $materials, 'processes' => $process], 200);
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

    public function downloadImage(Request $request){


        $fullPath = storage_path('storage/' . $request->path);
        if (!file_exists($fullPath)) {
            return response()->json(['error' => 'File not found'], 404);
        }
        return response()->download($fullPath);
    }

    public function updateProduct(Request $request){
        $validated = $request->validate([
            'id' => 'required|numeric',
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

        $product = Product::where('drawing_nr', $validated['drawing_nr'])->get();
        $product->part_nr = $validated['part_nr'];
        $product->revision = $validated['revision'];
        $product->description = $validated['description'];
        $product->additional_info = $validated['additional_info'];
        $product->weight = $validated['weight'];
        $product->save();

        $files = File_list::where('product_id', $product->id)->get()->map(function($ref){
            return File::find($ref->file_id);
        });

        foreach($validated['files'] as $newFile){
            foreach($files as $file){
                if($file['id'] == $newFile['id']){
                    $file['type'] = $newFile['type'];
                }
            }
        }
        $files->save();

        $materials = Product_material::where('product_id', $product->id)->get()->map(function($ref){
            return Material::find($ref->material_id);
        });

        foreach($validated['materials'] as $newMaterial){
            foreach($materials as $material){
                if($material['id'] == $newMaterial['id']){
                    $material['name'] = $newMaterial['name'];
                }
            }
        }
        $materials->save();

        
    }

    public function getProduct(Request $request) {
        if(Auth::user()->permission != 1){
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'id' => 'required|numeric',
        ]);

        $query = Product::with([
            'client',                      
            'processes.processList',        
            'materials',
            'children.client',
            'children.processes.processList',
            'children.materials',
            'children.files',                    
            'children',                     
            'files',                        
        ]);

        $query->where('id', $validated['id']);
        Log::info($validated['id']);
        $product = $query->first();

        if(!$product){
            return response()->json([['message' => 'Product not found!']]);
        }

        return response()->json(['message' => 'Product founded', 'data' => $product]);

    }

    public function getProductbyName(Request $request) {
        if(Auth::user()->permission != 1){
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $query = Product::with([
            'client',                      
            'processes.processList',        
            'materials',
            'children.client',
            'children.processes.processList',
            'children.materials',
            'children.files',                   
            'children',                     
            'files',                        
        ]);
        $search = $request->input('drawing_nr', '');

        $query->where('drawing_nr', 'like', "%{$search}%");
        $product = $query->take(10)->get();

        if(!$product){
            return response()->json([['message' => 'Product not found!']]);
        }

        return response()->json(['message' => 'Product founded', 'data' => $product]);

    }

    public function getPlans(Request $request)
    {
        $perPage = $request->input('perPage', 10);
        $page = $request->input('page', 1);
        $sort = $request->input('sort', 'id');
        $direction = $request->input('direction', 'asc');
        $search = $request->input('search', '');
        $filters = $request->input('filters', []);

        $query = Plan::with([
            'client',
            'user',
            'product.processes.processList',
        ]);

        $map = [
            0 => 'Pending',
            1 => 'In Production',
            2 => 'Completed',
            3 => 'Cancelled',
        ];

        $reverseMap = array_change_key_case(array_flip($map), CASE_LOWER);

        $map = [
            0 => 'Not Outsourced',
            1 => 'Waiting Supplier',
            2 => 'Delivered',
            3 => 'Cancelled'
        ];

        $OutreverseMap = array_change_key_case(array_flip($map), CASE_LOWER);
        
        if ($search) {
            $query->where(function ($q) use ($search, $reverseMap, $OutreverseMap) {

                $q->where('po_nr', 'like', "%{$search}%")
                ->orWhere('po_date', 'like', "%{$search}%")
                ->orWhere('total', 'like', "%{$search}%")
                ->orWhere('invoice', 'like', "%{$search}%");


                $q->orWhereHas('user', fn($uq) => $uq->where('name', 'like', "%{$search}%"))
                ->orWhereHas('client', fn($cq) => $cq->where('name', 'like', "%{$search}%"))
                ->orWhereHas('product', fn($pq) => $pq->where('drawing_nr', 'like', "%{$search}%"));

                $searchLower = strtolower($search);
                foreach ($reverseMap as $label => $num) {
                    if (str_contains($label, $searchLower)) {
                        $q->orWhere('statuss', $num);
                    }
                }

                foreach ($OutreverseMap as $label => $num) {
                    if (str_contains($label, $searchLower)) {
                        $q->orWhere('outsource_statuss', $num);
                    }
                }
            });
        }

        if($filters){
            foreach ($filters as $column => $value) {
                if ($value === null || $value === '') continue;

                switch ($column) {
                    case 'user.last_name':
                        $query->whereHas('user', fn($uq) => $uq->where('last_name', 'like', "%{$value}%"));
                        break;

                    case 'client.name':
                        $query->whereHas('client', fn($cq) => $cq->where('name', 'like', "%{$value}%"));
                        break;

                    case 'product.drawing_nr':
                        $query->whereHas('product', fn($pq) => $pq->where('drawing_nr', 'like', "%{$value}%"));
                        break;

                    case 'statuss_label':
                        $val = strtolower($value);
                        if (isset($reverseStatusMap[$val])) {
                            $query->where('statuss', $reverseStatusMap[$val]);
                        }
                        break;

                    case 'outsource_statuss_label':
                        $val = strtolower($value);
                        if (isset($reverseOutsourceMap[$val])) {
                            $query->where('outsource_statuss', $reverseOutsourceMap[$val]);
                        }
                        break;

                    default:
                        $query->where($column, 'like', "%{$value}%");
                }
            }
        }

        $total = $query->count();

        $plans = $query->get();

        return response()->json([
            'data' => $plans,
            'total' => $total,
        ]);
    }

    public function getProducts(Request $request)
    {
        $perPage = $request->input('perPage', 10);
        $page = $request->input('page', 1);
        $sort = $request->input('sort', 'id');
        $direction = $request->input('direction', 'asc');
        $search = $request->input('search', '');

        $query = Product::with([
            'client',                      
            'processes.processList',        
            'materials',                   
            'children.client',
            'children.processes.processList',
            'children.materials',
            'children.files',                   
            'files',                        
        ]);
        
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                ->orWhere('drawing_nr', 'like', "%{$search}%")
                ->orWhere('part_nr', 'like', "%{$search}%")
                ->orWhere('revision', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhere('additional_info', 'like', "%{$search}%")
                ->orWhere('weight', 'like', "%{$search}%")
                ->orWhere('created_at', 'like', "%{$search}%")
                ->orWhere('updated_at', 'like', "%{$search}%")
                ->orWhereHas('client', fn($cq) => 
                    $cq->where('name', 'like', "%{$search}%")
                );
            });
        }

        $total = $query->count();

        $sortableColumns = [
            'id', 'drawing_nr', 'part_nr', 'revision',
            'description', 'additional_info', 'weight',
            'created_at', 'updated_at'
        ];

        if (in_array($sort, $sortableColumns)) {
            $query->orderBy($sort, $direction);
        } elseif ($sort === 'client.name') {
            $query->join('clients', 'products.client_id', '=', 'clients.id')
                ->orderBy('clients.name', $direction)
                ->select('products.*');
        }

        $products = $query->skip(($page - 1) * $perPage)
                      ->take($perPage)
                      ->get();

        return response()->json([
            'data' => $products,
            'total' => $total,
        ]);
    }

}