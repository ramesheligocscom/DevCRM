import FollowUpDetails from '../details/[id].vue'

export default [
  {
    path: '/:type/follow-up/:id',
    name: 'follow-up',
    component: FollowUpDetails,
    meta: { title: 'Follow up' },
  },
]

