<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

/**
 *使用的是RESTful风格，等同于每个方法上的路由
 **/
class UsersController extends Controller
{


    public function __construct()
    {
        //中间件，除了create store方法未登录可以进入，其他页面都需要登录
        $this->middleware('auth', [
            'except' => ['create', 'store','confirmEmail']//参数是类里面的方法名
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
        $users = User::paginate(10);
        return view('users.index', compact('users'));
    }

    //个人资料页面, Route::get("users/{user}", "UsersController@show")->name("users.show")
    public function show(User $user)
    {
        // $user->gravatar();
        // if(Auth::user()->can("update", $user)){
        $statuses = $user->statuses()
            ->orderBy('created_at', 'desc')
            ->paginate(30);
        return view('users.show', compact('user', 'statuses'));
        // } else {
        //   session()->flash("danger", "您不能修改别人的信息");
        //   return redirect()->back();
        // }
    }

    //创建用户, Route::post("users", "UsersController@store")->name("users.store")
    public function store(Request $request)
    {
        $this->validate($request, [
            "name" => "required|max:255|min:3",
            "email" => "required|email|unique:users|max:255",
            "password" => "required|min:6|confirmed"
        ], [
            "required" => ":attribute 必须的",
            "max" => ":attribute 最大长度",
            "min" => ":attribute 最小长度",
            "email" => ":attribute 格式错误",
            "unique" => ":attribute 已经存在",
            "confirmed" => ":attribute 不一致"
        ], [
            "name" => "用户名",
            "email" => "邮箱",
            "password" => "密码"
        ]);

        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => bcrypt($request->password)
        ]);
        $this->sendEmailConfirmationTo($user);
        session()->flash('success', '验证邮件已发送到你的注册邮箱上，请注意查收。');
        return redirect('/');
//      Auth::login($user);
//      session()->flash('success', '欢迎，您将在这里开启一段新的旅程~');
//      return redirect()->route('users.show', [$user]);
    }

    //编辑页面， Route::get("users/{user}/edit", "UsersController@edit")->name("users.edit")
    public function edit(User $user)
    {
        if (Auth::user()->can('update', $user)) {//等同于$this->authorize('update', $user);只是不会报错
            return view('users.edit', compact('user'));
        } else {
            session()->flash('danger', '你权限不足');
            Auth::logout();
            return redirect()->route("login");
        }
    }


    //更新用户, Route::patch("users/{user}", "UsersController@update")->name("users.update")
    public function update(User $user, Request $request)
    {
        $this->authorize('update', $user);//进入页面的user id与登录id不一样是会拒绝访问，跳报错页面
        $this->validate($request, [
            "name" => "required|max:255",
            "password" => "nullable|confirmed|max:255"
        ], ["required" => "必须的",
            "max" => "最大长度",
            "confirmed" => "不一致"
        ], [
            "name" => ":attribute 用户名",
            "password" => ":attribute 密码"
        ]);

        if (!empty($request->password)) {
            $bool = $user->update(["name" => $request->name, "password" => bcrypt($request->password)]);
        } else {
            $bool = $user->update(["name" => $request->name]);
        }

        if ($bool) {
            session()->flash("success", "更新成功");
            return redirect()->route("users.show", [$user]);
        } else {
            session()->flash("danger", "出错了，更新失败");
            return redirect()->back();
        }
    }

    //删除用户, Route::delete("users/{user}, "UsersController@destroy")->name("users.detory")
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);
        $user->delete();
        session()->flash('success', '成功删除用户！');
        return back();
    }

    protected function sendEmailConfirmationTo($user)
    {
        $view = 'emails.confirm';
        $data = compact('user');
        $from = 'northfox11@163.com';
        $name = 'northfox11@163.com';
        $to = $user->email;
        $subject = "感谢注册 Sample 应用！请确认你的邮箱。";
        Mail::send($view, $data, function ($message) use ($from, $name, $to, $subject) {
            $message->to($to)->subject($subject);
        });
    }

    public function confirmEmail($token)
    {
        $user = User::where('activation_token', $token)->firstOrFail();

        $user->activated = true;
        $user->activation_token = null;
        $user->save();

        Auth::login($user);
        session()->flash('success', '恭喜你，激活成功！');
        return redirect()->route('users.show', [$user]);
    }

    public function followings(User $user)
    {
        $users = $user->followings()->paginate(30);
        $title = '关注的人';
        return view('users.show_follow', compact('users', 'title'));
    }

    public function followers(User $user)
    {
        $users = $user->followers()->paginate(30);
        $title = '粉丝';
        return view('users.show_follow', compact('users', 'title'));
    }
}
