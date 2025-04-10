import ClientDetail from '../details/[id].vue'
import CustomerList from '../list/index.vue'

export default [
  {
    path: '/clients',
    name: 'clients-list',
    component: CustomerList,
    meta: { title: 'Client' },
  },
  {
    path: '/clients/details/:id',
    name: 'client-details-id',
    component: ClientDetail,
    meta: { title: 'Client Detail' },
  },
]

