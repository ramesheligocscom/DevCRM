<script setup>
import "@vuepic/vue-datepicker/dist/main.css";
import dayjs from "dayjs";
import { debounce } from "lodash";
import Swal from "sweetalert2";
import { getCurrentInstance, ref } from "vue";
import AddClientFilterDrawer from './components/AddClientFilterDrawer.vue';
import ClientAddNewDrawer from './components/ClientAddNewDrawer.vue';

const instance = getCurrentInstance();
const $can = instance?.proxy?.$can;

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

// =================================================== Pagination and Search code =============================================================>
const pagination = ref({
  current_page: 1,
  per_page: 10,
  total: 0,
  last_page: 1,
});

watch(itemsPerPage, () => {
  fetchClients();
});

const paginate = (page) => {
  pagination.value.current_page = page;
  fetchClients();
};

const updateOptions = (options) => {
  sortBy.value = options.sortBy[0]?.key;
  orderBy.value = options.sortBy[0]?.order;
};


const debouncedFetchClients = debounce(() => {
  fetchClients();
}, 500);

watch(searchQuery, (newValue) => {
  if (newValue || newValue === "") {
    debouncedFetchClients();
  }
});

watch(() => pagination.value.current_page, () => {
  fetchClients();
});

// ======================================================== Client header ==================================================>
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

const toggleHeaderVisibility = (updatedHeaders) => {
  headers.value = [...updatedHeaders];
  localStorage.setItem("clientsTableHeaders", JSON.stringify(headers.value));
};
// ======================================================== Fetching Client  ==================================================>


const fetchClients = async () => {
  loading.value = true;

  try {
    const response = await $api(`/api/clients?search=${searchQuery.value ?? ""}&status=${statusFilter.value ?? ""}&page=${pagination.value.current_page}&sort_key=${sortBy.value ?? ""}&sort_order=${orderBy.value ?? ""}&per_page=${itemsPerPage.value}`);

    // Set clients data
    filteredClients.value = response.data;

    // Update pagination
    pagination.value = {
      current_page: response.meta.current_page,
      per_page: response.meta.per_page,
      total: response.meta.total,
      last_page: response.meta.last_page,
    };

    console.log('Fetched clients:', filteredClients.value);

    // Update cards - you'll need to adjust these based on actual API response
    clientCards.value = [
      {
        title: response.meta.total ?? 0, // Using total count from pagination
        text: "Total Clients",
        color: "primary",
        icon: "tabler-users",
      },
      // These would need to come from your API response
      // You might need to filter the data to get active/inactive counts
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
    openClientModal.value = false;
    openClientFilterModal.value = false;
  }
};
// ======================================================== Edit Client  ==================================================>
const editBranch = (item) => {
  currentClient.value = JSON.parse(JSON.stringify(item));
  openClientModal.value = true;
};


const resolveStatusVariant = (status) => {
  if (status === "Active") return { color: "success", text: "Active" };
  else if (status === "In Active") return { color: "error", text: "In Active" };
  else return { color: "success", text: "Active" };
};

// ======================================================== Delete Client  ==================================================>
const deleteClient = async (id) => {
  try {
    const { value: inputValue, isConfirmed } = await Swal.fire({
      title: `Delete Client Info?`,
      html: `<p style="color: #333; font-size: 14px; margin-top: -10px; margin-bottom: 15px;">
                 Are you sure you want to permanently delete <strong>record</strong>?
               </p>
               <label for="swal-input" >
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
    console.log("Delete response:", response);
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

fetchClients();

</script>

<template>
  <section>
    <!-- <section v-if="$can('client', 'view-client')"> -->

    <VRow class="mb-3">
      <!-- <VCol v-for="(item, index) in clientCards" :keys="index" cols="12" lg="4" md="3">
        <VCard :title="item.title" :subtitle="item.text" @click="filterdCardData(item.text, index)"
          :class="{ 'highlighted-card': selectedCard === index }">
          <template #append>
            <VAvatar variant="tonal" rounded="" :color="item.color" :icon="item.icon" />
          </template>
</VCard>
</VCol> -->
    </VRow>

    <VCard class="mb-6">
      <!-- <template #append> -->
      <div class="d-flex justify-lg-space-between" style="margin: 20px">
        <div>
          <div class="text-h5">Client</div>
          <VChip v-for="(data, index) in SelectedFilterValue" :key="index" closable @click:close="removeFilter(index)"
            style="font-size: x-small; height: 25px" class="mr-2">
            {{ data }}
          </VChip>
        </div>

        <div class="d-flex gap-3">
          <VSelect v-if="$can('client', 'assign-to') || $can('client', 'delete-client')" label="Bulk Action"
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
          <VBtn rounded="" icon="tabler-plus" @click="
            (openClientModal = !openClientModal), (currentClient = null)
            " class=""></VBtn>
        </div>
      </div>
      <!-- </template> -->

      <VDivider />

      <!-- SECTION datatable -->
      <BaseSpinner class="d-flex" v-if="loading" />

      <VDataTable :items="filteredClients" :items-length="filteredClients.length"
        :headers="headers.filter((header) => header.checked)" class="text-no-wrap" @update:options="updateOptions"
        :key="filteredClients.length" v-model:items-per-page="itemsPerPage" v-model:page="page" show-select
        v-model="selectedClient" v-else>
        <!---------- name ---------------->
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
                    <u>{{ (item.name) }}</u>
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

        <!---------- contact_person ---------------->
        <template #item.contact_person="{ item }">
          {{ item.contact_person || "Not Availlable" }}
        </template>

        <!---------- Phone ---------------->
        <template #item.phone="{ item }">
          {{ item.phone.substring(0, 5) + "-" + item.phone.substring(5) }}
        </template>

        <!---------- Client Assign ---------------->
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

        <!---------- Client Status ---------------->
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

        <!---------- Date Created ---------------->
        <template #item.created_at="{ item }">
          {{ dayjs(item.created_at).format("DD/MM/YYYY") }}
        </template>

        <!-------------- Actions -------------------->
        <template #item.actions="{ item }">
          <IconBtn @click="editBranch(item)">
            <VIcon icon="tabler-pencil" />
          </IconBtn>

          <Router-link v-if="$can('client', 'show-client')" :to="{
            name: 'admin-clients-view-id',
            params: { id: item.id },
          }">
            <VIcon color="secondary" icon="tabler-eye" />
          </Router-link>

          <IconBtn @click="deleteClient(item.id)">
            <VIcon icon="tabler-trash" />
          </IconBtn>
        </template>

        <!-- pagination -->
        <template #bottom>
          <Pagination :pagination="pagination" :itemsPerPage="itemsPerPage" @update:itemsPerPage="itemsPerPage = $event"
            @paginate="paginate" />
        </template>
      </VDataTable>
    </VCard>

    <ClientAddNewDrawer v-model:isDrawerOpen="openClientModal" :currentClient="currentClient" @submit="fetchClients"
      v-if="openClientModal" :clients="clients" />
    <AddClientFilterDrawer v-model:isDrawerOpen="openClientFilterModal" @updatebranches="updateFilteredBranches"
      v-if="openClientFilterModal" :clients="clients" @filterdValue="getFilteredValue" />
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
