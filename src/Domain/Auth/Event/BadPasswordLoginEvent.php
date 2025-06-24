<?php

namespace App\Domain\Auth\Event;

use App\Entity\User;

class BadPasswordLoginEvent
{
    public function __construct(private readonly User $user)
    {
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
