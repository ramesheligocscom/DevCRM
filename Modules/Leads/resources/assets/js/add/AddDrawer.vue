<script setup>
import { nextTick, ref } from 'vue'
import { PerfectScrollbar } from 'vue3-perfect-scrollbar'
import { VForm } from 'vuetify/components/VForm'

const props = defineProps({
  isDrawerOpen: {
    type: Boolean,
    required: true,
  },
})

const emit = defineEmits(['update:isDrawerOpen'])

const handleDrawerModelValueUpdate = val => {
  emit('update:isDrawerOpen', val)
}

const refVForm = ref()
const name = ref('')
const contactPerson = ref('')
const contactPersonRole = ref('')
const email = ref('')
const phone = ref('')
const address = ref('')
const status = ref('')
const source = ref('')
const assignedUser = ref('')
const note = ref('')
const visitAssignee = ref('')
const visitTime = ref('')
const clientId = ref('')
const quotationId = ref('')
const contractId = ref('')
const invoiceId = ref('')
const isSubmitting = ref(false) // Prevent multiple submissions

const resetForm = () => {
  refVForm.value?.reset()
  emit('update:isDrawerOpen', false)
}

const closeNavigationDrawer = () => {
  emit('update:isDrawerOpen', false)
  nextTick(() => {
    refVForm.value?.reset()
    refVForm.value?.resetValidation()
  })
}

const submitForm = async () => {
  const { valid } = await refVForm.value.validate()
  if (!valid) return // Stop submission if form is invalid

  isSubmitting.value = true // Prevent multiple submissions

  try {
    const payload = {
      name: name.value,
      contact_person: contactPerson.value,
      contact_person_role: contactPersonRole.value,
      email: email.value,
      phone: phone.value,
      address: address.value,
      status: status.value,
      source: source.value,
      assigned_user: assignedUser.value,
      note: note.value,
      visit_assignee: visitAssignee.value,
      visit_time: visitTime.value,
      client_id: clientId.value,
      quotation_id: quotationId.value,
      contract_id: contractId.value,
      invoice_id: invoiceId.value,
    }

    const { data: responseData } = await useApi(createUrl('/leads'), {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' }, // Ensure JSON headers
      body: JSON.stringify(payload), // Convert to JSON string
    })

    console.log('API Response:', responseData)
    resetForm()
  } catch (error) {
    console.error('API Error:', error)
  } finally {
    isSubmitting.value = false
  }
}
</script>

<template>
  <VNavigationDrawer :model-value="props.isDrawerOpen" temporary location="end" width="370" border="none"
    @update:model-value="handleDrawerModelValueUpdate">
    <AppDrawerHeaderSection title="Add a Customer" @cancel="closeNavigationDrawer" />

    <VDivider />

    <VCard flat>
      <PerfectScrollbar :options="{ wheelPropagation: false }" class="h-100">
        <VCardText style="block-size: calc(100vh - 5rem);">
          <VForm ref="refVForm" @submit.prevent="submitForm">
            <VRow>
              <VCol cols="12">
                <AppTextField v-model="name" label="Name*" :rules="[requiredValidator]" placeholder="John Doe" />
              </VCol>

              <VCol cols="12">
                <AppTextField v-model="contactPerson" label="Contact Person*" placeholder="Jane Doe" />
              </VCol>

              <VCol cols="12">
                <AppTextField v-model="contactPersonRole" label="Role*" placeholder="Manager" />
              </VCol>

              <VCol cols="12">
                <AppTextField v-model="email" label="Email*" :rules="[requiredValidator, emailValidator]"
                  placeholder="johndoe@email.com" />
              </VCol>

              <VCol cols="12">
                <AppTextField v-model="phone" label="Phone*" :rules="[requiredValidator]"
                  placeholder="+(123) 456-7890" />
              </VCol>

              <VCol cols="12">
                <AppTextField v-model="address" label="Address*" :rules="[requiredValidator]"
                  placeholder="45, Rocker Terrace" />
              </VCol>

              <VCol cols="12">
                <AppSelect v-model="status" label="Status" :items="['Active', 'Inactive', 'Pending']"
                  :rules="[requiredValidator]" />
              </VCol>

              <VCol cols="12">
                <AppSelect v-model="source" label="Source" :items="['Website', 'Referral', 'Advertisement']"
                  :rules="[requiredValidator]" />
              </VCol>

              <VCol cols="12">
                <AppTextField v-model="assignedUser" label="Assigned User*" placeholder="User ID" />
              </VCol>

              <VCol cols="12">
                <AppTextField v-model="note" label="Note" placeholder="Additional information" />
              </VCol>

              <VCol cols="12">
                <AppTextField v-model="visitAssignee" label="Visit Assignee*" placeholder="Employee Name" />
              </VCol>

              <VCol cols="12">
                <AppTextField v-model="visitTime" label="Visit Time*" type="datetime-local" />
              </VCol>

              <VCol cols="12">
                <AppTextField v-model="clientId" label="Client ID*" placeholder="Client Identifier" />
              </VCol>

              <VCol cols="12">
                <AppTextField v-model="quotationId" label="Quotation ID" placeholder="Quotation Identifier" />
              </VCol>

              <VCol cols="12">
                <AppTextField v-model="contractId" label="Contract ID" placeholder="Contract Identifier" />
              </VCol>

              <VCol cols="12">
                <AppTextField v-model="invoiceId" label="Invoice ID" placeholder="Invoice Identifier" />
              </VCol>

              <VCol cols="12">
                <div class="d-flex gap-4 justify-start pb-10">
                  <VBtn type="submit" color="primary" :loading="isSubmitting">
                    Add
                  </VBtn>
                  <VBtn color="error" variant="tonal" @click="resetForm">
                    Discard
                  </VBtn>
                </div>
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
      </PerfectScrollbar>
    </VCard>
  </VNavigationDrawer>
</template>
