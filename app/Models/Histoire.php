<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $user_id
 * @property string $titre
 * @property string $resume
 * @property string $couverture
 * @property string $statut
 * @property string $progression
 * @property integer $restriction_age
 * @property string $mot_cles
 * @property string $created_at
 * @property string $updated_at
 * @property CategorieHistoire[] $categorieHistoires
 * @property Chapitre[] $chapitres
 * @property User $user
 * @property HistoireFavori[] $histoireFavoris
 * @property Lecture[] $lectures
 */
class Histoire extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['user_id', 'titre', 'resume', 'couverture', 'statut', 'progression', 'restriction_age', 'mot_cles', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function categorieHistoires()
    {
        return $this->hasMany('App\Models\CategorieHistoire');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function chapitres()
    {
        return $this->hasMany('App\Models\Chapitre');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function histoireFavoris()
    {
        return $this->hasMany('App\Models\HistoireFavori');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function lectures()
    {
        return $this->hasMany('App\Models\Lecture');
    }
}
