<?php

namespace Modules\FollowUp\Constants;

class FollowUpConst
{
    const FOLLOW_UP_PERMISSION_LIST = [
        # 3. Clients Permission
        [
            'name' => 'Follow Up',
            'position' => 3,
            "icon" => 'tabler-hierarchy-2',
            "category" => [
                [
                    'name' => 'Follow Up',
                    "permission_list" => [
                        ["name" => 'Follow up', "action" => "followUp", "slug" => 'view'],
                        ["name" => 'Create Follow Up', "action" => "followUp", "slug" => 'create'],
                        ["name" => 'Delete Follow Up', "action" => "followUp", "slug" => 'delete'],
                        ["name" => 'Edit Follow Up', "action" => "followUp", "slug" => 'edit'],
                        ["name" => 'Activity Timeline', "action" => "followUp", "slug" => 'activity-timeline'],
                    ]
                ],
            ]
        ],
    ];
}
