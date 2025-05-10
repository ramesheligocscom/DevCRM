import CreateQuotation from '../add/AddDrawer.vue'
import QuotationDetail from '../details/[id].vue'
import EditQuotation from '../edit/EditDrawer.vue'
import QuotationList from '../list/index.vue'

export default [
  {
    path: '/quotations',
    name: 'quotation-list',
    component: QuotationList,
    meta: { title: 'Quotations' },
  },
  {
    path: '/quotations/details/:id',
    name: 'quotation-details-id',
    component: QuotationDetail,
    meta: { title: 'Quotation Detail' },
  },
  {
    path: '/quotations/edit/:id',
    name: 'quotation-edit',
    component: EditQuotation,
    meta: { title: 'Edit Quotation' },
  },
  {
    path: '/quotations/create',
    name: 'quotation-create',
    component: CreateQuotation,
    meta: { title: 'Create Quotation' },
  },
]

