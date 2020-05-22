<?php

namespace App\Constants;

class ClientMessages
{
    public const PHONE_ALREADY_REGISTERED = 'The user with this phone number has already registered in Electra app.';
    public const ALL_INVITES_ALREADY_USED = 'Sorry, you’ve reached the limit of invites per person.';
    public const INVITE_ALREADY_SENT = 'Invite has already been sent to this phone number.';
    public const INVITE_TEXT = '%s John Snow invited you to try Electra taxi service. \n'
        . 'Once you accept the invite, you will get a free trip. \n'
        . 'Follow this link %s to get your free trip with Electra.';
    public const INVITE_SENT = 'Invitation has been sent to the specified phone number.';
}
