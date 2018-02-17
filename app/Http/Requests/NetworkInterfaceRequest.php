<?php

namespace App\Http\Requests;

use App\Rules\IpAddress;
use Illuminate\Foundation\Http\FormRequest;

class NetworkInterfaceRequest extends FormRequest
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
            'conf_type' => 'required|in:dhcp,static',
            'ip_address' => ['required_if:conf_type,static', new IpAddress],
            'netmask' => ['required_if:conf_type,static', new IpAddress],
            'gateway' => ['required_if:conf_type,static', new IpAddress],
            'dns' => ['required_if:conf_type,static', new IpAddress],
            'metric' => ['required_if:conf_type,static|numeric'],
        ];
    }
}
