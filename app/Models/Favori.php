<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $user_id
 * @property string $nom
 * @property string $created_at
 * @property string $updated_at
 * @property User $user
 * @property HistoireFavori[] $histoireFavoris
 */
class Favori extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['user_id', 'nom', 'created_at', 'updated_at'];

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
        return $this->hasMany('App\Models\HistoireFavori', 'favoris_id');
    }
}
