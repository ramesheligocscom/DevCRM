import roleCreate from '../roles/add/index.vue';
import roleEdit from '../roles/edit/[id].vue';
import roleList from '../roles/index.vue';
export default [
  {
    path: '/roles',
    name: 'role-list',
    component: roleList,
    meta: { title: 'Role and Permission', permission: [{ action: 'role', subject: 'view' }] },
  },
  {
    path: '/role-permission-create',
    name: 'role-create',
    component: roleCreate,
    meta: { title: 'Role and Permission Create', permission: [{ action: 'role', subject: 'create' }] },
  },
  {
    path: '/role-permission/edit/:id',
    name: 'role-edit',
    component: roleEdit,
    meta: { title: 'Role and Permission Edit', permission: [{ action: 'role', subject: 'edit' }] },
  },
]
