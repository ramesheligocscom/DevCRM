<script setup>
import AppSelect from '@/@core/components/app-form-elements/AppSelect.vue'
import AppTextField from '@/@core/components/app-form-elements/AppTextField.vue'
import { v4 as uuidv4 } from 'uuid'
import { onMounted, ref, watch, watchEffect } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { PerfectScrollbar } from 'vue3-perfect-scrollbar'
import { toast } from 'vue3-toastify'
import { VForm } from 'vuetify/components'

const route = useRoute()
const router = useRouter()
const quotationId = route.params.id

const refForm = ref()
const valid = ref(true)
const isLoading = ref(false)
let isSubmitting = false

const record = ref({
  quotation_number: '',
  valid_uptil: '',
  quotation_type: '',
  title: '',
  status: '',
  items: [],
  custom_header_text: '',
  payment_terms: '',
  terms_conditions: '',
  lead_id: '',
  client_id: '',
  contract_id: '',
})

// Generate a new empty item
const newItem = () => ({
  item_id: uuidv4(),
  name: '',
  description: '',
  quantity: 1,
  unit_price: 0,
  tax_rate: 0,
  tax_amount: 0,
  discount_rate: 0,
  discount_amount: 0,
  subtotal: 0,
  total: 0,
  custom_fields: {},
})

// Load existing quotation data
const loadQuotation = async () => {
  try {
    isLoading.value = true
    const response = await $api(`/quotations/${quotationId}`)

    const quotation = response?.data

    if (!quotation) {
      toast.error('Quotation not found.')
      router.push({ name: 'quotation-list' })
      return
    }

    record.value = {
      ...quotation,
      items: quotation.items?.map(item => ({
        ...newItem(),
        ...item,
      })) ?? [],
    }
  } catch (err) {
    console.error('Failed to load quotation:', err)
    toast.error('Failed to load quotation.')
    router.push({ name: 'quotation-list' })
  } finally {
    isLoading.value = false
  }
}

// Add new item row
const addItem = () => {
  record.value.items.push(newItem())
}

// Remove item by index
const removeItem = index => {
  record.value.items.splice(index, 1)
}

// Validate each item before submit
const validateItems = () => {
  for (const item of record.value.items) {
    if (!item.name || item.quantity <= 0 || item.unit_price <= 0) {
      toast.error('Each item must have Name, Quantity > 0, and Unit Price > 0.')
      return false
    }
  }
  return true
}

// Calculate dynamic fields for item
const calculateItemValues = item => {
  const quantity = parseFloat(item.quantity || 0)
  const unitPrice = parseFloat(item.unit_price || 0)
  const taxRate = parseFloat(item.tax_rate || 0)
  const discountRate = parseFloat(item.discount_rate || 0)

  const subtotal = quantity * unitPrice
  const taxAmount = (subtotal * taxRate) / 100
  const discountAmount = (subtotal * discountRate) / 100
  const total = subtotal + taxAmount - discountAmount

  item.subtotal = parseFloat(subtotal.toFixed(2))
  item.tax_amount = parseFloat(taxAmount.toFixed(2))
  item.discount_amount = parseFloat(discountAmount.toFixed(2))
  item.total = parseFloat(total.toFixed(2))
}

// Watch each item for real-time calculation
watch(
  () => record.value.items,
  items => {
    for (const item of items) {
      watchEffect(() => calculateItemValues(item))
    }
  },
  { deep: true }
)

// Submit form
const onSubmit = async () => {
  if (isSubmitting) return
  isSubmitting = true

  const { valid: isValid } = await refForm.value.validate()
  if (!isValid || !validateItems()) {
    isSubmitting = false
    return
  }

  try {
    isLoading.value = true

    const res = await $api(`/quotations/${quotationId}`, {
      method: 'PUT',
      body: JSON.stringify(record.value),
    })

    if (res?.data) {
      toast.success(res?.data?.message || 'Quotation updated successfully!')
      router.push({ name: 'quotation-list' })
    }
  } catch (err) {
    console.error(err)
    toast.error(err?.response?.data?.message || 'An error occurred while updating.')
  } finally {
    isSubmitting = false
    isLoading.value = false
  }
}

onMounted(loadQuotation)
</script>

<template>
  <VCard flat>
    <PerfectScrollbar :options="{ wheelPropagation: false }" class="h-100">
      <VCardText style="block-size: calc(100vh - 5rem);">
        <VForm ref="refForm" v-model="valid" @submit.prevent="onSubmit">
          <VRow>
            <VCol cols="12" class="mt-4">
              <VRow>
                <VCol cols="12">
                  <strong class="text-primary">Basic</strong>
                </VCol>

                <VCol cols="12" md="6">
                  <AppTextField v-model="record.quotation_number" label="Quotation Number" readonly />
                </VCol>

                <VCol cols="12" md="6">
                  <AppDateTimePicker v-model="record.valid_uptil" label="Valid Until" />
                </VCol>

                <VCol cols="12" md="6">
                  <AppSelect v-model="record.quotation_type" label="Quotation Type" :items="['manual']" />
                </VCol>

                <VCol cols="12" md="6">
                  <AppTextField v-model="record.title" label="Title" />
                </VCol>

                <VCol cols="12" md="6">
                  <AppSelect v-model="record.status" label="Status" :items="['Pending', 'Approved', 'Rejected']" />
                </VCol>

                <VCol cols="12" md="6">
                  <AppSelect v-model="record.client_id" label="Client" :items="[]" />
                </VCol>

                <VCol cols="12" md="6">
                  <AppSelect v-model="record.lead_id" label="Lead" :items="[]" />
                </VCol>

                <VCol cols="12" md="6">
                  <AppSelect v-model="record.contract_id" label="Contract" :items="[]" />
                </VCol>

                <VCol cols="12">
                  <AppTextField v-model="record.custom_header_text" label="Custom Header Text" />
                </VCol>

                <VCol cols="12">
                  <AppTextField v-model="record.payment_terms" label="Payment Terms" />
                </VCol>

                <VCol cols="12">
                  <AppTextField v-model="record.terms_conditions" label="Terms & Conditions" />
                </VCol>
              </VRow>
            </VCol>

            <VCol cols="12" v-if="record.items.length">
              <strong class="text-primary">Items</strong>
            </VCol>

            <VCol cols="12" v-for="(item, index) in record.items" :key="item.item_id">
              <VRow class="border rounded pa-3 mb-3">
                <VCol cols="12" md="6">
                  <AppTextField v-model="item.name" label="Name*" />
                </VCol>

                <VCol cols="12" md="6">
                  <AppTextField v-model="item.description" label="Description" />
                </VCol>

                <VCol cols="12" md="4">
                  <AppTextField v-model="item.quantity" label="Quantity*" type="number" min="0.01" step="0.01" />
                </VCol>

                <VCol cols="12" md="4">
                  <AppTextField v-model="item.unit_price" label="Unit Price*" type="number" min="0" step="0.01" />
                </VCol>

                <VCol cols="12" md="4">
                  <AppTextField v-model="item.tax_rate" label="Tax Rate (%)" type="number" min="0" max="100"
                    step="0.01" />
                </VCol>

                <VCol cols="12" md="4">
                  <AppTextField v-model="item.discount_rate" label="Discount Rate (%)" type="number" min="0" max="100"
                    step="0.01" />
                </VCol>

                <VCol cols="12" md="4">
                  <AppTextField v-model="item.subtotal" label="Subtotal" type="number" readonly />
                </VCol>

                <VCol cols="12" md="4">
                  <AppTextField v-model="item.total" label="Total" type="number" readonly />
                </VCol>

                <VCol cols="12" class="d-flex justify-end">
                  <VBtn icon color="error" @click="removeItem(index)">
                    <VIcon icon="tabler-trash" />
                  </VBtn>
                </VCol>
              </VRow>
            </VCol>

            <VCol cols="12" class="d-flex justify-end">
              <VBtn color="primary" @click="addItem" prepend-icon="tabler-plus">
                Add Item
              </VBtn>
            </VCol>

            <VCol cols="12" class="d-flex gap-4 justify-start pt-6 pb-10">
              <VBtn type="submit" color="primary" :loading="isLoading">
                Update
              </VBtn>
              <VBtn color="error" variant="tonal" @click="loadQuotation">
                Reset
              </VBtn>
            </VCol>
          </VRow>
        </VForm>
      </VCardText>
    </PerfectScrollbar>
  </VCard>
</template>
