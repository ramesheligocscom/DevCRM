<script setup>
import Clients from './tabs/Clients.vue'
import Information from './tabs/Information.vue'
import Invoices from './tabs/Invoices.vue'
import Quotations from './tabs/Quotations.vue'

const route = useRoute('contract-details-id')
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
    title: 'Invoices',
    icon: 'tabler-bell',
  },
]

const { data } = await useApi(`/contracts/${route.params.id}`)
if (data.value)
  InfoData.value = data.value.data

</script>

<template>
  <div>
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
            <Invoices />
          </VWindowItem>
          <VWindowItem>
            <Quotations />
          </VWindowItem>
        </VWindow>
      </VCol>
    </VRow>
    <div v-else>
      <VAlert type="error" variant="tonal">
        Contract with ID {{ route.params.id }} not found!
      </VAlert>
    </div>
  </div>
</template>
