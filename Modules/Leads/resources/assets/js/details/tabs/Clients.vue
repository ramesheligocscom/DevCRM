<script setup>
import { ref, watch } from 'vue'
import { useRoute } from 'vue-router'

// Route for getting lead ID from params
const route = useRoute()
const leadId = route.params.id

const searchQuery = ref('')

// Data table options
const itemsPerPage = ref(10)
const page = ref(1)
const sortBy = ref()
const orderBy = ref()

// Data table Headers
const headers = [
  { title: 'NAME', key: 'name' },
  { title: 'CONTACT PERSON', key: 'contact_person' },
  { title: 'CONTACT PERSON ROLE', key: 'contact_person_role' },
  { title: 'EMAIL', key: 'email' },
  { title: 'PHONE', key: 'phone' },
  { title: 'STATUS', key: 'status' },
  { title: 'ASSIGNED USER', key: 'assigned_user' },
]

// Status resolver
const resolveStatusVariant = status => {
  if (status === 1) return { color: 'primary', text: 'Current' }
  else if (status === 2) return { color: 'success', text: 'Professional' }
  else if (status === 3) return { color: 'error', text: 'Rejected' }
  else if (status === 4) return { color: 'warning', text: 'Resigned' }
  else return { color: 'info', text: 'Applied' }
}

// Sorting + options
const updateOptions = options => {
  sortBy.value = options.sortBy[0]?.key
  orderBy.value = options.sortBy[0]?.order
  fetchData()
}

// API Data
const dataItems = ref([])
const totalItems = ref(0)

const fetchData = async () => {
  const response = await $api(`/leads/${leadId}/clients?search=${searchQuery.value ?? ''}&page=${page.value}&sort_key=${sortBy.value ?? ''}&sort_order=${orderBy.value ?? ''}&per_page=${itemsPerPage.value}`)
  dataItems.value = response.data
  totalItems.value = response.meta.total
}

// Fetch when filters or leadId change
watch([searchQuery, itemsPerPage, page], fetchData, { immediate: true })
</script>

<template>
  <div>
    <VCard>
      <VCardText>
        <div class="d-flex justify-space-between flex-wrap gap-y-4">
          <AppTextField v-model="searchQuery" style="max-inline-size: 280px; min-inline-size: 280px;"
            placeholder="Search Name" />
          <div class="d-flex flex-row gap-4 align-center flex-wrap">
            <AppSelect v-model="itemsPerPage" :items="[5, 10, 20, 50, 100]" />

            <VBtn prepend-icon="tabler-upload" variant="tonal" color="secondary">
              Export
            </VBtn>
          </div>
        </div>
      </VCardText>

      <VDivider />

      <VDataTableServer v-model:items-per-page="itemsPerPage" v-model:page="page" :items="dataItems" item-value="name"
        :headers="headers" :items-length="totalItems" show-select class="text-no-wrap" @update:options="updateOptions">
        <!-- name + avatar column -->
        <template #item.name="{ item }">
          <div class="d-flex align-center gap-x-3">
            <VAvatar size="34" :variant="!item.avatar ? 'tonal' : undefined">
              <VImg v-if="item.avatar" :src="item.avatar" />
              <span v-else>{{ item.name.slice(0, 2).toUpperCase() }}</span>
            </VAvatar>
            <div class="d-flex flex-column">
              <RouterLink :to="{ name: 'lead-details-id', params: { id: item.id } }"
                class="text-link font-weight-medium d-inline-block" style="line-height: 1.375rem;">
                {{ item.name }}
              </RouterLink>
              <div class="text-body-2">
                {{ item.email }}
              </div>
            </div>
          </div>
        </template>

        <!-- status -->
        <template #item.status="{ item }">
          <VChip :color="resolveStatusVariant(item.status).color" size="small">
            {{ resolveStatusVariant(item.status).text }}
          </VChip>
        </template>

        <!-- pagination bottom slot -->
        <template #bottom>
          <TablePagination v-model:page="page" :items-per-page="itemsPerPage" :total-items="totalItems" />
        </template>
      </VDataTableServer>
    </VCard>
  </div>
</template>
