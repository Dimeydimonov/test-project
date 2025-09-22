<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Services\Interfaces\Auth\AuthServiceInterface;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class VerifyEmailController extends Controller
{
    /**
     * @var \App\Services\Interfaces\Auth\AuthServiceInterface
     */
    protected $authService;

    /**
     * @param  \App\Services\Interfaces\Auth\AuthServiceInterface  $authService
     * @return void
     */
    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
        $this->middleware('auth');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }
    /**
     * @param  \Illuminate\Foundation\Auth\EmailVerificationRequest  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function __invoke(EmailVerificationRequest $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $request->wantsJson()
                ? new JsonResponse([], 204)
                : redirect()->intended(RouteServiceProvider::HOME.'?verified=1');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        if ($response = $this->verified($request)) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect()->intended(RouteServiceProvider::HOME.'?verified=1');
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    protected function verified(Request $request)
    {
        //
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function resend(Request $request)
    {
        $user = $request->user();
        
        if ($user->hasVerifiedEmail()) {
            return $request->wantsJson()
                ? new JsonResponse([], 204)
                : redirect()->intended($this->redirectPath());
        }

        $this->authService->sendEmailVerificationNotification($user);

        return $request->wantsJson()
            ? new JsonResponse(['message' => 'Ссылка для подтверждения отправлена на вашу электронную почту.'])
            : back()->with('status', 'Ссылка для подтверждения отправлена на вашу электронную почту.');
    }

    /**
     * @return string
     */
    public function redirectPath()
    {
        if (method_exists($this, 'redirectTo')) {
            return $this->redirectTo();
        }

        return property_exists($this, 'redirectTo') ? $this->redirectTo : '/home';
    }
}
