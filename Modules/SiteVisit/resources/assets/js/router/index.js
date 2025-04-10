import SiteVisitDetails from '../details/[id].vue'

export default [
  {
    path: '/:type/site-visit/:id',
    name: 'site-visit',
    component: SiteVisitDetails,
    meta: { title: 'Site Visit' },
  },
]

