<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Log;
use Cloudinary\Api\Upload\UploadApi;
use \App\Models\Chapitre;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Commentaire;
use App\Models\Histoire;
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
    if (!$request->hasFile('image') || !$request->file('image')->isValid()) {
        return response()->json(['message' => 'Aucun fichier valide fourni.'], 400);
    }

    $file = $request->file('image');
    
    try {
        $uploadResult = Cloudinary::unsignedUpload($file->getRealPath(), env('CLOUDINARY_UPLOAD_PRESET'), [
            'folder' => 'images',
        ]);

        $url = $uploadResult->getSecurePath();

        return response()->json(['url' => $url], 200);
    } catch (\Exception $e) {
        Log::error('Erreur lors de l\'upload: ' . $e->getMessage());
        return response()->json(['message' => 'Erreur lors de l\'upload de l\'image: ' . $e->getMessage()], 500);
    }
}


   /* public function uploadImage(Request $request)
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
    }*/



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
 
*/
            // Supprimer toutes les images de l'ancien contenu
           // $chapitre->supprimerImages();

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

public function enregistrerChapitre(Request $request)
{
    Log::info('Données reçues : ', $request->all());
    try {
        $chapitre = Chapitre::findOrFail($request->get('id'));
        $chapitre_updated = $chapitre->update($request->all());
        return response()->json(['message' => 'Chapitre enregistré.', 'chapitre' => $chapitre_updated], 200);
    } catch (\Exception $e) {
        Log::error('Erreur lors de l\'enregistrement du chapitre : ' . $e->getMessage());
        return response()->json(['message' => "Erreur lors de l'enregistrement du chapitre.", 'error' => $e->getMessage()], 500);
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

    public function lectureChapitre($id, Request $request)
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
    
            // Vérifier si la requête concerne une histoire ou un chapitre
            $forStory = $request->boolean('forStory'); 
    
            if ($forStory) {
                // Si forStory est true, récupérer le premier chapitre de l'histoire
                $histoire = Histoire::find($id);
    
                if (!$histoire) {
                    return response()->json(['message' => 'Histoire introuvable.'], 404);
                }
    
                // Récupérer le premier chapitre de cette histoire
                $chapitre = $histoire->chapitres()->orderBy('numero')->first();
    
                if (!$chapitre) {
                    return response()->json(['message' => 'Aucun chapitre trouvé pour cette histoire.'], 404);
                }
            } else {
                // Si forStory est false, récupérer directement le chapitre par son ID
                $chapitre = Chapitre::find($id);
    
                if (!$chapitre) {
                    return response()->json(['message' => 'Chapitre introuvable.'], 404);
                }
            }
    
            // Vérifier si l'utilisateur a déjà vu ce chapitre
            $userAction = UserAction::where('user_id', $userId)
                ->where('chapitre_id', $chapitre->id)
                ->where('action', 'lecture')
                ->first();
    
            $verifyUserLike = UserAction::where('user_id', $userId)
                ->where('chapitre_id', $chapitre->id)
                ->where('action', 'like')
                ->first();
    
            // Si l'utilisateur n'a pas encore vu le chapitre, ajouter une action 'lecture'
            if (!$userAction) {
                UserAction::create([
                    'user_id' => $userId,
                    'chapitre_id' => $chapitre->id,
                    'action' => 'lecture',
                ]);
            }
    
            // Compter les statistiques pour ce chapitre
            $nombreCommentaires = Commentaire::where('chapitre_id', $chapitre->id)->count();
            $nombreLikes = UserAction::where('chapitre_id', $chapitre->id)->where('action', 'like')->count();
            $nombreVues = UserAction::where('chapitre_id', $chapitre->id)->where('action', 'lecture')->count();
    
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
                    'deja_liker' => $verifyUserLike ? true : false,
                ],
            ], 200);
        } catch (\Exception $e) {
            // Gérer toute erreur
            return response()->json([
                'message' => 'Erreur lors de la récupération du chapitre.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
   /* public function lectureChapitre($id, Request $request)
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

       


        //end for the situation
        $histoire = Histoire::find($id); // Ici, $id est l'ID de l'histoire, pas du chapitre

        // Vérifier si l'histoire existe
        if ($histoire) {
            // Récupérer le premier chapitre de cette histoire (en supposant que les chapitres sont triés par numéro ou date de création)
            $premierChapitre = $histoire->chapitres()->orderBy('numero')->first(); // Utiliser 'numero' ou un autre critère pour déterminer le premier chapitre

            if ($premierChapitre) {
                $chapitreId = $premierChapitre->id;
                // Vous pouvez maintenant utiliser $idPremierChapitre
            } else {
                // Aucun chapitre trouvé
                return response()->json(['message' => 'Aucun chapitre trouvé pour cette histoire.'], 404);
            }
        }
        // Rechercher le chapitre
        $chapitre = Chapitre::find($chapitreId);

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

        $verifyUserLike = UserAction::where('user_id', $userId)
            ->where('chapitre_id', $id)
            ->where('action', 'like')
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
                'deja_liker' => $verifyUserLike ? true : false, // Indique si l'utilisateur a déjà vu le chapitre
            ],
        ], 200); // 200 OK
    } catch (\Exception $e) {
        // Gérer toute erreur
        return response()->json([
            'message' => 'Erreur lors de la récupération du chapitre.',
            'error' => $e->getMessage(),
        ], 500); // 500 Internal Server Error
    }
}*/

public function toggleLikeChapter(String $id, Request $request)
{
    try{
        // Vérifier si l'utilisateur est authentifié
    $userId = Auth::id();
    if (!$userId) {
        return response()->json(['message' => 'Utilisateur non authentifié'], 401);
    }

    // Vérifier si le paramètre liked est bien présent dans la requête
    if (!isset($request->liked)) {
        return response()->json(['message' => 'Paramètre "liked" manquant'], 400);
    }

    // Si liked est true, ajouter un like
    if ($request->liked) {
       
        // Créer une nouvelle action "like"
        UserAction::create([
            'user_id' => $userId,
            'chapitre_id' => $id,
            'action' => 'like'
        ]);

        return response()->json(['message' => 'Chapitre liké avec succès'], 201);
    } 
    // Si liked est false, supprimer le like existant
    else {
        $deleted = UserAction::where('user_id', $userId)
            ->where('chapitre_id', $id)
            ->where('action', 'like')
            ->delete();

        if ($deleted) {
            return response()->json(['message' => 'Like supprimé avec succès'], 200);
        } else {
            return response()->json(['message' => 'Aucun like trouvé à supprimer'], 404);
        }
    }
    }catch(\Exception $e) {
        // Gérer toute erreur
        return response()->json([
            'message' => 'Erreur lors du like du chapitre',
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
          //  $chapitre->supprimerImages();
    
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
    
   
//fin gael
}


