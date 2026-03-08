<?php

use Carbon\Carbon;

if (!function_exists('checkVendorAbility')) {
    function checkVendorAbility(string $type, string $status, object|array|null $vendor = null)
    {
        if ($status == 'temporary_close') {
            $temporaryClose = getWebConfig(name: 'temporary_close');
            return $type == 'inhouse' ? ($temporaryClose['status'] ?? 0) : ($vendor['temporary_close'] ?? 0);
        }

        if ($status == 'vacation_status' && in_array($type, ['inhouse', 'vendor'])) {
            if ($type == 'inhouse') {
                $vacationConfig = getWebConfig(name: 'vacation_add');
                $vacationStatus = $vacationConfig['status'] ?? 0;
                $vacationDurationType = $vacationConfig['vacation_duration_type'] ?? 0;
                $vacationStartDate = $vacationConfig['vacation_start_date'];
                $vacationEndDate = $vacationConfig['vacation_end_date'];
            } else {
                $vacationStatus = $vendor['vacation_status'] ?? 0;
                $vacationDurationType = $vacationConfig['vacation_duration_type'] ?? 0;
                $vacationStartDate = $vendor['vacation_start_date'] ?? null;
                $vacationEndDate = $vendor['vacation_end_date'] ?? null;
            }

            if ($vacationStatus) {
                if ($vacationDurationType == 'until_change') {
                    return $vacationStatus;
                } else {
                    if (!is_null($vacationStartDate) && !is_null($vacationEndDate)) {
                        $start = Carbon::parse($vacationStartDate);
                        $end = Carbon::parse($vacationEndDate);
                        $today = Carbon::now();
                        return $today->between($start, $end);
                    }
                    return false; 
                }
            }
        }

        return false;
    }
}
