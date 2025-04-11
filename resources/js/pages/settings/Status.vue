<template>
  <section v-if="$can('status', 'view')">
    <VCard class="mb-6">
      <!-- dir="rtl" -->
      <div class="d-flex justify-lg-space-between align-center" style="margin: 20px;">
        <!-- Tabs on the Right in RTL -->
        <div>
          <div class="text-h5">
            <VTabs v-model="tabType" direction="horizontal" class="v-tabs-pill disable-tab-transition">
              <VTab v-for="page in pageList" :key="page">
                {{ page }}
              </VTab>
            </VTabs>
          </div>
        </div>

        <!-- Actions on the Left in RTL -->
        <div class="d-flex gap-3">
          <AppTextField v-model="searchQuery" style="max-inline-size: 280px; min-inline-size: 280px;"
            placeholder="Search " />

          <VBtn v-if="$can('status', 'create')" prepend-icon="tabler-plus"
            @click="isDialogVisible = !isDialogVisible; currentUser = null;">
            Add New
          </VBtn>

          <FilterHeaderTableBtn :slug="tableHeaderSlug" @filterHeaderValue="getFilteredHeaderValue" />
        </div>
      </div>

      <VDivider />

      <BaseSpinner class="d-flex" v-if="loading" />
      <VDataTableServer v-else v-model:items-per-page="pagination.per_page" :items="statusList"
        :items-length="statusList.length" :headers="headers.filter((header) => header.checked)" class="text-no-wrap"
        mobile-breakpoint="600" @update:options="updateTableSort">
        <!-- <template #item.status_color="{ item }">
          <div :style="{ backgroundColor: item.status_color }"
            style=" display: inline-block; border: 1px solid #ccc; border-radius: 50%; block-size: 16px; cursor: pointer;inline-size: 16px;"
            :title="`Click to copy: ${item.status_color}`" @click="copyColorCode(item.status_color)"></div>
        </template> -->

        <!-- Status -->
        <template #item.status="{ item }">
          <div v-if="editingStatusId === item.id && item.is_predefined == 1">
            <VSelect v-model="item.status" @update:modelValue="(value) => updateStatus(item)"
              @blur="editingStatusId = null" :items="statusOptions" item-title="text" item-value="value">
            </VSelect>
          </div>
          <div v-else @dblclick="editingStatusId = item.id">
            <span :class="item.position > 0 ? 'text-success' : 'text-error'" style="cursor: pointer;">
              {{ item.position > 0 ? "Active" : "In-Active" }}
            </span>
          </div>
        </template>

        <!-- Color -->
        <template #item.status_color="{ item }">
          <div v-if="editingColorId === item.id">
            <VTextField type="color" v-model="item.status_color" style="border: none; background: transparent;"
              @input="(e) => updateStatusColor(item, e.target.value)" @blur="editingColorId = null" />
          </div>
          <div v-else :style="{ backgroundColor: item.status_color }" class="color-circle"
            :title="`Double-click to edit.`"
           @dblclick="editingColorId = item.id"></div>
           <!-- Click to copy: ${item.status_color}.  @click="copyColorCode(item.status_color)"  -->
        </template>

        <!-- Actions -->
        <template #item.actions="{ item }">
          <IconBtn v-if="$can('status', 'update') && item.is_predefined == 1" @click="editBranch(item)">
            <VIcon icon="tabler-pencil" />
          </IconBtn>
          <IconBtn v-if="$can('status', 'delete') && item.is_predefined == 1" @click="openDeleteDialog(item)" v-tooltip="'Delete Status'">
            <VIcon icon="tabler-trash" />
          </IconBtn>
        </template>

        <!-- pagination -->
        <template #bottom>
          <div class="d-flex align-center justify-space-between flex-wrap gap-3 px-6 py-3">
            <p class="text-disabled mb-0">
              Showing {{ pagination.from }} to {{ pagination.to }} of
              {{ pagination.total }} entries
            </p>
            <div class="d-flex flex-wrap gap-2 align-center">
              <AppSelect :model-value="pagination.per_page" :items="[10, 25, 50, 100]" @update:model-value="
                (val) => {
                  pagination.per_page = val;
                  pageStatusList();
                }
              " style="inline-size: 6.25rem;" />

              <v-pagination v-model="pagination.current_page" :length="pagination.last_page" :total-visible="5" />
            </div>
          </div>
        </template>
      </VDataTableServer>
    </VCard>

    <AddEditStatusDrawer v-if="isDialogVisible" @submit="pageStatusList" :currentInfo="currentInfo"
      v-model:isDialogVisible="isDialogVisible" />

    <!-- 👉 Delete Dialog -->
    <DeleteDialog v-model:isDialogVisible="isDeleteDialogOpen" confirm-title="Delete!"
      confirmation-question="Are you sure want to delete Status?" :currentItem="currentInfo"
      @submit="pageStatusList" :action="'force_delete'"
      :endpoint="`/settings/page-status-delete/${currentInfo?.id}`" @close="isDeleteDialogOpen = false" />
  </section>
</template>

<script setup>
import "@vuepic/vue-datepicker/dist/main.css";
import { onMounted, ref } from "vue";
import { toast } from "vue3-toastify";
import AddEditStatusDrawer from "./AddEditStatusDrawer.vue";
// Data table Headers
const tableHeaderSlug = ref("setting-status-list");
const headers = ref([]);
const getFilteredHeaderValue = async (headerList) => {
  headers.value = headerList;
};

const loading = ref(true);
const searchQuery = ref("");
const statusList = ref([]);
const pagination = ref({
  current_page: 1,
  last_page: 1,
  total: 0,
  per_page: 10,
  from: 0,
  to: 0,
});
const sortBy = ref();
const orderBy = ref();
const isDialogVisible = ref(false);
const isDeleteDialogOpen = ref(false);
const currentInfo = ref(null);
const tabType = ref(0);
const tabPage = ref("All");

const statusOptions = ref([
  { text: "Active", value: "1" },
  { text: "In-Active", value: "0" },
]);

const editingStatusId = ref(null);
const editingColorId = ref(null);

// Update table sort options
const updateTableSort = (options) => {
  sortBy.value = options.sortBy[0]?.key || "";
  orderBy.value = options.sortBy[0]?.order || "";
};

const editBranch = (item) => {
  currentInfo.value = item;
  isDialogVisible.value = true;
};

const openDeleteDialog = (item) => {
  currentInfo.value = item;
  isDeleteDialogOpen.value = true;
};

const pageList = ref([]);
const fetchPageList = async () => {
  try {
    const params = { type: "list" };
    const response = await $api("/settings/page", { params });
    // ✅ Prepend "All" to the page list
    pageList.value = ["All", ...response.data];
  } catch (error) {
    console.error("Error fetching status list:", error);
    toast.error(
      error?.response?.data?.message || "Error fetching status list."
    );
  }
};

const pageStatusList = async () => {
  loading.value = true;
  try {
    const params = {
      search: searchQuery.value || "",
      type: tabPage.value || "All",
      page: pagination.value.current_page,
      sort_key: sortBy.value || "",
      sort_order: orderBy.value || "",
      per_page: pagination.value.per_page,
    };

    const response = await $api("/settings/status-list", { params });
    const { data, ...paginationData } = response.data;
    statusList.value = data ?? [];
    pagination.value = { ...paginationData };
    isDialogVisible.value = false;
  } catch (error) {
    console.error("Error fetching status list:", error);
    toast.error(
      error?.response?.data?.message || "Error fetching status list."
    );
  } finally {
    loading.value = false;
  }
};

const copyColorCode = (color) => {
  return;
  navigator.clipboard
    .writeText(color)
    .then(() => toast.success(`Copied: ${color}`))
    .catch(() => toast.error("Failed to copy color"));
};

const updateStatus = async (item) => {
  try {
    const res = await $api(`/settings/status-update/${item.id}`, {
      method: 'POST',
      body: { status: parseInt(item.status) == 0 ? 0 : 1 }
    });
    toast.success(res?.message || "Status updated successfully");
  } catch (err) {
    toast.error(err?.response?.data?.message || "Error updating status");
  } finally {
    editingStatusId.value = null;
  }
};

const updateStatusColor = async (item, newColor) => {
  item.status_color = newColor;
  console.log("Updated color:", newColor);
  try {
    const res = await $api(`/settings/change-color-status/${item.id}`, {
      method: 'POST',
      body: { status_color: newColor }
    });
    toast.success(res?.message || "Color updated successfully");
    await pageStatusList();
  } catch (err) {
    toast.error(err?.response?.data?.message || "Error updating color");
  } finally {
    editingColorId.value = null;
  }
};

// Watchers to handle pagination updates dynamically
watch(
  [
    () => pagination.value.current_page,
    () => pagination.value.per_page,
    searchQuery,
  ],
  () => {
    pageStatusList();
  }
);

watch(tabType, (newVal) => {
  tabPage.value = pageList.value[newVal];
  pagination.value.current_page = 1;
  pageStatusList();
});

onMounted(() => {
  fetchPageList(), pageStatusList();
});
</script>
<style scoped>
.text-success {
  color: green;
  font-weight: 600;
}

.text-error {
  color: red;
  font-weight: 600;
}

.color-circle {
  display: inline-block;
  border: 1px solid #ccc;
  border-radius: 50%;
  block-size: 16px;
  cursor: pointer;
  inline-size: 16px;
}
</style>
