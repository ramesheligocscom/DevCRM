import ContractDetail from '../details/[id].vue'
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
]

