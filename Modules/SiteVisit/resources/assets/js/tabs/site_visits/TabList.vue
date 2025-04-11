<script setup>
import "@vuepic/vue-datepicker/dist/main.css";
import { computed, getCurrentInstance, ref } from "vue";
import ConfirmDialog from '../../dialog/ConfirmDialog.vue';
import AddDrawer from './AddDrawer.vue';

const instance = getCurrentInstance();
const $can = instance?.proxy?.$can;

// State
const searchQuery = ref("");
const clients = ref([]);
const currentClient = ref(null);
const itemsPerPage = ref(10);
const page = ref(1);
const sortBy = ref();
const orderBy = ref();
const loading = ref(true);
const filteredClients = ref([]);
const clientCards = ref([]);
const statusFilter = ref("");
const openClientModal = ref(false);
const openClientFilterModal = ref(false);
const selectedClient = ref([]);
const action_bulk = ref(null);
const selectedCard = ref(0);
const currentLead = ref(null);
const isDeleteDialogOpen = ref(false)
// Table headers configuration
const tableHeaderSlug = ref('client-site-visit');
const headers = ref([]);
const getFilteredHeaderValue = async (headerList) => { headers.value = headerList; };

const bulkAction = ref([]);
const assignedToArray = ref([]);

const statusChips = ref([
  { text: "All", value: "" },
  { text: "Scheduled", value: "scheduled" },
  { text: "Completed", value: "completed" },
  { text: "Canceled", value: "canceled" },
  { text: "Rescheduled", value: "rescheduled" }
]);

const dropdownUserList = async () => {
  try {
    const response = await $api(`/dropdown-user-list`);
    bulkAction.value = [];
    const userList = response.data || [];
    if ($can('client', 'delete')) bulkAction.value.push({ id: "delete", name: "Delete" });
    if ($can('client', 'assign-to')) bulkAction.value = [...bulkAction.value, ...userList];

    assignedToArray.value = response.data;
  } catch (error) {
    console.error("dropdownUserList : " + error);
  }
};

// Computed properties
const searchedClients = computed(() => {
  if (!searchQuery.value) return filteredClients.value;

  const query = searchQuery.value.toLowerCase();
  return filteredClients.value.filter(client => {
    return (
      (client.name && client.name.toLowerCase().includes(query)) ||
      (client.email && client.email.toLowerCase().includes(query)) ||
      (client.contact_person && client.contact_person.toLowerCase().includes(query)) ||
      (client.phone && client.phone.toLowerCase().includes(query))
    );
  });
});

// Pagination
const pagination = ref({
  current_page: 1,
  per_page: 10,
  total: 0,
  last_page: 1,
});

const fetchClients = async () => {
  loading.value = true;

  try {
    const response = await $api(`/sitevisit?status=${statusFilter.value ?? ""}&per_page=${itemsPerPage.value}`);

    filteredClients.value = response.data;
    clients.value = response.data; // Store all clients for reference

    pagination.value = {
      current_page: response.meta.current_page,
      per_page: response.meta.per_page,
      total: response.meta.total,
      last_page: response.meta.last_page,
    };

    clientCards.value = [
      {
        title: response.meta.total ?? 0,
        text: "Total Site Visits",
        color: "primary",
        icon: "tabler-users",
      },
      {
        title: response.data.filter(client => client.status === 'scheduled').length ?? 0,
        text: "Scheduled",
        color: "info",
        icon: "tabler-calendar",
      },
      {
        title: response.data.filter(client => client.status === 'completed').length ?? 0,
        text: "Completed",
        color: "success",
        icon: "tabler-check",
      },
      {
        title: response.data.filter(client => client.status === 'canceled').length ?? 0,
        text: "Canceled",
        color: "error",
        icon: "tabler-x",
      },
    ];
  } catch (error) {
    console.error('Error fetching site visits:', error);
  } finally {
    loading.value = false;
  }
};

const editBranch = (item) => {
  currentClient.value = JSON.parse(JSON.stringify(item));
  openClientModal.value = true;
};

const resolveStatusVariant = (status) => {
  if (status === "Active") return { color: "success", text: "Active" };
  else if (status === "In Active") return { color: "error", text: "In Active" };
  else return { color: "success", text: "Active" };
};

const openDeleteDialog = (item) => {
  currentLead.value = JSON.parse(JSON.stringify(item));
  isDeleteDialogOpen.value = true;
}

const refresh = async (newData) => {
  // First fetch fresh data
  await fetchClients();
  
  // Close modals
  isDeleteDialogOpen.value = false;
  openClientModal.value = false;
  
  // If we have new data, update the local state immediately
  if (newData) {
    const index = filteredClients.value.findIndex(item => item.id === newData.id);
    if (index !== -1) {
      // Update existing item
      filteredClients.value[index] = { ...filteredClients.value[index], ...newData };
    } else {
      // Add new item to the beginning of the list
      filteredClients.value.unshift(newData);
    }
  }
};

const handleStatusChange = (newStatus) => {
  statusFilter.value = newStatus;
  fetchClients();
};

// Initial fetch
dropdownUserList();
fetchClients();
</script>

<template>
  <section v-if="$can('client', 'view')">
    <VCard class="mb-6">
      <div class="d-flex justify-lg-space-between" style="margin: 20px">
        <div>
          <div class="text-h5">Site Visit</div>

          <VChip v-for="(data, index) in SelectedFilterValue" :key="index" closable @click:close="removeFilter(index)"
            style="font-size: x-small; height: 25px" class="mr-2">
            {{ data }}
          </VChip>
        </div>

        <div class="d-flex gap-3">
        

          <AppTextField v-model="searchQuery" placeholder="Search"
            style="max-inline-size: 200px; min-inline-size: 200px" />


          <!-- Filter Header Btn FilterHeaderTableBtn -->
          <FilterHeaderTableBtn :slug="tableHeaderSlug" @filterHeaderValue="getFilteredHeaderValue" />
          <VBtn v-if="$can('client', 'create')" prepend-icon="tabler-plus"
            @click="openClientModal = true; currentClient = null" >
            Add New
            </VBtn>
        </div>
      </div>

      <VDivider />

      <!-- Loading spinner -->
      <BaseSpinner class="d-flex" v-if="loading" />

      <!-- Client Data Table -->
      <VDataTable v-else :items="searchedClients" :items-length="searchedClients.length"
        :headers="headers.filter((header) => header.checked)" class="text-no-wrap" @update:options="updateOptions"
        v-model:items-per-page="itemsPerPage" v-model:page="page" show-select v-model="selectedClient">
        
        <template #item.visit_time="{ item }">
          {{ item?.visit_time || '-' }}
        </template>
        
        <template #item.assignee_name="{ item }">
          {{ item.assignee_name || '-' }}
        </template>
        
        <template #item.status="{ item }">
          {{ item.status || '-' }}
        </template>
        
        <template #item.visit_notes="{ item }">
          {{ item.visit_notes || '-' }}
        </template>
        
        <template #item.created_at="{ item }">
          {{ item.created_at || '-' }}
        </template>
        
        <template #item.created_by="{ item }">
          {{ item.creator_name || '-' }}
        </template>


        <template #item.updated_at="{ item }">
          {{ item.updated_at || '-' }}
        </template>
        <template #item.updated_by="{ item }">
          {{ item.updater_name || '-' }}
        </template>
     

        <template #item.action="{ item }">
          <IconBtn @click="editBranch(item)" v-if="$can('client', 'edit')">
            <VIcon icon="tabler-pencil" />
          </IconBtn>


          <IconBtn v-if="$can('client', 'delete')" @click="openDeleteDialog(item)">
            <VIcon icon="tabler-trash" />
          </IconBtn>
        </template>

        <template #bottom>
          <Pagination :pagination="pagination" :itemsPerPage="itemsPerPage" @update:itemsPerPage="itemsPerPage = $event"
            @paginate="paginate" />
        </template>
      </VDataTable>
    </VCard>

    <!-- 👉 Confirm Dialog -->
    <ConfirmDialog v-model:isDialogVisible="isDeleteDialogOpen" confirm-title="Delete!"
      confirmation-question="Are you sure want to delete lead?" :currentItem="currentLead" @submit="refresh"
      :endpoint="`/sitevisit/${currentLead?.id}`" @close="isDeleteDialogOpen = false" />
    <!-- Client Add/Edit Drawer -->
    <AddDrawer v-model:isDrawerOpen="openClientModal" :currentClient="currentClient" @submit="refresh"
      @statusChange="handleStatusChange" v-if="openClientModal" :clients="clients" />
  </section>
</template>

<style scoped>
.industry_card {
  padding: 30px;
}

.custom-link {
  text-decoration: none;
  color: inherit;
}

.custom-link:hover {
  text-decoration: underline;
  color: var(--v-theme-primary);
}

.highlighted-card {
  border: 2px solid rgb(var(--v-theme-primary)) !important;
}
</style>
