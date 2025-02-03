<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Ajouter une nouvelle catégorie.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:191',
        ]);

        try {
            $category = Category::create([
                'nom' => $validated['nom'],
            ]);

            return response()->json([
                'message' => 'Catégorie ajoutée avec succès.',
                'category' => $category,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de l\'ajout de la catégorie.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Obtenir toutes les catégories (ID et nom uniquement).
     */
    public function index()
    {
        // Renvoie uniquement les IDs et noms des catégories
        $categories = Category::select('id', 'nom')->get();

        return response()->json($categories);
    }
}
