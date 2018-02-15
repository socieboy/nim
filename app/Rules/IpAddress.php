<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Concerns\ValidatesAttributes;

class IpAddress implements Rule
{
    use ValidatesAttributes;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        if (request('type') == 'static') {
            return $this->validateUrl($attribute, $value);
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute must be a valid IP address.';
    }
}
