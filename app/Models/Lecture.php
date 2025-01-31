<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $bibliotheque_id
 * @property integer $histoire_id
 * @property integer $chapitre_id
 * @property integer $position_text
 * @property string $created_at
 * @property string $updated_at
 * @property Chapitre $chapitre
 * @property Bibliotheque $bibliotheque
 * @property Histoire $histoire
 */
class Lecture extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['bibliotheque_id', 'histoire_id', 'chapitre_id', 'position_text', 'created_at', 'updated_at'];

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
    public function bibliotheque()
    {
        return $this->belongsTo('App\Models\Bibliotheque');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function histoire()
    {
        return $this->belongsTo('App\Models\Histoire');
    }
}
