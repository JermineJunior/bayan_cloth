<?php

namespace App\Http\Requests\Purchase;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePurchaseRequest extends FormRequest
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
            'supplier_id' => 'nullable|exists:tree4s,id',
            'invoice_number' => 'required|numeric',
            'date' => 'required|date',
            'sub_total' => 'required|numeric|min:0',
            'discount_amount' => 'numeric|min:0',
            'total' => 'required|numeric|min:0',
            'note' => 'nullable|string|max:1000',
            'cash_name' => 'nullable|exists:tree4s,id',
            'bank_name' => 'nullable|exists:tree4s,id',
            'cash_amount_total' => 'nullable|numeric',
            'bank_amount_total' => 'nullable|numeric',
            'transaction_no' => ['nullable', 'string', 'max:100', Rule::requiredIf(fn () => (float) str_replace(',', '', $this->bank_amount_total ?? '') > 0)],

            'product_id' => 'required|array',
            'product_id.*' => 'required|exists:products,id',

            'stock_id' => ['required', Rule::exists('branch_stock', 'stock_id')->where('branch_id', session('branch_id'))],

            'unit_id' => 'required|array',
            'unit_id.*' => 'required|numeric|exists:units,id',

            'qty' => 'required|array',
            'qty.*' => 'required|numeric|min:1',

            'price' => 'required|array',
            'price.*' => ['required', 'regex:/^\d+(\.\d{1,2})?$/'],

            'row_sub_total' => 'required|array',
            'row_sub_total.*' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'supplier_id.required' => 'يجب اختيار العميل.',
            'supplier_id.exists' => 'العميل المحدد غير موجود في النظام.',

            'invoice_number.required' => 'يجب إدخال رقم الفاتورة.',
            'invoice_number.numeric' => 'يجب أن يكون رقم الفاتورة عددًا صحيحًا.',

            'date.required' => 'يجب إدخال تاريخ الفاتورة.',
            'date.date' => 'تاريخ الفاتورة غير صالح.',

            'total_with_tax.required' => 'يجب إدخال المبلغ الإجمالي مع الضريبة.',
            'total_with_tax.numeric' => 'يجب أن يكون المبلغ الإجمالي مع الضريبة رقمًا صحيحًا أو عشريًا.',

            'discount_amount.numeric' => 'يجب أن يكون الخصم رقمًا صحيحًا أو عشريًا.',

            'tax_amount.required' => 'يجب إدخال قيمة الضريبة.',
            'tax_amount.numeric' => 'يجب أن تكون الضريبة رقمًا صحيحًا أو عشريًا.',

            'total.required' => 'يجب إدخال المبلغ الإجمالي.',
            'total.numeric' => 'يجب أن يكون المبلغ الإجمالي رقمًا صحيحًا أو عشريًا.',


            'cash_name.exists' => 'الخزنة المحددة غير موجودة.',
            'bank_name.exists' => 'البنك المحدد غير موجود.',
            'cash_amount_total.numeric' => 'يجب ان يكون المبلغ تقدا رقما صحيحا او عشريا.',
            'bank_amount_total.numeric' => 'يجب ان يكون المبلغ عبر البنك رقما صحيحا او عشريا.',
            'transaction_no.required' => 'يرجى إدخال رقم عملية البنك.',

            'product_id.required' => 'يجب إدخال المنتجات.',
            'product_id.array' => 'المنتجات يجب أن تكون في شكل قائمة.',
            'product_id.*.required' => 'يجب تحديد المنتج.',
            'product_id.*.exists' => 'المنتج المحدد غير موجود.',

            'stock_id.exists' => 'المخزن المحدد غير تابع لهذا الفرع.',

            'unit_id.required' => 'يجب تحديد الوحدات.',
            'unit_id.array' => 'الوحدات يجب أن تكون في شكل قائمة.',
            'unit_id.*.required' => 'يجب تحديد الوحدة.',
            'unit_id.*.numeric' => 'يجب أن تكون الوحدة رقمًا صحيحًا.',
            'unit_id.*.exists' => 'الوحدة المحددة غير موجودة.',

            'qty.required' => 'يجب تحديد الكمية.',
            'qty.array' => 'الكميات يجب أن تكون في شكل قائمة.',
            'qty.*.required' => 'يجب إدخال الكمية.',
            'qty.*.numeric' => 'يجب أن تكون الكمية رقمًا.',
            'qty.*.min' => 'يجب أن تكون الكمية على الأقل 1.',

            'price.required' => 'يجب إدخال السعر.',
            'price.array' => 'الأسعار يجب أن تكون في شكل قائمة.',
            'price.*.required' => 'يجب إدخال سعر المنتج.',
            'price.*.regex' => 'يجب أن يكون السعر رقمًا صحيحًا أو عشريًا بأقصى حد رقمين بعد الفاصلة.',

            'row_sub_total.required' => 'يجب إدخال المجموع الفرعي.',
            'row_sub_total.array' => 'المجاميع الفرعية يجب أن تكون في شكل قائمة.',
            'row_sub_total.*.required' => 'يجب إدخال المجموع الفرعي لكل عنصر.',
            'row_sub_total.*.numeric' => 'يجب أن يكون المجموع الفرعي رقمًا صحيحًا أو عشريًا.',
            'row_sub_total.*.min' => 'يجب أن يكون المجموع الفرعي على الأقل 0.',
        ];
    }
}
