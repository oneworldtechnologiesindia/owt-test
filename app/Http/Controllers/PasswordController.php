<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PasswordController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index()
    {
        return view('password.view');
    }

    public function update(Request $request)
    {
        if ($request->ajax()) {
            $user = Auth::user();
            $rules = array(
                'current_password' => 'required',
                'password' => 'required|confirmed|min:8|string|different:current_password',
            );

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $result = ['status' => false, 'errors' => $validator->errors(), 'data' => []];
                return response()->json($result, 400);
            } elseif (!(Hash::check($request->current_password, $user->password))) {
                $result = ['status' => false, 'errors' => ['current_password' => trans('translation.Current_password_do_not_match')], 'data' => []];
                return response()->json($result, 400);
            } else {
                $user->password = Hash::make($request->password);
                if ($user->save()) {
                    $result = ['status' => true, 'message' => trans('translation.Password update successfully'), 'data' => []];
                } else {
                    $result = ['status' => false, 'message' => trans('translation.Password update fail'), 'data' => []];
                }
            }
            return response()->json($result);
        }
    }
}
