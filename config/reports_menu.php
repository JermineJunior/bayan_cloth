<?php

return [
    'sales' => [
        'title' => 'المبيعات',
        'items' => [
            ['label' => 'تقرير المبيعات حسب العميل', 'route' => 'report.sale_invoice.customer.index', 'can' => 'تقرير المبيعات حسب العميل'],
            ['label' => 'تقرير راجع مبيعات حسب العميل', 'route' => 'report.sale_return_invoice.customer.index', 'can' => 'تقرير راجع مبيعات حسب العميل'],
            ['label' => 'تقرير مبيعات حسب المستخدم', 'route' => 'report.sale_invoice.agent.index', 'can' => 'تقرير مبيعات حسب المستخدم'],
            ['label' => 'تقرير راجع ميبعات حسب المستخدم', 'route' => 'report.sale_return_invoice.agent.index', 'can' => 'تقرير راجع مبيعات حسب المستخدم'],
            ['label' => 'تقرير مبيعات حسب الشركة', 'route' => 'report.sale_invoice.category.index', 'can' => 'تقرير مبيعات حسب الشركة'],
            ['label' => 'تقرير مردود مبيعات حسب الشركة', 'route' => 'report.sale_return_invoice.category.index', 'can' => 'تقرير مردود مبيعات حسب الشركة'],
            ['label' => 'تقرير مبيعات حسب الصنف', 'route' => 'report.sale_invoice.product.index', 'can' => 'تقرير مبيعات حسب الشركة'],
            ['label' => 'تقرير مبيعات مفصل', 'route' => 'report.sale_invoice.details.index', 'can' => 'تقرير مبيعات مفصل'],
            ['label' => 'تقرير مردود مبيعات مفصل', 'route' => 'report.sale_return_invoice.details.index', 'can' => 'تقرير مردود مبيعات مفصل'],
            ['label' => 'تقرير مبيعات ملخص', 'route' => 'report.sale_invoice.summary.index', 'can' => 'تقرير مبيعات ملخص'],
            ['label' => 'تقرير مردود مبيعات ملخص', 'route' => 'report.sale_return_invoice.summary.index', 'can' => 'تقرير مردود مبيعات ملخص'],
            ['label' => 'تقرير المبيعات حسب المندوب', 'route' => 'report.sale_invoice.representative.index', 'can' => 'تقرير المبيعات حسب المندوب'],
            ['label' => 'تقرير مردود المبيعات حسب المندوب', 'route' => 'report.sale_invoice_return.representative.index', 'can' => 'تقرير مردود المبيعات حسب المندوب'],
            ['label' => 'تقرير العملاء الاكثر مبيعا', 'route' => 'report.sale_invoice.top_customers.index', 'can' => 'تقرير العملاء الاكثر مبيعا'],
        ],
    ],
    'purchases' => [
        'title' => 'المشتريات',
        'items' => [
            ['label' => 'تقرير المشتريات حسب المورد', 'route' => 'report.purchase_ivoice.supplier.index', 'can' => 'تقرير المشتريات حسب المورد'],
            ['label' => 'تقرير راجع المشتريات حسب المورد', 'route' => 'report.purchase_return_invoice.supplier.index', 'can' => 'تقرير راجع مشتريات حسب المورد'],
            ['label' => 'تقرير المشتريات حسب المستخدم', 'route' => 'report.purchase_ivoice.agent.index', 'can' => 'تقرير مشتريات حسب المستخدم'],
            ['label' => 'تقرير راجع مشتريات حسب المستخدم', 'route' => 'report.purchase_return_invoice.agent.index', 'can' => 'تقرير راجع مشتريات حسب المستخدم'],
            ['label' => 'تقرير مشتريات حسب الشركة', 'route' => 'report.purchase_invoice.category.index', 'can' => 'تقرير مشتريات حسب الشركة'],
            ['label' => 'تقرير مردود مشتريات حسب الشركة', 'route' => 'report.purchase_return_invoice.category.index', 'can' => 'تقرير مردود مشتريات حسب الشركة'],
            ['label' => 'تقرير مشتريات حسب الصنف', 'route' => 'report.purchase_invoice.product.index', 'can' => 'تقرير مشتريات حسب الشركة'],
            ['label' => 'تقرير مشتريات مفصل', 'route' => 'report.purchase_invoice.details.index', 'can' => 'تقرير مشتريات مفصل'],
            ['label' => 'تقرير مردود مشتريات مفصل', 'route' => 'report.purchase_return_invoice.details.index', 'can' => 'تقرير مردود مشتريات مفصل'],
            ['label' => 'تقرير مشتريات ملخص', 'route' => 'report.purchase_invoice.summary.index', 'can' => 'تقرير مشتريات ملخص'],
            ['label' => 'تقرير مردود مشتريات ملخص', 'route' => 'report.purchase_return_invoice.summary.index', 'can' => 'تقرير مردود مشتريات ملخص'],
        ],
    ],
    'financial' => [
        'title' => 'التقارير المالية',
        'items' => [
            ['label' => 'تقرير الموقف المالي للعميل', 'route' => 'report.customer.account', 'can' => 'تقرير الموقف المالي للعميل'],
            ['label' => 'تقرير الموقف المالي للمورد', 'route' => 'report.supplier.account', 'can' => 'تقرير الموقف المالي للمورد'],
            ['label' => 'كشف حساب خزنة', 'route' => 'report.safe.account', 'can' => 'كشف حساب خزنة'],
            ['label' => 'كشف حساب بنك', 'route' => 'report.bank.account', 'can' => 'كشف حساب بنك'],
            ['label' => 'تقرير كشف المصروفات', 'route' => 'report.expense', 'can' => 'كشف حساب المصروفات'],
            ['label' => 'تقرير كشف الايرادات', 'route' => 'report.revenue', 'can' => 'كشف حساب الايرادات'],
            ['label' => 'تقرير مدينونيات العملاء', 'route' => 'report.customer.account.index', 'can' => 'تقرير مدينونيات العملاء'],
            ['label' => 'تقرير مديونيات العملاء التفصيلي', 'route' => 'report.customer_debts.index', 'can' => 'تقرير مدينونيات العملاء'],
            ['label' => 'التقرير الشامل', 'route' => 'report.complete.index', 'can' => 'التقرير الشامل'],
        ],
    ],
    'reps' => [
        'title' => 'المندوبين',
        'items' => [
            ['label' => 'تقرير المبيعات حسب المندوب', 'route' => 'report.sale_invoice.representative.index', 'can' => 'تقرير المبيعات حسب المندوب'],
            ['label' => 'تقرير مردود المبيعات حسب المندوب', 'route' => 'report.sale_invoice_return.representative.index', 'can' => 'تقرير مردود المبيعات حسب المندوب'],
            ['label' => 'تقرير مديونيات عملاء المندوب', 'route' => 'report.representative.customer.index', 'can' => 'تقرير مديونيات عملاء المندوب'],
        ],
    ],
    'stock' => [
        'title' => 'المخازن',
        'items' => [
            ['label' => 'تقرير المنتجات الاكثر مبيعا', 'route' => 'report.top-products', 'can' => 'تقرير المنتجات الاكثر طلبا'],
            ['label' => 'تقرير المنتجات التي تجاوزت تاريخ الصلاحية', 'route' => 'report.expired-products', 'can' => 'تقرير المنتجات التي تجاوزت تاريخ الصلاحية'],
            ['label' => 'تقرير المنتجات التي شارفت على تجاوز تاريخ الصلاحية', 'route' => 'report.expiring-products', 'can' => 'تقرير المنتجات التي شارفت على تجاوز تاريخ الصلاحية'],
            ['label' => 'تقرير المنتجات التي تجاوزت حد الطلب', 'route' => 'report.low_stock', 'can' => 'تقرير المنتجات التي تجازوت حد الطلب'],
            ['label' => 'تقرير مفصل للمخازن حسب الفترة', 'route' => 'report.stock.details.index', 'can' => 'تقرير مفصل للمخازن حسب الفترة'],
            ['label' => 'تقرير حركة المخزن', 'route' => 'burren.info', 'can' => 'تقرير حركة المخزن'],
            ['label' => 'تقرير كشف السعر', 'route' => 'select.representative', 'can' => 'تقرير كشف السعر'],
            ['label' => 'تقرير الجرد للمبيعات', 'route' => 'report.burren.sale_price.index', 'can' => 'تقرير الجرد للمبيعات'],
            ['label' => 'تقرير الجرد للمشتريات', 'route' => 'report.burren.purchase_price.index', 'can' => 'تقرير الجرد للمشتريات'],
        ],
    ],
    'accounting' => [
        'title' => 'التقارير المحاسبية',
        'items' => [
            ['label' => 'تقرير كشف حسابات', 'route' => 'account.statement', 'can' => 'تقرير كشف حساب'],
            ['label' => 'تقرير دفتر اليومية', 'route' => 'journal.index', 'can' => 'تقرير دفتر اليومية'],
            ['label' => 'تقرير القيود', 'route' => 'journalEntry.index', 'can' => 'تقرير القيود'],
            ['label' => 'الدفاتر المحاسبية', 'route' => 'accountingBooks.index', 'can' => 'الدفاتر المحاسبية'],
        ],
    ],
];
