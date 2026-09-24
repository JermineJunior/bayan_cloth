<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <style>
        /* إزالة هوامش الصفحة نهائياً */
        @page {
            margin: 0;
        }

        body {
            font-family: dejavusans, sans-serif;
            direction: rtl;
            text-align: right;
            font-size: 14px;
            margin: 0;
            padding: 15px;
        }

        .invoice {
            width: 100%;
            margin: auto;
            background: #fff;
        }

        .invoice-details {
            border: 1px solid #eee;
            background: #f9f9f9;
        }

        .invoice-details td {
            padding: 10px;
            vertical-align: top;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            page-break-inside: auto;
        }

        thead {
            display: table-header-group;
        }

        tfoot {
            display: table-footer-group;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        table th,
        table td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: center;
        }

        table thead {
            background: #f1f1f1;
        }
    </style>
</head>

<body>

    <div class="invoice">

        <!-- الهيدر -->
        <table style="width:100%; border:none; margin-bottom:20px;">
            <tr>

                <td style="width:20%; text-align:right;">
                    @if (GeneralSiteSettings('logo'))
                        <img src="{{ asset('image/logo/bayan.jpg') }}" style="width:60px;height:60px;">
                    @endif
                </td>

                <td style="width:60%; text-align:center;">
                    <h2 style="margin:0;font-size:22px;font-weight:bold;color:#042d7a;">
                        {{ GeneralSiteSettings('name') }}
                    </h2>
                    <p style="margin:3px 0;">{{ GeneralSiteSettings('address') }}</p>
                    <p style="margin:0;">{{ GeneralSiteSettings('phone') }}</p>
                </td>

                <td style="width:20%; text-align:left;">
                    @if (GeneralSiteSettings('logo'))
                        <img src="{{ asset('image/logo/bayan.jpg') }}" style="width:60px;height:60px;">
                    @endif
                </td>

            </tr>
        </table>

        <!-- بيانات الفاتورة -->
        <table class="invoice-details" style="width:100%; margin:15px 0;">
            <tr>
                <td style="width:33%;">
                    <p><strong>رقم الفاتورة:</strong> {{ $sale_invoice->invoice_number }}</p>
                    <p><strong>تاريخ الفاتورة:</strong> {{ $sale_invoice->date }}</p>
                </td>
                <td style="width:33%;">
                    <p><strong>اسم العميل:</strong> {{ $sale_invoice->customer->tree4_name ?? '---' }}</p>
                </td>
                <td style="width:33%;">
                    <p><strong>اسم المندوب:</strong> {{ $sale_invoice->representative->tree4_name ?? '---' }}</p>
                </td>
            </tr>
        </table>

        <!-- الجدول -->
        <table>

            <thead>
                <tr>
                    <th>م</th>
                    <th>المنتج</th>
                    <th>الكمية</th>
                    <th>البونص</th>
                    <th>الوحدة</th>
                    <th>تاريخ الصلاحية</th>
                    <th>سعر الوحدة</th>
                    <th>الإجمالي</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($sale_invoice->details as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->product->name }}</td>
                        <td>{{ number_format($item->qty) }}</td>
                        <td>{{ number_format($item->bounce) }}</td>
                        <td>{{ $item->unit->name }}</td>
                        <td>{{ $item->expire_date }}</td>
                        <td>{{ number_format($item->price, 2) }}</td>
                        <td>{{ number_format($item->row_sub_total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>

            <tfoot>
                <tr>
                    <td colspan="6"></td>
                    <td><strong>جملة الفاتورة</strong></td>
                    <td>{{ number_format($sale_invoice->sub_total_amount + $sale_invoice->tax_amount, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="6"></td>
                    <td><strong>الخصم</strong></td>
                    <td>{{ number_format($sale_invoice->discount_amount, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="6"></td>
                    <td><strong>صافي الفاتورة</strong></td>
                    <td>{{ number_format($sale_invoice->total_amount, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="6"></td>
                    <td><strong>مدفوع نقداً</strong></td>
                    <td>{{ number_format($sale_invoice->cash_amount_total, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="6"></td>
                    <td><strong>مدفوع بالبنك</strong></td>
                    <td>{{ number_format($sale_invoice->bank_amount_total, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="6"></td>
                    <td><strong>أجل</strong></td>
                    <td>{{ number_format($sale_invoice->deferend_amount, 2) }}</td>
                </tr>
                @if ($sale_invoice->customer_id != null)
                    <tr>
                        <td colspan="6"></td>
                        <td>جملة المطالبة السابقة</td>
                        <td>{{ number_format($madinTotal - $dainTotal) }}</td>
                    </tr>
                    @if ($sale_invoice->deferend_amount > 0)
                        <tr>
                            <td colspan="6"></td>
                            <td>جملة المطالبة الكلية</td>
                            <td>{{ number_format($allMadinTotal - $allDainTotal) }}
                            </td>
                        </tr>
                    @endif
                @endif
            </tfoot>

        </table>

    </div>

</body>

</html>
