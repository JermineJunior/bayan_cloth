<?php

namespace App\Imports;

use App\Models\Unit;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ProductsImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            // تخطي الهيدر
            if ($index === 0) {
                continue;
            }

            // تأكد الصف ما فاضي
            if (!isset($row[0], $row[1], $row[3])) {
                continue;
            }

            // جلب الشركة
            $category = Category::where('name', $row[3])->first();

            // لو الشركة غير موجودة → تخطي الصف فقط
            if (!$category) {
                continue;
            }

            $unit = Unit::where('name', $row[5])->first();

            // تحقق التكرار
            $exists = Product::where('barcode', $row[0])->orWhere('name', $row[1]);

            // تحقق من name_en فقط لو فيه قيمة
            if (!empty($row[2])) {
                $exists->orWhere('name_en', $row[2]);
            }

            if ($exists->exists()) {
                continue;
            }

            $expireDate = null;
            if (!empty($row[6])) {
                // إذا الرقم جاي من Excel كرقم
                if (is_numeric($row[6])) {
                    $unixDate = ($row[6] - 25569) * 86400; // تحويل Excel date إلى timestamp
                    $expireDate = gmdate('Y-m-d', $unixDate);
                } else {
                    // لو موجود بصيغة نصية مثل 12/2/2026
                    $expireDate = \Carbon\Carbon::createFromFormat('d/m/Y', $row[6])->format('Y-m-d');
                }
            }

            // ✅ الحفظ الفعلي
            Product::create([
                'barcode' => $row[0],
                'name' => $row[1],
                'name_en' => $row[2],
                'category_id' => $category->id,
                'reorder_level' => $row[4] ?? 0,
                'unit_reorder_level' => $unit->id ?? null,
                'expire_date' => $expireDate,
            ]);
        }
    }
}
