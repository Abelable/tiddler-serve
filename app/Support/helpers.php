<?php

if (!function_exists('current_app_id')) {
    function current_app_id()
    {
        return app()->bound('current_app_id')
            ? app('current_app_id')
            : null;
    }
}
