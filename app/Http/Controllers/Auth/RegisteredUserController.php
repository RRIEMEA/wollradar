<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\AdminNewUserPendingApproval;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Throwable;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
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
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => 'PENDING',
            'is_approved' => false,
        ]);

        $admins = User::query()
            ->where('is_admin', true)
            ->where('is_approved', true)
            ->get(['id', 'name', 'email']);

        app()->terminating(function () use ($admins, $user) {
            $admins->each(function (User $admin) use ($user) {
                try {
                    $admin->notify(new AdminNewUserPendingApproval($user));
                } catch (Throwable $exception) {
                    report($exception);

                    Log::warning('Admin-Benachrichtigung zur Registrierung konnte nicht versendet werden.', [
                        'admin_id' => $admin->id,
                        'admin_email' => $admin->email,
                        'pending_user_id' => $user->id,
                        'pending_user_email' => $user->email,
                    ]);
                }
            });
        });

        event(new Registered($user));

        //Auth::login($user);

        return redirect()->route('register.pending');
    }
}
