<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\UserApprovedNotification;
use App\Notifications\UserRejectedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AdminUserApprovalNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_receives_an_email_notification_when_approved(): void
    {
        Notification::fake();

        $admin = User::factory()->create([
            'is_admin' => true,
            'is_approved' => true,
            'status' => 'APPROVED',
        ]);

        $pendingUser = User::factory()->create([
            'is_admin' => false,
            'is_approved' => false,
            'status' => 'PENDING',
        ]);

        $response = $this
            ->actingAs($admin)
            ->patch(route('admin.users.approve', $pendingUser));

        $response->assertRedirect(route('admin.users.pending'));

        $pendingUser->refresh();

        $this->assertTrue($pendingUser->is_approved);
        $this->assertSame('APPROVED', $pendingUser->status);
        $this->assertNotNull($pendingUser->approved_at);
        $this->assertSame($admin->id, $pendingUser->approved_by);

        Notification::assertSentTo($pendingUser, UserApprovedNotification::class);
    }

    public function test_user_receives_an_email_notification_when_rejected(): void
    {
        Notification::fake();

        $admin = User::factory()->create([
            'is_admin' => true,
            'is_approved' => true,
            'status' => 'APPROVED',
        ]);

        $pendingUser = User::factory()->create([
            'is_admin' => false,
            'is_approved' => false,
            'status' => 'PENDING',
        ]);

        $response = $this
            ->actingAs($admin)
            ->patch(route('admin.users.reject', $pendingUser));

        $response->assertRedirect(route('admin.users.pending'));

        $pendingUser->refresh();

        $this->assertFalse($pendingUser->is_approved);
        $this->assertSame('REJECTED', $pendingUser->status);
        $this->assertNull($pendingUser->approved_at);
        $this->assertNull($pendingUser->approved_by);

        Notification::assertSentTo($pendingUser, UserRejectedNotification::class);
    }
}
