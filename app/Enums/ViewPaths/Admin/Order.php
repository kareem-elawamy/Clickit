<?php

namespace App\Enums\ViewPaths\Admin;

enum Order
{
    const LIST = [
        URI => 'list',
        VIEW => 'admin-views.order.list'
    ];

    const GENERATE_INVOICE = [
        URI => 'generate-invoice',
        VIEW => 'admin-views.order.invoice'
    ];

    const VIEW = [
        URI => 'details',
        VIEW => 'admin-views.order.order-details'
    ];

    const VIEW_POS = [
        URI => '',
        VIEW => 'admin-views.pos.order.order-details'
    ];

    const UPDATE_STATUS = [
        URI => 'status',
        VIEW => ''
    ];

}
