<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * Card 5532: a new email address needs the current password, so a
     * stolen session can't move the account to another inbox and then
     * reset the password there.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
        ];

        if ($this->changesEmail()) {
            $rules['current_password'] = ['required', 'string', 'current_password'];
        }

        return $rules;
    }

    public function changesEmail(): bool
    {
        return $this->input('email') !== $this->user()->email;
    }
}
