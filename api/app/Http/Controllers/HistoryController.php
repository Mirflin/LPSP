<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\ActionHistory;

class HistoryController extends Controller
{
    public function getHistory(Request $request) {

        if(Auth::user()->permission != 1){
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $perPage = $request->input('perPage', 10);
        $page = $request->input('page', 1);
        $sort = $request->input('sort', 'id');
        $direction = $request->input('direction', 'desc');
        $search = $request->input('search');

        $query = ActionHistory::with([
            'user',                                             
        ]);

        $total = $query->count();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                ->orWhere('created_at', 'like', "%{$search}%")
                ->orWhere('action', 'like', "%{$search}%")
                ->orWhere('updated_at', 'like', "%{$search}%")
                ->orWhereHas('user', fn($cq) => 
                    $cq->where('last_name', 'like', "%{$search}%")
                );
            });
        }

        $query->orderBy('created_at', 'desc');

        $history = $query->skip(($page - 1) * $perPage)
                      ->take($perPage)
                      ->get();

        return response()->json([
            'history' => $history,
            'total' => $total,
        ]);
    }
}
