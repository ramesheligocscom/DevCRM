<template>
  <IconBtn id="notification-btn">
    <VTooltip bottom>
      <template v-slot:activator="{ on, attrs }">
        <VBadge v-bind="on" color="error" :content="unreadCount" offset-x="2" offset-y="3">
          <VIcon size="24" icon="tabler-bell" />
        </VBadge>
      </template>
      <span>Notifications</span>
    </VTooltip>

    <VMenu ref="notificationMenu" activator="parent" min-width="300px" location="bottom end" offset="8">
      <VCard class="d-flex flex-column">
        <!-- Header -->
        <VCardItem class="notification-section">
          <VCardTitle class="text-h6">Notifications</VCardTitle>

          <template #append>
           <VBtn v-show="false" @click="testMessageSendPusher()">Pusher</VBtn>
            <VChip v-show="unreadCount > 0" size="small" color="primary" class="me-2">
              {{ unreadCount }} New
            </VChip>
            <IconBtn v-show="notification_list.length" size="34"
              @click="markAllReadOrUnread">
              <VIcon size="20" color="high-emphasis" :icon="unreadCount > 0 ? 'tabler-mail' : 'tabler-mail-opened'" />
              <VTooltip activator="parent" location="start">
                {{ unreadCount == 0 ? 'Mark all as unread' : 'Mark all as read' }}
              </VTooltip>
            </IconBtn>
          </template>
        </VCardItem>

        <VDivider />

        <!-- Notifications List -->
        <PerfectScrollbar :options="{ wheelPropagation: false }" style="max-block-size: 380px;">
          <VList class="notification-list rounded-0 py-0">
            <template v-for="(notification, index) in notification_list" :key="notification.id">
              <VDivider v-if="index > 0" />

              <VListItem link lines="one" min-height="60" class="list-item-hover-class" @click="goToPage(notification)">
                <div class="d-flex align-start gap-3">
                  <VAvatar size="40" :image="notification.img || undefined" :icon="notification.icon || undefined"
                    :variant="notification.img ? undefined : 'tonal'">
                    <span v-if="notification.creator">{{ avatarText(notification.creator.name) }}</span>
                  </VAvatar>
                  <div>
                    <VListItemTitle class="text-sm font-weight-medium">{{ notification.title }}</VListItemTitle>
                    <VListItemSubtitle class="text-body-2">
                      Created By: {{ notification.creator?.name ?? "Unknown" }}
                    </VListItemSubtitle>
                    <VListItemSubtitle class="text-sm text-disabled">
                      {{ timeAgo(notification.created_at) }}
                    </VListItemSubtitle>
                  </div>
                  <VSpacer />
                  <VIcon size="10" icon="tabler-circle-filled"
                    :color="(notification.read && !notification.read.is_read) || !notification.read ? 'primary' : '#a8aaae'"
                    class="mb-2" @click.stop="toggleReadUnread(notification.read, notification.id)" />
                  <VIcon v-if="$can('notification', 'delete')" size="20" icon="tabler-x" class="visible-in-hover"
                    @click="removeMoveNotification(notification.id)" />
                </div>
              </VListItem>
            </template>

            <VListItem v-show="!notification_list.length" class="text-center text-medium-emphasis"
              style="block-size: 56px;">
              <VListItemTitle>No Notification Found!</VListItemTitle>
            </VListItem>
          </VList>
        </PerfectScrollbar>

        <VDivider />

        <!-- Footer -->
        <VCardText v-show="(notification_list.length || unreadCount > 0) && $can('notification', 'view')" class="pa-4">
          <RouterLink v-if="route.name != 'notifications'" to="/notifications">
            <VBtn block size="small"> View All Notifications </VBtn>
          </RouterLink>
        </VCardText>
        <RouterLink v-if="route.name != 'notifications'" to="/notifications">
            <VBtn block size="small"> View All Notifications </VBtn>
          </RouterLink>
      </VCard>
    </VMenu>
  </IconBtn>
</template>

<script setup>
import moment from 'moment';
import { getCurrentInstance, onMounted } from "vue";
import { useRoute, useRouter } from 'vue-router';
import { PerfectScrollbar } from 'vue3-perfect-scrollbar';
import { toast } from "vue3-toastify";
import echo from "../../echo.js";

const instance = getCurrentInstance();
const $can = instance?.proxy?.$can;

const router = useRouter();
const route = useRoute();

const unreadCount = ref(0);
const loading = ref(false);
const notificationMenu = ref(null);

const notification_count = ref(null);
const getNotificationCount = async () => {
  try {
    const response = await $api('/notification-count', { method: 'POST', body: {} });
    notification_count.value = response.data ?? null;
    unreadCount.value = notification_count.value ? notification_count.value.un_read : 0;
  } catch (error) {
    let errorMessage = error._data.message ?? "Error occurred while processing the request.";
    toast.error(errorMessage);
  }
};

const notification_list = ref([]);
const getLatestFiveNotificationList = async () => {
  try {
    loading.value = true;
    const response = await $api('/latest-five-notification-list', { method: 'POST', body: {} });
    notification_list.value = response.data ?? [];
  } catch (error) {
    let errorMessage = error?._data.message ?? "Error occurred while processing the request.";
    toast.error(errorMessage);
  } finally {
    loading.value = false;
  }
};

const timeAgo = (date) => {
  return moment(date).fromNow(); // Formats it like "2 hours ago"
};

const markAllReadOrUnread = async () => {
  try {
    let is_read = true;
    if (unreadCount.value == 0) {
      is_read = false;
    }
    const response = await $api('/mark-all-read-or-un-read', { method: 'POST', body: { is_read: is_read } });
    toast.success(response.message);
    await getNotificationCount(),
      await getLatestFiveNotificationList();
  } catch (error) {
    let errorMessage = error._data.message ?? "Error occurred while processing the request.";
    toast.error(errorMessage);
  }
}

const goToPage = async (notification) => {
  try {
    if (!notification.read) {
      await isReadNotification(notification.id, true);
    }
    // if (notification.module_type == "Lead Created in SRM" && $can('leads', 'show-site-risk-management')) {
    //   router.push(`/admin/site-risk-managment/view/${notification.module_id}`);
    // } else if ((notification.module_type == "Lead Assign User" || notification.module_type == "New Create Lead" || notification.module_type == "Lead Status") && $can('leads', 'view-lead')) {
    //   router.push(`/admin/leads`);
    // } else if (notification.module_type == "Follow Up SRM" && $can('leads', 'show-lead') && notification.lead) {
    //   router.push(`/admin/leads/view/${notification.module_id}`);
    // } else if ((notification.module_type == "Create Client" || notification.module_type == "Client Assign User") && $can('client', 'view-client')) {
    //   router.push(`/admin/clients`);
    // }else if (notification.module_type == "Client Visit Technician" && $can('client', 'show-client')) {
    //   router.push(`/admin/clients/view/${notification.module_id}`);
    // }else if ((notification.module_type == "Create Quotation" || notification.module_type == "Quotation Status") && $can('quotation', 'view-quotation')) {
    //   router.push(`/admin/quotations`);
    // } else if (notification.module_type == "Create Contract"  && $can('contract', 'view-contract')) {
    //   router.push(`/admin/contracts`);
    // } else if (notification.module_type == "Create Invoice"  && $can('invoice', 'view-invoice')) {
    //   router.push(`/admin/invoices-list`);
    // } else if ((notification.module_type == "Scheduling Assign User" || notification.module_type == "Schedule Date Change" || notification.module_type == "Schedule Status Update") && $can('schedule', 'view-schedule')) {
    //   router.push(`/admin/contract-schedulings`);
    // } 
    
  } catch (error) {
    let errorMessage = error._data.message ?? "Error occurred while processing the request.";
    toast.error(errorMessage);
  }
}

const toggleReadUnread = async (read, notification_id) => {
  if (read && read.is_read) {
    await isReadNotification(notification_id, false);
  } else {
    await isReadNotification(notification_id, true);
  }
}

const isReadNotification = async (notification_id, is_read) => {
  try {
    const response = await $api('/is-read-notification', { method: 'POST', body: { notification_id: notification_id, is_read: is_read } });
    toast.success(response.message);
    await getNotificationCount(),
      await getLatestFiveNotificationList();
  } catch (error) {
    let errorMessage = error._data.message ?? "Error occurred while processing the request.";
    toast.error(errorMessage);
  }
}

const testMessageSendPusher = async () => {
  try {
    const response = await $api('/test-message-send-pusher', { method: 'POST', body: {} });
    toast.success(response.message);
  } catch (error) {
    let errorMessage = error._data.message ?? "Error occurred while processing the request.";
    toast.error(errorMessage);
  }
}

const removeMoveNotification = async (notification_id) => {
  notification_list.value.forEach((item, index) => { if (notification_id === item.id) notification_list.value.splice(index, 1) });
}

onMounted(async () => {
  await getNotificationCount(),
  await getLatestFiveNotificationList();

  const userId = localStorage.getItem('user_id') ?? null;
  echo.channel(`notification-channel-${userId}`).listen(".new-notification", (data) => {
    console.log('notification-channel ' ,JSON.stringify(data));
    if (data.message) toast.info(data.message);
    getNotificationCount(); 
    getLatestFiveNotificationList();
  });
});
</script>

<style lang="scss">
.notification-section {
  padding-block: 0.75rem;
  padding-inline: 1rem;
}

.list-item-hover-class {
  .visible-in-hover {
    display: none;
  }

  &:hover {
    .visible-in-hover {
      display: block;
    }
  }
}

.notification-list.v-list {
  .v-list-item {
    border-radius: 0 !important;
    margin: 0 !important;
    padding-block: 0.75rem !important;
  }
}

// Badge Style Override for Notification Badge
.notification-badge {
  .v-badge__badge {
    padding: 0;
    block-size: 18px;
    min-inline-size: 18px;
  }
}
</style>
