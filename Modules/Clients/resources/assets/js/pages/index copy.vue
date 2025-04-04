<script setup>
import "@vuepic/vue-datepicker/dist/main.css";
import dayjs from "dayjs";
import { debounce } from "lodash";
import Swal from "sweetalert2";
import { getCurrentInstance, onMounted, ref } from "vue";
import { toast } from "vue3-toastify";
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

  const data = await $api(`/api/clients?search=${searchQuery.value ?? ""}&status=${statusFilter.value ?? ""
    }&page=${pagination.value.current_page}
    &sort_key=${sortBy.value ?? ""}&sort_order=${orderBy.value ?? ""
    }&per_page=${itemsPerPage.value}`);
  // console.log(data);
  filteredClients.value = data.client_data;
  pagination.value = {
    current_page: data.pagination.current_page,
    per_page: data.pagination.per_page,
    total: data.pagination.total,
    last_page: data.pagination.last_page,
  };

  clientCards.value = [
    {
      title: data.total_count ?? 0,
      text: "Total Clients",
      color: "primary",
      icon: "tabler-users",
    },
    {
      title: data.Active ?? 0,
      text: "Active",
      color: "success",
      icon: "tabler-users-plus",
    },
    {
      title: data.in_active ?? 0,
      text: "In Active",
      color: "error",
      icon: "tabler-user-up",
    },
  ];

  loading.value = false;
  openClientModal.value = false;
  openClientFilterModal.value = false;
};

// ======================================================== Edit Client  ==================================================>
const editBranch = (item) => {
  currentClient.value = JSON.parse(JSON.stringify(item));
  openClientModal.value = true;
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


// ======================================================== Dropdown list  ==================================================>
const bulkAction = ref([]);
const assignedToArray = ref([]);

const dropdownUserList = async () => {
  try {
    const response = await $api(`/dropdown-user-list`);
    bulkAction.value = [];
    const userList = response.data || [];
    if ($can('client', 'delete-client')) bulkAction.value.push({ id: "delete", name: "Delete" });
    if ($can('client', 'assign-to')) bulkAction.value = [...bulkAction.value, ...userList];

    assignedToArray.value = response.data;
  } catch (error) {
    console.error("dropdownUserList : " + error);
  }
};


onMounted(() => {
  dropdownUserList();
  fetchClients();
});

const resolveStatusVariant = (status) => {
  if (status === "Active") return { color: "success", text: "Active" };
  else if (status === "In Active") return { color: "error", text: "In Active" };
  else return { color: "success", text: "Active" };
};

// ======================================================== Array Data  ==================================================>
const status = ref([
  { value: "Active", title: "Active" },
  { value: "In Active", title: "In Active" },
]);

// ======================================================== Fetching Modal Data  ==================================================>
const updateFilteredBranches = (filteredbranches) => {
  console.log("Filtered Clients: ", filteredbranches);

  filteredClients.value = filteredbranches;

  // 🟢 Update Pagination Dynamically
  pagination.value.total = filteredClients.value.length;
  pagination.value.last_page = Math.ceil(
    filteredClients.value.length / itemsPerPage.value
  ); // Adjust last_page
  pagination.value.current_page = 1; // Reset to first page to avoid empty results

  const statusCounts = filteredClients.value.reduce(
    (acc, lead) => {
      acc.total++; // Count total Clients
      acc[lead.status] = (acc[lead.status] || 0) + 1; // Count each status
      return acc;
    },
    { total: 0, Active: 0, "In Active": 0 }
  );

  clientCards.value = [
    {
      title: statusCounts.total,
      text: "Total Clients",
      color: "primary",
      icon: "tabler-users",
    },
    {
      title: statusCounts["Active"],
      text: "Active",
      color: "Success",
      icon: "tabler-users-plus",
    },
    {
      title: statusCounts["In Active"],
      text: "In Active",
      color: "error",
      icon: "tabler-user-up",
    },
  ];

  console.log(filteredClients.value, pagination.value);
};

// ======================================================== Assign To update  ==================================================>
const onClientAssignUpdate = async (item, value) => {
  try {
    const { data } = await $api(`/api/clients/${item.id}`, {
      method: "PUT",
      body: { assigned_user: value },
    });

    if (data) {
      fetchClients();
      toast.success("Client assigned successfully.");
    } else {
      throw new Error(data?.message || "Failed to assign client.");
    }
  } catch (error) {
    toast.error(
      error.message || "An error occurred while assigning the client."
    );
  }
};

// ======================================================== Active & Inactive update  ==================================================>
const onStatusChange = async (item, value) => {
  try {
    const { data } = await $api(`/api/clients/${item.id}`, {
      method: "PUT",
      body: { status: value },
    });

    if (data) {
      fetchClients();
      leadStatusUpdate.value = ""; // Reset only on success
      toast.success("Client status updated successfully.");
    } else {
      throw new Error(data?.message || "Failed to update client status.");
    }
  } catch (error) {
    toast.error(error.message || "An error occurred while updating status.");
  }
};

const leadStatusUpdate = reactive(
  filteredClients.value.reduce((acc, filteredLead) => {
    acc[filteredLead.id] = filteredLead.status;
    return acc;
  }, {})
);

// ======================================================== Bulk Action  ==================================================>
watch(action_bulk, async (newVal) => {

  if (action_bulk.value != null) {
    const selectedIds = Object.values(selectedClient.value);
    if (selectedIds.length === 0) {
      console.warn("No items selected for deletion.");
      action_bulk.value = null;
      return;
    }

    if (action_bulk.value == "delete") {
      const { value: formValues, isConfirmed } = await Swal.fire({
        title: `Delete Clients Records?`,
        html: `<p style="color: #333; font-size: 14px; margin-top: -10px; margin-bottom: 15px;">
                 Are you sure you want to permanently delete <strong>${selectedIds.length} records</strong>?
               </p>
               <label for="swal-input-comment" >
                 Type in <strong>"DELETE"</strong> to confirm
               </label>
               <input id="swal-input-comment" class="swal2-input" placeholder="Type 'DELETE' to confirm" required />`,
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#d33',
        didOpen: () => {
          const input = document.getElementById('swal-input-comment');
          const confirmButton = Swal.getConfirmButton();

          confirmButton.disabled = true;

          input.addEventListener('input', () => {
            confirmButton.disabled = input.value.trim() !== 'DELETE';
          });
        },
        preConfirm: () => {
          const comment = document.getElementById('swal-input-comment').value;
          if (comment.trim() !== 'DELETE') {
            Swal.showValidationMessage("You must type 'DELETE' to proceed.");
            return false;
          }
          return { comment };
        }
      });

      if (isConfirmed) {
        await performBulkAction(selectedIds, filteredClients.value, newVal, 'Client', (deletedIds) => { console.log(`Deleted IDs: ${deletedIds}`); });
      } else {
        selectedClient.value = null;
        action_bulk.value = null;
        return;
      }
    } else {
      await performBulkAction(selectedIds, filteredClients.value, newVal, 'Client', (deletedIds) => { console.log(`Deleted IDs: ${deletedIds}`); });
    }

    selectedClient.value = null;
    action_bulk.value = null;
    fetchClients();
  }
});

// ================================================= Filtered data by cards ==========================================>
const filterdCardData = async (status, index) => {
  if (selectedCard.value === index) {
    selectedCard.value = null;
    await fetchClients();
  } else {
    selectedCard.value = index;

    if (status === "Total Clients") {
      statusFilter.value = 'All';
      await fetchClients();
    } else {
      statusFilter.value = status;
      const data = await $api(`/api/clients?search=${searchQuery.value ?? ""
        }&status=${statusFilter.value ?? ""}&page=${pagination.value.current_page}
            &sort_key=${sortBy.value ?? ""}&sort_order=${orderBy.value ?? ""
        }&per_page=${itemsPerPage.value}`);

      console.log(data.client_data);

      pagination.value = {
        current_page: data.pagination.current_page,
        per_page: data.pagination.per_page,
        total: data.pagination.total,
        last_page: data.pagination.last_page,
      };
      filteredClients.value = data.client_data;
      // Compute status counts dynamically from filteredClients
      const statusCounts = filteredClients.value.reduce(
        (acc, lead) => {
          acc.total++; // Count total Clients
          acc[lead.status] = (acc[lead.status] || 0) + 1; // Count each status
          return acc;
        },
        { total: 0, Active: 0, "In Active": 0 }
      );

      // Assign counts to the leadCards array
      clientCards.value = [
        {
          title: statusCounts.total,
          text: "Total Clients",
          color: "primary",
          icon: "tabler-users",
        },
        {
          title: statusCounts["Active"],
          text: "Active",
          color: "success",
          icon: "tabler-users-plus",
        },
        {
          title: statusCounts["In Active"],
          text: "In Active",
          color: "error",
          icon: "tabler-user-up",
        },
      ];
    }
  }
};

const clientName = (name) => {
  if (!name) return "N/A"; // Handle null or empty names

  const clientName = name.split(" - (")[0]; // Extract before " - ("
  return clientName.length > 10 ? clientName.slice(0, 10) + "..." : clientName;
};

const isDefaultChipVisible = ref(true);
const SelectedFilterValue = ref([]);

const getFilteredValue = (data) => {
  console.log(data);
  SelectedFilterValue.value = data[0];
  console.log(SelectedFilterValue.value);
};

const removeFilter = async (key) => {
  delete SelectedFilterValue.value[key]; // Remove the selected filter key
  console.log("Remaining Filters:", SelectedFilterValue.value);

  // Extract startDate and endDate if 'dateFilter' exists
  let startDate = "";
  let endDate = "";
  if (SelectedFilterValue.value.dateFilter) {
    const dateParts = SelectedFilterValue.value.dateFilter.split(" - ");
    startDate = dateParts[0]?.trim() || "";
    endDate = dateParts[1]?.trim() || "";
  }

  // Construct API query parameters dynamically
  const params = new URLSearchParams();
  if (SelectedFilterValue.value.statusFilter)
    params.append("status", SelectedFilterValue.value.statusFilter);
  if (startDate) params.append("startDate", startDate);
  if (endDate) params.append("endDate", endDate);

  try {
    const data = await $api(`/api/clients?${params.toString()}`);
    console.log(data);

    filteredClients.value = data.client_data;
    pagination.value = {
      current_page: data.pagination.current_page,
      per_page: data.pagination.per_page,
      total: data.pagination.total,
      last_page: data.pagination.last_page,
    };

    const statusCounts = filteredClients.value.reduce(
      (acc, lead) => {
        acc.total++; // Count total leads
        acc[lead.status] = (acc[lead.status] || 0) + 1; // Count each status
        return acc;
      },
      { total: 0, Active: 0, "In Active": 0 }
    );

    // Assign counts to the leadCards array
    clientCards.value = [
      {
        title: statusCounts.total,
        text: "Total Clients",
        color: "primary",
        icon: "tabler-users",
      },
      {
        title: statusCounts["Active"],
        text: "Active",
        color: "success",
        icon: "tabler-users-plus",
      },
      {
        title: statusCounts["In Active"],
        text: "In Active",
        color: "error",
        icon: "tabler-user-up",
      },
    ];
  } catch (error) {
    console.error("API Error:", error);
  }
};



const exportLoader = ref(false);
const selectedType = ref("xlsx");
const getClientExportList = async () => {
  try {
    exportLoader.value = true;
    let startDate = null;
    let endDate = null;
    if (SelectedFilterValue.value.dateFilter) {
      const dateParts = SelectedFilterValue.value.dateFilter.split(" - ");
      startDate = dateParts[0]?.trim() || "";
      endDate = dateParts[1]?.trim() || "";
    }
    const response = await $api(`/client-export`,
      {
        params: {
          search: searchQuery.value || '',
          startDate: startDate ? startDate : '',
          endDate: endDate ? endDate : '',
          status: statusFilter.value || '',
          type: selectedType.value || 'xlsx',
        },
      });

    exportLoader.value = false;

    if (response.data && response.data.url) {
      // Fetch actual file content
      const fileResponse = await fetch(response.data.url);
      const fileBlob = await fileResponse.blob();

      // Create download link
      const link = document.createElement("a");
      link.href = URL.createObjectURL(fileBlob);
      link.setAttribute("download", response.data.file_name);
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    } else {
      console.error("Download URL not found in response");
    }
  } catch (error) {
    exportLoader.value = false;
    let errorMessage = error._data.message ?? "Error occurred while processing the request.";
    toast.error(errorMessage);
  }
};
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
                      <u>{{ clientName(item.name) }}</u>
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
                    <u>{{ clientName(item.name) }}</u>
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
          <IconBtn v-if="$can('client', 'edit-client')" @click="editBranch(item)">
            <VIcon icon="tabler-pencil" />
          </IconBtn>

          <Router-link v-if="$can('client', 'show-client')" :to="{
            name: 'admin-clients-view-id',
            params: { id: item.id },
          }">
            <VIcon color="secondary" icon="tabler-eye" />
          </Router-link>

          <IconBtn v-if="$can('client', 'delete-client')" @click="deleteClient(item.id)">
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
