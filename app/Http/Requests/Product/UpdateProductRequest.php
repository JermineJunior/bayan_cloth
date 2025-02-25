<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
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
            'barcode' =>['max:255',Rule::unique('products','barcode')->ignore($this->product->id , 'id')],
            'name' => ['required','string','max:255',Rule::unique('products','name')->ignore($this->product->id , 'id')],
            'category_id' => 'required|exists:categories,id',
            'stock_id.*' => 'required|exists:stocks,id',
            'purchase_price' => 'required|numeric|min:0',
            'reorder_level' => 'required|integer|min:0',
            'expire_date' => 'required|date|after_or_equal:today',
            'units.*.unit_id' => 'required|exists:units,id',
        ];
    }

    public function messages()
    {
        return [
            'barcode.unique' => 'الباركود يجب أن يكون فريدًا.',
            'name.required' => 'يرجى إدخال اسم المنتج.',
            'name.unique' => 'اسم المنتج يجب أن يكون فريدًا.',
            'category_id.required' => 'يرجى اختيار القسم.',
            'category_id.exists' => 'القسم المحدد غير موجود.',
            'stock_id.*.required' => 'يرجى اختيار المخزن.',
            'stock_id.*.exists' => 'المخزن المحدد غير موجود.',
            'purchase_price.required' => 'يرجى إدخال سعر الشراء.',
            'reorder_level.required' => 'يرجى إدخال مستوى إعادة الطلب.',
            'expire_date.required' => 'يرجى تحديد تاريخ الصلاحية.',
            'expire_date.after_or_equal' => 'تاريخ الصلاحية يجب أن يكون اليوم أو بعده.',
            'units.*.unit_id.required' => 'يرجى اختيار الوحدة.',
            'units.*.unit_id.exists' => 'الوحدة المحددة غير موجودة.',
        ];
    }
}
