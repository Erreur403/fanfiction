<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $user_id
 * @property integer $chapitre_id
 * @property string $action
 * @property string $created_at
 * @property string $updated_at
 * @property User $user
 * @property Chapitre $chapitre
 */
class UserAction extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['user_id', 'chapitre_id', 'action', 'created_at', 'updated_at'];

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
    public function chapitre()
    {
        return $this->belongsTo('App\Models\Chapitre');
    }
}
