<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\lpsp_credentials;

class LPSPcreds extends Controller
{
    function getCredentials(Request $request){
        if(Auth::user()->permission == 1){
            $creds = lpsp_credentials::find(1);
            return $creds ? response()->json($creds) : response()->json(['message' => 'Credentials not found'], 404);
        }else{
            return response()->json([ 'message' => 'Unauthorized' ], 403);
        }
    }
    function updateCredentials(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'vat_nr' => 'required|string|max:50',
            'bank' => 'required|string|max:255',
            'iban' => 'required|string|max:34',
            'export_address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'registration' => 'required|string|max:50',
        ]);
        if(Auth::user()->permission == 1){
            $creds = lpsp_credentials::find(1);
            if(!$creds) {
                return response()->json(['message' => 'Credentials not found'], 404);
            }

            $creds->name = $request->input('name');
            $creds->address = $request->input('address');
            $creds->vat_nr = $request->input('vat_nr');
            $creds->bank = $request->input('bank');
            $creds->iban = $request->input('iban');
            $creds->export_address = $request->input('export_address');
            $creds->phone = $request->input('phone');
            $creds->registration_nr = $request->input('registration');

            if($creds->save()){
                return response()->json(['message' => 'Credentials updated successfully'], 200);
            } else {
                return response()->json(['message' => 'Failed to update credentials'], 500);
            }
        }else{
            return response()->json([ 'message' => 'Unauthorized' ], 403);
        }
    }
}
