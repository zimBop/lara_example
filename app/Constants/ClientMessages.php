<?php

namespace App\Constants;

class ClientMessages
{
    public const PHONE_ALREADY_REGISTERED = 'The user with this phone is already registered with Electra.';
    public const INVITE_ALREADY_SENT = 'An invitation has already been sent to this phone number.';
    public const INVITE_TEXT = '%s invites you to ride green with Electra. \n'
        . 'Accept this invite below and your first trip is on us! \n'
        . 'Follow the link %s to get started!';
    public const INVITE_SENT = 'Invitation has been sent to the specified phone number.';
}
