<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $chapitre_id
 * @property integer $user_id
 * @property integer|null $commentaire_parent_id
 * @property string $content
 * @property string $created_at
 * @property string $updated_at
 * @property Commentaire|null $parent
 * @property Commentaire[] $replies
 * @property Commentaire[] $recursive
 * @property Chapitre $chapitre
 * @property User $user
 */
class Commentaire extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['chapitre_id', 'user_id', 'commentaire_parent_id', 'content', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo('App\Models\Commentaire', 'commentaire_parent_id');
    }

    /**
     * Relation pour récupérer les enfants du commentaire.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function replies()
    {
        return $this->hasMany('App\Models\Commentaire', 'commentaire_parent_id');
    }

    /**
     * Relation pour récupérer les enfants du commentaire.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function recursive()
    {
        return $this->hasMany('App\Models\Commentaire', 'commentaire_parent_id')->with('recursive'); // Récursif
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function chapitre()
    {
        return $this->belongsTo('App\Models\Chapitre');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
