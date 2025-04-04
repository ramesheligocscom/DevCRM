import LeadDetail from '../details/[id].vue'
import LeadList from '../list/index.vue'
export default [
  {
    path: '/leads',
    name: 'lead-list',
    component: LeadList,
    meta: { title: 'Leads' },
  },
  {
    path: '/leads/details/:id',
    name: 'lead-details-id',
    component: LeadDetail,
    meta: { title: 'Lead Detail' },
  },
]

