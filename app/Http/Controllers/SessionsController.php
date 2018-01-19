<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class SessionsController extends Controller
{
    public function create()
    {
      return view('sessions.create');
    }

    public function store(Request $request)
    {
      //表单验证返回值是数组
      $credentials = $this->validate($request, [
                          "email"=>"required|email",
                          "password"=>"required|min:6"
                        ],[
                          "required"=>":attribute 是必须的",
                          "email"=>":attribute 格式错误",
                          "min"=>":attribute 长度不够"
                        ],[
                          "email"=>"邮箱",
                          "password"=>"密码"
                        ]);
      if (Auth::attempt($credentials, $request->has('remember'))) {
        session()->flash('success', '欢迎回来！');
        return redirect()->route('users.show', [Auth::user()]);
      } else {
          session()->flash('danger', '很抱歉，您的邮箱和密码不匹配');
          return redirect()->back();
      }
    }

    public function destroy()
    {
      Auth::logout();
      session()->flash('success', '您已成功退出！');
      return redirect()->route("login");
    }
}
