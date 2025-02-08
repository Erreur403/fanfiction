<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    //

    public function updateProfil( Request $request ){
        // Valider les données d'entrée
        
        try {
            $currentUserId = Auth::id();
            $user = User::find($currentUserId);
            $updated_user = $user->update($request->all());
    
            // Réponse de succès
            return response()->json([
                'message' => 'User modifiée avec succès.',
                'data' => $updated_user,
            ], 200); // 201 Created
        } catch (\Exception $e) {
            // Réponse en cas d'erreur
            return response()->json([
                'message' => 'Erreur lors de la mise à jour du User.',
                'error' => $e->getMessage(),
            ], 500); // 500 Internal Server Error
        }
    }

    public function getProfil()
{
    try {
        // Récupérer un profil
        $currentUserId =  Auth::id();
        $profil = User::select('name', 'email', 'biographie', 'profile')->find($currentUserId);

        // Réponse de succès, même si la liste est vide
        if($profil != null){
            return response()->json([
                'message' =>  'profil récupérer avec succès',
                'data' => $profil,
            ], 200); // 200 OK
        }else{
            return response()->json([
                'message' =>  'échec profil non récupérer ',
                'data' => $profil,
            ],404);
        }
    } catch (\Exception $e) {
        // Réponse en cas d'erreur
        return response()->json([
            'message' => 'Erreur lors de la récupération du profil.',
            'error' => $e->getMessage(),
        ], 500); // 500 Internal Server Error
    }
}
 
}
 