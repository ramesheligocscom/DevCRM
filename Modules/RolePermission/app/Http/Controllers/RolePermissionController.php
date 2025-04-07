<?php

namespace Modules\RolePermission\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\RolePermission\Constants\RolePermissionConst;
use Modules\RolePermission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Modules\RolePermission\Models\Permission;
use Modules\RolePermission\Models\PermissionCategory;
use Modules\RolePermission\Models\PermissionType;
use Modules\RolePermission\Models\RolePermission;
use Modules\RolePermission\Models\UserRole;

class RolePermissionController extends Controller
{
    const CONTROLLER_NAME = "Role Permission Controller";

    public function optionRoleList(Request $request)
    {
        try {
            $list = Role::where('name', '!=', RolePermissionConst::SUPER_ADMIN)->select('id', 'name')->get();
            return $this->actionSuccess('Option Role list successfully.', $list);
        } catch (\Exception $e) {
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
    }

    public function getRoleList(Request $request)
    {
        $this->updateRolePermission();

        try {
            $query = Role::query();
            // $query->where('name', '!=', RolePermissionConst::SUPER_ADMIN);

            # Filter by search query
            if ($search = $request->input('search')) {
                $query->where('name', 'LIKE', "%{$search}%");
            }

            # Sort results
            if ($sortKey = $request->input('sort_key')) {
                $sortOrder = $request->input('sort_order', 'asc');
                $query->orderBy($sortKey, $sortOrder);
            }

            # Pagination
            $perPage = $request->input('per_page', 10);
            $roles = $query->with('users')->orderBy('position', 'asc')->paginate($perPage);

            return $this->actionSuccess('Role list retrieved successfully.', customizingResponseData($roles));
        } catch (\Exception $e) {
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
    }

    public function getTargetRankList()
    {
        try {
            $roles = Role::whereNotIn('name', [
                RolePermissionConst::SUPER_ADMIN,
            ])->get(['id', 'name']);
            return response()->json(['success' => true, 'data' => $roles, 'message' => 'Target By Rank List']);
        } catch (\Exception $e) {
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
    }

    public function getLegalRole()
    {
        try {
            $legalRole = null;
            // $legalRole = Role::where('name', RolePermissionConst::LEGAL)->select('id', 'name')->first();

            if ($legalRole) {
                return response()->json(['data' => $legalRole]);
            }
        } catch (\Exception $e) {
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
    }

    public function getRoleInfo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role_id' => 'required|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return $this->actionFailure($validator->errors()->first());
        }

        try {
            $role = Role::where('id', $request->role_id)->first();
            return $this->actionSuccess('Role info retrieved successfully.', $role);
        } catch (\Exception $e) {
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
    }

    public function duplicateRoleCreate(Request $request)
    {
        $validator = Validator::make(['role_id' => $request->role_id], [
            'role_id' => 'required|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return $this->actionFailure($validator->errors()->first());
        }

        try {
            $role = Role::where('id', $request->role_id)->with('role_permission_list')->first();
            # Create base name and slug
            $newRoleName = $role->name . ' Copy';
            $newRoleSlug = Str::slug($newRoleName);
            $originalRoleSlug = $newRoleSlug;

            # Check if the slug already exists, and append a number if needed
            $counter = 1;
            while (Role::where('slug', $newRoleSlug)->exists()) {
                $newRoleSlug = $originalRoleSlug . '-' . $counter;
                $counter++;
            }

            $duplicate_role = Role::updateOrCreate(
                [
                    'name' => $newRoleName,
                    'slug' => $newRoleSlug,
                    'position' => $role->position,
                    'status' => $role->status,
                    'description' => $role->description,
                    "created_by" => request()->user()->id ?? null,
                ]
            );

            foreach ($role->role_permission_list as $key => $value) {
                RolePermission::updateOrCreate([
                    'role_id' => $duplicate_role->id,
                    'permission_id' => $value->permission_id,
                ]);
            }

            return $this->actionSuccess('Duplicate Role create successfully.', $role);
        } catch (\Exception $e) {
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
    }

    public function getPermissionList(Request $request)
    {
        $role_id = $request->role_id ?? null;
        $search = $request->search ?? null;

        # Validate Role
        if ($role_id) {
            $role = Role::find($role_id);
            if (!$role) {
                return $this->actionFailure("Role not found!");
            }
        }

        # Fetch permission types, categories, and permissions with search and role filter
        $query = PermissionType::query();

        if ($search) {
            $query->where('name', 'LIKE', '%' . $search . '%')
                ->orWhereHas('permission_category', function ($q) use ($search) {
                    $q->where('name', 'LIKE', '%' . $search . '%')
                        ->orWhereHas('permissions', function ($q) use ($search) {
                            $q->where('title', 'LIKE', '%' . $search . '%');
                        });
                });
        }

        $permissionData = $query->with([
            'permission_category.permissions' => function ($query) use ($role_id) {
                $query->select('id', 'title', 'slug', 'action', 'permission_category_id', 'permission_type_id', 'permission_subcategory_id');
            }
        ])->withCount('permissions')->get(['id', 'name', 'icon']);

        foreach ($permissionData as $key => $value) {
            foreach ($value->permission_category as $key => $category) {
                $all_category_permission_count = 0;
                foreach ($category->permissions as $key => $permission) {
                    $permission->check_permission = DB::table('role_permissions')->where('role_id', $role_id)->where('permission_id', $permission->id)->first() ? true : false;
                    if ($permission->check_permission) {
                        $all_category_permission_count++;
                    }
                }
                $category->all_category_permission = $all_category_permission_count == count($category->permissions) ? true : false;
            }
        }

        return $this->actionSuccess('Permission list fetched successfully!', $permissionData);
    }

    /**
     * Create or update a role and assign permissions.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveRole(Request $request)
    {
        $this->validate($request, [
            'role_name' => 'required|string|max:255',
            'position' => 'required|integer|min:0',
            'status' => 'required|in:Active,In-Active',
            'permission_list' => 'required|array',
            'permission_list.*.id' => 'required|uuid',
        ]);

        DB::beginTransaction();
        try {
            $slug = Str::slug($request->role_name);

            # If creating a new role (no role_id) or changing the name on update, ensure slug uniqueness
            if (!$request->role_id || Role::where('id', $request->role_id)->value('name') !== $request->role_name) {
                $originalSlug = $slug;
                $counter = 1;

                while (Role::where('slug', $slug)->where('id', '!=', $request->role_id)->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
            }

            $role = Role::updateOrCreate(
                ['id' => $request->role_id],
                [
                    'name' => $request->role_name,
                    'slug' => $slug,
                    'position' => $request->position,
                    'status' => $request->status,
                    'description' => $request->description,
                    "created_by" => request()->user()->id ?? null,
                ]
            );

            # Remove all existing permissions for the role
            RolePermission::where('role_id', $role->id)->delete();

            # Assign new permissions
            $permissions = collect($request->permission_list)->map(function ($permission) use ($role) {
                return [
                    'role_id' => $role->id,
                    'permission_id' => $permission['id'],
                ];
            });

            foreach ($permissions as $permission) {
                RolePermission::create($permission);
            }

            DB::commit();
            return $this->actionSuccess('Role saved successfully.', ['role_id' => $role->id]);
        } catch (\Exception $e) {
            DB::rollback();
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
    }

    public function roleDelete(Request $request)
    {
        $validator = Validator::make(['role_id' => $request->role_id], [
            'role_id' => 'required|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return $this->actionFailure($validator->errors()->first());
        }

        if (strtolower(trim($request->delete_text)) !== 'delete') {
            return $this->actionFailure('Your Delete input value is wrong. If you are permanently deleting the file, please type "delete" to confirm!');
        }

        if (UserRole::where('role_id', $request->role_id)->exists()) {
            return $this->actionFailure("Role User Already Assign");
        }

        DB::beginTransaction();
        try {
            $role = Role::where('id', $request->role_id)->delete();
            RolePermission::where('role_id', $request->role_id)->delete();
            DB::commit();
            return $this->actionSuccess("Role permanently deleted successfully.", $role);
        } catch (\Exception $e) {
            DB::rollBack();
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
    }

    public function rollAssignPermissionList(Request $request)
    {
        try {
            $role_list = Role::with('role_permission_list', 'role_permission_list.permission')->get();

            $list = [];
            foreach ($role_list as $key => $role) {
                $info = ['name' => $role->name];
                $permission = [];
                foreach ($role->role_permission_list as $key => $role_permission) {
                    if ($role_permission->permission) $permission[] = $role_permission->permission->permission;
                }
                $info['permission'] = $permission;
                $list[] = $info;
            }

            return $this->actionSuccess("Role permission list Successfully", $list);
        } catch (\Exception $e) {
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
    }

    /**
     * It returns the permissions of the user.
     *
     * @param Request request The request object.
     *
     * @return JSON Response
     */
    public function userPermission(Request $request)
    {
        try {
            $user = Auth::user();
            return $this->actionSuccess('Login User get permission list.', $user->getPermissionsViaRoles());
        } catch (\Exception $e) {
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
        return $this->actionFailure("RoleConstants::FAIL_USER_PERMISSION");
    }

    private function updateRolePermission()
    {
        if (Permission::where('action', 'reminder')->where('slug', "view")->exists()) return;

        # Clean up old data
        // DB::table('role_permissions')->whereNotNull('role_id')->delete();
        // Permission::whereNotNull('title')->delete();
        // PermissionCategory::whereNotNull('name')->delete();
        // PermissionType::whereNotNull('name')->delete();

        # role Permission Create
        $permission_list = RolePermissionConst::PERMISSION_LIST;
        DB::beginTransaction();
        try {
            foreach ($permission_list as $type) {
                try {
                    $permission_type = PermissionType::updateOrCreate(
                        ['name' => $type['name']],
                        [
                            'slug' => Str::slug($type['name']),
                            'icon' => $type['icon'],
                        ]
                    );
                    foreach ($type['category'] as $category) {
                        $permission_category = PermissionCategory::updateOrCreate(
                            ['permission_type_id' => $permission_type->id, 'name' => $category['name']],
                            [
                                'slug' => Str::slug($category['name']),
                                'permission_type_id' => $permission_type->id,
                            ]
                        );
                        foreach ($category['permission_list'] as $permission) {
                            $full_permission = $permission['action'] . '_' . $permission['slug'];

                            Permission::updateOrCreate(
                                [
                                    'permission_type_id' => $permission_type->id,
                                    'permission_category_id' => $permission_category->id,
                                    'title' => $permission['name'],
                                ],
                                [
                                    'permission' => $full_permission,
                                    'slug' => $permission['slug'],
                                    'action' => $permission['action'],
                                    'description' => $permission['name'] . ' description',
                                    'permission_type_id' => $permission_type->id,
                                    'permission_category_id' => $permission_category->id,
                                    'permission_subcategory_id' => null,
                                ]
                            );
                        }
                    }
                } catch (\Exception $e) {
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
        }

        # Role Permission assign
        $role_list = Role::get();
        foreach ($role_list as $role) {
            createNewRole($role);
        }
    }
}
