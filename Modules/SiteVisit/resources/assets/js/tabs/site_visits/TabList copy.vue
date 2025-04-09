<script setup>
import AddDrawer from './AddDrawer.vue';

const searchQuery = ref('')

// Data table options
const itemsPerPage = ref(10)
const page = ref(1)
const sortBy = ref()
const currentClient = ref(null);
const openClientModal = ref(false);
const orderBy = ref()

const updateOptions = options => {
  sortBy.value = options.sortBy[0]?.key
  orderBy.value = options.sortBy[0]?.order
}

const headers = [
  {
    title: 'Visit time',
    key: 'visit_time',
  },
  {
    title: 'visit assignee',
    key: 'visit_assignee',
  },
  {
    title: 'created_at',
    key: 'created_at',
  },
  {
    title: 'created_by',
    key: 'created_by',
  },
  {
    title: 'status',
    key: 'status',
  },
  {
    title: 'lead_id',
    key: 'lead_id',
  },
  {
    title: 'client_id',
    key: 'client_id',
  },
  {
    title: 'visit_notes',
    key: 'visit_notes',
  },
  {
    title: 'Actions',
    key: 'actions',
    sortable: false,
  },
]

const resolveStatus = status => {
  if (status === 'Delivered')
    return { color: 'success' }
  if (status === 'Out for Delivery')
    return { color: 'primary' }
  if (status === 'Ready to Pickup')
    return { color: 'info' }
  if (status === 'Dispatched')
    return { color: 'warning' }
}

const {
  data: ordersData,
  execute: fetchOrders,
} = await useApi(createUrl('/apps/ecommerce/orders', {
  query: {
    q: searchQuery,
    page,
    itemsPerPage,
    sortBy,
    orderBy,
  },
}))



const editBranch = (item) => {
  currentClient.value = JSON.parse(JSON.stringify(item));
  openClientModal.value = true;
};


const orders = computed(() => ordersData.value?.orders || [])
const totalOrder = computed(() => ordersData.value?.total || 0)

const deleteOrder = async id => {
  await $api(`/apps/ecommerce/orders/${id}`, { method: 'DELETE' })
  fetchOrders()
}
</script>

<template>
  <VCard>
    <VCardText>
      <div class="d-flex justify-space-between align-center flex-nowrap w-100">
        <!-- Left: Title -->
        <h5 class="text-h5 mb-0">
          Site Visits
        </h5>

        <!-- Right: Search + Filter Button Group -->
        <div class="d-flex align-center gap-2">
          <AppTextField v-model="searchQuery" placeholder="Search Order"
            style="max-inline-size: 200px; min-inline-size: 200px;" />

          <FilterBtn :menu-list="headers" @update:menuList="toggleHeaderVisibility" />
          <VBtn rounded icon="tabler-plus" @click="openClientModal = true" />
        </div>
      </div>

    </VCardText>

    <VDivider />
    <VDataTableServer v-model:items-per-page="itemsPerPage" v-model:page="page" :headers="headers" :items="orders"
      item-value="id" :items-length="totalOrder" class="text-no-wrap" @update:options="updateOptions">
      <!-- Order ID -->
      <template #item.order="{ item }">
        <RouterLink :to="{ name: 'apps-ecommerce-order-details-id', params: { id: item.order } }">
          #{{ item.order }}
        </RouterLink>
      </template>

      <!-- Date -->
      <template #item.date="{ item }">
        {{ new Date(item.visit_time).toDateString() }}
      </template>

      <!-- Status -->
      <template #item.status="{ item }">
        <VChip label :color="resolveStatus(item.status)?.color" size="small">
          {{ item.status }}
        </VChip>
      </template>

      <!-- Spent -->
      <template #item.spent="{ item }">
        ${{ item.spent }}
      </template>

      <!-- Actions -->
      <template #item.actions="{ item }">
        <IconBtn>
          <VIcon icon="tabler-dots-vertical" />
          <VMenu activator="parent">
            <VList>
              <VListItem value="view" :to="{ name: 'apps-ecommerce-order-details-id', params: { id: item.order } }">
                View
              </VListItem>
              <VListItem value="delete" @click="deleteOrder(item.id)">
                Delete
              </VListItem>
            </VList>
          </VMenu>
        </IconBtn>
      </template>


      <!-- pagination -->
      <template #bottom>
        <TablePagination v-model:page="page" :items-per-page="itemsPerPage" :total-items="totalOrder" />
      </template>
    </VDataTableServer>
  </VCard>

  <!-- Client Add/Edit Drawer -->
  <AddDrawer v-model:isDrawerOpen="openClientModal" :currentClient="currentClient" @submit="fetchClients"
    v-if="openClientModal" :clients="clients" />
</template>
