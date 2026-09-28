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
     * Convert blank prices to 0 before validation, so an existing unit can be
     * saved with a 0 price instead of failing on the NOT NULL price columns.
     */
    protected function prepareForValidation(): void
    {
        if (! is_array($this->units)) {
            return;
        }

        $this->merge([
            'units' => collect($this->units)->map(function ($unit) {
                if (! is_array($unit)) {
                    return $unit;
                }

                foreach (['price', 'price_purchase'] as $field) {
                    if (! array_key_exists($field, $unit) || $unit[$field] === null || $unit[$field] === '') {
                        $unit[$field] = 0;
                    }
                }

                return $unit;
            })->all(),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'barcode' =>['max:255',Rule::unique('products','barcode')->ignore($this->product->id,'id')],
            'name' => ['required','string','max:255',Rule::unique('products','name')->ignore($this->product->id,'id')],
            'category_id' => 'required|exists:categories,id',
            'stock_id.*' => 'required|exists:stocks,id',
            'reorder_level' => 'nullable|integer|min:0',
            'units.*.unit_id' => 'required|exists:units,id',
            'units.*.price' => 'nullable|numeric|min:0',
            'units.*.price_purchase' => 'nullable|numeric|min:0',
        ];
    }

    public function messages()
    {
        return [
            'barcode.unique' => 'الباركود يجب أن يكون فريدًا.',
            'name.required' => 'يرجى إدخال اسم المنتج.',
            'name.unique' => 'اسم المنتج يجب أن يكون فريدًا.',
            'category_id.required' => 'يرجى اختيار الشركة.',
            'category_id.exists' => 'الشركة المحدد غير موجود.',
            'stock_id.*.required' => 'يرجى اختيار المخزن.',
            'stock_id.*.exists' => 'المخزن المحدد غير موجود.',
            'reorder_level.required' => 'يرجى إدخال مستوى إعادة الطلب.',
            'expire_date.required' => 'يرجى تحديد تاريخ الصلاحية.',
            'expire_date.after_or_equal' => 'تاريخ الصلاحية يجب أن يكون اليوم أو بعده.',
            'units.*.unit_id.required' => 'يرجى اختيار الوحدة.',
            'units.*.unit_id.exists' => 'الوحدة المحددة غير موجودة.',
            'units.*.price.numeric' => 'سعر البيع يجب أن يكون رقمًا.',
            'units.*.price.min' => 'سعر البيع يجب أن يكون أكبر من أو يساوي صفر.',
            'units.*.price_purchase.numeric' => 'سعر الشراء يجب أن يكون رقمًا.',
            'units.*.price_purchase.min' => 'سعر الشراء يجب أن يكون أكبر من أو يساوي صفر.',
        ];
    }
}
