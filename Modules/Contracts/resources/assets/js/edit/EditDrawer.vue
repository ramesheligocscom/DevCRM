<script setup>
import { v4 as uuidv4 } from 'uuid'
import { onMounted, ref, watch, watchEffect } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { PerfectScrollbar } from 'vue3-perfect-scrollbar'
import { toast } from 'vue3-toastify'
import { VForm } from 'vuetify/components'

const route = useRoute()
const router = useRouter()

const contractId = route.params.id

const refForm = ref()
const valid = ref(true)
const isLoading = ref(false)
let isSubmitting = false

const record = ref({
  title:"",
  description:"",
  items: [],
  start_date: '',
  end_date: '',
  status: '',
  client_id: '',
  quotation_id: '',
  invoice_id: '',
})

// Helper for creating new item object
const newItem = (isNew = true) => ({
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
  attributes: [],
  isNew:isNew
})

// Load existing contract data
const loadContract = async () => {
  try {
    isLoading.value = true
    const response = await $api(`/contracts/${contractId}`)

    const contract = response?.data

    if (!contract) {
      toast.error('Contract not found.')
      return
    }

    record.value = {
      ...record.value,
      ...contract,
      items: contract.items?.map(item => ({
        ...newItem(false),
        ...item,
      })) ?? [],
    }
  } catch (err) {
    console.error('Failed to load contract:', err)
    toast.error('Failed to load contract.')
  } finally {
    isLoading.value = false
  }
}

// Add item
const addItem = () => {
  record.value.items.push(newItem())
}

// Remove item
const removeItem = index => {
  record.value.items.splice(index, 1)
}

// Validate items
const validateItems = () => {
  for (const item of record.value.items) {
    if (!item.name || item.quantity <= 0 || item.unit_price <= 0) {
      toast.error('Each item must have Name, Quantity > 0, and Unit Price > 0.')
      return false
    }
  }
  return true
}

// Calculate item values
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

// Watch item values
watch(
  () => record.value.items,
  items => {
    for (const item of items) {
      watchEffect(() => calculateItemValues(item))
    }
  },
  { deep: true }
)

// Update contract
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
    // ✅ Remove `isNew` from each item before sending
    const payload = {
          ...record.value,
          items: record.value.items.map(({ isNew, ...item }) => item),
        }

    const res = await $api(`/contracts/${contractId}`, {
      method: 'PUT',
      body: JSON.stringify(payload),
    })

    if (res?.data) {
      toast.success(res?.data?.message || 'Contract updated successfully!')
      router.push({ name: 'contract-list' })

    }
  } catch (err) {
    console.error(err)
    toast.error(err?._data?.message || 'An error occurred while updating.')
  } finally {
    isSubmitting = false
    isLoading.value = false
  }
}

const loadingAttributes = ref(false)
const attributeItems = ref([])
const fetchAttributes = async (search = '') => {
  loadingAttributes.value = true
  try {
    const { data } = await $api('/product-services', {
      params: { search },
    })
    attributeItems.value = data // adapt if your API response is shaped differently
  } catch (e) {
    console.error('Failed to load attributeItems', e)
  } finally {
    loadingAttributes.value = false
  }
}

const onProductSelected = (product, item) => {
  if (!product) return

  // Set name and price
  item.name = product.name
  item.unit_price = parseFloat(product.price)

  // Set custom fields (attributes)
  item.attributes = product.attributes.map((val) =>{
    return {
      key:val.key,
      value:val.value,
    }
  });
}
// Remove item attribute by index
const removeAttribute = (itemIndex, attributeIndex) => {
  record.value.items[itemIndex].attributes.splice(attributeIndex, 1)
}
const addAttribute = (itemIndex) => {
  record.value.items[itemIndex].attributes.push({
    key: '',
    value: '',
  })
}

onMounted(loadContract)
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
                  <AppTextField v-model="record.title" label="Title"/>
                </VCol>
                <VCol cols="12" md="6">
                  <AppDateTimePicker v-model="record.start_date" label="Start Date*" />
                </VCol>

                <VCol cols="12" md="6">
                  <AppDateTimePicker v-model="record.end_date" label="End Date*" />
                </VCol>

                <VCol cols="12" md="6">
                  <AppSelect v-model="record.status" label="Status*" :items="['Pending', 'Approved', 'Rejected']" />
                </VCol>

                <VCol cols="12" md="6">
                  <AppSelect v-model="record.client_id" label="Client ID*" :items="[]" />
                </VCol>

                <VCol cols="12" md="6">
                  <AppSelect v-model="record.quotation_id" label="Quotation ID" :items="[]" />
                </VCol>

                <VCol cols="12" md="6">
                  <AppSelect v-model="record.invoice_id" label="Invoice ID" :items="[]" />
                </VCol>

                <VCol cols="12" md="12">
                  <AppTextField v-model="record.description" label="Description"
                     />
                </VCol>
              </VRow>
            </VCol>

            <VCol cols="12" v-if="record.items.length">
              <strong class="text-primary">Items</strong>
            </VCol>

            <VCol cols="12" v-for="(item, index) in record.items" :key="item.item_id">
              <VRow class="border rounded pa-3 mb-3">
                <VCol cols="12" md="12" v-if="item.isNew">
                  <AppAutocomplete
                  label="Product/Service"
                  :items="attributeItems"
                  item-title="name"
                  :loading="loadingAttributes"
                  :searchable="true"
                  @update:search="fetchAttributes"
                  return-object
                  v-model="item.product"
                  @update:modelValue="val => onProductSelected(val, item)"
                  placeholder="Search Product Service "
                />
                </VCol>

                <VCol cols="12" md="4">
                  <AppTextField v-model="item.name" label="Name*" />
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

                <VCol cols="12" md="12">
                  <AppTextField v-model="item.description" label="Description" />
                </VCol>

                <VCol cols="12">
                  <VRow align="center" class="mb-2">
                    <VCol cols="6">
                      <strong class="text-primary">Attributes</strong>
                    </VCol>
                    <VCol cols="6" class="d-flex justify-end">
                      <VBtn
                        size="small"
                        variant="tonal"
                        color="primary"
                        prepend-icon="tabler-plus"
                        @click="addAttribute(index)"
                      >
                        Add Attribute
                      </VBtn>
                    </VCol>
                  </VRow>
                </VCol>

                <VCol cols="12" md="12" v-for="(attribute, i) in item.attributes" :key="i">
                  <VRow >
                    <VCol cols="12" lg="6" md="6">
                      <AppTextField v-model="attribute.key" :label="`${i+1}.Attribute Key`" />
                    </VCol>
                    <VCol cols="12" lg="6" md="6" >
                      <AppTextField v-model="attribute.value" :label="`Attribute Value`">
                        <template #append>
                          <VBtn icon="tabler-trash" size="small" color="error" @click="removeAttribute(index , i)" />

                        </template>
                      </AppTextField>
                    </VCol>
                  </VRow>  
                </VCol>


                <VCol cols="12" class="d-flex justify-end">
                  <VBtn color="error" @click="removeItem(index)" variant="tonal" prepend-icon="tabler-trash">
                    Delete Item
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
              <VBtn color="error" variant="tonal" @click="loadContract">
                Reset
              </VBtn>
            </VCol>
          </VRow>
        </VForm>
      </VCardText>
    </PerfectScrollbar>
  </VCard>
</template>
