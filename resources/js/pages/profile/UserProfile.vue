<script setup>
import { panelDetails } from "@layouts/stores/panel";
import { storeToRefs } from "pinia";
import { useRouter } from "vue-router";
import { PerfectScrollbar } from "vue3-perfect-scrollbar";

const router = useRouter();
const user = useCookie("userData");
// Store
const panelStore = panelDetails();
const { getUserDetails, getUserPermission } = panelStore;
const { userInfo } = storeToRefs(panelStore);

// Wait until user info is loaded before usage
await getUserDetails();
getUserPermission();

// Logout
const logout = async () => {
  try {
    await $api(`/logout`, {
      onResponseError({ response }) {
        errors.value = response._data.errors;
      },
    });
    useCookie("userAbilityRules").value = null;
    useCookie("accessToken").value = null;
    user.value = null;
  } catch (err) {
    console.error(err);
    useCookie("userAbilityRules").value = null;
    useCookie("accessToken").value = null;
  }
  router.replace("/login");
};

const userProfileList = [
  { type: "divider" },
  {
    type: "navItem",
    icon: "tabler-user",
    title: "Profile",
    to: {
      name: "profile-id",
      params: { id: user.value.uuid },
    },
  },
];
</script>
<template>
  <VBadge
    v-if="user"
    dot
    bordered
    location="bottom right"
    offset-x="1"
    offset-y="2"
    color="success"
  >
    <VAvatar
      size="38"
      class="cursor-pointer"
      :color="!(user && user.avatar) ? 'primary' : undefined"
      :variant="!(user && user.avatar) ? 'tonal' : undefined"
    >
      <VImg v-if="user && user.avatar" :src="user.avatar" />
      <VIcon v-else icon="tabler-user" />

      <!-- SECTION Menu -->
      <VMenu activator="parent" width="240" location="bottom end" offset="12px">
        <VList>
          <VListItem>
            <div class="d-flex gap-2 align-center">
              <VListItemAction>
                <VBadge
                  dot
                  location="bottom right"
                  offset-x="3"
                  offset-y="3"
                  color="success"
                  bordered
                >
                  <VAvatar
                    :color="!(user && user.avatar) ? 'primary' : undefined"
                    :variant="!(user && user.avatar) ? 'tonal' : undefined"
                  >
                    <VImg v-if="user && user.avatar" :src="user.avatar" />
                    <VIcon v-else icon="tabler-user" />
                  </VAvatar>
                </VBadge>
              </VListItemAction>

              <div>
                <h6 class="text-h6 font-weight-medium">
                  {{ user.name || user.user_name }}
                </h6>
                <VListItemSubtitle class="text-capitalize text-disabled">
                  <template v-if="userInfo?.roles?.length">
                    <div v-for="role in userInfo.roles" :key="role.id">
                      {{ role.name }}
                    </div>
                  </template>
                  <template v-else-if="user?.roles?.length">
                    <div v-for="role in user.roles" :key="role.id">
                      {{ role.name }}
                    </div>
                  </template>
                </VListItemSubtitle>
              </div>
            </div>
          </VListItem>

          <PerfectScrollbar :options="{ wheelPropagation: false }">
            <template v-for="item in userProfileList" :key="item.title">
              <VListItem v-if="item.type === 'navItem'" :to="item.to">
                <template #prepend>
                  <VIcon :icon="item.icon" size="22" />
                </template>

                <VListItemTitle>{{ item.title }}</VListItemTitle>

                <template v-if="item.badgeProps" #append>
                  <VBadge rounded="sm" class="me-3" v-bind="item.badgeProps" />
                </template>
              </VListItem>

              <VDivider v-else class="my-2" />
            </template>

            <div class="px-4 py-2">
              <VBtn
                block
                size="small"
                color="error"
                append-icon="tabler-logout"
                @click="logout"
              >
                Logout
              </VBtn>
            </div>
          </PerfectScrollbar>
        </VList>
      </VMenu>
      <!-- !SECTION -->
    </VAvatar>
  </VBadge>
</template>
