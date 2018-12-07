<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Request;
use Illuminate\Validation\Rule;
use App\Models\GoodSku;

class OrderRequest extends Request
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
            // 判断用户提交的地址 ID 是否存在于数据库并且属于当前用户
            // 后面这个条件非常重要，否则恶意用户可以用不同的地址 ID 不断提交订单来遍历出平台所有用户的收货地址
            // 'address_id'     => ['required', Rule::exists('harvest_addresses', 'id')->where('c_id', session('uid'))],
            // 'g_sku_id'       => [
            //                         'required',
            //                         function ($attribute, $value, $fail) {
            //                             if (!GoodSku::where('id', $value)) {
            //                                 $fail('该商品不存在');
            //                                 return;
            //                             }
            //                         }
            //                     ]
        ];
    }
}
