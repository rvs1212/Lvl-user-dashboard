<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'id' => $this->route('id'),
        ]);
    }
    private const NAME_MAX = 255;
    private const POSTCODE_MAX = 20;


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id'         => ['required','integer','exists:users,id'],

            // user fields
            'first_name' => ['required','string','max:'.self::NAME_MAX],
            'last_name'  => ['required','string','max:'.self::NAME_MAX],
            'email'      => [
                'required',
                'email',
                'max:'.self::NAME_MAX,
                // unique in users,email except this record
                Rule::unique('users','email')->ignore($this->input('id')),
            ],
            // password may be omitted (no change) or provided to update
            'password'   => ['sometimes','nullable','string','min:8'],

            // address fields
            'country'    => ['required','string','max:'.self::NAME_MAX],
            'city'       => ['required','string','max:'.self::NAME_MAX],
            'post_code'  => ['required','string','max:'.self::POSTCODE_MAX],
            'street'     => ['required','string','max:'.self::NAME_MAX],
        ];
    }
}
