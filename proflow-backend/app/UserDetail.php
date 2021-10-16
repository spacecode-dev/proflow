<?php

namespace App;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class UserDetail extends Model
{
    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @param $value
     * @return Application|UrlGenerator|string|null
     */
    public function getProfilePictureAttribute($value)
    {
        if ($value) {
            $condition = Str::contains($value, ['https', 'http']);
            return $condition ? $value : url('storage/profile_picture/tmp/' . $value);
        }
        return $value;
    }

    /**
     * @param $token
     * @return string
     */
    protected function getToken($token)
    {
        $userDetail = UserDetail::where('google_token', $token)->first();
        if (!empty($userDetail)) {
            return User::find($userDetail->user_id);
        }
        return '';
    }

    /**
     * @return BelongsToMany
     */
    public function issue()
    {
        return $this->belongsToMany('App\Issue', 'user_id')
            ->withPivot('user_id');
    }
}
