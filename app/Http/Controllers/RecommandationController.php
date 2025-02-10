<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Histoire;
use App\Models\User;
use App\Models\UserAction;
use App\Models\CategoriePrefere;
use Illuminate\Support\Facades\Auth;
use App\Models\Chapitre;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
            $user = Auth::user();  //User::find(6);
            if (!$user) {
                return response()->json(['message' => 'Utilisateur non authentifié'], 401);
            }

            // Récupérer les actions de lecture de l'utilisateur
            $lectures = UserAction::where('user_id', $user->id)
                ->where('action', 'lecture')
                ->with('chapitre.histoire')
                ->get();
               
          //  dd($lectures);

         /* $lectures = $lecturesG->groupBy('chapitre.histoire.id') 
          ->take(5);*/
            // Organiser les données
            $histoires = [];
            foreach ($lectures as $lecture) {
                if ($lecture->chapitre) {
                    $histoire = $lecture->chapitre->histoire;
                    $dernierChapitreLu = $lecture->chapitre->numero;
                    $dernierChapitreLuId = $lecture->chapitre->id;
            
                    if (!isset($histoires[$histoire->id])) {
                        $histoires[$histoire->id] = [
                            'id' =>$histoire->id,
                            'couverture' => $histoire->couverture,
                            'titre' => $histoire->titre,
                            'dernier_chapitre_lu' => $dernierChapitreLu,
                            'dernier_chapitre_lu_id' => $dernierChapitreLuId,
                        ];
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
            $user =  Auth::user(); //User::find(1);
            if (!$user) {
                return response()->json(['message' => 'Utilisateur non authentifié'], 401);
            }

            // Récupérer les catégories préférées de l'utilisateur
            $categoriesPreferees = CategoriePrefere::where('user_id', $user->id)
                ->pluck('categorie_id');

               
            // Récupérer les histoires ayant au moins une de ces catégories
            $histoires = Histoire::whereHas('categorieHistoires', function ($query) use ($categoriesPreferees) {
                $query->whereIn('categorie_id', $categoriesPreferees);
            })->whereHas('chapitres', function ($query) {
                $query->where('statut', '=', 'Publier'); // Assurez-vous que 'progression' ou une autre colonne reflète l'état "publié"
            })->select('id','couverture', 'titre')
            ->get();

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
            $histoires = Histoire::whereHas('chapitres', function ($query) {
                $query->where('statut', '=', 'Publier'); // Assurez-vous que 'progression' ou une autre colonne reflète l'état "publié"
            })->select('couverture', 'titre', 'id')
            ->get();

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
     * Récupère les 10 histoires les plus vues et likées.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMostPopularStories()
    {
        try {
            // Compter les lectures et les likes par chapitre
            $chapitreStats = UserAction::whereIn('action', ['lecture', 'like'])
                ->select('chapitre_id', 'action', DB::raw('count(*) as total'))
                ->groupBy('chapitre_id', 'action')
                ->get();

                

            // Agréger les données par histoire
            $histoireStats = [];
            foreach ($chapitreStats as $stat) {
                $chapitre = Chapitre::find($stat->chapitre_id);
               if($chapitre){
                $histoireId = $chapitre->histoire_id;

                

                if (!isset($histoireStats[$histoireId])) {
                    $histoireStats[$histoireId] = [
                        'total_views' => 0,
                        'total_likes' => 0,
                    ];
                }

                if ($stat->action === 'lecture') {
                    $histoireStats[$histoireId]['total_views'] += $stat->total;
                } elseif ($stat->action === 'like') {
                    $histoireStats[$histoireId]['total_likes'] += $stat->total;
                }
            }

            // Trier les histoires par popularité (somme des vues et des likes)
            uasort($histoireStats, function ($a, $b) {
                $aTotal = $a['total_views'] + $a['total_likes'];
                $bTotal = $b['total_views'] + $b['total_likes'];
                return $bTotal <=> $aTotal; // Tri décroissant
            });

            // Récupérer les 4 histoires les plus populaires
            $topHistoireIds = array_slice(array_keys($histoireStats), 0, 4, true);
            $mostPopularStories = Histoire::whereIn('id', $topHistoireIds)
                ->select('id', 'titre', 'couverture')
                ->get()
                ->map(function ($histoire) use ($histoireStats) {
                    return [
                        'id' => $histoire->id,
                        'titre' => $histoire->titre,
                        'couverture' => $histoire->couverture,
                        'total_views' => $histoireStats[$histoire->id]['total_views'],
                        'total_likes' => $histoireStats[$histoire->id]['total_likes'],
                    ];
                });
               }
             

            return response()->json([
                'success' => true,
                'data' => $mostPopularStories
            ], 200);

        } catch (\Exception $e) {
            // Log l'erreur pour le débogage
            Log::error('Erreur lors de la récupération des histoires les plus populaires: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la récupération des histoires les plus populaires.'
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
}