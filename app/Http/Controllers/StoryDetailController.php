<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Histoire;

class StoryDetailController extends Controller
{
    /**
     * Récupérer les détails d'une histoire.
     */
    public function getStoryDetails($id)
    {
        $histoire = Histoire::with(['category', 'chapitre'])
            ->findOrFail($id);

        return response()->json([
            'id' => $histoire->id,
            'title' => $histoire->titre,
            'author' => $histoire->user->name,
            'cover_image' => $histoire->couverture,
            'description' => $histoire->resume,
            'categories' => $histoire->categories->pluck('nom'),
            'views' => $histoire->vues,
            'likes' => $histoire->likes,
            'progress' => $histoire->progression,
        ]);
    }

    /**
     * Récupérer des histoires similaires.
     */
    public function getSimilarStories($id)
    {
        $histoire = Histoire::findOrFail($id);

        $similarStories = Histoire::whereHas('categories', function ($query) use ($histoire) {
            $query->whereIn('categories.id', $histoire->categories->pluck('id'));
        })
        ->where('id', '!=', $id)
        ->take(5)
        ->get();

        return response()->json($similarStories);
    }
}
