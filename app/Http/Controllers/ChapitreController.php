<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use \App\Models\Chapitre;

class ChapitreController extends Controller
{
    //

     /**
     * Upload d'image via l'éditeur.
     */
    public function uploadImage(Request $request)
    {
        // Vérifiez si le fichier est bien fourni
        if (!$request->hasFile('image') || !$request->file('image')->isValid()) {
            return response()->json(['message' => 'Aucun fichier valide fourni.'], 400);
        }

        // Récupération du fichier
        $file = $request->file('image');

        // Générer un nom de fichier unique
        $fileName = uniqid() . '.' . $file->getClientOriginalExtension();

        // Sauvegarder le fichier dans un dossier (exemple : storage/app/public/images)
        $path = $file->storeAs('public/images', $fileName);

        // Retourner l'URL publique de l'image
        $url = Storage::url($path);

        return response()->json(['url' => $url], 200);
    }


    /**
 * Enregistrement d'un chapitre avec son contenu JSON.
 */
public function storeChapitre(Request $request)
{
    // Validation des données entrantes
    $validated = $request->validate([
        'titre' => 'required|string|max:255',
        'content' => 'required|array', // On attend un tableau JSON
    ]);

    try {
        // Sauvegarder les informations dans la base de données
        $chapitre = Chapitre::create([
            'histoire_id' => 1,
            'numero' =>1,
            'vues' => 0,
            'likes' =>0,
            'titre' => $validated['titre'],
            'content' => json_encode($validated['content']), // Stocker en tant que JSON
        ]);

        return response()->json(['message' => 'Chapitre publié avec succès.', 'chapitre' => $chapitre], 200);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Erreur lors de la publication du chapitre.', 'error' => $e->getMessage()], 500);
    }
}


public function getChapitre($id)
    {
        try {
            // Rechercher le chapitre
            $chapitre = Chapitre::find($id);

            // Vérifier si le chapitre existe
            if (!$chapitre) {
                return response()->json([
                    'message' => 'Chapitre introuvable.',
                ], 404); // 404 Not Found
            }

            // Retourner uniquement les données nécessaires
            return response()->json([
                'message' => 'Chapitre récupéré avec succès.',
                'data' => [
                    'id' => $chapitre->id,
                    'titre' => $chapitre->titre,
                    'content' => $chapitre->content,
                ],
            ], 200); // 200 OK
        } catch (\Exception $e) {
            // Gérer toute erreur
            return response()->json([
                'message' => 'Erreur lors de la récupération du chapitre.',
                'error' => $e->getMessage(),
            ], 500); // 500 Internal Server Error
        }
    }
}
