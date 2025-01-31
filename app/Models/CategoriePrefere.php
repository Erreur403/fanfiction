<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $user_id
 * @property integer $categorie_id
 * @property string $created_at
 * @property string $updated_at
 * @property User $user
 * @property Category $category
 */
class CategoriePrefere extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['user_id', 'categorie_id', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo('App\Models\Category', 'categorie_id');
    }
}
