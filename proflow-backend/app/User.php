<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\VerifyApiEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, Notifiable;

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at',
        'is_issue_invited'
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'pivot',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function sendApiEmailVerificationNotification()
    {
        $this->notify(new VerifyApiEmail); // my notification
    }

    /**
     * @return BelongsToMany
     */
    public function company()
    {
        return $this->belongsToMany('App\Company')
            ->withPivot('role_id');
    }

    /**
     * @return HasOne
     */
    public function userDetail()
    {
        return $this->hasOne('App\UserDetail', 'user_id', 'id');
    }

    /**
     * @return mixed
     */
    public function getSignupStep()
    {
        return $this->userDetail;
    }

    /**
     * @param $token
     * @return HasOne
     */
    public function googleTokenAvailable($token)
    {
        return $this->hasOne('App\UserDetail', 'user_id', 'id')
            ->where('google_token', '=', $token);
    }

    public function sendInviteEmailNotification()
    {
        $this->notify(new SendInviteEmail); // my notification
    }

    /**
     * @return BelongsToMany
     */
    public function role()
    {
        return $this->belongsToMany('App\Role', 'company_user', 'role_id', 'id');
    }

    /**
     * @return BelongsToMany
     */
    public function mentionIssue()
    {
        return $this->belongsToMany('App\Issue')
            ->withPivot('user_id')
            ->wherePivot('is_mention', 1);
    }

    /**
     * @return BelongsToMany
     */
    public function invitedIssue()
    {
        return $this->belongsToMany('App\Issue')
            ->withPivot('user_id')
            ->wherePivot('is_invited', 1);
    }

    /**
     * @return BelongsToMany
     */
    public function followerIssue()
    {
        return $this->belongsToMany('App\Issue')
            ->withPivot('user_id')
            ->wherePivot('is_follower', 1);
    }

    /**
     * @return BelongsToMany
     */
    public function issueStep()
    {
        return $this->belongsToMany('App\IssueStep')
            ->withPivot('user_id');
    }
}
