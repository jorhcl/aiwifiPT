<?php

/**
 *
 * @author. Jorge Cortes Lopez
 *
 * Class   UploadContactsRequest
 *      Class to validate contacts file
 *
 *
 *
 */


namespace App\Http\Requests\Contact;

use Illuminate\Foundation\Http\FormRequest;

class UploadContactsRequest extends FormRequest
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
            //
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ];
    }
}
