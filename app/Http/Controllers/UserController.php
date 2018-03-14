<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

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
            'except'=>['show','create','store','index']
        ]);

        /**
         * only 只有 guest 可以使用 create 方法
         */
        $this->middleware('guest', [
            'only' => ['create']
        ]);
    }

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
        Auth::login($user);

        session()->flash('success','欢迎，您将在这里开启一段新的旅程~');
        return redirect()->route('users.show', [$user]);
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


    public function destroy(User $user)
    {
        // 删除授权策略，必须满足destroy的策略才能继续执行
        $this->authorize('destroy',$user);

        $user->delete();
        session()->flash('success', '成功删除用户！');
        return back();
    }
}
