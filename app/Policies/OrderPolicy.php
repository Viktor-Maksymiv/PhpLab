<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Order $order): bool
    {
        return $this->canManage($user, $order);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Order $order): bool
    {
        return $this->canManage($user, $order);
    }

    public function delete(User $user, Order $order): bool
    {
        return $this->canManage($user, $order);
    }

    public function pay(User $user, Order $order): bool
    {
        return $this->canManage($user, $order);
    }

    private function canManage(User $user, Order $order): bool
    {
        return $user->isStaff() || (int) $order->user_id === $user->id;
    }
}
