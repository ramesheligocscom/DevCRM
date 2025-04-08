import CreateInvoice from '../add/AddDrawer.vue'
import InvoiceDetail from '../details/[id].vue'
import EditInvoice from '../edit/EditDrawer.vue'
import InvoiceList from '../list/index.vue'

export default [
  {
    path: '/invoices',
    name: 'invoice-list',
    component: InvoiceList,
    meta: { title: 'Invoices' },
  },
  {
    path: '/invoices/details/:id',
    name: 'invoice-details-id',
    component: InvoiceDetail,
    meta: { title: 'Invoice Detail' },
  },
  {
    path: '/invoices/edit/:id',
    name: 'invoice-edit',
    component: EditInvoice,
    meta: { title: 'Edit Invoice' },
  },
  {
    path: '/invoices/create',
    name: 'invoice-create',
    component: CreateInvoice,
    meta: { title: 'Create Invoice' },
  },
]

