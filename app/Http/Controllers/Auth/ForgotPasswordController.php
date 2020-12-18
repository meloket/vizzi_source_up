<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use App\Models\Domain;
use App\User;
use Illuminate\Support\Facades\Auth;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function getEmail(Request $request)
    {
        // todo: move this to middleware!
      $domain = explode('.', $request->getHost());

      $subdomain_first = $domain[0];

      $domain = Domain::where('domain', $subdomain_first)->first();

      return view('auth/passwords/email')->with('domain', $domain);
    }
    
    public function postEmail(Request $request)
    {
        // todo: move this to middleware!
      $domain = explode('.', $request->getHost());

      $subdomain_first = $domain[0];

      $domain = Domain::where('domain', $subdomain_first)->first();

      $email = $request->get('email');
      $token = $request->get('token');

      $user = User::where('token', $token)->first();
      if ($user->email == $email) {
         Auth::login($user);
    
         $pass = $request->get('password');

         $user->password = bcrypt($pass);
         $user->save();

          session(['status', 'Email Sent Successfully - Please wait for email.']);
          
          return redirect('/');
      }

      return view('auth/passwords/email')->with('domain', $domain);
    }

    /**
     * Get the response for a successful password reset link.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendResetLinkResponse(Request $request, $response)
    {
      session(['status', 'Email Sent Successfully - Please wait for email.']);
      
      return redirect('forgot-password');
    }

    /**
     * Get the response for a failed password reset link.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
      session(['status', 'status: Something went wrong. Please contact support.']);

      return redirect('forgot-password');
    }
}
