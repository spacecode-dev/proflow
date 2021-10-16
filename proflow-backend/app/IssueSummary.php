<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ObservantTrait;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class IssueSummary extends Model
{
    use ObservantTrait;

    /**
     * @var array
     */
    protected $fillable = [
        'id', 'text', 'type', 'issue_id'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'issue_summary' => 'array'
    ];

    /**
     * @return MorphMany
     */
    public function comments()
    {
        return $this->morphMany('App\IssueComment', 'commentable');
    }
}
