<script setup>
import { onMounted, ref } from "vue";
import SourceSettings from "@/views/apps/ecommerce/settings/SourceSettings.vue";
import SettingsStatus from "@/views/apps/ecommerce/settings/SettingsStatus.vue";
import SettingUnit from "@/views/apps/ecommerce/settings/SettingUnit.vue";
import SettingsGeneral from "@/views/apps/ecommerce/settings/SettingsGeneral.vue";
import SettingsLocations from "@/views/apps/ecommerce/settings/SettingsLocations.vue";
import SettingsPayment from "@/views/apps/ecommerce/settings/SettingsPayment.vue";
import WhatsappIntegrationSettings from "@/views/apps/ecommerce/settings/WhatsappIntegrationSettings.vue";
import SettingsAccountDetails from "@/views/apps/ecommerce/settings/SettingsAccountDetails.vue";
import SettingsNotifications from "@/views/apps/ecommerce/settings/SettingsNotifications.vue";

const tabsData = [
  {
    icon: "tabler-settings",
    title: "General Settings",
  },
  // {
  //   icon: "tabler-credit-card",
  //   title: "Payments",
  // },
  {
    icon: "tabler-list-details",
    title: "Status Details",
  },
];

const activeTab = ref(null);
const setting = ref([]);
const errors = ref([]);
const states = ref([]);

</script>

<template>
  <VRow>
    <VCol cols="3">
      <!-- <SettingsGeneral :setting="setting" :states="states" /> -->
      <VTabs v-model="activeTab" direction="vertical" class="v-tabs-pill disable-tab-transition">
        <VTab v-for="(tabItem, index) in tabsData" :key="index" :prepend-icon="tabItem.icon">
          {{ tabItem.title }}
        </VTab>
      </VTabs>
    </VCol>

    <VCol cols="12" md="9">
      <VWindow v-model="activeTab" class="disable-tab-transition" :touch="false">
        <VWindowItem>
          <SettingsGeneral />
        </VWindowItem>
 
        <VWindowItem>
          <SettingsStatus />
        </VWindowItem>
 
      </VWindow>
    </VCol>
  </VRow>
</template>

<style lang="scss">
.my-class {
  padding: 1.25rem;
  border-radius: 0.375rem;
  background-color: rgba(var(--v-theme-on-surface), var(--v-hover-opacity));
}
</style>
