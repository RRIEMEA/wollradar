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
    private const STATUS_PENDING = 'PENDING';
    private const STATUS_APPROVED = 'APPROVED';
    private const STATUS_REJECTED = 'REJECTED';
    private const STATUS_DEACTIVATED = 'DEACTIVATED';

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
            'status' => self::STATUS_APPROVED,
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
            'status' => self::STATUS_REJECTED,
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

    public function deactivate(Request $request, User $user): RedirectResponse
    {
        if (! $user->is_approved) {
            return redirect()
                ->route('admin.users.pending')
                ->with('status', 'Nur freigegebene Benutzer können deaktiviert werden.');
        }

        if ($request->user()->is($user)) {
            return redirect()
                ->route('admin.users.pending')
                ->with('status', 'Du kannst dein eigenes Konto nicht deaktivieren.');
        }

        $user->forceFill([
            'status' => self::STATUS_DEACTIVATED,
            'is_approved' => false,
            'is_admin' => false,
            'approved_at' => null,
            'approved_by' => null,
        ])->save();

        return redirect()
            ->route('admin.users.pending')
            ->with('status', 'Benutzer wurde deaktiviert.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->is_approved || ! in_array($user->status, [self::STATUS_DEACTIVATED, self::STATUS_REJECTED], true)) {
            return redirect()
                ->route('admin.users.pending')
                ->with('status', 'Gelöscht werden können nur deaktivierte oder abgelehnte Benutzer.');
        }

        $email = $user->email;
        $user->delete();

        return redirect()
            ->route('admin.users.pending')
            ->with('status', "Benutzer wurde gelöscht: {$email}");
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
            ->where(function ($query) {
                $query->whereNull('status')->orWhere('status', self::STATUS_PENDING);
            })
            ->orderByDesc('created_at')
            ->get(['id','name','email','status','created_at','is_admin','is_approved','privacy_acknowledged_at']);

        $rejectedUsers = User::query()
            ->where('status', self::STATUS_REJECTED)
            ->orderByDesc('created_at')
            ->get(['id','name','email','status','created_at','is_admin','is_approved','privacy_acknowledged_at']);

        $approvedUsers = User::query()
            ->where('is_approved', true)
            ->orderByDesc('created_at')
            ->get(['id','name','email','status','created_at','is_admin','is_approved','privacy_acknowledged_at']);

        $deactivatedUsers = User::query()
            ->where('status', self::STATUS_DEACTIVATED)
            ->orderByDesc('created_at')
            ->get(['id','name','email','status','created_at','is_admin','is_approved','privacy_acknowledged_at']);

        return view('admin.users.pending', compact(
            'pendingUsers',
            'rejectedUsers',
            'approvedUsers',
            'deactivatedUsers'
        ));
    }

}
