<script setup>
import moment from 'moment'
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
const currentLead = ref(null);

// Data table Headers
const tableHeaderSlug = ref('lead-list');
const headers = ref([]);
const getFilteredHeaderValue = async (headerList) => { headers.value = headerList; };

const editBranch = (item) => {
  currentLead.value = JSON.parse(JSON.stringify(item));
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
  fetchLeads();
}
const dataItems = ref([])
const totalItems = ref(0)

const fetchLeads = async () => {
  try {
    const response = await $api(
      `/leads?search=${searchQuery.value ?? ""}&page=${page.value}&sort_key=${sortBy.value ?? ""}&sort_order=${orderBy.value ?? ""}&per_page=${itemsPerPage.value}`
    )

    dataItems.value = response.data
    totalItems.value = response.meta.total
  } catch (err) {
    console.error('Failed to fetch leads:', err)
    // Optionally show a toast
    toast.error('Failed to load leads')
  }
}

const addLead = (item) => {
  currentLead.value = null;
  isAddEditDrawerOpen.value = true;
}

const openDeleteDialog = (item) => {
  currentLead.value = JSON.parse(JSON.stringify(item));
  isDeleteDialogOpen.value = true;
}

fetchLeads();

const makeDateFormat = (date , onlyDate = false) => {
    if(onlyDate)
    return moment(date).format('DD-MM-Y');
    else
    return moment(date).format('LLLL');
};
</script>

<template>
  <div v-if="$can('leads', 'view')">
    <VCard>
      <VCardText>
        <div class="d-flex justify-space-between flex-wrap gap-y-4">
          <AppTextField v-model="searchQuery" style="max-inline-size: 280px; min-inline-size: 280px;"
            placeholder="Search Name" />
          <div class="d-flex flex-row gap-4 align-center flex-wrap">
            <AppSelect v-model="itemsPerPage" :items="[5, 10, 20, 50, 100]" />

            <VBtn v-if="$can('leads', 'export-list')" prepend-icon="tabler-upload" variant="tonal" color="secondary">
              Export
            </VBtn>
            <VBtn v-if="$can('leads', 'create')" prepend-icon="tabler-plus" @click="addLead()">
              Add New
            </VBtn>

            <!-- Filter Header Btn FilterHeaderTableBtn -->
            <FilterHeaderTableBtn :slug="tableHeaderSlug" @filterHeaderValue="getFilteredHeaderValue" />
          </div>
        </div>
      </VCardText>

      <VDivider />
      <VDataTableServer v-model:items-per-page="itemsPerPage" v-model:page="page" :items="dataItems" item-value="name"
        :headers="headers.filter((header) => header.checked)" :items-length="totalItems" show-select
        class="text-no-wrap" @update:options="updateOptions">

        <template #item.name="{ item }">
          <RouterLink :to="{ name: 'lead-details-id', params: { id: item.id } }"
                  class="text-link font-weight-medium d-inline-block" style="line-height: 1.375rem;">
                  {{ item.name }}
          </RouterLink>
        </template>

        <!-- creator -->
        <template #item.created_by="{ item }">
          {{ item.creator?.name || '—' }}
        </template>
        <!-- updater -->
        <template #item.last_updated_by="{ item }">
          {{ item.updater?.name || '-' }}
        </template>
         <!-- assigned_user -->
         <template #item.assigned_user="{ item }">
          {{ item.assigned_user?.name || '-' }}
        </template>
         <!-- visit_assignee -->
         <template #item.visit_assignee="{ item }">
          {{ item.visit_assignee?.name || '-' }}
        </template>
        <!-- status -->
        <template #item.status="{ item }">
          <VChip :color="resolveStatusVariant(item.status).color" size="small">
            {{ resolveStatusVariant(item.status).text }}
          </VChip>
        </template>
        <template #item.created_at="{ item }">
          {{ makeDateFormat(item.created_at )}}
        </template>

        <template #item.updated_at="{ item }">
          {{ item.updater ? makeDateFormat(item.updated_at ) : '-'}}
        </template>
        <!-- Actions Column -->
        <template #item.action="{ item }">
          <IconBtn :to="{ name: 'lead-details-id', params: { id: item.id } }">
            <VIcon icon="tabler-eye" />
          </IconBtn>
          <IconBtn v-if="$can('leads', 'edit')" @click="editBranch(item)">
            <VIcon icon="tabler-pencil" />
          </IconBtn>
          <IconBtn v-if="$can('leads', 'delete')" @click="openDeleteDialog(item)">
            <VIcon icon="tabler-trash" />
          </IconBtn>
        </template>
        <template #bottom>
          <TablePagination v-model:page="page" :items-per-page="itemsPerPage" :total-items="totalItems" />
        </template>
      </VDataTableServer>
    </VCard>


    <!-- 👉 Confirm Dialog -->
    <ConfirmDialog v-model:isDialogVisible="isDeleteDialogOpen" confirm-title="Delete!"
      confirmation-question="Are you sure want to delete lead?" :currentItem="currentLead" @submit="fetchLeads"
      :endpoint="`/leads/${currentLead?.id}`" @close="isDeleteDialogOpen = false" />

    <AddEditDrawer v-model:is-drawer-open="isAddEditDrawerOpen" :currentLead="currentLead" @submit="fetchLeads"
      @close="isAddEditDrawerOpen = false" />
  </div>
</template>
