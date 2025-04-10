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
  call_status: "",
  lead_prospect: "",
  call_summary: "",
  lead_id: "",
  client_id: "",
});

onMounted(() => {
  if (props.currentClient) {
    client.value = _.cloneDeep({
      call_status: props.currentClient.call_status,
      lead_prospect: props.currentClient.lead_prospect,
      call_summary: props.currentClient.call_summary,
      lead_id: props.currentClient.lead_id,
      client_id: props.currentClient.client_id,
    });
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
      call_status: client.value.call_status,
      lead_prospect: client.value.lead_prospect,
      call_summary: client.value.call_summary,
      lead_id: client.value.lead_id,
      client_id: client.value.client_id,
    };

    const res = await $api(
      props.currentClient
        ? `/followup/${props.currentClient.id}`
        : `/followup`,
      {
        method: props.currentClient ? "PUT" : "POST",
        body: payload,
      }
    );
console.log(res);
    
    // Check if we have a valid response with data and status
    if (res?.data) {
      // Get status from response data
      const status = res.status;
      
      // Check if status is success (200 or 201)
      if (status === 200 || status === 201) {
        toast.success(res?.message || (props.currentClient ? "Follow Up updated successfully" : "Follow Up created successfully"));

        // Emit submit event with the response data
        emit("submit", res.data);

        // Close the modal and reset form
        emit("update:isDrawerOpen", false);

        // Reset form data
        client.value = {
          call_status: "",
          lead_prospect: "",
          call_summary: "",
          lead_id: "",
          client_id: "",
        };

        // Reset form validation
        await nextTick(() => {
          refForm.value?.reset();
          refForm.value?.resetValidation();
        });
      } else {
        toast.error(res?.message || "An error occurred");
      }
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
  { text: "Completed", value: "completed" },
  { text: "Pending", value: "pending" },
  { text: "No Answer", value: "no_answer" },
  { text: "Busy", value: "busy" },
  { text: "Failed", value: "failed" }
];

const leadProspectOptions = [
  { text: "Lead", value: "lead" },
  { text: "Prospect", value: "prospect" }
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
      <AppDrawerHeaderSection :title="currentClient ? 'Edit Follow Up' : 'Add Follow Up'"
        @cancel="closeNavigationDrawer" />
      <VDivider />
      <PerfectScrollbar :options="{ wheelPropagation: false }">
        <VCard class="department_card">
          <VForm ref="refForm" v-model="valid" @submit.prevent="onSubmit">
            <VRow>
              <VCol cols="12">
                <VSelect v-model="client.call_status" :items="statusOptions" label="Call Status" placeholder="Select Call Status *"
                  item-title="text" item-value="value" clearable />
              </VCol>

              <VCol cols="12">
                <VSelect v-model="client.lead_prospect" :items="leadProspectOptions" label="Lead Prospect" placeholder="Select Lead Prospect *"
                  item-title="text" item-value="value" clearable />
              </VCol>

              <VCol cols="12">
                <AppTextField v-model="client.call_summary" label="Call Summary" placeholder="Enter call summary" autofocus />
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
                  <template #item="{ item }">
                    <VListItem :title="item.raw.name" :subtitle="item.raw.email" />
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
