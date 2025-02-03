<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use \App\Models\Chapitre;
use Illuminate\Support\Facades\Auth;

class ChapitreController extends Controller
{
    //
//début controller gaël

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
      //  $fileName = uniqid() . '.' . $file->getClientOriginalExtension();

        // Sauvegarder le fichier dans un dossier (exemple : storage/app/public/images)
        $path = $file->store('images', 'public');

        // Retourner l'URL publique de l'image

     //   $absoluteUrl = asset(Storage::disk('public')->url($path));
      //  $url = Storage::disk('public')->url($path);
      $url = url("storage/{$path}");

     // dd(Storage::disk('public')->exists('images/GUd6ZuGR3PXy40A2OnS0CNqFkCiMCggaGLqZzUfc.jpg'));


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
        'content' => 'required', // On attend un tableau JSON
        'id' =>'required',
        'statut' => 'required',
    ]);

    try {
        // Sauvegarder les informations dans la base de données
        $chapitre = Chapitre::create([
            'histoire_id' => $validated['id'],
            'numero' =>1,
            'statut' => $validated['statut'],
            'titre' => $validated['titre'],
            'content' => $validated['content'] ,//json_encode(), // Stocker en tant que JSON
        ]);

        return response()->json(['message' => 'Chapitre publié avec succès.', 'chapitre' => $chapitre], 200);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Erreur lors de la publication du chapitre.', 'error' => $e->getMessage()], 500);
    }
}

public function updateChapitre(Request $request)
{
    // Validation des données entrantes
    $validated = $request->validate([
        'titre' => 'required|string|max:255',
        'content' => 'required', // On attend un tableau JSON
        'id' =>'required',
        'statut' => 'required',
    ]);

    try {
        // Sauvegarder les informations dans la base de données
        $chapitre = Chapitre::findOrFail($validated['id']); 
/*
         // Décoder l'ancien contenu et extraire les images
         $ancienContent = json_decode($chapitre->content, true);
         $imagesAnciennes = $this->extraireImages($ancienContent);
 
         // Supprimer toutes les images de l'ancien contenu
         foreach ($imagesAnciennes as $image) {
             $this->supprimerImage($image);
         }
*/
        $chapitre_updated = $chapitre->update([
            'titre' => $validated['titre'],
            'content' => $validated['content'],
            'statut' => $validated['statut'],
        ]);

        return response()->json(['message' => 'Chapitre mis à jour avec succès.', 'chapitre' => $chapitre_updated], 200);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Erreur lors de la mise à jour du chapitre.', 'error' => $e->getMessage()], 500);
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

    public function destroy($id)
    {
        try {
            $chapitre = Chapitre::findOrFail($id);
            $chapitre->delete();

            return response()->json([
                'success' => true,
                'message' => 'Chapitre supprimé avec succès'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression du chapitre',
                'error' => $e->getMessage()
            ], 500);
        }
    }
//fin gael
}

//peut aider

/**
 * Fonction pour extraire les URLs des images d'un contenu Quill.
 */
/*
private function extraireImages($content)
{
    $images = [];

    foreach ($content as $element) {
        if (isset($element['insert']['image'])) {
            $images[] = $element['insert']['image'];
        }
    }

    return $images;
}


private function supprimerImage($imageUrl)
{
    // Vérifier si l'image est stockée localement
    if (strpos($imageUrl, 'http') === false) { // Supposons que les images locales ne commencent pas par "http"
        $filePath = public_path($imageUrl); // Adapter selon ton stockage
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
}
*/
//fin peu aider gael
