<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\UserApprovedNotification;
use App\Notifications\UserRejectedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class UserApprovalController extends Controller
{
    public function index(): View
    {
        return $this->pending();
    }

    public function approve(Request $request, User $user): RedirectResponse
    {
        // Optional: verhindert, dass man sich selbst "kaputt approved" oder unnötig updatet
        if ($user->is_approved) {
            return redirect()
                ->route('admin.users.pending')
                ->with('status', 'Benutzer war bereits freigegeben.');
        }

        // Wichtig: forceFill umgeht fillable-Probleme sauber
        $user->forceFill([
            'is_approved' => true,
            'status' => 'APPROVED',         // empfohlen, weil du bereits status=PENDING nutzt
            'approved_at' => now(),
            'approved_by' => $request->user()->id,
        ])->save();

        app()->terminating(function () use ($user) {
            try {
                $user->notify(new UserApprovedNotification());
            } catch (Throwable $exception) {
                report($exception);

                Log::warning('Freigabe-Mail konnte nicht versendet werden.', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                ]);
            }
        });

        return redirect()
            ->route('admin.users.pending')
            ->with('status', 'Benutzer wurde freigegeben.');
    }

    public function reject(Request $request, User $user): RedirectResponse
    {
        if ($user->is_approved) {
            return redirect()
                ->route('admin.users.pending')
                ->with('status', 'Freigegebene Benutzer können hier nicht abgelehnt werden.');
        }

        // Variante 1 (empfohlen): User behalten, aber Status setzen
        $user->forceFill([
            'status' => 'REJECTED',
            'is_approved' => false,
            'approved_at' => null,
            'approved_by' => null,
        ])->save();

        app()->terminating(function () use ($user) {
            try {
                $user->notify(new UserRejectedNotification());
            } catch (Throwable $exception) {
                report($exception);

                Log::warning('Ablehnungs-Mail konnte nicht versendet werden.', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                ]);
            }
        });

        return redirect()
            ->route('admin.users.pending')
            ->with('status', 'Benutzer wurde abgelehnt.');
    }

    public function makeAdmin(User $user): RedirectResponse
    {
        $user->is_admin = true;
        $user->save();

        return back()->with('status', "Benutzer ist jetzt Admin: {$user->email}");
    }

    public function removeAdmin(User $user): RedirectResponse
    {
        // Safety: prevent self-demotion
        if (auth()->id() === $user->id) {
            return back()->with('status', 'Du kannst dir deine eigenen Admin-Rechte nicht entziehen.');
        }

        $user->is_admin = false;
        $user->save();

        return back()->with('status', "Admin-Rechte entfernt: {$user->email}");
    }

    public function pending(): View
    {
        $pendingUsers = User::query()
            ->where(function ($q) {
                $q->whereNull('is_approved')->orWhere('is_approved', false);
            })
            ->orderByDesc('created_at')
            ->get(['id','name','email','status','created_at','is_admin','is_approved']);

        $approvedUsers = User::query()
            ->where('is_approved', true)
            ->orderByDesc('created_at')
            ->get(['id','name','email','status','created_at','is_admin','is_approved']);

        return view('admin.users.pending', compact('pendingUsers', 'approvedUsers'));
    }

}
