export default [
  {
    path: '/leads',
    component: () => import('../../pages/lead/list/index.vue'),
    children: [
      {
        path: '',
        name: 'lead-list',
        component: () => import('../../pages/lead/list/index.vue'),
        meta: { title: 'Leads' },
      },
    ],
  },
]
