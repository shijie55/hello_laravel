<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $fillable = [
        'content'
    ];
    //关联数据表，相当于rails model的belongs_to
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
