<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CategorieHistoire;
use App\Models\CategoriePrefere;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use Illuminate\Support\Facades\Log;

class CategorieController extends Controller
{
    //
//début controller gaël

    public function storeCategoriesPreferee(Request $request){

        try {
            $categories = $request->get('categories'); 
    
           /* if (!is_array($categories)) {
                return response()->json(['message' => 'Format invalide', 'success' => false], 400);
            }*/
        
            // Boucler sur les nouvelles catégories et les insérer
            
            foreach ($categories as $categorie) {
                CategoriePrefere::create([
                    'user_id' => Auth::id(),
                    'categorie_id' => $categorie,
                ]);
            }
    
            return response()->json(['message' => 'catégories préférées enregistrées avec succès.', 'success' => true], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la publication de l\'histoire.','error' => $e->getMessage(), 'success' => false], 500);
        }
    }

    public function getCategoriesForStory($histoireId)
    {
        try {
     
            // Récupérer les ID des catégories liées à l'histoire donnée
               $categoriesLiees = CategorieHistoire::where('histoire_id', $histoireId)
                   ->pluck('categorie_id')
                   ->toArray();
       
               // Récupérer toutes les catégories et ajouter l'attribut 'checked'
               $categories = Category::select('id', 'nom')->get()->map(function ($category) use ($categoriesLiees) {
                   $category->checked = in_array($category->id, $categoriesLiees) ? true : false;
                   return $category;
               });
       
       
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

    public function updateCategory(String $id , Request $request){


// Afficher la requête en console
    Log::info('Requête reçue:', $request->all());
    //return null;

    try {
        CategorieHistoire::where('histoire_id','=' ,$id)->delete();

        $categories = $request->get('categories'); // Ou $request->input('categories')

    if (!is_array($categories)) {
        return response()->json(['message' => 'Format invalide'], 400);
    }

    // Boucler sur les nouvelles catégories et les insérer
    
    foreach ($categories as $categorie) {
        CategorieHistoire::create([
            'histoire_id' => $id,
            'categorie_id' => $categorie["id"],
        ]);
    }
        return response()->json([
            'message' => 'Catégories mises à jour avec succès',
        ], 200); 
    } catch (\Exception $e) {
        // Réponse en cas d'erreur
        return response()->json([
            'message' => 'Erreur lors de la mise à jour des catégories de l\'histoire.',
            'error' => $e->getMessage(),
        ], 500);
    }

    } 

    /**
     * Ajouter une catégorie.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeCategoriesHistoire(Request $request)
    {
        // Valider les données d'entrée
        $validated = $request->validate([
            'nom' => 'required|string',
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
    public function getCategoriesIndex()
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
//fin gael

}
