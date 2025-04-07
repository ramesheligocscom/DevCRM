<script setup>
import AppSelect from '@/@core/components/app-form-elements/AppSelect.vue'
import AppTextField from '@/@core/components/app-form-elements/AppTextField.vue'
import { nextTick, ref, watch } from 'vue'
import { PerfectScrollbar } from 'vue3-perfect-scrollbar'
import { toast } from 'vue3-toastify'
import { VForm } from 'vuetify/components'

const props = defineProps({
  isDrawerOpen: Boolean,
  currentRecord: { type: Object, default: null }
})

const emit = defineEmits(['update:isDrawerOpen', 'submit'])

const refForm = ref()
const valid = ref(true)
const isLoading = ref(false)
let isSubmitting = false

const record = ref({
  items: '',
  start_date: '',
  end_date: '',
  sub_total: '',
  discount: '',
  tax: '',
  status: '',
  client_id: '',
  quotation_id: '',
  invoice_id: '',
  created_by: '',
  last_updated_by: '',
})

const resetForm = () => {
  record.value = {
    items: '',
    start_date: '',
    end_date: '',
    sub_total: '',
    discount: '',
    tax: '',
    status: '',
    client_id: '',
    quotation_id: '',
    invoice_id: '',
    created_by: '',
    last_updated_by: '',
  }
}

watch(() => props.isDrawerOpen, val => {
  if (val) {
    if (props.currentRecord?.id) {
      record.value = JSON.parse(JSON.stringify(props.currentRecord))
    } else {
      resetForm()
    }

    nextTick(() => refForm.value?.resetValidation())
  }
})

const handleDrawerModelValueUpdate = val => {
  emit('update:isDrawerOpen', val)
}

const closeNavigationDrawer = () => {
  emit('update:isDrawerOpen', false)
  resetForm()
  nextTick(() => refForm.value?.resetValidation())
}

const onSubmit = async () => {
  if (isSubmitting) return
  isSubmitting = true

  const { valid: isValid } = await refForm.value.validate()
  if (!isValid) {
    isSubmitting = false
    return
  }

  try {
    isLoading.value = true
    const payload = record.value

    const endpoint = props.currentRecord
      ? `/contracts/${props.currentRecord.id}?_method=PUT`
      : '/contracts'

    const res = await useApi(endpoint, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload),
    })

    if (res?.data) {
      toast.success(res?.data?.message || 'Saved successfully')
      emit('submit')
      emit('update:isDrawerOpen', false)
      resetForm()
    }
  } catch (err) {
    console.error(err)
    toast.error(err?.response?.data?.message || 'Error occurred')
  } finally {
    isSubmitting = false
    isLoading.value = false
  }
}
</script>

<template>
  <VNavigationDrawer :model-value="props.isDrawerOpen" temporary location="end" width="370" border="none"
    @update:model-value="handleDrawerModelValueUpdate">

    <AppDrawerHeaderSection :title="props.currentRecord ? 'Edit Record' : 'Add Record'"
      @cancel="closeNavigationDrawer" />
    <VDivider />

    <VCard flat>
      <PerfectScrollbar :options="{ wheelPropagation: false }" class="h-100">
        <VCardText style="block-size: calc(100vh - 5rem);">
          <VForm ref="refForm" v-model="valid" @submit.prevent="onSubmit">
            <VRow>
              <VCol cols="12">
                <AppTextField v-model="record.items" label="Items*" />
              </VCol>

              <VCol cols="12">
                <AppTextField v-model="record.start_date" label="Start Date*" type="date" />
              </VCol>

              <VCol cols="12">
                <AppTextField v-model="record.end_date" label="End Date*" type="date" />
              </VCol>

              <VCol cols="12">
                <AppTextField v-model="record.sub_total" label="Subtotal*" type="number" />
              </VCol>

              <VCol cols="12">
                <AppTextField v-model="record.discount" label="Discount" type="number" />
              </VCol>

              <VCol cols="12">
                <AppTextField v-model="record.tax" label="Tax" type="number" />
              </VCol>

              <VCol cols="12">
                <AppSelect v-model="record.status" label="Status*" :items="['Pending', 'Approved', 'Rejected']" />
              </VCol>

              <VCol cols="12">
                <AppTextField v-model="record.client_id" label="Client ID*" />
              </VCol>

              <VCol cols="12">
                <AppTextField v-model="record.quotation_id" label="Quotation ID" />
              </VCol>

              <VCol cols="12">
                <AppTextField v-model="record.invoice_id" label="Invoice ID" />
              </VCol>

              <VCol cols="12">
                <AppTextField v-model="record.created_by" label="Created By*" />
              </VCol>

              <VCol cols="12">
                <AppTextField v-model="record.last_updated_by" label="Last Updated By*" />
              </VCol>

              <VCol cols="12" class="d-flex gap-4 justify-start pb-10">
                <VBtn type="submit" color="primary" :loading="isLoading">
                  {{ props.currentRecord ? 'Update' : 'Add' }}
                </VBtn>
                <VBtn color="error" variant="tonal" @click="resetForm">
                  Discard
                </VBtn>
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
      </PerfectScrollbar>
    </VCard>
  </VNavigationDrawer>
</template>
