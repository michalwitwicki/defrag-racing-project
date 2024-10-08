<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
            'country'   =>  ['required', 'string', 'max:3']
        ])->validate();

        $pattern = '/\^\w/';

        $plainName = preg_replace($pattern, '', $input['name']);

        return User::create([
            'username'   =>  $input['username'],
            'name'       => $input['name'],
            'email'      => $input['email'],
            'oldhash'    => NULL,
            'password'   => Hash::make($input['password']),
            'country'    => $input['country'],
            'plain_name' => $plainName
        ]);
    }
}
