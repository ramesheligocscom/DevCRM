<script setup>
import moment from 'moment';
import { toast } from 'vue3-toastify';
import AddDrawer from '../add/AddDrawer.vue';
import ConfirmDialog from '../dialog/ConfirmDialog.vue';
const searchQuery = ref('')
const isAddEditDrawerOpen = ref(false)
const isDeleteDialogOpen = ref(false)
// Data table options

const itemsPerPage = ref(10)
const page = ref(1)
const sortBy = ref()
const orderBy = ref()
const currentSiteVisit = ref(null);

// Data table Headers
const tableHeaderSlug = ref('client-site-visit');
const headers = ref([]);
const getFilteredHeaderValue = async (headerList) => { headers.value = headerList; };

const editBranch = (item) => {
  currentSiteVisit.value = JSON.parse(JSON.stringify(item));
  isAddEditDrawerOpen.value = true;
};


const resolveStatusVariant = status => {
  if (status === 'scheduled') return { color: 'primary', text: 'Scheduled' }
  else if (status === 'completed') return { color: 'success', text: 'Completed' }
  else if (status === 'canceled') return { color: 'error', text: 'Canceled' }
  else if (status === 'rescheduled') return { color: 'warning', text: 'Rescheduled' }
  else return { color: 'secondary', text: 'Unknown' }
}


const updateOptions = options => {
  sortBy.value = options.sortBy[0]?.key
  orderBy.value = options.sortBy[0]?.order
  fetchSiteVisits();
}
const dataItems = ref([])
const totalItems = ref(0)

const fetchSiteVisits = async () => {
  try {
   

    const response = await $api(
      `/sitevisit?search=${searchQuery.value ?? ""}&page=${page.value}&sort_key=${sortBy.value ?? ""}&sort_order=${orderBy.value ?? ""}&per_page=${itemsPerPage.value}`
    )

    dataItems.value = response.data
    totalItems.value = response.meta.total
  } catch (err) {
    console.error('Failed to fetch site visits:', err)
    // Optionally show a toast
    toast.error('Failed to load site visits')
  }
}

const addSiteVisit = (item) => {
  currentSiteVisit.value = null;
  isAddEditDrawerOpen.value = true;
}

const openDeleteDialog = (item) => {
  currentSiteVisit.value = JSON.parse(JSON.stringify(item));
  isDeleteDialogOpen.value = true;
}

const refresh = () => {
  fetchSiteVisits();
}

const makeDateFormat = (date , onlyDate = false) => {
    if(onlyDate)
    return moment(date).format('DD-MM-Y');
    else
    return moment(date).format('LLLL');
};
</script>

<template>
  <div v-if="$can('client', 'view')">
    <VCard>
      <VCardText>
        <div class="d-flex justify-space-between flex-wrap gap-y-4">
          <AppTextField v-model="searchQuery" style="max-inline-size: 280px; min-inline-size: 280px;"
            placeholder="Search By Visit Note" 
            @input="fetchSiteVisits"
            />
          <div class="d-flex flex-row gap-4 align-center flex-wrap">
            <AppSelect v-model="itemsPerPage" :items="[5, 10, 20, 50, 100]" />

            <VBtn v-if="$can('client', 'export-list')" prepend-icon="tabler-upload" variant="tonal" color="secondary">
              Export
            </VBtn>
            <VBtn v-if="$can('client', 'create')" prepend-icon="tabler-plus" @click="addSiteVisit()">
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
        <!-- status -->
        <template #item.status="{ item }">
          <VChip :color="resolveStatusVariant(item.status).color" size="small">
            {{ resolveStatusVariant(item.status).text }}
          </VChip>
        </template>

        <template #item.visit_time="{ item }">
          {{ item.visit_time ? makeDateFormat(item.visit_time ) : '-'}}
        </template>
        
        <template #item.created_at="{ item }">
          {{ makeDateFormat(item.created_at )}}
        </template>

        <template #item.updated_at="{ item }">
          {{ item.updater ? makeDateFormat(item.updated_at ) : '-'}}
        </template>
        <!-- Actions Column -->
        <template #item.action="{ item }">
          <IconBtn v-if="$can('client', 'edit')" @click="editBranch(item)">
            <VIcon icon="tabler-pencil" />
          </IconBtn>
          <IconBtn v-if="$can('client', 'delete')" @click="openDeleteDialog(item)">
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
      confirmation-question="Are you sure want to delete lead?" :currentItem="currentSiteVisit" @submit="refresh"
      :endpoint="`/sitevisit/${currentSiteVisit?.id}`" @close="isDeleteDialogOpen = false" />

    <AddDrawer v-model:is-drawer-open="isAddEditDrawerOpen" :currentItem="currentSiteVisit" @submit="refresh"
      @close="isAddEditDrawerOpen = false" />
  </div>
</template>
