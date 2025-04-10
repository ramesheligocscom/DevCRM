<script setup>
import "@vuepic/vue-datepicker/dist/main.css";
import { onMounted, ref } from "vue";
import AddStatusDrawer from "./AddEditStatusDrawer.vue";

// Data table Headers
const tableHeaderSlug = ref('setting-status-list');
const headers = ref([]);
const getFilteredHeaderValue = async (headerList) => { headers.value = headerList; };

const loading = ref(true);
const searchQuery = ref("");
const statusList = ref([]);
const pagination = ref({ current_page: 1, last_page: 1, total: 0, per_page: 10, from: 0, to: 0 });
const sortBy = ref();
const orderBy = ref();
const isDialogVisible = ref(false);
const isDeleteDialogOpen = ref(false);
const currentInfo = ref(null);

// Update table sort options
const updateTableSort = (options) => {
  sortBy.value = options.sortBy[0]?.key || '';
  orderBy.value = options.sortBy[0]?.order || '';
};

const editBranch = (item) => {
  currentInfo.value =  item;
  openBranchModal.value = true;
};

const openDeleteDialog = (item) => {
    currentInfo.value = item;
    isDeleteDialogOpen.value = true;
}

const pageList = ref([]);
const fetchPageList = async () => { 
  // const res = await $api(`/page?type=list`); pageList.value = res.page; console.log(res.page);
 }

const pageStatusList = async () => {
  // loading.value = true;
  // const data = await $api(`/status-list?search=${searchQuery.value ?? ''}`);
  // console.log('pageStatusList ', data);
  // // filteredLeads.value = data;
  // loading.value = false;
  // // isDialogVisible.value = false;
};

// Watchers to handle pagination updates dynamically
watch([() => pagination.value.current_page, () => pagination.value.per_page, searchQuery], () => {
  pageStatusList();
});

onMounted(() => { fetchPageList , pageStatusList(); });
</script>

<template>
  <section>
    <VCard class="mb-6" title="Status">
      <template #append>
        <AppTextField v-model="searchQuery" placeholder="Search Status" style="max-inline-size: 280px; min-inline-size: 280px;" class="mr-3" />
        <VBtn rounded="" icon="tabler-plus" @click="(openBranchModal = !openBranchModal), (currentLead = null)" class=""></VBtn>
        
        <!-- Filter Header Btn FilterHeaderTableBtn -->
        <FilterHeaderTableBtn :slug="tableHeaderSlug" @filterHeaderValue="getFilteredHeaderValue" />
      </template>
      <VDivider />

      <BaseSpinner class="d-flex" v-if="loading" />
      <VDataTableServer  v-else  v-model:items-per-page="pagination.per_page" :items="statusList"
          :items-length="statusList.length" :headers="headers" class="text-no-wrap" mobile-breakpoint="600"
          @update:options="updateTableSort">
      
        <!-- Actions -->
        <template #item.actions="{ item }">
          <IconBtn @click="editBranch(item)">
            <VIcon icon="tabler-pencil" />
          </IconBtn>
          <IconBtn v-if="$can('status', 'delete')" @click="openDeleteDialog(item)"
              v-tooltip="'Delete Status'">
              <VIcon v-if="item.deleted_at" icon="tabler-trash" />
              <VIcon v-else icon="tabler-trash" />
            </IconBtn>
        </template>

        <!-- pagination -->
        <template #bottom>
          <div class="d-flex align-center justify-space-between flex-wrap gap-3 px-6 py-3">
              <p class="text-disabled mb-0"> Showing {{ pagination.from }} to {{ pagination.to }} of {{
                pagination.total }} entries </p>
              <div class="d-flex flex-wrap gap-2 align-center">
                <AppSelect :model-value="pagination.per_page" :items="[10, 25, 50, 100]"
                  @update:model-value="val => { pagination.per_page = val; getUserLoginLogList(); }"
                  style="inline-size: 6.25rem;" />

                <v-pagination v-model="pagination.current_page" :length="pagination.last_page" :total-visible="5" />
              </div>
            </div>
        </template>
      </VDataTableServer>
    </VCard>

    <AddStatusDrawer v-if="isDialogVisible"  @submit="pageStatusList" :currentInfo="currentInfo" v-model:isDialogVisible="isDialogVisible" />

    <!-- 👉 Delete Dialog -->
    <DeleteDialog v-model:isDialogVisible="isDeleteDialogOpen" confirm-title="Delete!"
    confirmation-question="Are you sure want to delete Status?" :currentItem="currentInfo" @submit="getUserLoginLogList" :action="'force_delete'"
    :endpoint="`/status/${currentInfo?.id}`" @close="isDeleteDialogOpen = false" />

<!-- <AddStatusDrawer v-model:isDrawerOpen="openBranchModal" :currentLead="currentLead" @submit="fetchPageStatus"
      v-if="openBranchModal" :branches="branches" /> -->
  </section>
</template>
