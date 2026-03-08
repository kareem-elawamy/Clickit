<?php

namespace App\Enums\ViewPaths\Admin;

enum HelpTopic
{

    const LIST = [
        URI => 'index',
        VIEW => 'admin-views.pages-and-media.help-topics.list',
    ];

    const STATUS = [
        URI => 'status',
        VIEW => '',
    ];

    const ADD = [
        VIEW => '',
    ];

    const UPDATE = [
        URI => 'update',
        VIEW => '',
    ];

    const DELETE = [
        VIEW => '',
    ];
}
