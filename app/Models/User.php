<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            $user->activation_token = str_random(30);
        });
    }

    //与gravatar交互得到图像
    public function gravatar($size = '100')
    {
        //$this->attributes["email"] 得到当前实例化该类时的数据库里的值
        $hash = md5(strtolower(trim($this->attributes['email'])));//trim去空格， strtolower变小写， md5进行转码
        return "http://www.gravatar.com/avatar/$hash?s=$size";//根据md5转码得到图片链接，size设置图片大小
    }
}
