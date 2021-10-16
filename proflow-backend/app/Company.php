<?php

namespace App;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Company extends Model
{


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'workspace_url', 'logo', 'created_by'
    ];


    protected $hidden = [
        'pivot',
    ];

    /**
     * @param $value
     * @return Application|UrlGenerator|string
     */
    public function getLogoAttribute($value)
    {
        return ($value) ? url('storage/logo/' . $value) : '';
    }

    /**
     * @return BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany('App\User')->withPivot('role_id');
    }

    /**
     * @return HasMany
     */
    public function groups()
    {
        $id = Auth::id();
        return $this->hasMany('App\Group')
            ->where(function ($query) use ($id) {
                $query->whereRaw("find_in_set(" . $id . ",member_id)")
                    ->orWhere('created_by', $id);
            });
    }

    /**
     * @param $groupId
     * @return HasMany
     */
    public function groupId($groupId)
    {
        $id = Auth::id();
        return $this->hasMany('App\Group')
            ->where('id', $groupId);
    }

    /**
     * @return HasMany
     */
    public function tags()
    {
        return $this->hasMany('App\Tag');
    }

    /**
     * @return HasMany
     */
    public function feedbacks()
    {
        return $this->hasMany('App\Feedback');
    }

}
