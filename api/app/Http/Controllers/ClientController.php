<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Events\ClientFetch;

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

    function createClient(Request $request){
        if(Auth::user()->permission != 1){
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'delivery_address' => 'required|string|max:255',
            'registration_nr' => 'nullable|string|max:50',
            'pvn_nr' => 'nullable|string|max:50',
            'bank' => 'nullable|string|max:50',
            'iban' => 'nullable|string|max:50',
            'payment_term' => 'nullable|max:50',
        ]);

        $client = Client::create($validated);

        broadcast(new ClientFetch($client))->toOthers();
        return response()->json(['message' => 'Client created successfully', 'client' => $client], 201);
    }

    function deleteClient($id){
        if(Auth::user()->permission != 1){
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $client = Client::find($id);

        if (!$client) {
            return response()->json(['message' => 'Client not found'], 404);
        }

        $client->delete();

        return response()->json(['message' => 'Client deleted successfully'], 204);
    }

    function updateClient(Request $request){
        $validated = $request->validate([
            'id' => 'required|integer',
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'delivery_address' => 'required|string|max:255',
            'registration_nr' => 'nullable|string|max:50',
            'pvn_nr' => 'nullable|string|max:50',
            'bank' => 'nullable|string|max:50',
            'iban' => 'nullable|string|max:50',
            'payment_term' => 'nullable|max:50',
        ]);

        if(Auth::user()->permission != 1){
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $client = Client::find($validated['id']);
        if($client){
            $client->name = $validated['name'];
            $client->address = $validated['address'];
            $client->contact_person = $validated['contact_person'];
            $client->email = $validated['email'];
            $client->phone = $validated['phone'];
            $client->delivery_address = $validated['delivery_address'];
            $client->registration_nr = $validated['registration_nr'];
            $client->pvn_nr = $validated['pvn_nr'];
            $client->bank = $validated['bank'];
            $client->iban = $validated['iban'];
            $client->payment_term = $validated['payment_term'];
            $client->save();

            broadcast(new ClientFetch($client))->toOthers();
            return response()->json(['message' => 'Client updated successfully!'], 200);
        }else{
            return response()->json([['message' => 'Client not found', 404]]);
        }
    }
}
