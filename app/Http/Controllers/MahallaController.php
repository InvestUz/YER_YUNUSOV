<?php

namespace App\Http\Controllers;

use App\Models\Mahalla;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class MahallaController extends Controller
{
    /**
     * Get mahallas by tuman ID
     */
    public function getByTuman($tumanId)
    {
        try {
            $mahallas = Mahalla::where('tuman_id', $tumanId)
                ->orderBy('name')
                ->get(['id', 'name', 'name_ru']);
            
            return response()->json($mahallas);
        } catch (\Exception $e) {
            Log::error('Error loading mahallas: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load mahallas'], 500);
        }
    }
    
    /**
     * Store a new mahalla
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'tuman_id' => 'required|exists:tumans,id',
                'name' => 'required|string|max:255',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }
            
            // Check if mahalla already exists in this tuman
            $exists = Mahalla::where('tuman_id', $request->tuman_id)
                ->where('name', $request->name)
                ->exists();
            
            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Бу маҳалла аллақачон мавжуд'
                ], 422);
            }
            
            // Create mahalla
            $mahalla = Mahalla::create([
                'tuman_id' => $request->tuman_id,
                'name' => $request->name,
                'name_ru' => $request->name,
                'creator_id' => Auth::id(),
                'updater_id' => Auth::id(),
            ]);
            
            Log::info('Mahalla created', [
                'mahalla_id' => $mahalla->id,
                'name' => $mahalla->name,
                'tuman_id' => $mahalla->tuman_id,
                'created_by' => Auth::id()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Маҳалла муваффақиятли қўшилди',
                'mahalla' => [
                    'id' => $mahalla->id,
                    'name' => $mahalla->name,
                    'name_ru' => $mahalla->name_ru
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating mahalla: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Хатолик юз берди: ' . $e->getMessage()
            ], 500);
        }
    }
}