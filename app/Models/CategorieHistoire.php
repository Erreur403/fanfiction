<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $categorie_id
 * @property integer $histoire_id
 * @property string $created_at
 * @property string $updated_at
 * @property Histoire $histoire
 * @property Category $category
 */
class CategorieHistoire extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['categorie_id', 'histoire_id', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function histoire()
    {
        return $this->belongsTo('App\Models\Histoire');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo('App\Models\Category', 'categorie_id');
    }
}
