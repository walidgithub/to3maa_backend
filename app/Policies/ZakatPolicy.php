<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Zakat;
use Illuminate\Auth\Access\Response;

class ZakatPolicy
{
    /**
     * Determine whether the user can permanently delete the model.
     */
    public function modify(User $user, Zakat $product): Response
    {
        return $user->id === $product->user_id ? Response::allow() : Response::deny('You do not own this zakat');
    }
}
