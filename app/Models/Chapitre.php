<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $histoire_id
 * @property integer $numero
 * @property string $titre
 * @property mixed $content
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
    protected $fillable = ['histoire_id', 'numero', 'titre', 'content', 'vues', 'likes', 'created_at', 'updated_at'];

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
}
