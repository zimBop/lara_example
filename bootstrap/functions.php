<?php

/**
 * @return string
 */
function db_driver(): string
{
    return config('database.connections.' . config('database.default') . '.driver');
}
