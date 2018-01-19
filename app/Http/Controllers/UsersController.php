<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

/**
*使用的是RESTful风格，等同于每个方法上的路由
**/
class UsersController extends Controller
{
    //创建用户的界面，Route::get("users/create", "UsersController@create")
    public function create()
    {
      return view('users.create');
    }

    //首页面， Route::get("users", "UsersController@index")
    public function index()
    {
        return "index";
    }
    //个人资料页面, Route::get("users/{user}", "UsersController@show")
    public function show(User $user)
    {
      $user->gravatar();
      return view("users.show", compact("user"));
    }
    //创建用户, Route::post("users", "UsersController@store")
    public function store(Request $request)
    {
      $this->validate($request, [
            "name"=>"required|max:10|min:3",
            "email"=>"required|email|unique:users|max:255",
            "password"=>"required|min:6|confirmed"
      ],[
            "required"=>":attribute 必须的",
            "max"=>":attribute 最大长度",
            "min"=>":attribute 最小长度",
            "email"=>":attribute 格式错误",
            "unique"=>":attribute 已经存在",
            "confirmed"=>":attribute 不一致"
      ],[
            "name"=>"用户名",
            "email"=>"邮箱",
            "password"=>"密码"
      ]);
    }

    //编辑页面， Route::get("users/{user}/edit", "UsersController@edit")
    public function edit()
    {
      return "edit";
    }

    //更新用户, Route::patch("users/{user}", "UsersController@update")
    public function update()
    {
      return "update";
    }

    //删除用户, Route::delete("users/{user}, "UsersController@destory")
    public function destory()
    {
      return "destory";
    }
}
