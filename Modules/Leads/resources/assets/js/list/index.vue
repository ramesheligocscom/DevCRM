<script setup>
import AddDrawer from '../add/AddDrawer.vue'

const searchQuery = ref('')
const isAddDrawerOpen = ref(false)

// Data table options
const itemsPerPage = ref(10)
const page = ref(1)
const sortBy = ref()
const orderBy = ref()

// Data table Headers
const headers = [
  { title: 'NAME', key: 'name' },
  { title: 'CONTACT PERSON', key: 'contact_person' },
  { title: 'CONTACT PERSON ROLE', key: 'contact_person_role' },
  // { title: 'EMAIL', key: 'email' },
  { title: 'PHONE', key: 'phone' },
  // { title: 'ADDRESS', key: 'address' },
  { title: 'STATUS', key: 'status' },
  { title: 'SOURCE', key: 'source' },
  { title: 'ASSIGNED USER', key: 'assigned_user' },
  // { title: 'NOTE', key: 'note' },
  // { title: 'VISIT ASSIGNEE', key: 'visit_assignee' },
  // { title: 'VISIT TIME', key: 'visit_time' },
  // { title: 'CREATED BY', key: 'created_by' },
  // { title: 'LAST UPDATED BY', key: 'last_updated_by' },
  // { title: 'CLIENT ID', key: 'client_id' },
  // { title: 'QUOTATION ID', key: 'quotation_id' },
  // { title: 'CONTRACT ID', key: 'contract_id' },
  // { title: 'INVOICE ID', key: 'invoice_id' },
  // { title: 'IS DELETED', key: 'is_deleted' },
]

const resolveStatusVariant = status => {
  if (status === 1) return { color: 'primary', text: 'Current' }
  else if (status === 2) return { color: 'success', text: 'Professional' }
  else if (status === 3) return { color: 'error', text: 'Rejected' }
  else if (status === 4) return { color: 'warning', text: 'Resigned' }
  else return { color: 'info', text: 'Applied' }
}

const updateOptions = options => {
  sortBy.value = options.sortBy[0]?.key
  orderBy.value = options.sortBy[0]?.order
}

const { data: responseData } = await useApi(createUrl('/v1/leads', {
  query: {
    q: searchQuery,
    itemsPerPage,
    page,
    sortBy,
    orderBy,
  },
}))

const dataItems = computed(() => responseData.value.data)
const totalItems = computed(() => responseData.value.meta.total)
</script>

<template>
  <div>
    <VCard>
      <VCardText>
        <div class="d-flex justify-space-between flex-wrap gap-y-4">
          <AppTextField v-model="searchQuery" style="max-inline-size: 280px; min-inline-size: 280px;"
            placeholder="Search Name" />
          <div class="d-flex flex-row gap-4 align-center flex-wrap">
            <AppSelect v-model="itemsPerPage" :items="[5, 10, 20, 50, 100]" />

            <VBtn prepend-icon="tabler-upload" variant="tonal" color="secondary">
              Export
            </VBtn>
            <VBtn prepend-icon="tabler-plus" @click="isAddDrawerOpen = !isAddDrawerOpen">
              Add New
            </VBtn>
          </div>
        </div>
      </VCardText>

      <VDivider />
      <VDataTableServer v-model:items-per-page="itemsPerPage" v-model:page="page" :items="dataItems" item-value="name"
        :headers="headers" :items-length="totalItems" show-select class="text-no-wrap" @update:options="updateOptions">
        <template #item.name="{ item }">
          <div class="d-flex align-center gap-x-3">
            <VAvatar size="34" :variant="!item.avatar ? 'tonal' : undefined">
              <VImg v-if="item.avatar" :src="item.avatar" />
              <span v-else>{{ avatarText(item.name) }}</span>
            </VAvatar>
            <div class="d-flex flex-column">
              <RouterLink :to="{ name: 'lead-details-id', params: { id: item.id } }"
                class="text-link font-weight-medium d-inline-block" style="line-height: 1.375rem;">
                {{ item.name }}
              </RouterLink>
              <div class="text-body-2">
                {{ item.email }}
              </div>
            </div>
          </div>
        </template>
        <!-- status -->
        <template #item.status="{ item }">
          <VChip :color="resolveStatusVariant(item.status).color" size="small">
            {{ resolveStatusVariant(item.status).text }}
          </VChip>
        </template>
        <template #bottom>
          <TablePagination v-model:page="page" :items-per-page="itemsPerPage" :total-items="totalItems" />
        </template>
      </VDataTableServer>
    </VCard>

    <AddDrawer v-model:is-drawer-open="isAddDrawerOpen" />
  </div>
</template>
