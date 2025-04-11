<template>
  <div class="dynamic-form">
    <div v-if="isLoading" class="loading-overlay">
      <div class="spinner"></div>
    </div>
    
    <form @submit.prevent="handleSubmit" class="form-container">
      <slot name="form-content"></slot>
      
      <div class="form-actions">
        <button type="button" @click="closeForm" class="btn btn-secondary">
          Close
        </button>
        <button type="submit" class="btn btn-primary" :disabled="isLoading">
          {{ submitButtonText }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'

const props = defineProps({
  submitButtonText: {
    type: String,
    default: 'Submit'
  },
  loadDataOnMount: {
    type: Boolean,
    default: true
  }
})

const emit = defineEmits(['close', 'submit', 'data-loaded'])

const isLoading = ref(false)

const loadData = async () => {
  if (!props.loadDataOnMount) return
  
  try {
    isLoading.value = true
    // Emit event for parent to handle data loading
    await emit('data-loaded')
  } catch (error) {
    console.error('Error loading form data:', error)
  } finally {
    isLoading.value = false
  }
}

const handleSubmit = async () => {
  try {
    isLoading.value = true
    await emit('submit')
  } catch (error) {
    console.error('Error submitting form:', error)
  } finally {
    isLoading.value = false
  }
}

const closeForm = () => {
  emit('close')
}

onMounted(() => {
  loadData()
})
</script>

<style scoped>
.dynamic-form {
  position: relative;
  width: 100%;
}

.loading-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(255, 255, 255, 0.8);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #f3f3f3;
  border-top: 4px solid #3498db;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

.form-container {
  padding: 1rem;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 1rem;
}

.btn {
  padding: 0.5rem 1rem;
  border-radius: 0.25rem;
  border: none;
  cursor: pointer;
  font-weight: 500;
}

.btn-primary {
  background-color: #3498db;
  color: white;
}

.btn-secondary {
  background-color: #95a5a6;
  color: white;
}

.btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style> 
