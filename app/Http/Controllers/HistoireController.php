<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Histoire;

class HistoireController extends Controller
{

    //

    public function edit()
{
   
   /* try {
        $histoires = Histoire::select(['id', 'titre', 'resume', 'couverture', 'statut', 'mot_cles', 'progression' ]) // Spécifiez les champs de la table "histoires"
        ->with([
            // Relations avec les champs spécifiés
            'chapitres:id,histoire_id,titre,numero,content,updated_at', // Sélectionne id, histoire_id, titre dans "chapitres"
            'categorieHistoires.category:id,nom', // Sélectionne id, nom dans "categories"
        ])
        ->get()
        ->map(function ($histoire) {
            $histoire->categories = $histoire->categorieHistoires->pluck('category'); // Extraire uniquement les catégories
            unset($histoire->categorieHistoires); // Supprimer la clé categorieHistoires
            return $histoire;
        });

       // $histoires = $histoires->find(1);

        // Réponse de succès, même si la liste est vide
        return response()->json([
            'message' => $histoires->isEmpty()
                ? 'cette histoire n\'est pas disponible.'
                : 'Liste des histoires récupérée avec succès.',
            'data' => $histoires,
        ], 200); // 200 OK
    } catch (\Exception $e) {
        // Réponse en cas d'erreur
        return response()->json([
            'message' => 'Erreur lors de la récupération de l\'histoire.',
            'error' => $e->getMessage(),
        ], 500); // 500 Internal Server Error
    }*/

$id = 1;
    try {
        $histoire = Histoire::select(['id', 'titre', 'resume', 'couverture', 'statut', 'mot_cles', 'progression'])
            ->with([
                'chapitres:id,histoire_id,titre,numero,content,updated_at',
                'categorieHistoires.category:id,nom',
            ])
            ->find($id);

        if (!$histoire) {
            return response()->json([
                'message' => 'Cette histoire n\'est pas disponible.',
                'data' => null,
            ], 404); // 404 Not Found
        }

        // Formate les catégories
        $histoire->categories = $histoire->categorieHistoires->pluck('category');
        unset($histoire->categorieHistoires);

        return response()->json([
            'message' => 'Histoire récupérée avec succès.',
            'data' => $histoire,
        ], 200); // 200 OK
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Erreur lors de la récupération de l\'histoire.',
            'error' => $e->getMessage(),
        ], 500); // 500 Internal Server Error
    }
}
public function updateStory(String $id, Request $request ){
    // Valider les données d'entrée
    
    // Créer la catégorie
    try {
        $histoire = Histoire::find($id);
        $updated_story = $histoire->update($request->all());

        // Réponse de succès
        return response()->json([
            'message' => 'histoire modifiée avec succès.',
            'data' => $updated_story,
        ], 200); // 201 Created
    } catch (\Exception $e) {
        // Réponse en cas d'erreur
        return response()->json([
            'message' => 'Erreur lors de la mise à jour de l\'histoire.',
            'error' => $e->getMessage(),
        ], 500); // 500 Internal Server Error
    }
}
}
