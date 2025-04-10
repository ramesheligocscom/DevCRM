<script setup>
import AppTextField from "@/@core/components/app-form-elements/AppTextField.vue";
import _ from "lodash";
import { onMounted, ref } from "vue";
import { PerfectScrollbar } from "vue3-perfect-scrollbar";
import { toast } from "vue3-toastify";
import { VForm } from "vuetify/components/VForm";
import { VBtn } from "vuetify/lib/components/index.mjs";
const valid = ref(true);
const refForm = ref(false);



const props = defineProps({
  isDrawerOpen: {
    type: Boolean,
    required: true,
  },
  clients: {
    type: Array,
    default: [],
  },
  currentClient: {
    type: Object,
    default: null,
  },
});

const customer = ref({
  addresses: [{ address: "" }],
});

const client = ref({
  visit_time: "",
  visit_assignee: "",
  status: "",
  visit_notes: "",
  lead_id: "",
  client_id: "",
});

onMounted(() => {
  if (props.currentClient) {
    client.value = _.cloneDeep({
      visit_time: props.currentClient.visit_time,
      visit_assignee: props.currentClient.visit_assignee,
      status: props.currentClient.status,
      visit_notes: props.currentClient.visit_notes,
      lead_id: props.currentClient.lead_id,
      client_id: props.currentClient.client_id,
    });
    date.value = props.currentClient.visit_time;
  }
});

const emit = defineEmits(["update:isDrawerOpen", "submit"]);

// 👉 drawer close
const closeNavigationDrawer = () => {
  emit("update:isDrawerOpen", false);
  nextTick(() => {
    refForm.value?.reset();
    refForm.value?.resetValidation();
  });
};

const handleDrawerModelValueUpdate = (val) => {
  emit("update:isDrawerOpen", val);
};

const isLoading = ref(false);
let isSubmitting = false;

const onSubmit = async () => {
  if (isSubmitting) return;
  isSubmitting = true;

  let { valid, errors } = await refForm.value.validate();
  if (!valid) {
    isSubmitting = false;
    return false;
  }

  try {
    isLoading.value = true;

    const payload = {
      visit_time: date.value,
      visit_assignee: client.value.visit_assignee,
      status: client.value.status,
      visit_notes: client.value.visit_notes,
      lead_id: client.value.lead_id,
      client_id: client.value.client_id,
    };

    const res = await $api(
      props.currentClient
        ? `/sitevisit/${props.currentClient.id}`
        : `/sitevisit`,
      {
        method: props.currentClient ? "PUT" : "POST",
        body: payload,
      }
    );

    if (res?.status === 200) {
      toast.success(res?.message || (props.currentClient ? "Site visit updated successfully" : "Site visit created successfully"));

      // Close the modal and reset form
      emit("submit");
      emit("update:isDrawerOpen", false);

      await new Promise((resolve) => setTimeout(resolve, 500));

      // Reset form data
      client.value = {
        visit_time: "",
        visit_assignee: "",
        status: "",
        visit_notes: "",
        lead_id: "",
        client_id: "",
      };
      date.value = "";

      // Reset form validation
      await nextTick(() => {
        refForm.value?.reset();
        refForm.value?.resetValidation();
      });
    } else {
      toast.error(res?.message || "An error occurred");
    }
  } catch (err) {
    console.error("Error:", err);
    toast.error(err?._data?.message || "An unexpected error occurred");
  } finally {
    isSubmitting = false;
    isLoading.value = false;
  }
};


const statusOptions = [
  { text: "Scheduled", value: "scheduled" },
  { text: "Completed", value: "completed" },
  { text: "Canceled", value: "canceled" },
  { text: "Rescheduled", value: "rescheduled" },
];


const userOptions = ref([
  {
    id: '550e8400-e29b-41d4-a716-446655440001',
    name: 'John Smith',
    email: 'john@example.com'
  },
  {
    id: '550e8400-e29b-41d4-a716-446655440002',
    name: 'Sarah Johnson',
    email: 'sarah@example.com'
  },
  {
    id: '550e8400-e29b-41d4-a716-446655440003',
    name: 'Michael Brown',
    email: 'michael@example.com'
  },
  {
    id: '550e8400-e29b-41d4-a716-446655440004',
    name: 'Emily Davis',
    email: 'emily@example.com'
  },
  {
    id: '550e8400-e29b-41d4-a716-446655440005',
    name: 'David Wilson',
    email: 'david@example.com'
  }
])

const users = ref({
  assigned_user: null // This will store the selected UUID
})
const Status = ref("");
const date = ref('')
</script>

<template>
  <div>
    <div v-if="isDrawerOpen" class="backdrop"></div>
    <VNavigationDrawer permanent :width="500" location="end" class="scrollable-content"
      :model-value="props.isDrawerOpen" @update:model-value="handleDrawerModelValueUpdate">
      <AppDrawerHeaderSection :title="currentClient ? 'Edit Site Visit' : 'Add Site Visit'"
        @cancel="closeNavigationDrawer" />
      <VDivider />
      <PerfectScrollbar :options="{ wheelPropagation: false }">
        <VCard class="department_card">
          <VForm ref="refForm" v-model="valid" @submit.prevent="onSubmit">
            <VRow>
              <VCol cols="12">
                <AppDateTimePicker v-model="date" label="Date & Time" placeholder="Select date and time"
                  :config="{ enableTime: true, dateFormat: 'Y-m-d H:i' }" />
              </VCol>
              <VCol cols="12">
                <VSelect v-model="client.visit_assignee" :items="userOptions" label="Visit Assignee"
                  placeholder="Visit Assignee *" item-title="name" item-value="id" clearable>
                  <template #selection="{ item }">
                    <span>{{ item.title }}</span>
                  </template>
                  <template #item="{ props, item }">
                    <VListItem v-bind="props" :title="item.raw.name" :subtitle="item.raw.email"></VListItem>
                  </template>
                </VSelect>
              </VCol>
              <VCol cols="12">
                <VSelect v-model="client.status" :items="statusOptions" label="Status" placeholder="Select Status *"
                  item-title="text" item-value="value" clearable />
              </VCol>
              <VCol cols="12">
                <AppTextField v-model="client.visit_notes" label="Visit Notes" placeholder="Visit Notes" autofocus />
              </VCol>
              <VCol cols="12">
                <VSelect v-model="client.client_id" :items="userOptions" label="Client" placeholder="Select Client"
                  item-title="name" item-value="id" clearable>
                  <template #item="{ item }">
                    <VListItem :title="item.raw.name" :subtitle="item.raw.email" />
                  </template>
                </VSelect>
              </VCol>
              <VCol cols="12">
                <VSelect v-model="client.lead_id" :items="userOptions" label="Lead" placeholder="Select Lead"
                  item-title="name" item-value="id" clearable>
                  <template #selection="{ item }">
                    <span>{{ item.title }}</span>
                  </template>
                  <template #item="{ props, item }">
                    <VListItem v-bind="props" :title="item.raw.name" :subtitle="item.raw.email"></VListItem>
                  </template>
                </VSelect>
              </VCol>

              <VCol cols="12">
                <VBtn type="submit" class="me-3" :loading="isLoading">
                  {{ currentClient ? "Update" : "Submit" }}
                </VBtn>
                <VBtn variant="tonal" color="secondary" @click="closeNavigationDrawer">
                  Cancel
                </VBtn>
              </VCol>
            </VRow>
          </VForm>
        </VCard>
      </PerfectScrollbar>
    </VNavigationDrawer>
  </div>
</template>

<style scoped>
.department_card {
  padding: 20px;
}

.small-textarea {
  flex-grow: 1;
}

.addresAdd {
  cursor: pointer;
  padding: 4px;
}
</style>
