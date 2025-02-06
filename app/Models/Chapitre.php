<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * @property integer $id
 * @property integer $histoire_id
 * @property integer $numero
 * @property string $titre
 * @property mixed $content
 * @property string $statut
 * @property integer $vues
 * @property integer $likes
 * @property string $created_at
 * @property string $updated_at
 * @property Histoire $histoire
 * @property Commentaire[] $commentaires
 * @property Lecture[] $lectures
 * @property UserAction[] $userActions
 */
class Chapitre extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['histoire_id', 'numero', 'titre', 'content', 'statut', 'vues', 'likes', 'created_at', 'updated_at'];

    // Si vous souhaitez manipuler directement le JSON comme un tableau PHP
    protected $casts = [
        'contenu' => 'array',
    ];
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function histoire()
    {
        return $this->belongsTo('App\Models\Histoire');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function commentaires()
    {
        return $this->hasMany('App\Models\Commentaire');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function lectures()
    {
        return $this->hasMany('App\Models\Lecture');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userActions()
    {
        return $this->hasMany('App\Models\UserAction');
    }

    /**
     * Extraire les URLs des images du contenu JSON.
     *
     * @return array
     */
    public function extraireUrlsImages(): array
    {
        $contenu = json_decode($this->content, true);
        $images = [];

        foreach ($contenu as $bloc) {
            if (isset($bloc['insert']['image'])) {
                $images[] = $bloc['insert']['image'];
            }
        }

        return $images;
    }

    /**
     * Supprimer les images associées à ce chapitre.
     */
    public function supprimerImages(): void
    {
        $images = $this->extraireUrlsImages();

        foreach ($images as $imageUrl) {
            $baseUrl = url('/') . '/storage/';
            $relativePath = str_replace($baseUrl, '', $imageUrl);

            if (Storage::disk('public')->exists($relativePath)) {
                Storage::disk('public')->delete($relativePath);
            }
        }
    }
}
