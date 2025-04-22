<template>
  <section v-if="$can('notification', 'view')">
    <BaseSpinner class="d-flex" v-if="loading" />
    <VCard class="mb-6" title="Notification List" v-else>
      <template #append>
        <div class="d-flex justify-content-between w-100 gap-3">
          <div>
            <AppTextField
              v-model="searchQuery"
              placeholder="Search"
              style="max-inline-size: 180px; min-inline-size: 180px;"
            />
          </div>
          <div class="d-flex gap-3">
            <FilterHeaderTableBtn
              :slug="tableHeaderSlug"
              @filterHeaderValue="getFilteredHeaderValue"
            />
          </div>
        </div>
      </template>
      <VDivider />
      <!-- Section database -->
      <VDataTable
        :items="notificationList"
        :items-length="notificationList.length"
        :headers="headers.filter((header) => header.checked)"
        class="text-no-wrap"
        @update:options="updateOptions"
        :key="notificationList.length"
        v-model:items-per-page="pagination.per_page"
        v-model:page="pagination.current_page"
      >
        <!-- Client Or Lead Name  -->
        <template #item.module_id="{ item }">
          <div v-if="item.lead">
            <div v-if="$can('leads', 'show-lead')">
              {{ item.lead ? item.lead.name : "" }}
            </div>
            <div v-else>{{ item.lead ? item.lead.name : "" }}</div>
          </div>
          <div v-else-if="item.client">
            <div v-if="$can('client', 'show-client')">
              {{ item.client ? item.client.name : "" }}
            </div>
            <div v-else>{{ item.client ? item.client.name : "" }}</div>
          </div>
        </template>

        <!-- created by -->
        <template #item.user_id="{ item }">
          <div
            v-if="
              $can('users', 'show-user') || $can('employee', 'show-employee')
            "
          >
            {{ item.creator ? item.creator.name : "" }}
          </div>
          <div v-else>{{ item.creator ? item.creator.name : "" }}</div>
        </template>

        <!-- Created At -->
        <template #item.created_at="{ item }">
          {{ dayjs(item.created_at).format("DD/MM/YYYY hh:mm A") }}
        </template>

        <!-- Is Read -->
        <!-- Is Read -->
        <template #item.is_read="{ item }">
          <div
            @dblclick="
              isReadNotification(
                item.id,
                item.read && item.read.is_read == true ? false : true
              )
            "
          >
            <v-chip
              :color="item.read && item.read.is_read ? 'primary' : 'error'"
              small
            >
              {{ item.read && item.read.is_read ? "Read" : "Un-Read" }}
            </v-chip>
          </div>
        </template>

        <!-- Actions -->
        <template #item.action="{ item }">
          <IconBtn
            @click="
              isReadNotification(
                item.id,
                item.read && item.read.is_read ? item.read.is_read : true
              )
            "
          >
            <VIcon icon="tabler-eye" />
          </IconBtn>
          <!-- <IconBtn @click="openDeleteDialog(item)" v-if="$can('notification', 'delete')">
            <VIcon icon="tabler-trash" />
          </IconBtn> -->
        </template>

        <!-- pagination -->
        <template #bottom>
          <div
            class="d-flex align-center justify-space-between flex-wrap gap-3 px-6 py-3"
          >
            <p class="text-disabled mb-0">
              Showing {{ pagination.from }} to {{ pagination.to }} of
              {{ pagination.total }} entries
            </p>
            <div class="d-flex flex-wrap gap-2 align-center">
              <AppSelect
                :model-value="pagination.per_page"
                :items="[10, 25, 50, 100]"
                @update:model-value="
                  (val) => {
                    pagination.per_page = val;
                    getLeads();
                  }
                "
                style="inline-size: 6.25rem;"
              />
              <v-pagination
                v-model="pagination.current_page"
                :length="pagination.last_page"
                :total-visible="5"
              />
            </div>
          </div>
        </template>
      </VDataTable>
    </VCard>
    <!-- 👉 Delete Dialog -->
    <DeleteDialog
      v-model:isDialogVisible="isDeleteDialogOpen"
      confirm-title="Delete!"
      confirmation-question="Are you sure want to delete Notification?"
      :currentItem="currentInfo"
      @submit="getNotificationList"
      :action="'force_delete'"
      :endpoint="`/delete-login-log/${currentInfo?.id}`"
      @close="isDeleteDialogOpen = false"
    />
  </section>
</template>

<script setup>
import "@vuepic/vue-datepicker/dist/main.css";
import dayjs from "dayjs";
import { onMounted, ref } from "vue";
import { useRoute, useRouter } from "vue-router";
import { toast } from "vue3-toastify";

const router = useRouter();
const route = useRoute();

const loading = ref(true);
const searchQuery = ref("");
const sortBy = ref();
const orderBy = ref();
const isDeleteDialogOpen = ref(false);
const currentInfo = ref(null);
const tableHeaderSlug = ref("notification-list");

const headers = ref([]);
const getFilteredHeaderValue = async (headerList) => {
  headers.value = headerList;
};

const notificationList = ref([]);
const pagination = ref({
  current_page: 1,
  last_page: 1,
  total: 0,
  per_page: 10,
  from: 0,
  to: 0,
});

const updateOptions = (options) => {
  sortBy.value = options.sortBy[0]?.key;
  orderBy.value = options.sortBy[0]?.order;
};

const getNotificationList = async () => {
  try {
    loading.value = true;
    const response = await $api("/notification-list", {
      method: "POST",
      body: {
        search: searchQuery.value || "",
        sort_key: sortBy.value || "",
        sort_order: orderBy.value || "",
        page: pagination.value.current_page,
        per_page: pagination.value.per_page,
      },
    });
    notificationList.value = response.data.data;
    pagination.value = {
      current_page: response.data.current_page,
      last_page: response.data.last_page,
      total: response.data.total,
      per_page: response.data.per_page,
      from: response.data.from,
      to: response.data.to,
    };
    loading.value = false;
  } catch (error) {
    let errorMessage =
      error?._data.message ?? "Error occurred while processing the request.";
    toast.error(errorMessage);
  } finally {
    loading.value = false;
  }
};

const isReadNotification = async (notification_id, is_read) => {
  try {
    const response = await $api("/is-read-notification", {
      method: "POST",
      body: { notification_id: notification_id, is_read: is_read },
    });
    toast.success(response.message);
    await getNotificationList();
  } catch (error) {
    let errorMessage =
      error._data.message ?? "Error occurred while processing the request.";
    toast.error(errorMessage);
  }
};

const openDeleteDialog = (item) => {
  currentInfo.value = item;
  isDeleteDialogOpen.value = true;
};

// Watchers to handle pagination updates dynamically
watch(
  [
    () => pagination.value.current_page,
    () => pagination.value.per_page,
    searchQuery,
  ],
  () => {
    getNotificationList();
  }
);

onMounted(() => {
  getNotificationList();
});
</script>
