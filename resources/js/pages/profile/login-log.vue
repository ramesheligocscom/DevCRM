<template>
  <section v-if="$can('loginLog', 'view')">
    <VCard title="Login Log List" class="mb-6">
      <template #append>
      </template>
      <VDivider />
      <VCardText>
        <div class="app-user-search-filter d-flex justify-space-between align-center flex-wrap gap-4">
          <div class="d-flex align-center gap-1">
            <AppTextField style="inline-size: 15.625rem;" v-model="searchQuery" placeholder="Search Login Log" />
            <VBtn class="search-icon-btn">
              <VIcon icon="tabler-search" />
            </VBtn>
          </div>
          <!-- Filter Header Btn FilterHeaderTableBtn -->
          <FilterHeaderTableBtn :slug="tableHeaderSlug" @filterHeaderValue="getFilteredHeaderValue" />
        </div>
      </VCardText>
      <VDivider />

      <BaseSpinner class="d-flex" v-if="loading" />
      <VCardText v-else class="px-0">
        <VDataTableServer v-model:items-per-page="pagination.per_page" :items="userLoginLogList"
          :items-length="userLoginLogList.length" :headers="headers" class="text-no-wrap" mobile-breakpoint="600"
          @update:options="updateTableSort">
          <!-- show-select -->
          <template #item.name="{ item }">
            <div class="d-flex align-center gap-x-4" v-if="item.user">
              <VAvatar size="34" :variant="!item.user.avatar ? 'tonal' : undefined">
                <VImg v-if="item.user && item.user.avatar" :src="item.user.avatar" />
                <span v-else>{{ item.user.name.charAt(0) }}</span>
              </VAvatar>
              <div class="d-flex flex-column">
                <h6 class="text-base">
                  {{ item.user.name }}
                </h6>
                <div class="text-sm">{{ item.user.phone }}</div>
              </div>
            </div>
            <div class="d-flex align-center gap-x-4" v-else>
              <h6 class="text-base">No Name</h6>
            </div>
          </template>

          <!-- Email -->
          <template #item.logged_at="{ item }">
            {{ dayjs(item.logged_at).format("DD/MM/YYYY hh:mm A") }}
          </template>

          <!-- Action -->
          <template #item.actions="{ item }">
            <!-- Delete Button -->
            <IconBtn v-if="$can('loginLog', 'delete')" @click="openDeleteDialog(item)"
              v-tooltip="'Delete Login Log'">
              <VIcon v-if="item.deleted_at" icon="tabler-database-x" />
              <VIcon v-else icon="tabler-trash" />
            </IconBtn>
          </template>

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
      </VCardText>
    </VCard>
            <!-- 👉 Delete Dialog -->
            <DeleteDialog v-model:isDialogVisible="isDeleteDialogOpen" confirm-title="Delete!"
            confirmation-question="Are you sure want to delete Login Log?" :currentItem="currentInfo" @submit="getUserLoginLogList" :action="'force_delete'"
            :endpoint="`/delete-notification/${currentInfo?.id}`" @close="isDeleteDialogOpen = false" />
  </section>
</template>
<script setup>
import dayjs from "dayjs";
import { onMounted, ref, watch } from 'vue';
import { toast } from "vue3-toastify";
import { VCardText } from 'vuetify/lib/components/index.mjs';

const searchQuery = ref('');
const loading = ref(true);

const userLoginLogList = ref([]);
const pagination = ref({ current_page: 1, last_page: 1, total: 0, per_page: 10, from: 0, to: 0 });
const sortBy = ref();
const orderBy = ref();
const isDeleteDialogOpen = ref(false)
const currentInfo = ref(null);

// Data table Headers
const tableHeaderSlug = ref('login-log-list');
const headers = ref([]);
const getFilteredHeaderValue = async (headerList) => { headers.value = headerList; };

onMounted(() => { getUserLoginLogList(); });

// Update table sort options
const updateTableSort = (options) => {
  sortBy.value = options.sortBy[0]?.key || '';
  orderBy.value = options.sortBy[0]?.order || '';
};

// Watchers to handle pagination updates dynamically
watch([() => pagination.value.current_page, () => pagination.value.per_page, searchQuery], () => {
  getUserLoginLogList();
});

const getUserLoginLogList = async () => {
  loading.value = true;
  try {
    const params = {
      search: searchQuery.value || '',
      page: pagination.value.current_page,
      sort_key: sortBy.value || '',
      sort_order: orderBy.value || '',
      per_page: pagination.value.per_page,
    };

    const response = await $api('/user-login-logs', { params });
    const { data, ...paginationData } = response.data;
    userLoginLogList.value = data;
    pagination.value = { ...paginationData };
  } catch (error) {
    console.error('Error fetching user list:', error);
    toast.error(error?.response?.data?.message || 'Error fetching user list.');
  } finally {
    loading.value = false;
  }
};

const openDeleteDialog = (item) => {
    currentInfo.value = item;
    isDeleteDialogOpen.value = true;
}
</script>
