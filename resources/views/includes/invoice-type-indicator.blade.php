@php
    $type = $type ?? 'sale';

    $config = [
        'sale' => [
            'title' => 'فاتورة مبيعات',
            'badge' => 'مبيعات',
            'chip' => 'ب',
            'color' => '#2563eb',
            'tint' => '#eff6ff',
            'border' => 'solid',
        ],
        'purchase' => [
            'title' => 'فاتورة مشتريات',
            'badge' => 'مشتريات',
            'chip' => 'ش',
            'color' => '#0d9488',
            'tint' => '#f0fdfa',
            'border' => 'solid',
        ],
        'sale_return' => [
            'title' => 'مردود مبيعات',
            'badge' => 'مردود',
            'chip' => 'ر',
            'color' => '#ea580c',
            'tint' => '#fff7ed',
            'border' => 'dashed',
        ],
        'purchase_return' => [
            'title' => 'مردود مشتريات',
            'badge' => 'مردود',
            'chip' => 'ر',
            'color' => '#16a34a',
            'tint' => '#f0fdf4',
            'border' => 'dashed',
        ],
        'preliminary' => [
            'title' => 'فاتورة مبدئية',
            'badge' => 'عرض سعر',
            'chip' => 'م',
            'color' => '#64748b',
            'tint' => '#f8fafc',
            'border' => 'dotted',
        ],
        'preliminary_transform' => [
            'title' => 'تحويل إلى مردود مبيعات',
            'badge' => 'مردود',
            'chip' => 'ر',
            'color' => '#ea580c',
            'tint' => '#fff7ed',
            'border' => 'dashed',
        ],
    ];

    $item = $config[$type] ?? $config['sale'];
@endphp

<div class="invoice-type-indicator d-flex flex-wrap justify-content-between align-items-center"
    style="background:{{ $item['tint'] }};border:1px {{ $item['border'] }} {{ $item['color'] }};border-inline-start-width:5px;border-inline-start-style:{{ $item['border'] }};border-radius:6px;padding:6px 12px;margin:0 0 12px 0;">

    <div class="d-flex align-items-center" style="gap:8px;">

        <span class="d-inline-flex align-items-center justify-content-center"
            style="width:26px;height:26px;border-radius:5px;background:{{ $item['color'] }};color:#fff;font-size:14px;font-weight:700;">{{ $item['chip'] }}</span>

        <span style="font-size:15px;font-weight:700;color:{{ $item['color'] }};line-height:1.4;">{{ $item['title'] }}</span>

    </div>

    <span class="badge"
        style="background:{{ $item['color'] }};color:#fff;font-size:12px;padding:4px 10px;border-radius:20px;font-weight:600;">{{ $item['badge'] }}</span>

</div>
