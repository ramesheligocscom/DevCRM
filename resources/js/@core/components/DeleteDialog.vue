<template>
  <!-- 👉 Delete Dialog -->
  <VDialog max-width="500" :model-value="props.isDialogVisible" @update:model-value="updateModelValue" scrollable
    persistent>
    <VCard class="text-center px-10 py-6">
      <VCardText>
        <VBtn icon variant="outlined" color="warning" class="my-4"
          style=" block-size: 88px;inline-size: 88px; pointer-events: none;">
          <span class="text-5xl">!</span>
        </VBtn>

        <h6 class="text-lg font-weight-medium">
          {{ props.confirmationQuestion }}
        </h6>

        <label v-if="props.action != 'restore'" class="d-block mt-4 mb-2 text-body-2">
          Type in <strong>"DELETE"</strong> to confirm
        </label>

        <VTextField v-if="props.action != 'restore'" v-model="confirmationText" placeholder="Type 'DELETE' to confirm"
          :error-messages="errorMessage" dense outlined hide-details="auto" />
      </VCardText>

      <VCardText class="d-flex align-center justify-center gap-2">
        <VBtn variant="elevated" @click="onConfirmation">
          Confirm
        </VBtn>
        <VBtn color="secondary" variant="tonal" @click="onCancel"> Cancel </VBtn>
      </VCardText>
    </VCard>
  </VDialog>
</template>
<script setup>
import { ref, watch } from 'vue';
import { toast } from 'vue3-toastify';

const props = defineProps({
  currentItem: { type: Object, required: true, },
  endpoint: { type: String, required: true, },
  confirmationQuestion: { type: String, required: true, },
  action: { type: String, required: false, },
  isDialogVisible: { type: Boolean, required: true, },
})

const confirmationText = ref('');
const errorMessage = ref('');

const emit = defineEmits(['update:isDialogVisible', 'confirm',])

watch([() => confirmationText.value],
  () => {
    if (confirmationText.value !== 'DELETE') {
      errorMessage.value = "You must type 'DELETE' exactly to confirm."
      return
    }
    errorMessage.value = ''
  }
);

const updateModelValue = val => {
  emit('update:isDialogVisible', val)
}

const onConfirmation = async () => {
  if (confirmationText.value !== 'DELETE' && props.action != "restore") {
    errorMessage.value = "You must type 'DELETE' exactly to confirm."
    return toast.error(errorMessage.value);
  }
  try {
    const response = await $api(`${props.endpoint}`, { method: "DELETE", body: { delete_text: confirmationText.value ?? null, action: props.action ?? 'force_delete' }, })
    toast.success(response.message);
    emit('confirm', true)
    emit('submit', response)
    updateModelValue(false)
  } catch (error) {
    console.error('Error deleting user:', error);
    toast.error(error?._data?.message ?? "Delete Request Failed!");
  }
}

const onCancel = () => {
  emit('confirm', false)
  emit('update:isDialogVisible', false)
}
</script>
