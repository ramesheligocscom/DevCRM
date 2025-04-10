<script setup>
import CustomerTabOverview from '../tabs/site_visits/TabsiteVisits.vue';
const route = useRoute('clients-view')
const customerData = ref()
const userTab = ref(null)

const tabs = [
  {
    title: 'Site visits',
    icon: 'tabler-map-pin',
  },
  {
    title: 'FolloW Up',
    icon: 'tabler-phone-call',
  }
]

const { data } = await useApi(`/apps/ecommerce/customers/${route.params.id}`)
if (data.value)
  customerData.value = data.value
const isAddCustomerDrawerOpen = ref(false)
</script>

<template>
  <div>
    <!-- 👉 Header  -->
    <div class="d-flex justify-space-between align-center flex-wrap gap-y-4 mb-6">
      <div>
        <h4 class="text-h4 mb-1">
          Customer ID #{{ route.params.id }}
        </h4>
        <div class="text-body-1">
          Aug 17, 2020, 5:48 (ET)
        </div>
      </div>
      <div class="d-flex gap-4">
        <VBtn variant="tonal" color="error">
          Delete Clients
        </VBtn>
        <VBtn variant="tonal" color="primary" @click="isAddCustomerDrawerOpen = true">
          Edit Clients
        </VBtn>
      </div>
    </div>
    <!-- 👉 Customer Profile  -->
    <VRow>
      <VCol cols="12" md="12" lg="12">
        <VTabs v-model="userTab" class="v-tabs-pill mb-3 disable-tab-transition">
          <VTab v-for="tab in tabs" :key="tab.title">
            <VIcon size="20" start :icon="tab.icon" />
            {{ tab.title }}
          </VTab>
        </VTabs>
        <VWindow v-model="userTab" class="disable-tab-transition" :touch="false">
          <VWindowItem>
            <CustomerTabOverview />
          </VWindowItem>
          <VWindowItem>
            <!-- <CustomerTabSecurity /> -->
          </VWindowItem>
        </VWindow>
      </VCol>
    </VRow>
  </div>
</template>
