import CreateProductService from '../add/AddDrawer.vue'
import ProductServiceDetail from '../details/[id].vue'
import EditProductService from '../edit/EditDrawer.vue'
import ProductServiceList from '../list/index.vue'

export default [
  {
    path: '/product-services',
    name: 'product-service-list',
    component: ProductServiceList,
    meta: { title: 'Product/Services' },
  },
  {
    path: '/product-services/details/:id',
    name: 'product-service-details-id',
    component: ProductServiceDetail,
    meta: { title: 'Product/Service Detail' },
  },
  {
    path: '/product-services/edit/:id',
    name: 'product-service-edit',
    component: EditProductService,
    meta: { title: 'Edit Product/Service' },
  },
  {
    path: '/product-services/create',
    name: 'product-service-create',
    component: CreateProductService,
    meta: { title: 'Create Product/Service' },
  },
]

