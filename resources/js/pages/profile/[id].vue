<template>
  <div>
    <BaseSpinner class="d-flex" v-if="loading" />
    <VRow v-else-if="userData">
      <VCol cols="12" md="5" lg="4">
        <UserBioPanel :currentUser="userData" @submit="getUserList" />
      </VCol>

      <VCol cols="12" md="7" lg="8">
        <div class="d-flex justify-space-between">
          <VTabs v-model="userTab" class="v-tabs-pill">
            <VTab v-for="tab in tabs" :key="tab.icon">
              <VIcon :size="18" :icon="tab.icon" class="me-1" />
              <span>{{ tab.title }}</span>
            </VTab>
          </VTabs>
        </div>

        <VWindow
          v-model="userTab"
          class="mt-6 disable-tab-transition"
          :touch="false"
        >
          <VWindowItem>
            <UserTabSecurity />
          </VWindowItem>
        </VWindow>
      </VCol>
    </VRow>
    <div v-else>
      <VAlert type="error" variant="tonal">
        Invoice with ID {{ route.params.id }} not found!
      </VAlert>
    </div>
  </div>
</template>

<script setup>
import { getCurrentInstance, onMounted, ref } from "vue";
import { useRoute } from "vue-router";
import UserBioPanel from "../user/view/UserProfile.vue";
import UserTabSecurity from './UserTabSecurity.vue';

const instance = getCurrentInstance();
const $can = instance?.proxy?.$can;
const route = useRoute();

const userTab = ref(null);
const userData = ref();
const loading = ref(true);

const tabs = [
  // {
  //   icon: "tabler-users",
  //   title: "Account",
  // },
  {
    icon: "tabler-lock",
    title: "Security",
  },
];

const getUserData = async () => {
  try {
    loading.value = true;
    const response = await $api(`user/${route.params.id}`);
    if (response) {
      userData.value = response.data;
    }
  } catch (error) {
    console.error(error._data.message);
  } finally {
    loading.value = false;
  }
};

onMounted(async () => {
  await getUserData();
});
</script>
