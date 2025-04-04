import CustomerDetail from '../details/[id].vue'
import CustomerList from '../list/index.vue'

export default [
  {
    path: '/clients',
    name: 'clients-list',
    component: CustomerList,
    meta: { title: 'Client' },
  },
  {
    path: '/clients/:id',
    name: 'clients-view',
    component: CustomerDetail,
    meta: { title: 'Client' },
  },
]

