<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreClidocs extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return [
            'cliente_id' => ['required', 'integer', Rule::unique('clidocs')],
            'img1' => ['string','nullable'],
            'img2' => ['string','nullable'],
        ];


    }

    public function messages() {
        return [
            'cliente_id.unique' => 'Existe documento scaneado, borrar antes de scanear.',
        ];
    }
}
