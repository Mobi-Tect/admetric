<?php

namespace App\Repositories;

use App\User;
use App\Adgroups;

class AdgroupsRepository
{
    /**
     * Get all of the Adgroups for a given user.
     *
     * @param  User  $user
     * @return Collection
     */
    public function forUser(User $user)
    {
        return Adgroups::where('user_id', $user->id)
                    ->orderBy('created_at', 'asc')
                    ->get();
    }
}