<script setup>
const props = defineProps({
  currentItem: {
    type: Object,
    required: true,
  },
  endpoint: {
    type: String,
    required: true,
  },
  confirmationQuestion: {
    type: String,
    required: true,
  },
  isDialogVisible: {
    type: Boolean,
    required: true,
  },
})

const emit = defineEmits([
  'update:isDialogVisible',
  'confirm',
])


const updateModelValue = val => {
  emit('update:isDialogVisible', val)
}

const onConfirmation = async () => {
  try {
    await $api(`${props.endpoint}`, { method: "DELETE" })
    emit('confirm', true)
    emit('submit')
    updateModelValue(false)
  } catch (error) {
    console.error("Failed to delete item:", error)
    // Show error toast
    toast.error(error.response?.data?.message || 'Failed to delete item')
    updateModelValue(false)
  }
}


const onCancel = () => {
  emit('confirm', false)
  emit('update:isDialogVisible', false)
}
</script>

<template>
  <!-- 👉 Confirm Dialog -->
  <VDialog max-width="500" :model-value="props.isDialogVisible" @update:model-value="updateModelValue">
    <VCard class="text-center px-10 py-6">
      <VCardText>
        <VBtn icon variant="outlined" color="warning" class="my-4"
          style=" block-size: 88px;inline-size: 88px; pointer-events: none;">
          <span class="text-5xl">!</span>
        </VBtn>

        <h6 class="text-lg font-weight-medium">
          {{ props.confirmationQuestion }}
        </h6>
      </VCardText>

      <VCardText class="d-flex align-center justify-center gap-2">
        <VBtn variant="elevated" @click="onConfirmation">
          Confirm
        </VBtn>
        <VBtn color="secondary" variant="tonal" @click="onCancel">
          Cancel
        </VBtn>
      </VCardText>
    </VCard>
  </VDialog>
</template>
