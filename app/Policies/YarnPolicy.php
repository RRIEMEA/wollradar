<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Yarn;

class YarnPolicy
{
    public function view(User $user, Yarn $yarn): bool
    {
        return $yarn->user_id === $user->id;
    }

    public function update(User $user, Yarn $yarn): bool
    {
        return $yarn->user_id === $user->id;
    }

    public function delete(User $user, Yarn $yarn): bool
    {
        return $yarn->user_id === $user->id;
    }
}
