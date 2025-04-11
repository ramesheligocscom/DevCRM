<template>
  <VDialog
    :width="$vuetify.display.smAndDown ? 'auto' : 900"
    :model-value="props.isDialogVisible"
    @update:model-value="dialogModelValueUpdate"
    scrollable
    persistent
  >
    <DialogCloseBtn @click="dialogModelValueUpdate(false)" />

    <VCard class="pa-sm-10 pa-2">
      <VForm ref="refForm" v-model="valid" @submit.prevent="onSubmit">
        <VRow>
          <VCol cols="12">
            <AppTextField
              v-model="credentials.password"
              label="Password"
              placeholder="············"
              :rules="[...requiredRule, ...minLengthRule(8)]"
              :type="isPasswordVisible ? 'text' : 'password'"
              :error-messages="errors.password"
              :append-inner-icon="isPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"
              @click:append-inner="isPasswordVisible = !isPasswordVisible"
            />
          </VCol>

          <VCol cols="12">
            <AppTextField
              v-model="credentials.confirmPassword"
              label="Confirm Password"
              placeholder="············"
              :rules="[...requiredRule, ...minLengthRule(8), ...confirmPasswordMatchRule(credentials.password)]"
              :type="isPasswordVisible ? 'text' : 'password'"
              :error-messages="errors.confirmPassword"
              autocomplete="on"
              :append-inner-icon="isPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"
              @click:append-inner="isPasswordVisible = !isPasswordVisible"
            />
          </VCol>

          <VCol cols="12">
            <VBtn block type="submit" :loading="loading">
              Save
            </VBtn>
          </VCol>
        </VRow>
      </VForm>
    </VCard>
  </VDialog>
</template>

<script setup>
import { confirmPasswordMatchRule, minLengthRule, requiredRule } from '@/validations/validationRules'
import { ref } from 'vue'
import { toast } from 'vue3-toastify'

const props = defineProps({
  user_id: { type: [String, Number], required: false },
  isDialogVisible: { type: Boolean, required: true },
})

const emit = defineEmits(['submit', 'update:isDialogVisible'])

const refForm = ref(null)
const valid = ref(true)
const isPasswordVisible = ref(false)
const loading = ref(false)

const credentials = ref({
  password: '',
  confirmPassword: '',
})

const errors = ref({
  password: '',
  confirmPassword: '',
})

const dialogModelValueUpdate = (val) => {
  resetForm()
  emit('update:isDialogVisible', val)
}

const resetForm = () => {
  credentials.value = { password: '', confirmPassword: '' }
  errors.value = { password: '', confirmPassword: '' }
  if (refForm.value) refForm.value.resetValidation()
  loading.value = false
}

const onSubmit = async () => {
  if (loading.value) return

  const { valid: isValid } = await refForm.value.validate()

  if (!isValid) {
    toast.error('Please correct the highlighted errors.')
    return
  }

  if (credentials.value.password !== credentials.value.confirmPassword) {
    errors.value.confirmPassword = 'Passwords do not match'
    toast.error('Passwords do not match')
    return
  }

  loading.value = true

  try {
    if (props.user_id) {
      const response = await $api(`/update-password/${props.user_id}`, {
        method: 'POST',
        body: credentials.value,
      })

      if (response.status) {
        toast.success(response.message || 'Password updated successfully')
        emit('submit', props.user_id)
        dialogModelValueUpdate(false)
      } else {
        toast.error(response.message || 'Something went wrong')
      }
    }
  } catch (err) {
    toast.error('Server error occurred')
    console.error(err)
  } finally {
    loading.value = false
  }
}
</script>
