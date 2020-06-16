<?php

/**
 * @return string
 */
function db_driver(): string
{
    return config('database.connections.' . config('database.default') . '.driver');
}

function is_client(): bool
{
    return Auth::guard('client')->check();
}

function is_driver(): bool
{
    return Auth::guard('driver')->check();
}

function centsToDollars($cents)
{
    return number_format(($cents/100), 2, '.', ' ');
}
