<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Histoire;
use App\Models\Chapitre;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class HistoireController extends Controller
{

    //
//début controller gaël

    //on affiche les éléments de l'histoire
    public function edit()
{

//$id =  Auth::id() || 1;
    try {
        $histoire = Histoire::select(['id', 'titre', 'resume', 'couverture', 'statut', 'mot_cles', 'progression'])
            ->with([
                'chapitres:id,histoire_id,titre,numero,content,updated_at',
                'categorieHistoires.category:id,nom',
            ])
            ->latest('created_at') // Tri par date de création décroissante
            ->first();
          //  ->find($id);

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
//controlleur pour mettre à jour les éléments d'une histoire, les éléments autres que sa catégories et ses chapitres
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


//récupère l'histoire la plus récente sur la page Home
public function getLatestStory()
    {
        try {
            // Récupérer l'ID de l'utilisateur connecté
            $userId = Auth::id();

            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié.',
                ], 401); // 401 Unauthorized
            }

            // Récupérer l'histoire la plus récente de l'utilisateur
            $histoire = Histoire::where('user_id', $userId)
                ->latest('created_at') // Tri par date de création décroissante
                ->first();

            if (!$histoire) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucune histoire trouvée pour cet utilisateur.',
                ], 404); // 404 Not Found
            }

            // Récupérer les chapitres de l'histoire
            $chapitres = Chapitre::where('histoire_id', $histoire->id)->get();

            // Compter les chapitres publiés et les brouillons
           // 'Publier', 'Brouillon'
            $totalChapitresPublies = $chapitres->where('statut', 'Publier')->count();
            $totalBrouillons = $chapitres->where('statut', 'Brouillon')->count();

            // Retourner les données sous forme de JSON
            return response()->json([
                'success' => true,
                'data' => [
                    'titre' => $histoire->titre, // Titre de l'histoire
                    'couverture' => $histoire->couverture,
                    'nbr_publies' => $totalChapitresPublies,
                    'nbr_brouillons' => $totalBrouillons,
                ],
            ], 200); // 200 OK

        } catch (\Exception $e) {
            // Journaliser l'erreur pour le débogage
            Log::error('Erreur dans HistoireController: ' . $e->getMessage());

            // Retourner une réponse d'erreur générique
            return response()->json([
                'success' => false,
                'message' => 'Une erreur s\'est produite lors de la récupération des données.',
            ], 500); // 500 Internal Server Error
        }
    }

    public function storeStory(Request $request)
{
    // Validation des données entrantes
    $validated = $request->validate([
        'titre' => 'required',
        'resume' => 'required', 
        'couverture' => 'required', 
    ]);

    try {
        // Sauvegarder les informations dans la base de données
        $histoire = Histoire::create([
        'user_id' => Auth::id(),
        'titre' => $validated['titre'],
        'resume' => $validated['resume'], 
        'couverture' => $validated['couverture'], 
 
        ]);

        return response()->json(['message' => 'histoire publié avec succès.', 'data' => $histoire->id], 200);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Erreur lors de la publication de l\'histoire.','error' => $e->getMessage()], 500);
    }
}

public function deleteStory($id)
{
    try {
        $histoire = Histoire::findOrFail($id);
        $histoire->delete();

        return response()->json([
            'success' => true,
            'message' => 'histoire supprimé avec succès'
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => "Erreur lors de la suppression de l'histoire",
            'error' => $e->getMessage()
        ], 500);
    }
}

//fin gael
}
