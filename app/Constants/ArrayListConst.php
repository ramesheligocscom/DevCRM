<?php

namespace App\Constants;

class ArrayListConst
{

  # TODO: this list Slug Plz make Unique And Change slug to plz Check Vue or js file in change slug 
  const HEADER_MANAGE_LIST = [
    # Setting Status List Header 
    [
      'title' => 'Setting Status List',
      'slug' => 'setting-status-list',
      'table' => 'admin_control_configs',
      'headers' => [
        ['title' => 'Page Name', 'key' => 'status_for', 'sortable' => true, 'align' => 'left', 'minWidth' => '140px', 'checked' => true],
        ['title' => 'Status Name', 'key' => 'status_text', 'sortable' => false, 'align' => 'left', 'checked' => true],
        ['title' => 'Color', 'key' => 'status_color', 'sortable' => false, 'align' => 'left', 'checked' => true],
        ['title' => 'Position', 'key' => 'position', 'sortable' => false, 'align' => 'left', 'checked' => true],
        ['title' => 'Status', 'key' => 'status', 'sortable' => false, 'align' => 'left', 'checked' => true],
        ['title' => 'Invoice Footer Text', 'key' => 'invoice_footer_text', 'sortable' => false, 'align' => 'left', 'checked' => false],
        ['title' => 'Contract Footer Text', 'key' => 'contract_footer_text', 'sortable' => false, 'align' => 'left', 'checked' => false],
        ['title' => 'Actions', 'key' => 'actions', 'sortable' => false, 'align' => 'center', 'checked' => true],
      ]
    ],
    # Login Log List Header 
    [
      'title' => 'Login Log List',
      'slug' => 'login-log-list',
      'table' => 'user_login_logs',
      'headers' => [
        ['title' => 'Name', 'key' => 'name', 'sortable' => true, 'align' => 'left', 'minWidth' => '140px', 'checked' => true],
        ['title' => 'Ip Address', 'key' => 'ip_address', 'sortable' => true, 'align' => 'left', 'checked' => true],
        ['title' => 'User Agent', 'key' => 'user_agent', 'sortable' => false, 'align' => 'left', 'checked' => true],
        ['title' => 'Country', 'key' => 'country', 'sortable' => false, 'align' => 'left', 'checked' => true],
        ['title' => 'State', 'key' => 'state', 'sortable' => false, 'align' => 'left', 'checked' => true],
        ['title' => 'City', 'key' => 'city', 'sortable' => false, 'align' => 'left', 'checked' => true],
        ['title' => 'Event', 'key' => 'event', 'sortable' => false, 'align' => 'left', 'checked' => true],
        ['title' => 'Date', 'key' => 'logged_at', 'sortable' => false, 'align' => 'left', 'checked' => true],
        ['title' => 'Actions', 'key' => 'actions', 'sortable' => false, 'align' => 'center', 'checked' => true],
      ]
    ],

    # User List Sidebar Menu 
    [
      'title' => 'User List',
      'slug' => 'user-list',
      'table' => 'users',
      'headers' => [
        ['title' => 'Name', 'key' => 'name', 'sortable' => true, 'align' => 'left', 'minWidth' => '140px', 'checked' => true],
        ['title' => 'Email', 'key' => 'email', 'sortable' => true, 'align' => 'left', 'checked' => true],
        ['title' => 'User Name', 'key' => 'user_name', 'sortable' => false, 'align' => 'left', 'checked' => true],
        ['title' => 'Role', 'key' => 'role', 'sortable' => false, 'align' => 'left', 'checked' => true],
        ['title' => 'status', 'key' => 'status', 'sortable' => false, 'align' => 'left', 'checked' => true],
        ['title' => 'Actions', 'key' => 'action', 'sortable' => false, 'align' => 'center', 'checked' => true],
      ]
    ],

    # Lead List Sidebar Menu 
    [
      'title' => 'lead List',
      'slug' => 'lead-list',
      'table' => 'leads',
      'headers' => [
        ['title' => 'Name', 'key' => 'name', 'sortable' => true, 'align' => 'left', 'minWidth' => '140px', 'checked' => true],
        ['title' => 'Email', 'key' => 'email', 'sortable' => true, 'align' => 'left', 'minWidth' => '140px', 'checked' => true],
        ['title' => 'Contact Person', 'key' => 'contact_person', 'sortable' => true, 'align' => 'left', 'checked' => true],
        ['title' => 'Contact Person Role', 'key' => 'contact_person_role', 'sortable' => true, 'align' => 'left', 'checked' => true],
        ['title' => 'Phone', 'key' => 'phone', 'sortable' => true, 'align' => 'left', 'checked' => true],
        ['title' => 'Source', 'key' => 'source', 'sortable' => true, 'align' => 'left', 'checked' => true],
        ['title' => 'Address', 'key' => 'address', 'sortable' => false, 'align' => 'left', 'checked' => true],
        ['title' => 'Assigned To', 'key' => 'assigned_user', 'sortable' => true, 'align' => 'left', 'checked' => true],
        ['title' => 'Lead Status', 'key' => 'status', 'sortable' => true, 'align' => 'left', 'checked' => true],
        ['title' => 'Created At', 'key' => 'created_at', 'sortable' => true, 'align' => 'left', 'checked' => true],
        ['title' => 'Created By', 'key' => 'created_by', 'sortable' => true, 'align' => 'left', 'checked' => true],
        ['title' => 'Last Updated At', 'key' => 'updated_at', 'sortable' => true, 'align' => 'left', 'checked' => true],
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

    # Quotation List Sidebar Menu 
    [
      'title' => 'Quotation List',
      'slug' => 'quotation-list',
      'table' => 'quotations',
      'headers' => [
        ['title' => 'Quotation Number', 'key' => 'quotation_number', 'sortable' => true, 'align' => 'left', 'checked' => true],
        ['title' => 'Valid Up Till', 'key' => 'valid_uptil', 'sortable' => true, 'align' => 'left', 'checked' => true],
        ['title' => 'Quotation Type', 'key' => 'quotation_type', 'sortable' => true, 'align' => 'left', 'checked' => true],
        ['title' => 'Title', 'key' => 'title', 'sortable' => true, 'align' => 'left', 'checked' => true],
        ['title' => 'Sub Total', 'key' => 'sub_total', 'sortable' => true, 'align' => 'left', 'checked' => true],
        ['title' => 'Discount', 'key' => 'discount', 'sortable' => true, 'align' => 'left', 'checked' => true],
        ['title' => 'Tax', 'key' => 'tax', 'sortable' => true, 'align' => 'left', 'checked' => true],
        ['title' => 'Total', 'key' => 'total', 'sortable' => true, 'align' => 'left', 'checked' => true],
        ['title' => 'Status', 'key' => 'status', 'sortable' => true, 'align' => 'left', 'checked' => true],
        ['title' => 'Custom Header Text', 'key' => 'custom_header_text', 'sortable' => false, 'align' => 'left', 'checked' => true],
        ['title' => 'Payment Terms', 'key' => 'payment_terms', 'sortable' => false, 'align' => 'left', 'checked' => true],
        ['title' => 'Terms & Conditions', 'key' => 'terms_conditions', 'sortable' => false, 'align' => 'left', 'checked' => true],
        ['title' => 'Created At', 'key' => 'created_at', 'sortable' => true, 'align' => 'left', 'checked' => true],
        ['title' => 'Created By', 'key' => 'created_by', 'sortable' => true, 'align' => 'left', 'checked' => true],
        ['title' => 'Last Updated At', 'key' => 'updated_at', 'sortable' => true, 'align' => 'left', 'checked' => true],
        ['title' => 'Last Updated By', 'key' => 'last_updated_by', 'sortable' => true, 'align' => 'left', 'checked' => true],
        ['title' => 'Action', 'key' => 'action', 'sortable' => false, 'align' => 'center', 'checked' => true],
      ]
    ],

    # Invoices List Sidebar Menu 
    [
      'title' => 'Invoice List',
      'slug' => 'invoice-list',
      'table' => 'invoices',
      'headers' => [
        ['title' => 'Invoice Number', 'key' => 'invoice_number', 'sortable' => true, 'align' => 'left', 'checked' => true],
        ['title' => 'Title', 'key' => 'title', 'sortable' => true, 'align' => 'left', 'checked' => true],
        ['title' => 'Sub Total', 'key' => 'sub_total', 'sortable' => true, 'align' => 'left', 'checked' => true],
        ['title' => 'Tax', 'key' => 'tax', 'sortable' => true, 'align' => 'left', 'checked' => true],
        ['title' => 'Discount', 'key' => 'discount', 'sortable' => true, 'align' => 'left', 'checked' => true],
        ['title' => 'Total', 'key' => 'total', 'sortable' => true, 'align' => 'left', 'checked' => true],
        ['title' => 'Status', 'key' => 'status', 'sortable' => true, 'align' => 'left', 'checked' => true],
        ['title' => 'Created At', 'key' => 'created_at', 'sortable' => true, 'align' => 'left', 'checked' => true],
        ['title' => 'Created By', 'key' => 'created_by', 'sortable' => true, 'align' => 'left', 'checked' => true],
        ['title' => 'Last Updated At', 'key' => 'updated_at', 'sortable' => true, 'align' => 'left', 'checked' => true],
        ['title' => 'Last Updated By', 'key' => 'last_updated_by', 'sortable' => true, 'align' => 'left', 'checked' => true],
        ['title' => 'Action', 'key' => 'action', 'sortable' => false, 'align' => 'center', 'checked' => true],
      ]
    ],

    # Product/Service List Sidebar Menu 
    [
      'title' => 'Product/Service List',
      'slug' => 'product-service-list',
      'table' => 'product_services',
      'headers' => [
        ['title' => 'Name', 'key' => 'name', 'sortable' => true, 'align' => 'left', 'minWidth' => '140px', 'checked' => true],
        ['title' => 'Price', 'key' => 'price', 'sortable' => true, 'align' => 'left', 'checked' => true],
        // ['title' => 'Attributes', 'key' => 'attributes', 'sortable' => false, 'align' => 'left', 'checked' => true],
        ['title' => 'Created At', 'key' => 'created_at', 'sortable' => true, 'align' => 'left', 'checked' => true],
        ['title' => 'Created By', 'key' => 'created_by', 'sortable' => true, 'align' => 'left', 'checked' => true],
        ['title' => 'Updated At', 'key' => 'updated_at', 'sortable' => true, 'align' => 'left', 'checked' => true],
        ['title' => 'Last Updated By', 'key' => 'updated_at', 'sortable' => true, 'align' => 'left', 'checked' => true],
        ['title' => 'Action', 'key' => 'action', 'sortable' => false, 'align' => 'center', 'checked' => true],
      ]
    ],

    # Contract List Sidebar Menu 
    [
      'title' => 'Contract List',
      'slug' => 'contract-list',
      'table' => 'contracts',
      'headers' => [
        ['title' => 'Title', 'key' => 'title', 'sortable' => true, 'align' => 'left', 'checked' => true],
        ['title' => 'Start Date', 'key' => 'start_date', 'sortable' => true, 'align' => 'left', 'checked' => true],
        ['title' => 'End Date', 'key' => 'end_date', 'sortable' => true, 'align' => 'left', 'checked' => true],
        ['title' => 'Sub Total', 'key' => 'sub_total', 'sortable' => false, 'align' => 'left', 'checked' => true],
        ['title' => 'Discount', 'key' => 'discount', 'sortable' => true, 'align' => 'left', 'checked' => true],
        ['title' => 'Tax', 'key' => 'tax', 'sortable' => true, 'align' => 'left', 'checked' => true],
        ['title' => 'Total', 'key' => 'total', 'sortable' => true, 'align' => 'left', 'checked' => true],
        ['title' => 'Status', 'key' => 'status', 'sortable' => true, 'align' => 'left', 'checked' => true],
        ['title' => 'Created At', 'key' => 'created_at', 'sortable' => true, 'align' => 'left', 'checked' => true],
        ['title' => 'Created By', 'key' => 'created_by', 'sortable' => true, 'align' => 'left', 'checked' => true],
        ['title' => 'Last Updated At', 'key' => 'updated_at', 'sortable' => true, 'align' => 'left', 'checked' => true],
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
    [
        'title' => 'Client Site Visit',
        'slug' => 'client-site-visit',
        'table' => 'site_visits',
        'headers' => [
          ['title' => 'Visit Time', 'key' => 'visit_time', 'sortable' => true, 'align' => 'left', 'minWidth' => '140px', 'checked' => true],
          ['title' => 'assignee name', 'key' => 'assignee_name', 'sortable' => true, 'align' => 'left', 'checked' => true],
          ['title' => 'Created At', 'key' => 'created_at', 'sortable' => true, 'align' => 'left', 'checked' => true],
          ['title' => 'Created By', 'key' => 'created_by', 'sortable' => false, 'align' => 'left', 'checked' => true],
          ['title' => 'Action', 'key' => 'action', 'sortable' => false, 'align' => 'center', 'checked' => true],
        ]
      ],
    [
      'title' => 'Follw up',
      'slug' => 'follow-up',
      'table' => 'follow-up',
        'headers' => [
          ['title' => 'Call Status', 'key' => 'call_status', 'sortable' => true, 'align' => 'left', 'minWidth' => '140px', 'checked' => true],
          ['title' => 'Call Summary', 'key' => 'call_summary', 'sortable' => true, 'align' => 'left', 'checked' => true],
          ['title' => 'Created At', 'key' => 'created_at', 'sortable' => true, 'align' => 'left', 'checked' => true],
          ['title' => 'Created By', 'key' => 'created_by', 'sortable' => false, 'align' => 'left', 'checked' => true],
          ['title' => 'Action', 'key' => 'action', 'sortable' => false, 'align' => 'center', 'checked' => true],
        ]
      ],
     
  ];
}
