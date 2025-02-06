<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use \App\Models\Chapitre;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Commentaire;
use App\Models\UserAction;

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
        
        // Récupérer les chapitres de l'histoire
        $nombreChapitresExistants = Chapitre::where('histoire_id','=', $validated['id'])->count() ;
        
        //retrouver le numéro
        $numero_chapitre = $nombreChapitresExistants +1;
        
     

        $chapitre = Chapitre::create([
            'histoire_id' => $validated['id'],
            'numero' =>$numero_chapitre,
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
                    'numero' => $chapitre->numero,
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

    public function lectureChapitre($id)
{
    try {
        // Récupérer l'ID de l'utilisateur authentifié
        $userId = Auth::id();

        // Vérifier si l'utilisateur est authentifié
        if (!$userId) {
            return response()->json([
                'message' => 'Utilisateur non authentifié.',
            ], 401); // 401 Unauthorized
        }

        // Rechercher le chapitre
        $chapitre = Chapitre::find($id);

        // Vérifier si le chapitre existe
        if (!$chapitre) {
            return response()->json([
                'message' => 'Chapitre introuvable.',
            ], 404); // 404 Not Found
        }

        // Vérifier si l'utilisateur a déjà vu ce chapitre
        $userAction = UserAction::where('user_id', $userId)
            ->where('chapitre_id', $id)
            ->where('action', 'lecture')
            ->first();

        // Si l'utilisateur n'a pas encore vu le chapitre, ajouter une action 'lecture'
        if (!$userAction) {
            UserAction::create([
                'user_id' => $userId,
                'chapitre_id' => $id,
                'action' => 'lecture',
            ]);
        }

        // Compter le nombre de commentaires pour ce chapitre
        $nombreCommentaires = Commentaire::where('chapitre_id', $id)->count();

        // Compter le nombre de likes pour ce chapitre
        $nombreLikes = UserAction::where('chapitre_id', $id)
            ->where('action', 'like')
            ->count();
        
        $nombreVues = UserAction::where('chapitre_id', $id)
        ->where('action', 'lecture')
        ->count();

        // Retourner les données du chapitre avec les informations supplémentaires
        return response()->json([
            'message' => 'Chapitre récupéré avec succès.',
            'data' => [
                'id' => $chapitre->id,
                'titre' => $chapitre->titre,
                'content' => $chapitre->content,
                'numero' => $chapitre->numero,
                'nombre_commentaires' => $nombreCommentaires,
                'nombre_likes' => $nombreLikes,
                'nombre_vues' => $nombreVues,
                'deja_vu' => $userAction ? true : false, // Indique si l'utilisateur a déjà vu le chapitre
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
            // Récupérer le chapitre à supprimer
            $chapitre = Chapitre::findOrFail($id);
            $histoire_id = $chapitre->histoire_id;
            $numeroSupprime = $chapitre->numero;
    
            // suppression des images du contenu JSON
            $chapitre->supprimerImages();
    
            // Supprimer le chapitre
            $chapitre->delete();
    
            // Mettre à jour les numéros des chapitres restants
            DB::statement('
                UPDATE chapitres
                SET numero = numero - 1
                WHERE histoire_id = ? AND numero > ?
            ', [$histoire_id, $numeroSupprime]);
    
            return response()->json([
                'success' => true,
                'message' => 'Chapitre et images associées supprimés avec succès'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression du chapitre',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Extraire les URLs des images du contenu JSON.
     *
     * @param array $contenu
     * @return array
     */
    private function extraireUrlsImages(array $contenu): array
    {
        $images = [];
    
        foreach ($contenu as $bloc) {
            if (isset($bloc['insert']['image'])) {
                $images[] = $bloc['insert']['image'];
            }
        }
    
        return $images;
    }
    
    /**
     * Supprimer une image du stockage en utilisant Laravel Storage.
     *
     * @param string $imageUrl
     * @return void
     */
    private function supprimerImageDuStockage(string $imageUrl): void
    {
        // Extraire le chemin relatif de l'image depuis l'URL
        $baseUrl = url('/') . '/storage/'; // URL de base du stockage public
        $relativePath = str_replace($baseUrl, '', $imageUrl); // Chemin relatif dans le stockage
    
        // Supprimer le fichier en utilisant Laravel Storage
        if (Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
        }
    }
//fin gael
}


