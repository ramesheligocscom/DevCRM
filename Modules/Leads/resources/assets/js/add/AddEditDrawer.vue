<script setup>
import { nextTick, ref } from 'vue'
import { PerfectScrollbar } from 'vue3-perfect-scrollbar'
import { VForm } from 'vuetify/components/VForm'

import { toast } from 'vue3-toastify'

const props = defineProps({
  isDrawerOpen: { type: Boolean, required: true },
  currentLead: { type: Object, default: null },
})

const emit = defineEmits(['update:isDrawerOpen', 'submit'])

const refForm = ref()
const valid = ref(true)
const isLoading = ref(false)
let isSubmitting = false

const lead = ref({
  name: '',
  contact_person: '',
  contact_person_role: '',
  email: '',
  phone: '',
  address: '',
  status: '',
  source: '',
  assigned_user: '',
  note: '',
  client_id: '',
  quotation_id: '',
  contract_id: '',
  invoice_id: '',
})


const resetForm = () => {
  lead.value = {
    name: '',
    contact_person: '',
    contact_person_role: '',
    email: '',
    phone: '',
    address: '',
    status: '',
    source: '',
    assigned_user: '',
    note: '',
    client_id: '',
    quotation_id: '',
    contract_id: '',
    invoice_id: '',
  }
}

watch(
  () => props.isDrawerOpen,
  (val) => {
    if (val) {
      if (props.currentLead?.id) {
        lead.value = JSON.parse(JSON.stringify(props.currentLead))
      } else {
        resetForm()
      }

      // Optionally reset form validations too
      nextTick(() => {
        refForm.value?.resetValidation()
      })
    }
  }
)


const handleDrawerModelValueUpdate = val => {
  emit('update:isDrawerOpen', val)
}

const closeNavigationDrawer = () => {
  emit('update:isDrawerOpen', false)
  // Reset lead data
  resetForm()

  // Emit a reset event to clear currentLead in the parent

  // Wait for DOM updates before resetting validation
  nextTick(() => {
    refForm.value?.resetValidation()
  })
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

    const payload = lead.value;

    const endpoint = props.currentLead
      ? `/leads/${props.currentLead.id}?_method=PUT`
      : '/leads'

    const res = await $api(endpoint, {
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
    toast.error(err?._data?.message || 'Error occurred')
  } finally {
    isSubmitting = false
    isLoading.value = false
  }
}
</script>

<template>

  <VNavigationDrawer :model-value="props.isDrawerOpen" temporary location="end" width="370" border="none"
    @update:model-value="handleDrawerModelValueUpdate">
    <AppDrawerHeaderSection :title="props.currentLead ? 'Edit Lead' : 'Add Lead'" @cancel="closeNavigationDrawer" />

    <VDivider />

    <VCard flat>
      <PerfectScrollbar :options="{ wheelPropagation: false }" class="h-100">
        <VCardText style="block-size: calc(100vh - 5rem);">
          <VForm ref="refForm" v-model="valid" @submit.prevent="onSubmit">
            <VRow>
              <VCol cols="12">

                
                <AppTextField v-model="lead.name" label="Name*"  placeholder="John Doe" :rules="[requiredValidator]" />
              </VCol>

              <VCol cols="12">
                <AppTextField v-model="lead.contact_person" label="Contact Person*" placeholder="Jane Doe" />
              </VCol>

              <VCol cols="12">
                <AppTextField v-model="lead.contact_person_role" label="Role*" placeholder="Manager" />
              </VCol>

              <VCol cols="12">
                <AppTextField v-model="lead.email" label="Email*" :rules="[requiredValidator, emailValidator]"
                  placeholder="email@example.com" />
              </VCol>

              <VCol cols="12">
                <AppTextField v-model="lead.phone" label="Phone*" :rules="[requiredValidator]"
                  placeholder="+(123) 456-7890" />
              </VCol>

              <VCol cols="12">
                <AppTextField v-model="lead.address" label="Address*" :rules="[requiredValidator]" />
              </VCol>

              <VCol cols="12">
                <AppSelect v-model="lead.status" label="Status*" :items="['Active', 'Inactive', 'Pending']"
                  :rules="[requiredValidator]" />
              </VCol>

              <VCol cols="12">
                <AppSelect v-model="lead.source" label="Source*" :items="['Website', 'Referral', 'Advertisement']"
                  :rules="[requiredValidator]" />
              </VCol>

              <VCol cols="12">
                <AppSelect v-model="lead.assigned_user" :items="[]" label="Assigned User*" placeholder="User ID" />
              </VCol>

              <VCol cols="12">
                <AppTextField v-model="lead.note" label="Note" placeholder="Additional information" />
              </VCol>

              <VCol cols="12">
                <AppSelect v-model="lead.client_id" :items="[]" label="Client ID*" placeholder="Client Identifier" />
              </VCol>

              <VCol cols="12">
                <AppSelect v-model="lead.quotation_id" :items="[]" label="Quotation ID"
                  placeholder="Quotation Identifier" />
              </VCol>

              <VCol cols="12">
                <AppSelect v-model="lead.contract_id" :items="[]" label="Contract ID"
                  placeholder="Contract Identifier" />
              </VCol>

              <VCol cols="12">
                <AppSelect v-model="lead.invoice_id" :items="[]" label="Invoice ID" placeholder="Invoice Identifier" />
              </VCol>

              <VCol cols="12" class="d-flex gap-4 justify-start pb-10">
                <VBtn type="submit" color="primary" :loading="isLoading">
                  {{ props.currentLead ? 'Update' : 'Add' }}
                </VBtn>
                <VBtn color="error" variant="tonal" @click="resetForm">
                  Reset
                </VBtn>
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
      </PerfectScrollbar>
    </VCard>
  </VNavigationDrawer>
</template>
