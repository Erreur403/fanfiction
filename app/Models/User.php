<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $name
 * @property string $email
 * @property string $email_verified_at
 * @property string $password
 * @property string $biographie
 * @property string $profile
 * @property string $remember_token
 * @property string $created_at
 * @property string $updated_at
 * @property Bibliotheque[] $bibliotheques
 * @property CategoriePrefere[] $categoriePreferes
 * @property Commentaire[] $commentaires
 * @property Favori[] $favoris
 * @property Follower[] $followers
 * @property Follower[] $followings
 * @property Histoire[] $histoires
 * @property Notification[] $notifications
 * @property UserAction[] $userActions
 */
class User extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['name', 'email', 'email_verified_at', 'password', 'biographie', 'profile', 'remember_token', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bibliotheques()
    {
        return $this->hasMany('App\Models\Bibliotheque');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function categoriePreferes()
    {
        return $this->hasMany('App\Models\CategoriePrefere');
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
    public function favoris()
    {
        return $this->hasMany('App\Models\Favori');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function followers()
    {
        return $this->hasMany('App\Models\Follower');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function followings()
    {
        return $this->hasMany('App\Models\Follower', 'follower_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function histoires()
    {
        return $this->hasMany('App\Models\Histoire');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notifications()
    {
        return $this->hasMany('App\Models\Notification');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userActions()
    {
        return $this->hasMany('App\Models\UserAction');
    }
}
