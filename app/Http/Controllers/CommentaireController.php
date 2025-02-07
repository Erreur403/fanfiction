<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Commentaire;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CommentaireController extends Controller
{
    /**
     * Enregistre un nouveau commentaire.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            // Validation des données
            $request->validate([
                'content' => 'required|string',
                'chapitre_id' => 'required|exists:chapitres,id',
            ]);

            // Création du commentaire
            $commentaire = Commentaire::create([
                'content' => $request->input('content'),
                'chapitre_id' => $request->input('chapitre_id'),
                'user_id' => Auth::id(), // Utilisateur connecté
            ]);

            // Charger les informations de l'utilisateur
            $commentaire->load('user:id,name,profile');

            // Ajouter le champ `currentuser`
            $commentaire->currentuser = true;

            return response()->json($commentaire, 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Une erreur est survenue lors de la création du commentaire.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Met à jour un commentaire existant.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            // Récupérer le commentaire
            $commentaire = Commentaire::findOrFail($id);

            // Vérifier que l'utilisateur connecté est l'auteur du commentaire
            if ($commentaire->user_id !== Auth::id()) {
                return response()->json(['error' => 'Vous n\'êtes pas autorisé à modifier ce commentaire.'], 403);
            }

            // Validation des données
            $request->validate([
                'content' => 'required|string',
            ]);

            // Mettre à jour le commentaire
            $commentaire->update([
                'content' => $request->input('content'),
            ]);

            // Charger les informations de l'utilisateur
            $commentaire->load('user:id,name,profile');

            // Ajouter le champ `currentuser`
            $commentaire->currentuser = true;

            return response()->json($commentaire);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Commentaire non trouvé.'], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Une erreur est survenue lors de la mise à jour du commentaire.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Supprime un commentaire.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            // Récupérer le commentaire
            $commentaire = Commentaire::findOrFail($id);

            // Vérifier que l'utilisateur connecté est l'auteur du commentaire
            if ($commentaire->user_id !== Auth::id()) {
                return response()->json(['error' => 'Vous n\'êtes pas autorisé à supprimer ce commentaire.'], 403);
            }

            // Supprimer le commentaire
            $commentaire->delete();

            return response()->json(['message' => 'Commentaire supprimé avec succès.'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Commentaire non trouvé.'], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Une erreur est survenue lors de la suppression du commentaire.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Récupère les commentaires de premier niveau pour un chapitre donné.
     *
     * @param int $chapitreId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCommentairesByChapitre($chapitreId)
    {
        try {
            // Récupérer les commentaires de premier niveau (sans parent)
            $commentaires = Commentaire::where('chapitre_id', $chapitreId)
                ->whereNull('commentaire_parent_id') // Uniquement les commentaires de premier niveau
                ->with('user:id,name,profile') // Charger les informations de l'utilisateur
                ->get();

            // Ajouter le champ `currentuser` pour chaque commentaire
            $userId = Auth::id(); // ID de l'utilisateur connecté
            $commentaires->transform(function ($commentaire) use ($userId) {
                $commentaire->currentuser = $commentaire->user_id === $userId;
                return $commentaire;
            });

            return response()->json($commentaires);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Une erreur est survenue lors de la récupération des commentaires.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
}

  /*  public function test(String $chapitreId)
{
    $comments = Commentaire::where('chapitre_id', $chapitreId)
        ->whereNull('commentaire_parent_id') // Uniquement les commentaires principaux
        ->with('recursive') // Charger les enfants récursivement
        ->get();

        dd($comments);
    return response()->json($comments);
}*/

