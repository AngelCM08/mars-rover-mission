<?php

namespace App\Http\Controllers;

use App\Models\RoverPosition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class RoverPositionController extends Controller
{
    /**
     * Guarda o actualiza la posición del rover para la sesión actual.
     */
    public function savePosition(Request $request)
    {
        $validatedData = $request->validate([
            'x' => 'required|integer|min:1|max:200',
            'y' => 'required|integer|min:1|max:200',
            'direction' => 'required|in:N,E,S,W',
        ]);
        
        // Obtener ID de sesión para identificar al usuario
        $sessionId = Session::getId();
        
        // Encontrar o crear un nuevo registro para esta sesión
        RoverPosition::updateOrCreate(
            ['session_id' => $sessionId],
            [
                'x' => $validatedData['x'],
                'y' => $validatedData['y'],
                'direction' => $validatedData['direction'],
            ]
        );
        
        return response()->json(['success' => true, 'message' => 'Posición guardada correctamente']);
    }
    
    /**
     * Recupera la posición guardada del rover para la sesión actual.
     */
    public function getPosition()
    {
        $sessionId = Session::getId();
        
        $position = RoverPosition::where('session_id', $sessionId)->first();
        
        if ($position) {
            return response()->json([
                'success' => true,
                'position' => [
                    'x' => $position->x,
                    'y' => $position->y,
                    'direction' => $position->direction,
                ]
            ]);
        }
        
        // Si no existe posición guardada, devolvemos null
        return response()->json([
            'success' => false,
            'position' => null
        ]);
    }
}
