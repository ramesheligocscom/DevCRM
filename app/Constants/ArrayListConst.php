<?php

namespace App\Constants;

class ArrayListConst
{
    # TODO: this list Slug Plz make Unique And Change slug to plz Check Vue or js file in change slug 
    const HEADER_MANAGE_LIST = [
        [
            'title' => 'Notification List',
            'slug' => 'notification-list',
            'table' => 'notifications',
            'headers' => [
                ['title' => 'Title', 'key' => 'title', 'sortable' => true, 'align' => 'left', 'minWidth' => '140px', 'checked' => true],
                ['title' => 'Type', 'key' => 'module_type', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Created By', 'key' => 'user_id', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Name', 'key' => 'module_id', 'sortable' => false, 'align' => 'left', 'checked' => true],
                ['title' => 'Is Read', 'key' => 'is_read', 'sortable' => false, 'align' => 'left', 'checked' => true],
                ['title' => 'Message', 'key' => 'message', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Created At', 'key' => 'created_at', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Action', 'key' => 'action', 'sortable' => false, 'align' => 'center', 'checked' => true],
            ]
        ],

        # Report header List 
        [
            'title' => 'Working lead Report',
            'slug' => 'working-lead-report',
            'table' => 'leads',
            'headers' => [
                ['title' => 'Name', 'key' => 'name', 'sortable' => true, 'align' => 'left', 'minWidth' => '140px', 'checked' => true],
                ['title' => 'Phone', 'key' => 'phone', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Source', 'key' => 'source', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Address', 'key' => 'address', 'sortable' => false, 'align' => 'left', 'checked' => true],
                ['title' => 'Assigned To', 'key' => 'assigned_user', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Created By', 'key' => 'made_by', 'sortable' => false, 'align' => 'left', 'checked' => true],
                ['title' => 'Lead Status', 'key' => 'status', 'sortable' => true, 'align' => 'left', 'checked' => true],
            ]
        ],
        [
            'title' => 'Lead Follow Up Report',
            'table' => 'folllow_ups',
            'slug' => 'lead-follow-up-report',
            'headers' => [
                ['title' => 'Name', 'key' => 'name', 'sortable' => true, 'align' => 'left', "minWidth" => "140px", 'checked' => true],
                ['title' => 'Phone', 'key' => 'phone', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Address', 'key' => 'address', 'sortable' => false, 'align' => 'left', 'checked' => true],
                ['title' => 'Assigned To', 'key' => 'assigned_user', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Call Status', 'key' => 'call_status', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Lead Prospect', 'key' => 'lead_prospect', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Next Call Time', 'key' => 'next_call_time', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Follow Up Status', 'key' => 'follow_up_status', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Call Summary', 'key' => 'call_summary', 'sortable' => true, 'align' => 'left', 'checked' => true],
            ]
        ],
        [
            'title' => 'Lead Follow Up Technician Report',
            'table' => 'folllow_ups',
            'slug' => 'lead-follow-up-technician-report',
            'headers' => [
                ['title' => 'Name', 'key' => 'name', 'sortable' => true, 'align' => 'left', "minWidth" => "140px", 'checked' => true],
                ['title' => 'Phone', 'key' => 'phone', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Address', 'key' => 'address', 'sortable' => false, 'align' => 'left', 'checked' => true],
                ['title' => 'Assigned To', 'key' => 'assigned_user', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Call Status', 'key' => 'call_status', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Lead Prospect', 'key' => 'lead_prospect', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Follow Up Status', 'key' => 'follow_up_status', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Visit Time', 'key' => 'visit_time', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Technician', 'key' => 'technician', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Call Summary', 'key' => 'call_summary', 'sortable' => true, 'align' => 'left', 'checked' => true],
            ]
        ],
        [
            'title' => 'Client Follow Up Report',
            'table' => 'folllow_ups',
            'slug' => 'client-follow-up-report',
            'headers' => [
                ['title' => 'Name', 'key' => 'name', 'sortable' => true, 'align' => 'left', "minWidth" => "140px", 'checked' => true],
                ['title' => 'Phone', 'key' => 'phone', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Address', 'key' => 'address', 'sortable' => false, 'align' => 'left', 'checked' => true],
                ['title' => 'Assigned To', 'key' => 'assigned_user', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Call Status', 'key' => 'call_status', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Lead Prospect', 'key' => 'lead_prospect', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Next Call Time', 'key' => 'next_call_time', 'sortable' => true, 'align' => 'left', 'checked' => true],
                // ['title' => 'Follow Up Status', 'key' => 'follow_up_status', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Call Summary', 'key' => 'call_summary', 'sortable' => true, 'align' => 'left', 'checked' => true],
            ]
        ],
        [
            'title' => 'Client Follow Up Technician Report',
            'table' => 'folllow_ups',
            'slug' => 'client-follow-up-technician-report',
            'headers' => [
                ['title' => 'Name', 'key' => 'name', 'sortable' => true, 'align' => 'left', "minWidth" => "140px", 'checked' => true],
                ['title' => 'Phone', 'key' => 'phone', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Address', 'key' => 'address', 'sortable' => false, 'align' => 'left', 'checked' => true],
                ['title' => 'Assigned To', 'key' => 'assigned_user', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Call Status', 'key' => 'call_status', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Lead Prospect', 'key' => 'lead_prospect', 'sortable' => true, 'align' => 'left', 'checked' => true],
                // ['title' => 'Follow Up Status', 'key' => 'follow_up_status', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Visit Time', 'key' => 'visit_time', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Technician', 'key' => 'technician', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Call Summary', 'key' => 'call_summary', 'sortable' => true, 'align' => 'left', 'checked' => true],
            ]
        ],
        [
            'title' => 'Quotation Follow Up Report',
            'table' => 'folllow_ups',
            'slug' => 'quotation-follow-up-report',
            'headers' => [
                ['title' => 'Title', 'key' => 'title', 'sortable' => true, 'align' => 'left', "minWidth" => "140px", 'checked' => true],
                ['title' => 'Quotation No', 'key' => 'quotation_number', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Segment', 'key' => 'segment', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Call Status', 'key' => 'call_status', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Lead Prospect', 'key' => 'lead_prospect', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Next Call Time', 'key' => 'next_call_time', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Call Summary', 'key' => 'call_summary', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Follow Up Status', 'key' => 'follow_up_status', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Client Name', 'key' => 'client_id', 'sortable' => true, 'align' => 'left', "minWidth" => "140px", 'checked' => true],
                ['title' => 'Lead Name', 'key' => 'lead_id', 'sortable' => true, 'align' => 'left', "minWidth" => "140px", 'checked' => true],
                ['title' => 'Phone', 'key' => 'phone', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Address', 'key' => 'address', 'sortable' => false, 'align' => 'left', 'checked' => true],
                // ['title' => 'Assigned To', 'key' => 'assigned_user', 'sortable' => true, 'align' => 'left', 'checked' => true],
            ]
        ],
        [
            'title' => 'Dashboard Client Follow Up Report',
            'table' => 'dashboard_folllow_ups',
            'slug' => 'dashboard_client-follow-up-report',
            'headers' => [
                ['title' => 'Name', 'key' => 'name', 'sortable' => true, 'align' => 'left', "minWidth" => "140px", 'checked' => true],
                ['title' => 'Phone', 'key' => 'phone', 'sortable' => true, 'align' => 'left', 'checked' => false],
                ['title' => 'Address', 'key' => 'address', 'sortable' => false, 'align' => 'left', 'checked' => false],
                ['title' => 'Assigned To', 'key' => 'assigned_user', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Call Status', 'key' => 'call_status', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Lead Prospect', 'key' => 'lead_prospect', 'sortable' => true, 'align' => 'left', 'checked' => false],
                ['title' => 'Next Call Time', 'key' => 'next_call_time', 'sortable' => true, 'align' => 'left', 'checked' => false],
                // ['title' => 'Follow Up Status', 'key' => 'follow_up_status', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Call Summary', 'key' => 'call_summary', 'sortable' => true, 'align' => 'left', 'checked' => false],
            ]
        ],
        [
            'title' => 'Dashboard Quotation Follow Up Report',
            'table' => 'dashboard_folllow_ups',
            'slug' => 'dashboard_quotation-follow-up-report',
            'headers' => [
                ['title' => 'Title', 'key' => 'title', 'sortable' => true, 'align' => 'left', "minWidth" => "140px", 'checked' => false],
                ['title' => 'Quotation No', 'key' => 'quotation_number', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Segment', 'key' => 'segment', 'sortable' => true, 'align' => 'left', 'checked' => false],
                ['title' => 'Call Status', 'key' => 'call_status', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Lead Prospect', 'key' => 'lead_prospect', 'sortable' => true, 'align' => 'left', 'checked' => false],
                ['title' => 'Next Call Time', 'key' => 'next_call_time', 'sortable' => true, 'align' => 'left', 'checked' => false],
                ['title' => 'Call Summary', 'key' => 'call_summary', 'sortable' => true, 'align' => 'left', 'checked' => false],
                ['title' => 'Follow Up Status', 'key' => 'follow_up_status', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Client Name', 'key' => 'client_id', 'sortable' => true, 'align' => 'left', "minWidth" => "140px", 'checked' => true],
                ['title' => 'Lead Name', 'key' => 'lead_id', 'sortable' => true, 'align' => 'left', "minWidth" => "140px", 'checked' => true],
                ['title' => 'Phone', 'key' => 'phone', 'sortable' => true, 'align' => 'left', 'checked' => false],
                ['title' => 'Address', 'key' => 'address', 'sortable' => false, 'align' => 'left', 'checked' => false],
            ]
        ],
    ];
}
