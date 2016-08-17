<?php

namespace App\Repositories;

use App\User;
use App\Metrics;

class MetricsRepository
{
    /**
     * Get all of the Account for a given user.
     *
     * @param  User  $user
     * @return Collection
     */
    public function forUser(User $user)
    {
        return Metrics::where('user_id', $user->id)
                    ->orderBy('sort', 'asc')
                    ->get();
    }
}