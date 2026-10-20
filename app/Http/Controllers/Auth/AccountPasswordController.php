<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

/** "Change password" pages; both forms submit to PasswordController@update (PUT /password). */
class AccountPasswordController extends Controller
{
    public function edit(): View
    {
        return view('admin.profile.password');
    }

    public function editSalesman(): View
    {
        return view('front.change_password');
    }
}
