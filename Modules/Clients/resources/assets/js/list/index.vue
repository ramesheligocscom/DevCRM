<script setup>
import "@vuepic/vue-datepicker/dist/main.css";
import dayjs from "dayjs";
import Swal from "sweetalert2";
import { computed, getCurrentInstance, ref } from "vue";
import AddDrawer from '../add/AddDrawer.vue';

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
const statusFilter = ref("active");
const openClientModal = ref(false);
const openClientFilterModal = ref(false);
const selectedClient = ref([]);
const action_bulk = ref(null);
const selectedCard = ref(0);

// Table headers configuration
const defaultHeaders = [
  {
    title: "Name",
    key: "name",
    minWidth: "140px",
    checked: true,
  },
  {
    title: "Contact Person",
    key: "contact_person",
    checked: true,
  },
  {
    title: "Phone No.",
    key: "phone",
    checked: true,
  },
  {
    title: "Assign To",
    key: "assigned_to",
    checked: true,
  },
  {
    title: "Status",
    key: "status",
    checked: true,
  },
  {
    title: "Date",
    key: "created_at",
    checked: true,
  },
  {
    title: "Actions",
    key: "actions",
    checked: true,
    sortable: false,
  },
];

const headers = ref(JSON.parse(localStorage.getItem("clientsTableHeaders")) || defaultHeaders);

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

// Methods
const toggleHeaderVisibility = (updatedHeaders) => {
  headers.value = [...updatedHeaders];
  localStorage.setItem("clientsTableHeaders", JSON.stringify(headers.value));
};

const fetchClients = async () => {
  loading.value = true;

  try {
    const response = await $api(`/api/clients?status=${statusFilter.value ?? ""}&per_page=${itemsPerPage.value}`);

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
        text: "Total Clients",
        color: "primary",
        icon: "tabler-users",
      },
      {
        title: response.data.filter(client => client.status === 'active').length ?? 0,
        text: "Active",
        color: "success",
        icon: "tabler-users-plus",
      },
      {
        title: response.data.filter(client => client.status !== 'active').length ?? 0,
        text: "In Active",
        color: "error",
        icon: "tabler-user-up",
      },
    ];
  } catch (error) {
    console.error('Error fetching clients:', error);
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

const deleteClient = async (id) => {
  try {
    const { value: inputValue, isConfirmed } = await Swal.fire({
      title: `Delete Client Info?`,
      html: `<p style="color: #333; font-size: 14px; margin-top: -10px; margin-bottom: 15px;">
               Are you sure you want to permanently delete <strong>record</strong>?
             </p>
             <label for="swal-input">
               Type in <strong>"DELETE"</strong> to confirm
             </label>
             <input id="swal-input" class="swal2-input" placeholder="Type 'DELETE' to confirm" required />`,
      focusConfirm: false,
      showCancelButton: true,
      confirmButtonText: 'Delete',
      cancelButtonText: 'Cancel',
      confirmButtonColor: '#d33',
      didOpen: () => {
        const input = document.getElementById('swal-input');
        const confirmButton = Swal.getConfirmButton();
        confirmButton.disabled = true;

        input.addEventListener('input', () => {
          confirmButton.disabled = input.value.trim() !== 'DELETE';
        });
      },
      preConfirm: () => {
        const input = document.getElementById('swal-input').value.trim();
        if (input !== 'DELETE') {
          Swal.showValidationMessage("You must type 'DELETE' to proceed.");
          return false;
        }
        return { delete_text: input };
      }
    });

    if (!isConfirmed) return;

    const response = await $api(`/api/clients/${id}`, { method: "DELETE" });
    await Swal.fire({
      title: response.message || "Deleted!",
      text: "Client has been deleted.",
      icon: "success",
    });

    fetchClients();
  } catch (error) {
    console.error("Error deleting Client:", error);
    Swal.fire({
      title: "Error",
      text: "An error occurred while deleting the Client.",
      icon: "error",
    });
  }
};

// Initial fetch
fetchClients();
</script>

<template>
  <section>
    <VCard class="mb-6">
      <div class="d-flex justify-lg-space-between" style="margin: 20px">
        <div>
          <div class="text-h5">Client</div>
          <VChip v-for="(data, index) in SelectedFilterValue" :key="index" closable @click:close="removeFilter(index)"
            style="font-size: x-small; height: 25px" class="mr-2">
            {{ data }}
          </VChip>
        </div>

        <div class="d-flex gap-3">
          <VSelect v-if="$can('client', 'assign-to') || $can('client', 'delete')" label="Bulk Action"
            v-model="action_bulk" :items="bulkAction" item-title="name" item-value="id"
            style="max-inline-size: 200px; min-inline-size: 200px; height: 39px"></VSelect>

          <AppTextField v-model="searchQuery" placeholder="Search"
            style="max-inline-size: 200px; min-inline-size: 200px" />

          <VBtn class="search-icon-btn" @click="openClientFilterModal = !openClientFilterModal">
            <VIcon icon="tabler-filter" />
          </VBtn>

          <VBtn v-if="$can('client', 'export-list')" @click="getClientExportList()" :loading="exportLoader"
            :disabled="exportLoader">
            <VIcon icon="tabler-file-spreadsheet" />
            <VTooltip activator="parent" location="top">Export Client Data</VTooltip>
          </VBtn>

          <FilterBtn :menu-list="headers" @update:menuList="toggleHeaderVisibility" />
          <VBtn rounded icon="tabler-plus" @click="openClientModal = true; currentClient = null" />
        </div>
      </div>

      <VDivider />

      <!-- Loading spinner -->
      <BaseSpinner class="d-flex" v-if="loading" />

      <!-- Client Data Table -->
      <VDataTable v-else :items="searchedClients" :items-length="searchedClients.length"
        :headers="headers.filter((header) => header.checked)" class="text-no-wrap" @update:options="updateOptions"
        v-model:items-per-page="itemsPerPage" v-model:page="page" show-select v-model="selectedClient">
        <!-- Name Column -->
        <template #item.name="{ item }">
          <v-tooltip location="top">
            <template v-slot:activator="{ props }">
              <RouterLink v-if="$can('client', 'show-client')" :to="`/admin/api/clients/view/${item.id}`"
                class="custom-link" v-bind="props">
                <VList class="card-list">
                  <VListItem>
                    <VListItemTitle class="font-weight-medium me-4">
                      <u>{{ item.name }}</u>
                    </VListItemTitle>
                    <VListItemSubtitle class="me-4">
                      {{ item.email }}
                    </VListItemSubtitle>
                  </VListItem>
                </VList>
              </RouterLink>
              <VList class="card-list" v-else>
                <VListItem>
                  <VListItemTitle class="font-weight-medium me-4">
                    <u>{{ item.name }}</u>
                  </VListItemTitle>
                  <VListItemSubtitle class="me-4">
                    {{ item.email }}
                  </VListItemSubtitle>
                </VListItem>
              </VList>
            </template>
            <span>{{ item.name.split(" - (")[0] }}</span>
          </v-tooltip>
        </template>

        <!-- Contact Person Column -->
        <template #item.contact_person="{ item }">
          {{ item.contact_person || "Not Available" }}
        </template>

        <!-- Phone Column -->
        <template #item.phone="{ item }">
          {{ item.phone ? item.phone.substring(0, 5) + "-" + item.phone.substring(5) : "" }}
        </template>

        <!-- Assign To Column -->
        <template #item.assigned_to="{ item }">
          <VSelect v-if="item.assigned_to == null && $can('client', 'assign-to')" :items="assignedToArray"
            v-model="item.assigned_to" item-title="name" item-value="id"
            @update:modelValue="(value) => onClientAssignUpdate(item, value)" label="Assigned To" />

          <span v-else class="font-weight-medium" size="small" @dblclick="item.assigned_to = null" style="
              justify-content: center !important;
              min-width: 90% !important;
            ">{{ item.assign_user ? item.assign_user.name : item.assigned_to }}
          </span>
        </template>

        <!-- Status Column -->
        <template #item.status="{ item }">
          <VSelect v-if="item.status == null && $can('client', 'status-update')" :items="status"
            v-model="leadStatusUpdate[item.id]" @update:modelValue="(value) => onStatusChange(item, value)"
            label="Update Status" />

          <VChip :color="resolveStatusVariant(item.status).color" class="font-weight-medium" size="small"
            @dblclick="item.status = null" v-else style="
              justify-content: center !important;
              min-width: 90% !important;
            ">
            {{ resolveStatusVariant(item.status).text }}
          </VChip>
        </template>

        <!-- Date Column -->
        <template #item.created_at="{ item }">
          {{ dayjs(item.created_at).format("DD/MM/YYYY") }}
        </template>

        <!-- Actions Column -->
        <template #item.actions="{ item }">
          <IconBtn @click="editBranch(item)">
            <VIcon icon="tabler-pencil" />x
          </IconBtn>

          <RouterLink :to="{
            name: 'clients-view',
            params: { id: item.id },
          }">

            <VIcon color="secondary" icon="tabler-eye" />
          </RouterLink>

          <IconBtn @click="deleteClient(item.id)">
            <VIcon icon="tabler-trash" />
          </IconBtn>
        </template>

        <!-- Pagination -->
        <template #bottom>
          <Pagination :pagination="pagination" :itemsPerPage="itemsPerPage" @update:itemsPerPage="itemsPerPage = $event"
            @paginate="paginate" />
        </template>
      </VDataTable>
    </VCard>

    <!-- Client Add/Edit Drawer -->
    <AddDrawer v-model:isDrawerOpen="openClientModal" :currentClient="currentClient" @submit="fetchClients"
      v-if="openClientModal" :clients="clients" />


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
