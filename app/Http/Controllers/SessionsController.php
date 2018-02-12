<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class SessionsController extends Controller
{
    public function __construct()
    {
        //中间件，登录后进入触发,改中间件在app/Http/Middleware/RedirectIfAuthenticated.php，修改过代码
        $this->middleware('guest', [
            'only' => ['create']//参数是类里面的方法名
        ]);
    }

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
          if(Auth::user()->activated) {
              session()->flash('success', '欢迎回来！');
              return redirect()->intended(route('users.show', [Auth::user()]));//intended登录后会回到登录前想进的页面
          } else {
              Auth::logout();
              session()->flash('warning', '你的账号未激活，请检查邮箱中的注册邮件进行激活。');
              return redirect('/');
          }
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
