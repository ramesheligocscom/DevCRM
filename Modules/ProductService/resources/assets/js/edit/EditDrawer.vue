<script setup>
import { v4 as uuidv4 } from 'uuid'
import { onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { PerfectScrollbar } from 'vue3-perfect-scrollbar'
import { toast } from 'vue3-toastify'
import { VForm } from 'vuetify/components'


const route = useRoute()
const router = useRouter()
const productServiceId = route.params.id

const refForm = ref()
const valid = ref(true)
const isLoading = ref(false)
let isSubmitting = false

const record = ref({
  name: "",
  price: null,
  attributes: [],
})

// Attribute types for the dropdown
const attributeTypes = ref([
  { title: 'Text', value: 'text' },
  { title: 'Number', value: 'number' },
  { title: 'Boolean', value: 'boolean' },
  { title: 'Date', value: 'date' },
])

// Generate a new empty attribute
const newAttribute = () => ({
  id: uuidv4(),
  key: '',
  type: 'text',
  value: '',
})

// Load existing product/service data
const loadProductService = async () => {
  try {
    isLoading.value = true
    const response = await $api(`/product-services/${productServiceId}`)

    const productService = response?.data

    if (!productService) {
      toast.error('Product/Service not found.')
      return
    }

    record.value = {
      ...productService,
    }
  } catch (err) {
    console.error('Failed to load product/service:', err)
    toast.error('Failed to load product/service.')
  } finally {
    isLoading.value = false
  }
}

// Add new attribute
const addAttribute = () => {
  record.value.attributes.push(newAttribute())
}

// Remove attribute by index
const removeAttribute = index => {
  record.value.attributes.splice(index, 1)
}

// Validate attributes before submit
const validateAttributes = () => {
  for (const attr of record.value.attributes) {
    if (!attr.key) {
      toast.error('All attributes must have a key name')
      return false
    }
    
    // Type-specific validation
    if (attr.type === 'number' && isNaN(attr.value)) {
      toast.error(`Attribute ${attr.key} must be a valid number`)
      return false
    }
  }
  return true
}

// Update product/service
const onSubmit = async () => {
  if (isSubmitting) return
  isSubmitting = true

  const { valid: isValid } = await refForm.value.validate()
  if (!isValid || !validateAttributes()) {
    isSubmitting = false
    return
  }

  try {
    isLoading.value = true

    // Format the attributes before sending
    const payload = {
      ...record.value,
    }

    const res = await $api(`/product-services/${productServiceId}`, {
      method: 'PUT',
      body: JSON.stringify(payload),
    })

    if (res?.data) {
      toast.success(res?.data?.message || 'Product/Service updated successfully!')
      router.push({ name: 'product-service-list' })

    }
  } catch (err) {
    console.error(err)
    toast.error(err?._data?.message || 'An error occurred while updating.')
  } finally {
    isSubmitting = false
    isLoading.value = false
  }
}

onMounted(loadProductService)
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
                  <strong class="text-primary">Basic Information</strong>
                </VCol>
                
                <VCol cols="12" md="6">
                  <AppTextField 
                    v-model="record.name" 
                    label="Name*" 
                    :rules="[requiredValidator]" 
                    placeholder="Enter product/service name"
                  />
                </VCol>

                <VCol cols="12" md="6">
                  <AppTextField 
                    v-model="record.price" 
                    label="Price" 
                    type="number" 
                    min="0"
                    step="0.01"
                    placeholder="Enter price"
                  />
                </VCol>
              </VRow>
            </VCol>

            <VCol cols="12">
              <strong class="text-primary">Attributes</strong>
              <p class="text-sm text-disabled">Add custom attributes to your product/service</p>
            </VCol>

            <VCol cols="12" v-for="(attr, index) in record.attributes" :key="attr.id">
              <VRow class="border rounded pa-3 mb-3">
                <VCol cols="12" md="3">
                  <AppTextField 
                    v-model="attr.key" 
                    label="Key*" 
                    placeholder="Attribute name"
                    :rules="[requiredValidator]"
                  />
                </VCol>

                <VCol cols="12" md="3">
                  <AppSelect
                    v-model="attr.type"
                    label="Type*"
                    :items="attributeTypes"
                    :rules="[requiredValidator]"
                  />
                </VCol>

                <VCol cols="12" md="5">
                  <AppTextField 
                    v-model="attr.value" 
                    :label="`Value (${attr.type})`" 
                    :type="attr.type"
                    :placeholder="`Enter ${attr.type} value`"
                  />
                </VCol>

                <VCol cols="12" md="1" class="d-flex align-center justify-end">
                  <VBtn
                    icon
                    color="error"
                    variant="text"
                    @click="removeAttribute(index)"
                  >
                    <VIcon icon="tabler-trash" size="20" />
                  </VBtn>
                </VCol>
              </VRow>
            </VCol>

            <VCol cols="12" class="d-flex justify-end">
              <VBtn 
                color="primary" 
                variant="tonal" 
                @click="addAttribute"
                prepend-icon="tabler-plus"
              >
                Add Attribute
              </VBtn>
            </VCol>

            <VCol cols="12" class="d-flex gap-4 justify-start pt-6 pb-10">
              <VBtn type="submit" color="primary" :loading="isLoading">
                Update
              </VBtn>
              <VBtn color="error" variant="tonal" @click="loadProductService">
                Reset
              </VBtn>
            </VCol>
          </VRow>
        </VForm>
      </VCardText>
    </PerfectScrollbar>
  </VCard>
</template>
