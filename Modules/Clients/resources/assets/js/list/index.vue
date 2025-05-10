<script setup>
import moment from 'moment';
import { ref } from 'vue';
import AddDrawer from '../add/AddDrawer.vue';
import ConfirmDialog from '../dialog/ConfirmDialog.vue';

const searchQuery = ref('')
const isDeleteDialogOpen = ref(false)
const openClientModal = ref(false)
const currentClient = ref(null)

// Data table options
const itemsPerPage = ref(10)
const page = ref(1)
const sortBy = ref()
const orderBy = ref()
const loading = ref(true)

const tableHeaderSlug = ref('client-list')
const headers = ref([])
const getFilteredHeaderValue = async (headerList) => { headers.value = headerList }

// Client data
const dataItems = ref([])
const totalItems = ref(0)

const resolveStatusVariant = (status) => {
  if (status === "Active") return { color: "success", text: "Active" }
  else if (status === "In Active") return { color: "error", text: "In Active" }
  else return { color: "success", text: "Active" }
}

const updateOptions = options => {
  sortBy.value = options.sortBy[0]?.key
  orderBy.value = options.sortBy[0]?.order
  fetchClients()
}

const fetchClients = async () => {
  loading.value = true
  try {
    const response = await $api(
      `/clients?search=${searchQuery.value ?? ""}&page=${page.value}&sort_key=${sortBy.value ?? ""}&sort_order=${orderBy.value ?? ""}&per_page=${itemsPerPage.value}`
    )

    dataItems.value = response.data
    totalItems.value = response.meta.total
  } catch (err) {
    console.error('Failed to fetch dataItems:', err)
  } finally {
    loading.value = false
  }
}

const openDeleteDialog = (item) => {
  currentClient.value = JSON.parse(JSON.stringify(item))
  isDeleteDialogOpen.value = true
}

const editClient = (item) => {
  currentClient.value = JSON.parse(JSON.stringify(item))
  openClientModal.value = true
}

const refresh = async () => {
  await fetchClients()
  isDeleteDialogOpen.value = false
}

const makeDateFormat = (date, onlyDate = false) => {
  if(onlyDate)
    return moment(date).format('DD-MM-Y')
  else
    return moment(date).format('LLLL')
}

</script>

<template>
  <div v-if="$can('client', 'view')">
    <VCard>
      <VCardText>
        <div class="d-flex justify-space-between flex-wrap gap-y-4">
          <AppTextField 
            v-model="searchQuery" 
            style="max-inline-size: 280px; min-inline-size: 280px;"
            placeholder="Search Name" 
            @input="fetchClients"
          />
          <div class="d-flex flex-row gap-4 align-center flex-wrap">
            <AppSelect 
              v-model="itemsPerPage" 
              :items="[5, 10, 20, 50, 100]" 
              @update:modelValue="fetchClients"
            />

            <VBtn 
              v-if="$can('client', 'export-list')" 
              prepend-icon="tabler-upload" 
              variant="tonal" 
              color="secondary"
            >
              Export
            </VBtn>

            <VBtn 
              v-if="$can('client', 'create')" 
              prepend-icon="tabler-plus"
              @click="openClientModal = true; currentClient = null"
            >
              Add New
            </VBtn>

            <!-- Filter Header Btn -->
            <FilterHeaderTableBtn 
              :slug="tableHeaderSlug" 
              @filterHeaderValue="getFilteredHeaderValue" 
            />
          </div>
        </div>
      </VCardText>

      <VDivider />

      <VDataTableServer v-model:items-per-page="itemsPerPage" v-model:page="page" :items="dataItems" item-value="name"
        :headers="headers.filter((header) => header.checked)" :items-length="totalItems" show-select
        class="text-no-wrap" @update:options="updateOptions">
        <!-- Name Column -->
        <template #item.name="{ item }">
          <RouterLink 
            v-if="$can('client', 'show')" 
            :to="{ name: 'client-details-id', params: { id: item.id } }"
            class="text-link font-weight-medium d-inline-block" 
            style="line-height: 1.375rem;"
          >
            {{ item.name }}
          </RouterLink>
          <span v-else class="font-weight-medium">
            {{ item.name }}
          </span>
        </template>

        <!-- Phone Column -->
        <template #item.phone="{ item }">
          {{ item.phone ? item.phone.substring(0, 5) + "-" + item.phone.substring(5) : "—" }}
        </template>

        <!-- Status Column -->
        <template #item.status="{ item }">
          <VChip :color="resolveStatusVariant(item.status).color" size="small">
            {{ resolveStatusVariant(item.status).text }}
          </VChip>
        </template>
        <!-- assigned_user -->
        <template #item.assigned_user="{ item }">
          {{ item.assigned_user?.name || '—' }}
        </template>
         <!-- creator -->
         <template #item.created_by="{ item }">
          {{ item.creator?.name || '—' }}
        </template>
        <!-- updater -->
        <template #item.last_updated_by="{ item }">
          {{ item.updater?.name || '-' }}
        </template>
        <!-- Created At Column -->
        <template #item.created_at="{ item }">
          {{ makeDateFormat(item.created_at, true) }}
        </template>
         <!-- updated_at -->
         <template #item.updated_at="{ item }">
          {{ item.updater ? makeDateFormat(item.updated_at ) : '-'}}

        </template>
        <!-- Actions Column -->
        <template #item.action="{ item }">
          <IconBtn 
            @click="editClient(item)" 
            v-if="$can('client', 'edit')"
          >
            <VIcon icon="tabler-pencil" />
          </IconBtn>

          <RouterLink 
            v-if="$can('client', 'show')" 
            :to="{ name: 'client-details-id', params: { id: item.id } }"
          >
            <VIcon color="secondary" icon="tabler-eye" />
          </RouterLink>

          <IconBtn 
            v-if="$can('client', 'delete')" 
            @click="openDeleteDialog(item)"
          >
            <VIcon icon="tabler-trash" />
          </IconBtn>
        </template>

        <template #bottom>
          <TablePagination 
            v-model:page="page" 
            :items-per-page="itemsPerPage" 
            :total-items="totalItems" 
          />
        </template>
      </VDataTableServer>
    </VCard>

    <!-- Confirm Delete Dialog -->
    <ConfirmDialog 
      v-model:isDialogVisible="isDeleteDialogOpen" 
      confirm-title="Delete!"
      confirmation-question="Are you sure want to delete this client?" 
      :currentItem="currentClient"
      @submit="refresh" 
      :endpoint="`/clients/${currentClient?.id}`" 
      @close="isDeleteDialogOpen = false" 
    />

    <!-- Add/Edit Client Drawer -->
    <AddDrawer 
      v-model:isDrawerOpen="openClientModal" 
      :currentClient="currentClient" 
      @submit="refresh"
      v-if="openClientModal" 
      :clients="dataItems" 
    />
  </div>
</template>

<style scoped>
.text-link {
  color: rgba(var(--v-theme-primary), var(--v-high-emphasis-opacity));
  text-decoration: underline;
}
</style>
