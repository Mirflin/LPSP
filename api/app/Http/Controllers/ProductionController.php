<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Material;
use App\Models\Plan;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

class ProductionController extends Controller
{
    public function createMaterial(Request $request) {
        if(Auth::user()->permission != 1){
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
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

        return response()->json(['message' => 'data fetched!', 'materials' => $materials, 'plans' => $plans, 'products' => $products], 200);
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