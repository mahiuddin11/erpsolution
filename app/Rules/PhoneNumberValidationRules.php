<?php

namespace App\Rules;

use App\Models\Branch;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;

class PhoneNumberValidationRules implements Rule
{
    /**
     * @var
     */
    private $request;

    /**
     * PhoneNumberValidationRules constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
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
        $phone = '88' . $this->request->phone;
        if (Branch::select('phone')->where('phone', $phone)->first()) {
            return false;
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
        return 'The :attribute has already been used with other account.';
    }
}