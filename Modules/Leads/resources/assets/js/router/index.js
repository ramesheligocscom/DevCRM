import LeadList from '@modules/Leads/resources/assets/js/pages/lead/'
import { createRouter, createWebHistory } from 'vue-router'

const routes = [
  {
    path: '/leads',
    name: 'lead-list',
    component: LeadList,
    meta: { title: 'Leads' },
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router
