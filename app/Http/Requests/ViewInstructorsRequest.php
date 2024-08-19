<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ViewInstructorsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // if the user is an admin or instructors_view permission
        if ($this->user()->can("instructors_view") || $this->user()->hasRole("admin")) {
            return true;
        }

        return false;
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
        ];
    }
}
