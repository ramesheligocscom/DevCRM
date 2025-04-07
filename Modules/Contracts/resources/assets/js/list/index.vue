<script setup>
import AddEditDrawer from '../add/AddEditDrawer.vue'
import ConfirmDialog from '../dialog/ConfirmDialog.vue'
const searchQuery = ref('')
const isAddEditDrawerOpen = ref(false)
const isDeleteDialogOpen = ref(false)

// Data table options
const itemsPerPage = ref(10)
const page = ref(1)
const sortBy = ref()
const orderBy = ref()
const currentContract = ref(null);

const headers = [
  { title: 'ITEMS', key: 'items' },
  { title: 'START DATE', key: 'start_date' },
  { title: 'END DATE', key: 'end_date' },
  { title: 'SUB TOTAL', key: 'sub_total' },
  { title: 'DISCOUNT', key: 'discount' },
  { title: 'TAX', key: 'tax' },
  { title: 'STATUS', key: 'status' },
  { title: 'CLIENT ID', key: 'client_id' },
  { title: 'QUOTATION ID', key: 'quotation_id' },
  { title: 'INVOICE ID', key: 'invoice_id' },
  { title: 'CREATED BY', key: 'created_by' },
  { title: 'LAST UPDATED BY', key: 'last_updated_by' },
  { title: 'ACTIONS', key: 'actions' }
];

const editBranch = (item) => {
  currentContract.value = JSON.parse(JSON.stringify(item));
  isAddEditDrawerOpen.value = true;
};

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
  fetchContracts();
}
const dataItems = ref([])
const totalItems = ref(0)

const fetchContracts = async () => {
  try {
    const response = await $api(
      `/contracts?search=${searchQuery.value ?? ""}&page=${page.value}&sort_key=${sortBy.value ?? ""}&sort_order=${orderBy.value ?? ""}&per_page=${itemsPerPage.value}`
    )

    dataItems.value = response.data
    totalItems.value = response.meta.total
  } catch (err) {
    console.error('Failed to fetch Contracts:', err)
    // Optionally show a toast
    toast.error('Failed to load Contracts')
  }
}

const addContract = (item) => {
  currentContract.value = null;
  isAddEditDrawerOpen.value = true;
}

const openDeleteDialog = (item) => {
  currentContract.value = JSON.parse(JSON.stringify(item));
  isDeleteDialogOpen.value = true;
}

fetchContracts();

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
            <VBtn prepend-icon="tabler-plus" @click="addContract()">
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
              <RouterLink :to="{ name: 'contract-details-id', params: { id: item.id } }"
                class="text-link font-weight-medium d-inline-block" style="line-height: 1.375rem;">
                {{ item.name }}
              </RouterLink>
              <div class="text-body-2">
                {{ item.email }}
              </div>
            </div>
          </div>
        </template>
        <!-- Actions Column -->
        <template #item.actions="{ item }">
          <IconBtn @click="editBranch(item)">
            <VIcon icon="tabler-pencil" />
          </IconBtn>

          <IconBtn @click="openDeleteDialog(item)">
            <VIcon icon="tabler-trash" />
          </IconBtn>
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

    <!-- 👉 Confirm Dialog -->
    <ConfirmDialog v-model:isDialogVisible="isDeleteDialogOpen" cancel-title="Delete" confirm-title="Delete!"
      confirm-msg="Contract deleted successfully." confirmation-question="Are you sure want to delete contract?"
      cancel-msg="Contract Deletion Cancelled!!" :currentContract="currentContract" @submit="fetchContracts"
      @close="isDeleteDialogOpen = false" />

    <AddEditDrawer v-model:is-drawer-open="isAddEditDrawerOpen" :currentContract="currentContract"
      @submit="fetchContracts" @close="isAddEditDrawerOpen = false" />
  </div>
</template>
