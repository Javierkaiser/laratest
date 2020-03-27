<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use App\Providers\RouteServiceProvider;

class VerificationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    public function verify(Request $request, User $user)
    {
        //Verificar la url
        if (! URL::hasValidSignature($request))
        {
            return \response()->json(["errors"=> [
                "mensaje"=> "Invalid verification link"
            ]], 422);
        }

        //Comprar si el usuario esta verificado
        if ($user->hasVerifiedEmail())
        {
            return \response()->json(["errors"=> [
                "mensaje"=> "Dirección ya verificada"
            ]], 422);
        }

        $user->markEmailAsVerified();
        event(new Verified($user));

        return \response()->json(["mensaje" => "Email verificado correctamente"], 200);
    }
    
    public function resend(Request $request)
    {
        $this->validate(
            $request,
            [
                'email' => ['email', 'required']
            ]
        );

        $user = User::where('email', $request->email)->first();

        if(! $user){
            return \response()->json(["errors" => [
                "email" => "No hay usuarios con ese mail"
            ]], 422);
        }

        if ($user->hasVerifiedEmail())
        {
            return \response()->json(["errors"=> [
                "mensaje"=> "Dirección ya verificada"
            ]], 422);
        }

        $user->sendEmailVerificationNotification();
        
        return \response()->json(["status" => "verification link resend"]);
    }
}
