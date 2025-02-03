<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Histoire;

class StoryController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'resume' => 'required|string',
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id',
            'couverture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $path = $request->file('couverture')->store('couvertures', 'public');

            $histoire = Histoire::create([
                'titre' => $validated['titre'],
                'resume' => $validated['resume'],
                'couverture' => $path,
            ]);

            $histoire->categories()->attach($validated['categories']);

            return response()->json([
                'message' => 'Histoire créée avec succès.',
                'histoire' => $histoire,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la création de l\'histoire.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getCategories()
    {
        try {
            $categories = \App\Models\Category::orderBy('nom')->get();
            return response()->json($categories);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la récupération des catégories.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
