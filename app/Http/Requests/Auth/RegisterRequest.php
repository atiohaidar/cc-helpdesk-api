<?php
namespace App\Http\Requests\Auth;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Set true jika semua user bisa akses
    }

    public function rules()
    {
        return [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|string|in:user,admin'
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'Email wajib diisi!',
            'email.email' => 'Format email tidak valid!',
            'password.min' => 'Password minimal 6 karakter!',
            'role.in' => 'Role hanya bisa user atau admin!'
        ];
    }
    protected function failedValidation (Validator $validator){
        // ini untuk inputannya, kli inputannya ada yang keliru, ngatur pesan error nya didsini (ini belum nge cek data yang ada di dalem database, baru inputan user doang)
        // ini kita nge ubah variabel default yang udah ada di laravel, jadi kita custom sendiri
        throw new HttpResponseException(response([
            "errors" => $validator->getMessageBag()
        ], 400));


    }
}
