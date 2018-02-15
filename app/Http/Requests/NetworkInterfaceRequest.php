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
            'type' => 'required|in:dhcp,static',
            'ip_address' => ['required_if:type,static', new IpAddress],
            'netmask' => ['required_if:type,static', new IpAddress],
            'gateway' => ['required_if:type,static', new IpAddress],
            'dns' => ['required_if:type,static', new IpAddress],
        ];
    }
}
