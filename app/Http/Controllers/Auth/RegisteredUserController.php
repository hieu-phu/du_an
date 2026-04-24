<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Auth\FirstLoginOtpService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    public function __construct(
        protected FirstLoginOtpService $firstLoginOtpService
    ) {
    }

    /**
     * Display the registration view (Vue via Inertia).
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'phone' => ['required', 'string', 'max:15', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'username' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
            ]);
            // $user->save();
            event(new Registered($user));

            $this->firstLoginOtpService->sendOtp($user);
            $this->firstLoginOtpService->storePendingLogin(
                $request,
                $user,
                'Email va mật khẩu',
                route('dashboard', absolute: false)
            );

            \Illuminate\Support\Facades\DB::commit();

            return redirect()->route('login.otp.view');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Lá»—i Ä‘Äƒng ký người dùng: ' . $e->getMessage());

            return back()->with('error', 'CÃ³ lá»—i xáº£y ra trong quá trÃ¬nh Ä‘Äƒng ký. Vui lòng thử láº¡i sau.');
        }
    }
}

