<?php

namespace App\Http\Controllers\Auth;

use App\Contracts\Authorization\AuthContact;
use App\Contracts\ForgotPasswordEmailContract;
use App\Http\Controllers\Controller;
use App\Managers\Authorization\AuthorizationManager;
use App\Managers\ForgotPasswordEmailManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class ForgotPassword2Controller
 * @package App\Http\Controllers\Auth
 */
class ForgotPassword2Controller extends Controller
{
    /** @var AuthorizationManager */
    private $authManager;

    /** @var ForgotPasswordEmailManager */
    private $forgotPasswordEmailManager;

    public function __construct(AuthContact $authManager, ForgotPasswordEmailContract $forgotPasswordEmailManager)
    {
        $this->authManager = $authManager;
        $this->forgotPasswordEmailManager = $forgotPasswordEmailManager;
    }

    public function forgotPassword()
    {
        return view('forgotPassword');
    }

    public function forgotPasswordEmail(Request $request)
    {
        // FIXME: FORGOT PASSWORD. PRE-REQUISITE: email column of cpa_security.sec_users table should be fill-up with unique user email. Procedure should do that.
        // Generated token/hash/otp (or whatever it is called!) should be unique!
        $email = $request->post('email');
        $user = $this->authManager->findUserByEmail($email);

        if($user) {
            $result = $this->authManager->generatePin($user);
            if($result['statusCode'] === 1) {
                $pin = $result['o_generated_pin']; // FIXME: FORGOT PASSWORD. CHANGE KEY AS REQUIRED.
                try {
                    if( $pin && ($status = $this->forgotPasswordEmailManager->send($email, $pin)) ) {
                        session()->flash('m-class', 'alert-success');
                        return redirect()->route('forgot-password')->with('message', 'Please check your email (also spam folder) and reset your password!');
                    }
                } catch(\Exception $exception) {
                    session()->flash('m-class', 'alert-danger');
                    return redirect()->route('forgot-password')->with('message', $exception->getMessage());
                }
            }
        } else {
            session()->flash('m-class', 'alert-danger');
            return redirect()->route('forgot-password')->with('message', 'User Not found!');
        }

        session()->flash('m-class', 'alert-danger');
        return redirect()->route('forgot-password')->with('message', 'User Not found!');
    }

    public function resetPassword(Request $request, $pin)
    {
        return view('anonResetPassword');
    }

    public function resetPasswordPost(Request $request, $pin) {
        $rules = [
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
        $mappedParams['p_pin'] = $pin;
        $mappedParams['p_password'] = $request->get('password');
        $mappedParams['o_status_code'] = &$o_status_code;
        $mappedParams['o_status_message'] = &$o_status_message;
        // DB::executeProcedure('cpa_security.SECURITY.RESET_PASSWORD', $mappedParams); // FIXME: FORGOT PASSWORD. ENABLE PROCEDURE WHEN REAL PROCEDURE IS WRITTEN!

        if ($mappedParams['o_status_code'] == 1) {
            Auth::logout();
            session()->flash('message', $mappedParams['o_status_message']);
            return redirect()->to('/');
        }

        $validator = \Illuminate\Support\Facades\Validator::make([], []);
        $validator->getMessageBag()->add('password', $mappedParams['o_status_message']);
        return redirect()->back()->withErrors($validator)->withInput();
    }
}
