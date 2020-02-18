<?php

namespace App\Http\Controllers\Auth;

use App\Ipuser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function username()
    {
        return 'username';
    }



    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {

        $ret = $this->guard()->attempt(
            $this->credentials($request), $request->filled('remember')
        );

        if ($ret == false)
            return $ret;

        if ($this->checkIp($request) == false){
            $this->guard()->logout();

            $request->session()->invalidate();
            throw ValidationException::withMessages([
                $this->username() => ['Acceso por IP bloqueado'],
            ]);

        }

        return $ret;
    }


    private function checkIp($request){

        $ips = Ipuser::getIpuser($request->user()->id);
        if ($ips != null){
            if (!in_array($request->ip(), $ips)) {
                return false;
            }
        }

        return true;

    }


    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {

        return $request->only($this->username(), 'password', 'blocked');
    }



    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {

        activity()
            ->causedBy($user)
            ->withProperties([
                'username' => $user->username,
                'ip' => $request->ip()
            ])
            ->log('Login');

        $data['login_at'] = date('Y-m-d H:i:s');
        $user->update($data);
    }

    //  /**
    //  * Get the failed login response instance.
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @return \Symfony\Component\HttpFoundation\Response
    //  *
    //  * @throws \Illuminate\Validation\ValidationException
    //  */
    // protected function sendFailedLoginResponse(Request $request)
    // {

    //     activity()
    //         ->withProperties(['username' => $request->input('username')])
    //         ->log('Failed Login');

    //     throw ValidationException::withMessages([
    //         $this->username() => [trans('auth.failed')],
    //     ]);
    // }



}
