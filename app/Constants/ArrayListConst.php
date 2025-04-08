<?php

namespace App\Constants;

class ArrayListConst
{
    # TODO: this list Slug Plz make Unique And Change slug to plz Check Vue or js file in change slug 
    const HEADER_MANAGE_LIST = [
        # Lead List Sidebar Menu 
        [
            'title' => 'lead List',
            'slug' => 'lead-list',
            'table' => 'leads',
            'headers' => [
                ['title' => 'Name', 'key' => 'name', 'sortable' => true, 'align' => 'left', 'minWidth' => '140px', 'checked' => true],
                ['title' => 'Contact Person', 'key' => 'contact_person', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Contact Person Role', 'key' => 'contact_person_role', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Phone', 'key' => 'phone', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Source', 'key' => 'source', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Address', 'key' => 'address', 'sortable' => false, 'align' => 'left', 'checked' => true],
                ['title' => 'Assigned To', 'key' => 'assigned_user', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Lead Status', 'key' => 'status', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Created By', 'key' => 'created_by', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Last Updated By', 'key' => 'last_updated_by', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Action', 'key' => 'action', 'sortable' => false, 'align' => 'center', 'checked' => true],
            ]
        ],

        # Client List Sidebar Menu 
        [
            'title' => 'Client List',
            'slug' => 'client-list',
            'table' => 'clients',
            'headers' => [
                ['title' => 'Name', 'key' => 'name', 'sortable' => true, 'align' => 'left', 'minWidth' => '140px', 'checked' => true],
                ['title' => 'Contact Person', 'key' => 'contact_person', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Phone', 'key' => 'phone', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Address', 'key' => 'address', 'sortable' => false, 'align' => 'left', 'checked' => true],
                ['title' => 'Assigned To', 'key' => 'assigned_to', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Status', 'key' => 'status', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Created By', 'key' => 'created_by', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Last Updated By', 'key' => 'last_updated_by', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Action', 'key' => 'action', 'sortable' => false, 'align' => 'center', 'checked' => true],
            ]
        ],

        # Contact List Sidebar Menu 
        [
            'title' => 'Contact List',
            'slug' => 'contact-list',
            'table' => 'contacts',
            'headers' => [
                // ['title' => 'Items', 'key' => 'items', 'sortable' => true, 'align' => 'left', 'minWidth' => '140px', 'checked' => true],
                ['title' => 'Start Date', 'key' => 'start_date', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'End Date', 'key' => 'end_date', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Sub Total', 'key' => 'sub_total', 'sortable' => false, 'align' => 'left', 'checked' => true],
                ['title' => 'Discount', 'key' => 'discount', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Tax', 'key' => 'tax', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Status', 'key' => 'status', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Client Id', 'key' => 'client_id', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Quotation Id', 'key' => 'quotation_id', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Invoice Id', 'key' => 'invoice_id', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Created By', 'key' => 'created_by', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Last Updated By', 'key' => 'last_updated_by', 'sortable' => true, 'align' => 'left', 'checked' => true],
                ['title' => 'Action', 'key' => 'action', 'sortable' => false, 'align' => 'center', 'checked' => true],
            ]
        ],

        # Bell Notification List Navbar Menu 
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
                ['title' => 'Created By', 'key' => 'made_by', 'sortable' => false, 'align' => 'left', 'checked' => true],
                ['title' => 'Action', 'key' => 'action', 'sortable' => false, 'align' => 'center', 'checked' => true],
            ]
        ],
    ];
}
