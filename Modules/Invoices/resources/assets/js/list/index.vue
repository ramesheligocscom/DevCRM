<script setup>
import moment from 'moment';
import { toast } from 'vue3-toastify';

import ConfirmDialog from '../dialog/ConfirmDialog.vue';
const searchQuery = ref('')
const isDeleteDialogOpen = ref(false)

// Data table options
const itemsPerPage = ref(10)
const page = ref(1)
const sortBy = ref()
const orderBy = ref()
const currentInvoice = ref(null);


const tableHeaderSlug = ref('invoice-list');
const headers = ref([]);
const getFilteredHeaderValue = async (headerList) => { headers.value = headerList; };

const resolveStatusVariant = status => {
  if (status === 1) return { color: 'primary', text: 'Current' }
  else if (status === 2) return { color: 'success', text: 'Professional' }
  else if (status === 3) return { color: 'error', text: 'Rejected' }
  else if (status === 4) return { color: 'warning', text: 'Resigned' }
  else return { color: 'info', text: 'Applied' }
}

const updateOptions = options => {
  sortBy.value = options.sortBy[0]?.key
  orderBy.value = options.sortBy[0]?.order
  fetchInvoices();
}
const dataItems = ref([])
const totalItems = ref(0)

const fetchInvoices = async () => {
  try {
    const response = await $api(
      `/invoices?search=${searchQuery.value ?? ""}&page=${page.value}&sort_key=${sortBy.value ?? ""}&sort_order=${orderBy.value ?? ""}&per_page=${itemsPerPage.value}`
    )

    dataItems.value = response.data
    totalItems.value = response.meta.total
  } catch (err) {
    console.error('Failed to fetch Invoices:', err)
    // Optionally show a toast
    toast.error('Failed to load Invoices')
  }
}

const openDeleteDialog = (item) => {
  currentInvoice.value = JSON.parse(JSON.stringify(item));
  isDeleteDialogOpen.value = true;
}

const refresh = () => {
  fetchInvoices();
}
const makeDateFormat = (date , onlyDate = false) => {
    if(onlyDate)
    return moment(date).format('DD-MM-Y');
    else
    return moment(date).format('LLLL');
};
</script>

<template>
  <div v-if="$can('invoice', 'view')">
    <VCard>
      <VCardText>
        <div class="d-flex justify-space-between flex-wrap gap-y-4">
          <AppTextField v-model="searchQuery" style="max-inline-size: 280px; min-inline-size: 280px;"
            placeholder="Search Title" 
            @input="fetchInvoices"
            />
          <div class="d-flex flex-row gap-4 align-center flex-wrap">
            <AppSelect v-model="itemsPerPage" :items="[5, 10, 20, 50, 100]" />

            <VBtn v-if="$can('invoice', 'export-list')" prepend-icon="tabler-upload" variant="tonal" color="secondary">
              Export
            </VBtn>
            <VBtn v-if="$can('invoice', 'create')" :to="{ name: 'invoice-create' }" prepend-icon="tabler-plus">
              Add New
            </VBtn>
            <!-- Filter Header Btn FilterHeaderTableBtn -->
            <FilterHeaderTableBtn :slug="tableHeaderSlug" @filterHeaderValue="getFilteredHeaderValue" />
          </div>
        </div>
      </VCardText>

      <VDivider />
      <VDataTableServer v-model:items-per-page="itemsPerPage" v-model:page="page" :items="dataItems" item-value="name"
        :headers="headers.filter((header) => header.checked)" :items-length="totalItems" show-select
        class="text-no-wrap" @update:options="updateOptions">

        <template #item.invoice_number="{ item }">
        <RouterLink :to="{ name: 'invoice-details-id', params: { id: item.id } }"
                class="text-link font-weight-medium d-inline-block" style="line-height: 1.375rem;">
                #{{ item.invoice_number }}
        </RouterLink>
        </template>

        <!-- Actions Column -->
        <template #item.action="{ item }">

          <IconBtn :to="{ name: 'invoice-details-id', params: { id: item.id } }">
            <VIcon icon="tabler-eye" />
          </IconBtn>
          <IconBtn :to="{ name: 'invoice-edit', params: { id: item.id } }">
            <VIcon icon="tabler-pencil" />
          </IconBtn>

          <IconBtn v-if="$can('invoice', 'delete')" @click="openDeleteDialog(item)">
            <VIcon icon="tabler-trash" />
          </IconBtn>
        </template>

        <!-- status -->
        <template #item.status="{ item }">
          <VChip :color="resolveStatusVariant(item.status).color" size="small">
            {{ resolveStatusVariant(item.status).text }}
          </VChip>
        </template>

        <!-- invoice_type -->
        <template #item.invoice_type="{ item }">
          {{ item.invoice_type }}
        </template>
        <!-- sub_total -->
        <template #item.sub_total="{ item }">
          ${{ item.sub_total || 0 }}
        </template>
        <!-- discount -->
        <template #item.discount="{ item }">
          ${{ item.discount || 0 }}
        </template>
        <!-- total -->
        <template #item.tax="{ item }">
          ${{ item.tax || 0 }}
        </template>
        <!-- total -->
        <template #item.total="{ item }">
          ${{ item.total || 0 }}
        </template>
        <!-- creator -->
        <template #item.created_by="{ item }">
          {{ item.creator?.name || '—' }}
        </template>
        <!-- updater -->
        <template #item.last_updated_by="{ item }">
          {{ item.updater?.name || '—' }}
        </template>
        <!-- created_at -->
        <template #item.created_at="{ item }">
          {{ makeDateFormat(item.created_at )}}
        </template>
        <!-- created_at -->
        <template #item.updated_at="{ item }">
          {{ item.updater ? makeDateFormat(item.updated_at ) : '-'}}
        </template>
        <template #bottom>
          <TablePagination v-model:page="page" :items-per-page="itemsPerPage" :total-items="totalItems" />
        </template>
      </VDataTableServer>
    </VCard>

    <!-- 👉 Confirm Dialog -->
    <ConfirmDialog v-model:isDialogVisible="isDeleteDialogOpen" confirm-title="Delete!"
      confirmation-question="Are you sure want to delete invoice?" :currentItem="currentInvoice" @submit="refresh"
      :endpoint="`/invoices/${currentInvoice?.id}`" @close="isDeleteDialogOpen = false" />

  </div>
</template>
