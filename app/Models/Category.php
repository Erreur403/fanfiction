<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $nom
 * @property string $created_at
 * @property string $updated_at
 * @property CategorieHistoire[] $categorieHistoires
 * @property CategoriePrefere[] $categoriePreferes
 */
class Category extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['nom', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function categorieHistoires()
    {
        return $this->hasMany('App\Models\CategorieHistoire', 'categorie_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function categoriePreferes()
    {
        return $this->hasMany('App\Models\CategoriePrefere', 'categorie_id');
    }
}
