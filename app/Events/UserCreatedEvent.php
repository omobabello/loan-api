<?php

namespace App\Events;

use App\Models\User;

class UserCreatedEvent extends Event
{
    public $user; 

    /**
     * Create a new event instance.
     *
     * @return void
     */
    
    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
