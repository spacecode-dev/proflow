<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ObservantTrait;

class Group extends Model
{
    use ObservantTrait;

    /**
     * @var array
     */
    protected $fillable = [
        'id', 'name', 'member_id', 'company_id', 'created_by'
    ];

    /**
     * @var array
     */
    protected $appends = ['member_list'];

    /**
     * @return mixed
     */
    protected function getMemberListAttribute()
    {
        return User::whereIn('id', explode(",", $this->member_id))->get();
    }
}
