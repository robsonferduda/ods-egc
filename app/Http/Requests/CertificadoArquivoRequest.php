<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CertificadoArquivoRequest extends FormRequest
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
            'arquivo' => 'required',
            'id_modelo_certificado_moc' => 'required|min:1'
        ];
    }

    public function messages()
    {
        return [
            'arquivo.required' => 'Campo <strong>arquivo</strong> é obrigatório',
            'id_modelo_certificado_moc.required' => 'Campo <strong>modelo</strong> é obrigatório',
            'id_modelo_certificado_moc.min' => 'Campo <strong>modelo</strong> é obrigatório'
        ];
    }
}
