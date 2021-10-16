<?php

namespace App;


use Illuminate\Database\Eloquent\Model;
use App\Traits\ObservantTrait;

class IssueFile extends Model
{
    use ObservantTrait;
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','file','type'
    ];

    protected $appends = [
        'url'
    ];

    public function getUrlAttribute()
    {
        if ($this->file) {
            return url('storage/issue_file/' . $this->file);
        }
    }

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'created_by');
    }



     //public $table='roles'; 
   


}
