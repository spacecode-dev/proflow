<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ObservantTrait;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Issue extends Model
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
        'title',
        'additional_info',
        'due_date',
        'people_involved_id',
        'visibility',
        'people_invited_id',
        'people_invite_email',
        'is_resolved',
        'resolve_text',
        'priority',
        'is_archived'
    ];

    /**
     * @return HasMany
     */
    protected $hidden = [
        'pivot'
    ];

    /**
     * @param string $type
     * @return HasMany
     */
    public function issueSummary($type = '')
    {
        if ($type == '')
            return $this->hasMany('App\IssueSummary');
        return $this->hasMany('App\IssueSummary')->where('type', $type);
    }

    /**
     * @return HasMany
     */
    public function issueFile()
    {
        return $this->hasMany('App\IssueFile');
    }

    /**
     * @return HasOne
     */
    public function userCreatedBy()
    {
        return $this->hasOne('App\User', 'id', 'created_by');
    }

      /**
     * @return MorphMany
     */
    public function comments()
    {
        return $this->morphMany('App\IssueComment', 'commentable');
    }

    /**
     * @return HasMany
     */
    public function resolvedIssueStep()
    {
        return $this->hasMany('App\IssueStep')
            ->where('is_resolved', 0)
            ->with('user');
    }

    /**
     * @return HasMany
     */
    public function issueStep()
    {
        return $this->hasMany('App\IssueStep')
            ->with('user');
    }

    /**
     * @return HasManyThrough
     */
    public function assignedIssueSteps()
    {
        return $this->hasManyThrough('App\IssueStepUser','App\IssueStep')->with('issueStep');
    }

    /**
     * @return HasMany
     */
    public function issueStepPosition()
    {
        return $this->hasMany('App\IssueStep')
            ->orderBy('position_id', 'desc')
            ->with('user');
    }

    /**
     * @return HasOne
     */
    public function nextStep()
    {
        return $this->hasOne('App\IssueStep')
            ->where('is_resolved', 0)
            ->whereNotNull('due_date')
            ->oldest('due_date')
            ->with('assignedUser');
    }

    /**
     * @return HasMany
     */
    public function unassignedNextSteps()
    {
        return $this->hasMany('App\IssueStep')
            ->where('is_unassigned', 1)
            ->with('user');
    }

    /**
     * @return BelongsToMany
     */
    public function tags()
    {
        return $this->belongsToMany('App\Tag')
            ->withPivot('tag_id')
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany
     */
    public function peopleInvolved()
    {
        return $this->belongsToMany('App\User')
            ->withPivot('user_id')
            ->with('userDetail')
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany
     */
    public function peopleFollowedUserDetail()
    {
        return $this->belongsToMany('App\User', 'issue_user', 'issue_id', 'user_id')
            ->where('is_follower', 1)
            ->withPivot('user_id')
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany
     */
    public function peopleMentionedUserDetail()
    {
        return $this->belongsToMany('App\User', 'issue_user', 'issue_id', 'user_id')
            ->where('is_mention', 1)
            ->withPivot('user_id')
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany
     */
    public function peopleInvitedUserDetail()
    {
        $inviteList = $this->belongsToMany('App\User', 'issue_user', 'issue_id', 'user_id')->where('is_invited', 1)
            ->withPivot('user_id')->withTimestamps();
        if ($this->people_invite_email) {
            $array = explode(',', $this->people_invite_email);
            array_push($array, $inviteList);
        }
        return $inviteList;
    }
}
