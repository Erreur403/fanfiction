<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Histoire;
use App\Models\Chapitre;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\CategorieHistoire;

class HistoireController extends Controller
{

    //
//début controller gaël

    //on affiche les éléments de l'histoire
    public function edit(String $id)
{


//$id=1;
    try {
        $histoire = Histoire::select(['id', 'titre', 'resume', 'couverture', 'statut', 'mot_cles', 'progression'])
            ->with([
                'chapitres:id,histoire_id,titre,numero,content,updated_at',
                'categorieHistoires.category:id,nom',
            ])->where('id', '=', $id)->first();
           

        if (!$histoire) {
            return response()->json([
                'message' => 'Cette histoire n\'est pas disponible.',
                'id' => $id,
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
            $userId =  Auth::id();

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

    public function mesHistoires()
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
    
            // Récupérer toutes les histoires de l'utilisateur
            $histoires = Histoire::where('user_id', $userId)
                ->with('chapitres') // Charger les chapitres en une seule requête
                ->get();
    
            if ($histoires->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucune histoire trouvée pour cet utilisateur.',
                ], 404); // 404 Not Found
            }
    
            // Préparer un tableau pour stocker les données de chaque histoire
            $data = [];
    
            foreach ($histoires as $histoire) {
                // Compter les chapitres publiés et les brouillons
                $totalChapitresPublies = $histoire->chapitres->where('statut', 'Publier')->count();
                $totalBrouillons = $histoire->chapitres->where('statut', 'Brouillon')->count();
    
                // Déterminer si l'histoire est publiée (au moins un chapitre publié)
                $published = $totalChapitresPublies > 0;
    
                // Ajouter les données de l'histoire au tableau
                $data[] = [
                    'titre' => $histoire->titre, // Titre de l'histoire
                    'couverture' => $histoire->couverture,
                    'nbr_publies' => $totalChapitresPublies,
                    'nbr_brouillons' => $totalBrouillons,
                    'published' => $published, // Ajout de l'attribut published
                ];
            }
    
            // Retourner les données sous forme de JSON
            return response()->json([
                'success' => true,
                'data' => $data,
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
        'categories' =>'required',
    ]);

    try {
        // Sauvegarder les informations dans la base de données
        $histoire = Histoire::create([
        'user_id' => Auth::id(),
        'titre' => $validated['titre'],
        'resume' => $validated['resume'], 
        'couverture' => $validated['couverture'], 
 
        ]);

        $categories = $request->get('categories'); 

        if (!is_array($categories)) {
            return response()->json(['message' => 'Format invalide'], 400);
        }
    
        // Boucler sur les nouvelles catégories et les insérer
        
        foreach ($categories as $categorie) {
            CategorieHistoire::create([
                'histoire_id' => $histoire->id,
                'categorie_id' => $categorie,
            ]);
        }

        return response()->json(['message' => 'histoire publié avec succès.', 'data' => $histoire->id], 200);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Erreur lors de la publication de l\'histoire.','error' => $e->getMessage()], 500);
    }
}

/*public function deleteStory($id)
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
}*/

public function deleteStory($id)
{
    try {
        $histoire = Histoire::findOrFail($id);
        $chapitres = Chapitre::where('histoire_id', $id)->get();

        foreach ($chapitres as $chapitre) {
            $chapitre->supprimerImages(); // Supprimer les images du chapitre
            $chapitre->delete(); // Supprimer le chapitre
        }

        $histoire->delete();

        return response()->json([
            'success' => true,
            'message' => 'Histoire et ses chapitres supprimés avec succès'
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => "Erreur lors de la suppression de l'histoire",
            'error' => $e->getMessage()
        ], 500);
    }
}

public function getOtherChapterWithStory($id)
{
    try {
        // Récupérer le chapitre par son ID
        $chapitre = Chapitre::find($id);

        // Vérifier si le chapitre existe
        if (!$chapitre) {
            return response()->json([
                'message' => 'Chapitre introuvable.',
            ], 404); // 404 Not Found
        }

        // Récupérer l'histoire associée au chapitre
        $histoire = $chapitre->histoire;

        // Vérifier si l'histoire existe
        if (!$histoire) {
            return response()->json([
                'message' => 'Histoire introuvable pour ce chapitre.',
            ], 404); // 404 Not Found
        }

        // Récupérer les informations de l'histoire
        $histoireData = [
            'id' => $histoire->id,
            'titre' => $histoire->titre,
            'couverture' => $histoire->couverture,
        ];

        // Récupérer les autres chapitres publiés de cette histoire (sauf le chapitre actuel)
        $autresChapitres = Chapitre::where('histoire_id', $histoire->id)
            ->where('statut', 'Publier') // Filtrer par statut "publié"
            ->select('id', 'numero', 'titre') // Sélectionner uniquement les champs nécessaires
            ->orderBy('numero') // Trier par numéro de chapitre
            ->get();

        // Retourner les données au format JSON
        return response()->json([
            'message' => 'Données récupérées avec succès.',
            'data' => [
                'histoire' => $histoireData,
                'chapitres' => $autresChapitres,
            ],
        ], 200); // 200 OK
    } catch (\Exception $e) {
        // Gérer toute erreur
        return response()->json([
            'message' => 'Erreur lors de la récupération des données.',
            'error' => $e->getMessage(),
        ], 500); // 500 Internal Server Error
    }
}

//fin gael
}


 