<?php

namespace Modules\ProductService\Constants;

class ProductServiceConst
{
    const PRODUCT_SERVICE_PERMISSION_LIST = [
        [
            'name' => 'Products & Services',
            'position' => 3, // Adjust position as needed
            "icon" => 'tabler-shopping-cart',
            "category" => [
                [
                    'name' => 'Products & Services',
                    "permission_list" => [
                        ["name" => 'View Product/Service List', "action" => "productService", "slug" => 'view'],
                        ["name" => 'Create new Product/Service', "action" => "productService", "slug" => 'create'],
                        ["name" => 'Edit Product/Service', "action" => "productService", "slug" => 'edit'],
                        ["name" => 'Export Product/Service list', "action" => "productService", "slug" => 'export-list'],
                        ["name" => 'Delete Product/Service', "action" => "productService", "slug" => 'delete'],
                        ["name" => 'View Product/Service Details', "action" => "productService", "slug" => 'show'],
                    ]
                ],
            ]
        ],
    ];
}
