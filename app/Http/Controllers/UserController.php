<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{

    public function __construct()
    {
        /**
         * 中间件配置权限.
         * except 除了 跳转，展示，注册，其他都需要auth中间件验证   首选，默认其他都是安全项
         * 意思是这下面的几个方法都是免检的，除此之外，都需要做用户登陆验证
         */
        $this->middleware('auth', [
            'except'=>['show','create','store','index','confirmEmail']
        ]);

        /**
         * only 只有 guest 可以使用 create 方法
         */
        $this->middleware('guest', [
            'only' => ['create']
        ]);
    }

    /**
     * 首页，暂时为展示用户，简单分页
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $users = User::paginate(8);
        return view('users.index',compact('users'));

    }

    /**
     * 跳转注册页面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * 个人信息页面
     * @param User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * 注册
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:50',
            'email'=>'required|email|unique:users|max:50',
            'password' => 'required|confirmed|min:6',
        ]);

        $user = User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>bcrypt($request->password),
        ]);

        //注册后自动登陆
//        Auth::login($user);
//        session()->flash('success','欢迎，您将在这里开启一段新的旅程~');
//        return redirect()->route('users.show', [$user]);

        //注册后发送邮箱验证
        $this->sendEmailConfirmationTo($user);
        session()->flash('success', '验证邮件已发送到你的注册邮箱上，请注意查收。');
        return redirect('/');


    }

    /**
     * 跳转到个人编辑页面
     * @param User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(User $user)
    {
        $this->authorize('update', $user);
        return view('users.edit', compact('user'));
    }

    /**
     * 更新个人信息
     * @param User $user
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(User $user, Request $request){

        $this->validate($request,[
            'name' => 'required|max:50',
            'password' => 'nullable|confirmed|min:6'
        ]);

        $data = [];
        $data['name'] = $request->name;
        if($request->password){
            $data['password'] = bcrypt($request->password);
        }
        $user->update($data);

        session()->flash('success','个人资料更新成功');

        return redirect()->route('users.show', $user->id);
    }

    /**
     * 删除用户
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(User $user)
    {
        // 删除授权策略，必须满足destroy的策略才能继续执行
        $this->authorize('destroy',$user);

        $user->delete();
        session()->flash('success', '成功删除用户！');
        return back();
    }

    /**
     * 验证token之后跳转到用户页面
     * @param $token
     * @return \Illuminate\Http\RedirectResponse
     */
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

    protected function sendEmailConfirmationTo(User $user){
        $view = 'emails.confirm';
        $data = compact('user');
        $from = 'sylar.bigb@gmail.com';
        $name = 'Sylar.Bigb';
        $to = $user->email;
        $subject = "感谢注册 Sample 应用！请确认你的邮箱。";

        Mail::send($view, $data, function ($message) use ($from, $name, $to, $subject) {
            $message->from($from, $name)->to($to)->subject($subject);
        });
    }
}
