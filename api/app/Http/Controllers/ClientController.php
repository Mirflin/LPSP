<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    function getClients(){
        if(Auth::user()->permission != 1){
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $clients = Client::all();

        if ($clients->isEmpty()) {
            return response()->json(['message' => 'No clients found'], 404);
        }

        return response()->json($clients, 200);
    }

    function getClientsWithParams(Request $request){
        if(Auth::user()->permission != 1){
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $params = $request->input('params', []);
    $page = isset($params['current_page']) ? (int)$params['current_page'] : 1;
    $perPage = isset($params['pagesize']) ? (int)$params['pagesize'] : 10;

    $query = Client::query();

    $clients = $query->paginate($perPage, ['*'], 'page', $page);

    Log::info("Fetched clients with params", ['params' => $params, 'count' => $clients->count()]);
    return response()->json($clients, 200);
    }
}
