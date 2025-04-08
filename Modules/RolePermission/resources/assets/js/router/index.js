import roleCreate from '../roles/add/index.vue';
import roleEdit from '../roles/edit/[id].vue';
import roleList from '../roles/index.vue';
export default [
  {
    path: '/roles',
    name: 'role-list',
    component: roleList,
    meta: { title: 'Role and Permission' },
  },
  {
    path: '/role-permission-create',
    name: 'role-create',
    component: roleCreate,
    meta: { title: 'Role and Permission Create' },
  },
  {
    path: '/role-permission/edit/:id',
    name: 'role-edit',
    component: roleEdit,
    meta: { title: 'Role and Permission Edit' },
  },
]
