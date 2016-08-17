<?php

namespace App\Repositories;

use App\User;
use App\Keyword;

class KeywordRepository
{
    /**
     * Get all of the Keyword for a given user.
     *
     * @param  User  $user
     * @return Collection
     */
    public function forUser(User $user)
    {
        return Keyword::where('user_id', $user->id)
                    ->orderBy('created_at', 'asc')
                    ->get();
    }
}