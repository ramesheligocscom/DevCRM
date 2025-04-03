<script setup>
const editDialog = ref(false)
const deleteDialog = ref(false)
const addDialog = ref(false)
const isLoading = ref(false)

const defaultItem = ref({
  id: -1,
  avatar: '',
  name: '',
  post: '',
  email: '',
  city: '',
  startDate: '',
  salary: -1,
  age: '',
  experience: '',
  status: -1,
})

const editedItem = ref({...defaultItem.value})
const editedIndex = ref(-1)
const leadList = ref([])

// status options
const selectedOptions = [
  { text: 'Current', value: 1 },
  { text: 'Professional', value: 2 },
  { text: 'Rejected', value: 3 },
  { text: 'Resigned', value: 4 },
  { text: 'Applied', value: 5 },
]

// headers
const headers = [
  { title: 'NAME', key: 'name' },
  { title: 'EMAIL', key: 'email' },
  { title: 'DATE', key: 'startDate' },
  { title: 'SALARY', key: 'salary' },
  { title: 'AGE', key: 'age' },
  { title: 'STATUS', key: 'status' },
  { title: 'ACTIONS', key: 'actions', sortable: false },
]

const resolveStatusVariant = status => {
  if (status === 1) return { color: 'primary', text: 'Current' }
  else if (status === 2) return { color: 'success', text: 'Professional' }
  else if (status === 3) return { color: 'error', text: 'Rejected' }
  else if (status === 4) return { color: 'warning', text: 'Resigned' }
  else return { color: 'info', text: 'Applied' }
}

// API Actions
const fetchLeads = async () => {
  try {
    isLoading.value = true
    const { data } = await $api('/leads')
    leadList.value = data
  } catch (error) {
    console.error('Error fetching leads:', error)
  } finally {
    isLoading.value = false
  }
}

const editItem = item => {
  editedIndex.value = leadList.value.findIndex(lead => lead.id === item.id)
  editedItem.value = { ...item }
  editDialog.value = true
}

const deleteItem = item => {
  editedIndex.value = leadList.value.findIndex(lead => lead.id === item.id)
  editedItem.value = { ...item }
  deleteDialog.value = true
}

const addItem = () => {
  editedItem.value = { ...defaultItem.value }
  editedIndex.value = -1
  addDialog.value = true
}

const close = () => {
  editDialog.value = false
  addDialog.value = false
  editedIndex.value = -1
  editedItem.value = { ...defaultItem.value }
}

const closeDelete = () => {
  deleteDialog.value = false
  editedIndex.value = -1
  editedItem.value = { ...defaultItem.value }
}

const save = async () => {
  try {
    isLoading.value = true
    if (editedIndex.value > -1) {
      // Update existing lead
      const { data } = await $api.put(`/leads/${editedItem.value.id}`, editedItem.value)
      leadList.value[editedIndex.value] = data
    } else {
      // Create new lead
      const { data } = await $api.post('/leads', editedItem.value)
      leadList.value.push(data)
    }
    close()
  } catch (error) {
    console.error('Error saving lead:', error)
  } finally {
    isLoading.value = false
  }
}

const deleteItemConfirm = async () => {
  try {
    isLoading.value = true
    await $api.delete(`/leads/${editedItem.value.id}`)
    leadList.value.splice(editedIndex.value, 1)
    closeDelete()
  } catch (error) {
    console.error('Error deleting lead:', error)
  } finally {
    isLoading.value = false
  }
}

// Initialize data
onMounted(() => {
  fetchLeads()
})
</script>

<template>
  <!-- 👉 Datatable  -->
  <VDataTable
    :headers="headers"
    :items="leadList"
    :items-per-page="5"
  >
    <!-- full name -->
    <template #item.name="{ item }">
      <div class="d-flex align-center">
        <!-- avatar -->
        <VAvatar
          size="32"
          :color="item.avatar ? '' : 'primary'"
          :class="item.avatar ? '' : 'v-avatar-light-bg primary--text'"
          :variant="!item.avatar ? 'tonal' : undefined"
        >
          <VImg
            v-if="item.avatar"
            :src="item.avatar"
          />
          <span v-else>{{ avatarText(item.name) }}</span>
        </VAvatar>

        <div class="d-flex flex-column ms-3">
          <span class="d-block font-weight-medium text-high-emphasis text-truncate">{{ item.name }}</span>
          <small>{{ item.post }}</small>
        </div>
      </div>
    </template>

    <!-- status -->
    <template #item.status="{ item }">
      <VChip
        :color="resolveStatusVariant(item.status).color"
        size="small"
      >
        {{ resolveStatusVariant(item.status).text }}
      </VChip>
    </template>

    <!-- Actions -->
    <template #item.actions="{ item }">
      <div class="d-flex gap-1">
        <IconBtn @click="editItem(item)">
          <VIcon icon="tabler-edit" />
        </IconBtn>
        <IconBtn @click="deleteItem(item)">
          <VIcon icon="tabler-trash" />
        </IconBtn>
      </div>
    </template>
  </VDataTable>

  <!-- 👉 Edit Dialog  -->
  <VDialog
    v-model="editDialog"
    max-width="600px"
  >
    <VCard title="Edit Item">
      <VCardText>
        <div class="text-body-1 mb-6">
          Name: <span class="text-h6">{{ editedItem?.name }}</span>
        </div>
        <VRow>
          <!-- name -->
          <VCol
            cols="12"
            sm="6"
          >
            <AppTextField
              v-model="editedItem.name"
              label="Lead name"
            />
          </VCol>

          <!-- email -->
          <VCol
            cols="12"
            sm="6"
          >
            <AppTextField
              v-model="editedItem.email"
              label="Email"
            />
          </VCol>

          <!-- salary -->
          <VCol
            cols="12"
            sm="6"
          >
            <AppTextField
              v-model="editedItem.salary"
              label="Salary"
              prefix="$"
              type="number"
            />
          </VCol>

          <!-- age -->
          <VCol
            cols="12"
            sm="6"
          >
            <AppTextField
              v-model="editedItem.age"
              label="Age"
              type="number"
            />
          </VCol>

          <!-- start date -->
          <VCol
            cols="12"
            sm="6"
          >
            <AppTextField
              v-model="editedItem.startDate"
              label="Date"
            />
          </VCol>

          <!-- status -->
          <VCol
            cols="12"
            sm="6"
          >
            <AppSelect
              v-model="editedItem.status"
              :items="selectedOptions"
              item-title="text"
              item-value="value"
              label="Standard"
            />
          </VCol>
        </VRow>
      </VCardText>

      <VCardText>
        <div class="self-align-end d-flex gap-4 justify-end">
          <VBtn
            color="error"
            variant="outlined"
            @click="close"
          >
            Cancel
          </VBtn>
          <VBtn
            color="success"
            variant="elevated"
            @click="save"
          >
            Save
          </VBtn>
        </div>
      </VCardText>
    </VCard>
  </VDialog>

  <!-- 👉 Delete Dialog  -->
  <VDialog
    v-model="deleteDialog"
    max-width="500px"
  >
    <VCard title="Are you sure you want to delete this item?">
      <VCardText>
        <div class="d-flex justify-center gap-4">
          <VBtn
            color="error"
            variant="outlined"
            @click="closeDelete"
          >
            Cancel
          </VBtn>
          <VBtn
            color="success"
            variant="elevated"
            @click="deleteItemConfirm"
          >
            OK
          </VBtn>
        </div>
      </VCardText>
    </VCard>
  </VDialog>
</template>
