<script setup>
import moment from 'moment'
import { toast } from 'vue3-toastify'
import Clients from './tabs/Clients.vue'
import FollowUps from './tabs/FollowUps.vue'
import Information from './tabs/Information.vue'
import Quotations from './tabs/Quotations.vue'
import SiteVisits from './tabs/SiteVisits.vue'
const route = useRoute('lead-details-id')
const InfoData = ref()
const tab = ref(null)

const tabs = [
 {
    title: 'Information',
    icon: 'tabler-user',
  },
  {
    title: 'Clients',
    icon: 'tabler-user',
  },
  {
    title: 'Quotations',
    icon: 'tabler-lock',
  },
  {
    title: 'Follow Ups',
    icon: 'tabler-map-pin',
  },
  {
    title: 'Site Visits',
    icon: 'tabler-bell',
  },
]

try {
  const { data } = await $api(`/leads/${route.params.id}`)
  InfoData.value = data
} catch (error) {
  console.error('Failed to fetch lead data:', error)
  toast.error(error?.response?.data?.message || 'Failed to load lead details.')
}

const makeDateFormat = (date , onlyDate = false) => {
    if(onlyDate)
    return moment(date).format('DD-MM-Y');
    else
    return moment(date).format('LLLL');
};
</script>

<template>
  <div>
    <!-- 👉 Header  -->
    <div class="d-flex justify-space-between align-center flex-wrap gap-y-4 mb-6">
      <div>
        <h4 class="text-h4 mb-1">
          Lead {{ InfoData.name }}
        </h4>
        <div class="text-body-1">
          {{ makeDateFormat(InfoData.created_at) }}
        </div>
      </div>
      <div class="d-flex gap-4">
        <VBtn variant="tonal" color="error" :to="{name:'lead-list'}">
         Back
        </VBtn>
      </div>
    </div>
    <!-- 👉 Lead Profile  -->
    <VRow v-if="InfoData">
      <VCol cols="12" md="12" lg="12">
        <VTabs v-model="tab" class="v-tabs-pill mb-3 disable-tab-transition">
          <VTab v-for="tab in tabs" :key="tab.title">
            <VIcon size="20" start :icon="tab.icon" />
            {{ tab.title }}
          </VTab>
        </VTabs>
        <VWindow v-model="tab" class="disable-tab-transition" :touch="false">
          <VWindowItem>
            <Information :InfoData="InfoData" />
          </VWindowItem>
          <VWindowItem>
            <Clients />
          </VWindowItem>
          <VWindowItem>
            <SiteVisits />
          </VWindowItem>
          <VWindowItem>
            <FollowUps />
          </VWindowItem>
          <VWindowItem>
            <Quotations />
          </VWindowItem>
        </VWindow>
      </VCol>
    </VRow>
    <div v-else>
      <VAlert type="error" variant="tonal">
        Lead with ID {{ route.params.id }} not found!
      </VAlert>
    </div>
  </div>
</template>
