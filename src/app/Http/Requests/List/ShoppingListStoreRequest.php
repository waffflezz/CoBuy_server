<?php

namespace App\Http\Requests\List;

use Illuminate\Foundation\Http\FormRequest;

class ShoppingListStoreRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'groupId' => 'required|exists:groups,id',
            'hidden' => 'boolean',
        ];
    }

    public function validated($key = null, $default = null): array
    {
        $data = parent::validated($key, $default);
        $data['hidden'] = $this->input('hidden', false);
        return $data;
    }
}
