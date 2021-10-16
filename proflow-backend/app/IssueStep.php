<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ObservantTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class IssueStep extends Model
{
    use ObservantTrait, SoftDeletes;

    /**
     * @var array
     */
    protected $dates = ['due_date'];

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'text',
        'due_date',
        'is_resolved',
        'issue_id',
        'is_unassigned'

    ];

    /**
     * @return HasMany
     */
    protected $hidden = [
        'pivot'
    ];

    /**
     * @return BelongsTo
     */
    public function issue()
    {
        return $this->belongsTo('App\Issue');
    }

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'created_by')
            ->with('userDetail');
    }

    /**
     * @return BelongsToMany
     */
    public function assignedUser()
    {
        return $this->belongsToMany('App\User')->withPivot('user_id')
            ->with('userDetail');
    }

    /**
     * @return MorphMany
     */
    public function comments()
    {
        return $this->morphMany('App\IssueComment', 'commentable');
    }

    /**
     * @return BelongsToMany
     */
    public function peopleAssignedTo()
    {
        return $this->belongsToMany('App\UserDetail', 'issue_step_user');
    }

    /**
     * @return BelongsToMany
     */
    public function peopleAssignedToUserDetail()
    {
        return $this->belongsToMany('App\User','issue_step_user','issue_step_id','user_id')
            ->withPivot('user_id')
            ->with('userDetail')
            ->withTimestamps();
    }


}
