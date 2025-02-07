<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class TestController extends Controller
{
//Début fonction test Gaël

    /**
     * Ajouter une catégorie.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Valider les données d'entrée
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:categories,nom',
        ]);

        // Créer la catégorie
        try {
            $category = Category::create([
                'nom' => $validated['nom'],
            ]);

            // Réponse de succès
            return response()->json([
                'message' => 'Catégorie créée avec succès.',
                'data' => $category,
            ], 201); // 201 Created
        } catch (\Exception $e) {
            // Réponse en cas d'erreur
            return response()->json([
                'message' => 'Erreur lors de la création de la catégorie.',
                'error' => $e->getMessage(),
            ], 500); // 500 Internal Server Error
        }
    }

    /**
     * Afficher toutes les catégories.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
{
    try {
        // Récupérer toutes les catégories
        $categories = Category::all();

        // Réponse de succès, même si la liste est vide
        return response()->json([
            'message' => $categories->isEmpty()
                ? 'Aucune catégorie disponible.'
                : 'Liste des catégories récupérée avec succès.',
            'data' => $categories,
        ], 200); // 200 OK
    } catch (\Exception $e) {
        // Réponse en cas d'erreur
        return response()->json([
            'message' => 'Erreur lors de la récupération des catégories.',
            'error' => $e->getMessage(),
        ], 500); // 500 Internal Server Error
    }
}

public function firstRoot(){
    try {
        return response()->json([
            'message' => 'Liste des catégories récupérée avec succès.'], 200); // 200 OK
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Erreur lors de la récupération des catégories.',
            'error' => $e->getMessage(),
        ], 500);
    }
}
//fin fonction test Gaël
}
