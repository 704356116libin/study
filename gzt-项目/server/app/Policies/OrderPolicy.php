<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\Order;
use App\Models\User;

use Illuminate\Auth\Access\HandlesAuthorization;

class OrderPolicy
{
    use HandlesAuthorization;

    public function __construct()
    {
    }
    public function own(User $user,Order $order)
    {
        return $order->user_id == $user->id;
    }

}
