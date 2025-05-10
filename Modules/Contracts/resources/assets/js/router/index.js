import CreateContract from '../add/AddDrawer.vue'
import ContractDetail from '../details/[id].vue'
import EditContract from '../edit/EditDrawer.vue'
import ContractList from '../list/index.vue'

export default [
  {
    path: '/contracts',
    name: 'contract-list',
    component: ContractList,
    meta: { title: 'Contracts' },
  },
  {
    path: '/contracts/details/:id',
    name: 'contract-details-id',
    component: ContractDetail,
    meta: { title: 'Contract Detail' },
  },
  {
    path: '/contracts/edit/:id',
    name: 'contract-edit',
    component: EditContract,
    meta: { title: 'Edit Contract' },
  },
  {
    path: '/contracts/create',
    name: 'contract-create',
    component: CreateContract,
    meta: { title: 'Create Contract' },
  },
]

