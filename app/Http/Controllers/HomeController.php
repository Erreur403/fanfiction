<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Histoire;
use App\Models\CategoriePrefere;
use App\Models\UserAction;

class HomeController extends Controller
{
    // Récupère les recommandations pour l'utilisateur
    public function getRecommendations(Request $request)
    {
        $userId = $request->user()->id;

        // Identifie les catégories préférées de l'utilisateur
        $preferredCategories = CategoriePrefere::where('user_id', $userId)->pluck('categorie_id');

        // Récupère les histoires liées à ces catégories
        $recommendations = Histoire::whereHas('categories', function ($query) use ($preferredCategories) {
            $query->whereIn('categories.id', $preferredCategories);
        })->take(10)->get();

        return response()->json($recommendations);
    }

    // Récupère les lectures en cours pour l'utilisateur
    public function getInProgressStories(Request $request)
    {
        $userId = $request->user()->id;

        // Identifie les actions de lecture de l'utilisateur
        $inProgress = UserAction::where('user_id', $userId)
            ->where('action', 'lecture')
            ->with('chapitre.histoire')
            ->take(10)
            ->get();

        return response()->json($inProgress);
    }

    // Récupère les histoires les plus populaires
    public function getPopularStories()
    {
        // Sélectionne les histoires les plus populaires par nombre de vues
        $popularStories = Histoire::orderBy('vues', 'desc')->take(10)->get();

        return response()->json($popularStories);
    }
}
