<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ObservantTrait;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
    use ObservantTrait, SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = [
        'id', 'title'
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'pivot'
    ];

    /**
     * @return BelongsToMany
     */
    public function issue()
    {
        return $this->belongsToMany('App\Issue')
            ->withPivot('tag_id');
    }

}
