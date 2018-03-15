<?php

namespace App\Http\Controllers;

use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StatusController extends Controller
{
    //

    public function __construct()
    {
        //此类中所有方法都需要做auth验证
        $this->middleware('auth');
    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'content'   =>  'required|max:140'
        ]);

        Auth::user()->statuses()->create([
            'content'   =>  $request->content
        ]);

        return redirect()->back();

    }


    public function destroy(Status $status)
    {
        $this->authorize('destroy', $status);
        $status->delete();
        session()->flash('success','微博已删除');
        return redirect()->back();
    }

}
