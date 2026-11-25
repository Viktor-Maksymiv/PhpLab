<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Ticket $ticket): bool
    {
        return $user->isStaff() || $this->owns($user, $ticket);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Ticket $ticket): bool
    {
        return $user->isAdmin() || $this->owns($user, $ticket);
    }

    public function delete(User $user, Ticket $ticket): bool
    {
        return $user->isAdmin() || $this->owns($user, $ticket);
    }

    private function owns(User $user, Ticket $ticket): bool
    {
        return $ticket->user_id !== null && (int) $ticket->user_id === $user->id;
    }
}
