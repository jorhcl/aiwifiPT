<?php

/**
 *
 * @author. Jorge Cortes Lopez
 *
 * Class   DeleteContactRequest
 *      Class to validate contact delete
 *
 *
 *
 */

namespace App\Http\Requests\Contact;

use Illuminate\Foundation\Http\FormRequest;

class DeleteContactRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:contacts,id'
        ];
    }

    /**
     *
     *  validation to make sure that the id is on the url
     *
     */
    public function prepareForValidation(): void
    {

        $this->merge([
            'id' => $this->route('id'),
        ]);
    }
}
