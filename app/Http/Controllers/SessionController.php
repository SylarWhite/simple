<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class SessionController extends Controller
{
    public function __construct()
    {
        /**
         * 只有 guest 才可以使用 create 方法注册
         * 只让未登录用户访问注册页面：
         */
        $this->middleware('guest', [
            'only'=>['create']
        ]);
    }

    //
    public function create()
    {
        return view('sessions.create');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $credentials = $this->validate($request, [
           'email' => 'required|email|max:255',
           'password' => 'required'
        ]);

        // $request->has('remember') 记住我
        if(Auth::attempt($credentials, $request->has('remember'))){
            if(Auth::user()->activated) {
                session()->flash('success', '欢迎回来！');
                /**
                 * intended 方法，该方法可将页面重定向到上一次请求尝试访问的页面上
                 * 并接收一个默认跳转地址参数，当上一次请求记录为空时，跳转到默认地址上。
                 */
                return redirect()->intended(route('users.show', [Auth::user()]));
            }else {
                Auth::logout();
                session()->flash('warning','您的邮箱未激活');
                return redirect('/');
            }

        }else{
            session()->flash('danger', '很抱歉，您的邮箱和密码不匹配');
            return redirect()->back();
        }

    }

    public function destroy()
    {
        Auth::logout();
        session()->flash('success','您已成功退出');
        return redirect('login');
    }
}
