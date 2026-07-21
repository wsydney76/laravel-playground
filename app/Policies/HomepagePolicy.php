<?php

namespace App\Policies;

use App\Models\Homepage;
use App\Models\User;

class HomepagePolicy
{
    /**
     * Everyone (including guests) may view the homepage data.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Homepage $homepage): bool
    {
        return true;
    }

    /**
     * Only admins may create, update, or delete the homepage record.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Homepage $homepage): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Homepage $homepage): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, Homepage $homepage): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Homepage $homepage): bool
    {
        return $user->isAdmin();
    }
}

