<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Histoire;
use App\Models\Commentaire;
use App\Models\UserAction;

class ReadController extends Controller
{
    /**
     * Récupérer les détails de l'histoire.
     */
    public function fetchStoryContent($id, Request $request)
    {
        $histoire =Histoire::with('chapitre')->findOrFail($id);
        $isLiked = UserAction::where('user_id', $request->user()->id)
            ->where('chapitre_id', $histoire->chapitre->pluck('id'))
            ->where('action', 'like')
            ->exists();

        return response()->json([
            'id' => $histoire->id,
            'title' => $histoire->titre,
            'content' => $histoire->chapitre->pluck('content')->join("\n"),
            'isLiked' => $isLiked,
        ]);
    }

    /**
     * Récupérer les commentaires.
     */
    public function getComments($id)
    {
        $comment = Commentaire::where('chapitre_id', $id)
            ->with('user')
            ->get();

        return response()->json($comment);
    }

    /**
     * Ajouter un commentaire.
     */
    public function addComment(Request $request, $id)
    {
        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        $comment = new Commentaire();
        $comment->content = $validated['content'];
        $comment->chapitre_id = $id;
        $comment->user_id = $request->user()->id;
        $comment->save();

        return response()->json($comment);
    }

    /**
     * Gérer le like.
     */
    public function toggleLike(Request $request, $id)
    {
        $userId = $request->user()->id;
        $action = UserAction::where('user_id', $userId)
            ->where('chapitre_id', $id)
            ->where('action', 'like')
            ->first();

        if ($action) {
            $action->delete();
            return response()->json(['liked' => false]);
        } else {
            UserAction::create([
                'user_id' => $userId,
                'chapitre_id' => $id,
                'action' => 'like',
            ]);
            return response()->json(['liked' => true]);
        }
    }
}
