<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Tools\SocialiteTool;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    public function wechat_login(SocialiteTool $tool)
    {
        $user = $tool->wechat_login();
        dd($user);
    }
}
