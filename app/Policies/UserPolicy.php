<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
     public function update(User $currentUser, User $user)
     {
         return $currentUser->id === $user->id;
     }

     public function delete(User $currentUser, User $user)
     {
        return $currentUser->is_admin && $currentUser->id !== $user->id;//用户为管理员，且删除的不是自己
     }
}
