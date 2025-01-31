<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $user_id
 * @property integer $follower_id
 * @property string $created_at
 * @property string $updated_at
 * @property User $followedUser
 * @property User $followerUser
 */
class Follower extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['user_id', 'follower_id', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function followedUser()
    {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function followerUser()
    {
        return $this->belongsTo('App\Models\User', 'follower_id');
    }
}
