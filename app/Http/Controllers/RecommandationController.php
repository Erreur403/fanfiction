<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Histoire;
use App\Models\User;
use App\Models\UserAction;
use App\Models\CategoriePrefere;
use Illuminate\Support\Facades\Auth;

class RecommandationController extends Controller
{
    /**
     * Récupère les histoires lues par l'utilisateur actuellement authentifié.
     * Ajoute le numéro du dernier chapitre lu pour chaque histoire.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getHistoiresLues()
    {
        try {
            $user = Auth::user();  // User::find(1);
            if (!$user) {
                return response()->json(['message' => 'Utilisateur non authentifié'], 401);
            }

            // Récupérer les actions de lecture de l'utilisateur
            $lectures = UserAction::where('user_id', $user->id)
                ->where('action', 'lecture')
                ->with('chapitre.histoire')
                ->get();

            // Organiser les données
            $histoires = [];
            foreach ($lectures as $lecture) {
                $histoire = $lecture->chapitre->histoire;
                $dernierChapitreLu = $lecture->chapitre->numero;

                if (!isset($histoires[$histoire->id])) {
                    $histoires[$histoire->id] = [
                        'histoire' => $histoire,
                        'dernier_chapitre_lu' => $dernierChapitreLu,
                    ];
                } else {
                    // Mettre à jour le dernier chapitre lu si nécessaire
                    if ($dernierChapitreLu > $histoires[$histoire->id]['dernier_chapitre_lu']) {
                        $histoires[$histoire->id]['dernier_chapitre_lu'] = $dernierChapitreLu;
                    }
                }
            }

            return response()->json([
                'message' => 'Histoires lues récupérées avec succès',
                'data' => array_values($histoires),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la récupération des histoires lues',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Récupère les histoires correspondant aux catégories préférées de l'utilisateur.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getHistoiresParCategoriesPreferees()
    {
        try {
            $user =    Auth::user(); //User::find(1);
            if (!$user) {
                return response()->json(['message' => 'Utilisateur non authentifié'], 401);
            }

            // Récupérer les catégories préférées de l'utilisateur
            $categoriesPreferees = CategoriePrefere::where('user_id', $user->id)
                ->pluck('categorie_id');

            // Récupérer les histoires ayant au moins une de ces catégories
            $histoires = Histoire::whereHas('categorieHistoires', function ($query) use ($categoriesPreferees) {
                $query->whereIn('categorie_id', $categoriesPreferees);
            })->get();

            return response()->json([
                'message' => 'Histoires par catégories préférées récupérées avec succès',
                'data' => $histoires,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la récupération des histoires par catégories préférées',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Récupère toutes les histoires écrites.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllHistoires()
    {
        try {
            $histoires = Histoire::all();

            return response()->json([
                'message' => 'Toutes les histoires récupérées avec succès',
                'data' => $histoires,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la récupération de toutes les histoires',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Récupère toutes les histoires écrites par l'utilisateur actuellement connecté.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getHistoiresUtilisateur()
    {
        try {
            $user =Auth::user();  //User::find(1) 
            if (!$user) {
                return response()->json(['message' => 'Utilisateur non authentifié'], 401);
            }

            $histoires = Histoire::where('user_id', $user->id)->get();

            return response()->json([
                'message' => 'Histoires de l\'utilisateur récupérées avec succès',
                'data' => $histoires,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la récupération des histoires de l\'utilisateur',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Récupère les 10 histoires les plus vues et likées.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getHistoiresPopulaires()
    {
        try {
            $histoires = Histoire::withCount(['lectures', 'histoireFavoris'])
                ->orderByDesc('lectures_count')
                ->orderByDesc('histoire_favoris_count')
                ->limit(10)
                ->get();

            return response()->json([
                'message' => 'Histoires populaires récupérées avec succès',
                'data' => $histoires,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la récupération des histoires populaires',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}