<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use Illuminate\Contracts\Session\Session;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class ResetPasswordController extends Controller
{
    public function resetPassword(Request $request) {
        $rules = [
            'cpassword' => 'required',
            'password' => 'required|min:8|regex:/^.*(?=.{8,})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[\d\X])(?=.*[!$#%]).*$/i|confirmed'

        ];

        $customMessages = [
            'required' => 'The :attribute field is required.',
            'regex' => "Password is not matched according to password rule."
        ];

        $val = $this->validate($request, $rules,$customMessages);


        $o_status_code = sprintf('%4000s', '');
        $o_status_message = sprintf('%4000s', '');

        $mappedParams = array();
        $mappedParams['p_user_id'] = Auth::id();
        $mappedParams['p_cpassword'] = $request->get('cpassword');
        $mappedParams['p_password'] = $request->get('password');
        $mappedParams['o_status_code'] = &$o_status_code;
        $mappedParams['o_status_message'] = &$o_status_message;
        DB::executeProcedure('cpa_security.SECURITY.CHANGE_PASSWORD', $mappedParams);

        if ($mappedParams['o_status_code'] == 1) {
            Auth::logout();
            session()->flash('message',$mappedParams['o_status_message']);
            return Redirect::to('/');
        }
        $validator = \Illuminate\Support\Facades\Validator::make([], []);
        $validator->getMessageBag()->add('password', $mappedParams['o_status_message']);
        return Redirect::back()->withErrors($validator)->withInput();
    }
}
