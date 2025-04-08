<script setup>
import AppSelect from '@/@core/components/app-form-elements/AppSelect.vue'
import AppTextField from '@/@core/components/app-form-elements/AppTextField.vue'
import { v4 as uuidv4 } from 'uuid'
import { ref, watch, watchEffect } from 'vue'
import { PerfectScrollbar } from 'vue3-perfect-scrollbar'
import { toast } from 'vue3-toastify'
import { VForm } from 'vuetify/components'

import { useRouter } from 'vue-router'

const router = useRouter()

const refForm = ref()
const valid = ref(true)
const isLoading = ref(false)
let isSubmitting = false

const record = ref({
  // quotation_number: '',
  valid_uptil: '',
  quotation_type: '',
  title: '',
  // sub_total: 0,
  // discount: 0,
  // tax: 0,
  // total: 0,
  status: '',
  items: [],
  custom_header_text: '',
  payment_terms: '',
  terms_conditions: '',
  lead_id: '',
  client_id: '',
  contract_id: '',
  // created_by: '',
  // last_updated_by: '',
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

    const res = await $api('/quotations', {
      method: 'POST',
      body: JSON.stringify(record.value),
    })

    if (res?.data) {
      toast.success(res?.data?.message || 'Contract created successfully!')
      // ✅ Redirect to quotation list
      router.push({ name: 'quotation-list' })
    }
  } catch (err) {
    console.error(err)
    toast.error(err?.response?.data?.message || 'An error occurred while saving.')
  } finally {
    isSubmitting = false
    isLoading.value = false
  }
}
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
                  <AppTextField v-model="record.valid_uptil" :rules="[requiredValidator]" label="Valid Until"
                    type="date" />
                </VCol>

                <VCol cols="12" md="6">
                  <AppSelect v-model="record.quotation_type" label="Quotation Type" :items="['manual']" />
                </VCol>

                <VCol cols="12" md="6">
                  <AppTextField v-model="record.title" :rules="[requiredValidator]" label="Title" />
                </VCol>

                <VCol cols="12" md="6">
                  <AppSelect v-model="record.status" :rules="[requiredValidator]" label="Status"
                    :items="['Pending', 'Approved', 'Rejected']" />
                </VCol>

                <VCol cols="12" md="6">
                  <AppSelect v-model="record.client_id" label="Client" :items="[]" />
                </VCol>

                <VCol cols="12" md="6">
                  <AppSelect v-model="record.lead_id" label="Lead" :items="[]" />
                </VCol>

                <VCol cols="12" md="6">
                  <AppSelect v-model="record.contract_id" label="Related Contract" :items="[]" />
                </VCol>

                <VCol cols="12">
                  <AppTextField v-model="record.custom_header_text" label="Custom Header Text" />
                </VCol>

                <VCol cols="12">
                  <AppTextField v-model="record.payment_terms" ss label="Payment Terms" />
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
                  <AppTextField v-model="item.quantity" label="Quantity*" type="number" min="1" />
                </VCol>

                <VCol cols="12" md="4">
                  <AppTextField v-model="item.unit_price" label="Unit Price*" type="number" />
                </VCol>

                <VCol cols="12" md="4">
                  <AppTextField v-model="item.tax_rate" label="Tax Rate (%)" type="number" />
                </VCol>

                <VCol cols="12" md="4">
                  <AppTextField v-model="item.discount_rate" label="Discount Rate (%)" type="number" />
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
                Add
              </VBtn>
              <VBtn color="error" variant="tonal" :to="{ name: 'quotation-list' }">
                Discard
              </VBtn>
            </VCol>
          </VRow>
        </VForm>
      </VCardText>
    </PerfectScrollbar>
  </VCard>
</template>
