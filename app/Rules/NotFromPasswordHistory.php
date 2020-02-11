<?php

namespace App\Rules;

use App\User;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class NotFromPasswordHistory implements Rule
{
    protected $user;

    protected $checkPrevious;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(User $user, $checkPrevious = 5)
    {
        $this->user = $user;
        $this->checkPrevious = $checkPrevious;
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
        foreach ($this->user->passwordHistory->take($this->checkPrevious) as $passwordHistory) {
            if (Hash::check($value, $passwordHistory->password)) {
                return false;
            }
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
        return 'You have already used that password.';
    }
}
