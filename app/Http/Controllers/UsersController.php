<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

/**
*使用的是RESTful风格，等同于每个方法上的路由
**/
class UsersController extends Controller
{


    public function __construct()
    {
        //中间件，除了create store方法未登录可以进入，其他页面都需要登录
        $this->middleware('auth', [
            'except' => ['create', 'store']//参数是类里面的方法名
        ]);
          //中间件，登录后进入触发,改中间件在app/Http/Middleware/RedirectIfAuthenticated.php，修改过代码
        $this->middleware('guest', [
            'only' => ['create']//参数是类里面的方法名
        ]);
    }

    //创建用户的界面，Route::get("users/create", "UsersController@create")->name("users.create")
    public function create()
    {
      return view('users.create');
    }

    //首页面， Route::get("users", "UsersController@index")->name("users.index")
    public function index()
    {
        return "index";
    }
    //个人资料页面, Route::get("users/{user}", "UsersController@show")->name("users.show")
    public function show(User $user)
    {
      // $user->gravatar();
      return view("users.show", compact("user"));
    }
    //创建用户, Route::post("users", "UsersController@store")->name("users.store")
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

      $user = User::create([
                "name"=>$request->name,
                "email"=>$request->email,
                "password"=>bcrypt($request->password)
              ]);
      Auth::login($user);
      session()->flash('success', '欢迎，您将在这里开启一段新的旅程~');
      return redirect()->route('users.show', [$user]);
    }

    //编辑页面， Route::get("users/{user}/edit", "UsersController@edit")->name("users.edit")
    public function edit(User $user)
    {
      $this->authorize('update', $user);
      return view('users.edit', compact('user'));
    }

    //更新用户, Route::patch("users/{user}", "UsersController@update")->name("users.update")
    public function update(User $user, Request $request)
    {
      $this->authorize('update', $user);//进入页面的user id与登录id不一样是会拒绝访问，跳报错页面
      $this->validate($request, [
        "name"=>"required|max:255",
        "password"=>"nullable|confirmed|max:255"
      ],["required"=>"必须的",
         "max"=>"最大长度",
         "confirmed"=>"不一致"
       ],[
         "name"=>":attribute 用户名",
         "password"=>":attribute 密码"
       ]);

       if (!empty($request->password)) {
         $bool = $user->update(["name"=>$request->name, "password"=>bcrypt($request->password)]);
       } else {
         $bool = $user->update(["name"=>$request->name]);
       }

       if ($bool) {
         session()->flash("success", "更新成功");
          return redirect()->route("users.show",[$user]);
       }else {
          session()->flash("danger", "出错了，更新失败");
          return redirect()->back();
       }
    }

    //删除用户, Route::delete("users/{user}, "UsersController@destory")->name("users.detory")
    public function destory()
    {
      return "destory";
    }
}
