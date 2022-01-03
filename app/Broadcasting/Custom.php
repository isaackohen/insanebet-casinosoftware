<?php

namespace App\Broadcasting;

class Custom
{
    /**
     * Create a new channel instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Authenticate the user's access to the channel.
     *
     * @param $user
     * @return array|bool
     */
    public function join($user, $id)
    {
        if ($id === 'Guest') {
            return true;
        }

        return auth('sanctum')->guest() ? false : $user->_id === $id;
    }
}