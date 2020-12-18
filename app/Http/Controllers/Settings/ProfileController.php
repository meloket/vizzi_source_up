<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use File;

class ProfileController extends Controller
{
    /**
     * Update the user's profile information.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $loggeduser = auth()->user();
        $user = $request->user();
        if ($loggeduser->id != $user->id) {
            die();
        }

        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$user->id,
        ]);

        return tap($user)->update($request->only('name', 'email', 'user_timezone_region', 'user_timezone'));
    }

    public function avatar(Request $request)
    {
        $file = $request->file('file');
        $user = auth()->user();
        if ($file) {
            if ($user->avatar != 'default.jpg') {
                File::delete(public_path('data/'.$user->avatar));
            }
            $new_name = rand() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('assets/img/avatar/'), $new_name);
            $user->avatar = $new_name;
        }
        $user->save();
        return response()->json($user);
    }
}
