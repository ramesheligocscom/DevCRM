
<template>
  <section>
    <VCard class="" title="Status">
      <VDivider />
            <VCardText>
                <div class="d-flex justify-space-between flex-wrap gap-y-4">
                    <AppTextField v-model="searchQuery" style="max-inline-size: 280px; min-inline-size: 280px;"
                        placeholder="Search Status" />
                    <div class="d-flex flex-row gap-4 align-center flex-wrap">
                        <VBtn v-if="$can('user', 'create')" prepend-icon="tabler-plus"
                            @click="isDialogVisible = !isDialogVisible; currentUser = null">
                            Add New
                        </VBtn>

                        <!-- Filter Header Btn FilterHeaderTableBtn -->
                        <FilterHeaderTableBtn :slug="tableHeaderSlug" @filterHeaderValue="getFilteredHeaderValue" />
                    </div>
                </div>
            </VCardText>
            <VDivider />

      <BaseSpinner class="d-flex" v-if="loading" />
      <VDataTableServer  v-else  v-model:items-per-page="pagination.per_page" :items="statusList"
          :items-length="statusList.length" :headers="headers.filter((header) => header.checked)" class="text-no-wrap" mobile-breakpoint="600"
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

    <AddEditStatusDrawer v-if="isDialogVisible"  @submit="pageStatusList" :currentInfo="currentInfo" v-model:isDialogVisible="isDialogVisible" />

    <!-- 👉 Delete Dialog -->
    <DeleteDialog v-model:isDialogVisible="isDeleteDialogOpen" confirm-title="Delete!"
    confirmation-question="Are you sure want to delete Status?" :currentItem="currentInfo" @submit="getUserLoginLogList" :action="'force_delete'"
    :endpoint="`/status/${currentInfo?.id}`" @close="isDeleteDialogOpen = false" />
  </section>
</template>

<script setup>
import "@vuepic/vue-datepicker/dist/main.css";
import { onMounted, ref } from "vue";
import AddEditStatusDrawer from "./AddEditStatusDrawer.vue";

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
const tabType = ref('All');

// Update table sort options
const updateTableSort = (options) => {
  sortBy.value = options.sortBy[0]?.key || '';
  orderBy.value = options.sortBy[0]?.order || '';
};

const editBranch = (item) => {
  currentInfo.value =  item;
  isDialogVisible.value = true;
};

const openDeleteDialog = (item) => {
    currentInfo.value = item;
    isDeleteDialogOpen.value = true;
}

const pageList = ref([]);
const fetchPageList = async () => { 
    try {
        const params = { type:'list', };
        const response = await $api('/settings/page', { params });
        console.log('fetchPageList ', response.data);
        pageList.value = response.data;
    } catch (error) {
        console.error('Error fetching status list:', error);
        toast.error(error?.response?.data?.message || 'Error fetching status list.');
    }  
 }

const pageStatusList = async () => {
  loading.value = true;
    try {
        const params = {
            search: searchQuery.value || '',
            type : tabType.value || 'All',
            page: pagination.value.current_page,
            sort_key: sortBy.value || '',
            sort_order: orderBy.value || '',
            per_page: pagination.value.per_page,
        };

        const response = await $api('/settings/status-list', { params });
        const { data, ...paginationData } = response.data;
        statusList.value = data ?? [];
        pagination.value = { ...paginationData };
        isDialogVisible.value = false;
    } catch (error) {
        console.error('Error fetching status list:', error);
        toast.error(error?.response?.data?.message || 'Error fetching status list.');
    } finally {
        loading.value = false;
    }
};

// Watchers to handle pagination updates dynamically
watch([() => pagination.value.current_page, () => pagination.value.per_page, searchQuery], () => {
  pageStatusList();
});

onMounted(() => { fetchPageList() , pageStatusList(); });
</script>
