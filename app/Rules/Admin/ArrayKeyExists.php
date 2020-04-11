<?php

namespace App\Rules\Admin;

use Illuminate\Contracts\Validation\Rule;

class ArrayKeyExists implements Rule
{
    private $array;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($array)
    {
        $this->array = $array;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return array_key_exists($value, $this->array);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Submitted :attribute value does not exist.';
    }
}
