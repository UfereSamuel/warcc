<?php

/**
 * Maps admin route names to required Spatie permissions (staff guard).
 * Routes not listed remain accessible to any authenticated admin (legacy fallback).
 */
return [

    'admin.dashboard' => 'view_analytics',
    'admin.staff-roster.*' => 'view_analytics',
    'admin.export.staff-roster' => 'export_reports',
    'admin.export.staff-roster-summary' => 'export_reports',

    'admin.roles.*' => 'manage_system',

    'admin.staff.index' => 'view_staff',
    'admin.staff.show' => 'view_staff',
    'admin.staff.create' => 'create_staff',
    'admin.staff.store' => 'create_staff',
    'admin.staff.edit' => 'edit_staff',
    'admin.staff.update' => 'edit_staff',
    'admin.staff.destroy' => 'delete_staff',
    'admin.staff.promote' => 'promote_staff',
    'admin.staff.demote' => 'demote_staff',
    'admin.staff.leave-balance' => 'edit_staff',
    'admin.admins.*' => 'view_staff',

    'admin.attendance.index' => 'view_attendance',
    'admin.attendance.daily-report' => 'view_attendance',
    'admin.attendance.export' => 'export_attendance',
    'admin.export.attendance' => 'export_attendance',

    'admin.weekly-trackers.*' => 'review_weekly_trackers',
    'admin.export.weekly-trackers' => 'export_reports',

    'admin.leave-types.*' => 'manage_leave_types',
    'admin.positions.*' => 'manage_positions',

    'admin.calendar.*' => 'manage_activity_calendar',
    'admin.activity-requests.*' => 'manage_activities',
    'admin.activity-reports.*' => 'review_activity_reports',

    'admin.complaints.*' => 'manage_complaints',

    'admin.public-events.*' => 'manage_public_events',
    'admin.content.*' => 'manage_content',

    'admin.settings.*' => 'manage_settings',
    'admin.email.*' => 'manage_settings',

    'admin.reports.*' => 'view_reports',
    'admin.reports' => 'view_reports',
    'admin.reports.export' => 'export_reports',
    'admin.export.dashboard-analytics' => 'export_reports',

    'admin.website-management.*' => 'manage_website',

];
