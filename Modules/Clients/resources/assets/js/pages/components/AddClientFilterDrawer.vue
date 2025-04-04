<script setup>
import { useConfigStore } from "@core/stores/config";
import { v4 as uuid } from "uuid"; // Import the uuid library
import { onMounted } from "vue";
import { toast } from "vue3-toastify";
import { useTheme } from "vuetify";

const vuetifyTheme = useTheme();
const configStore = useConfigStore();
const selectedTheme = ref([configStore.theme]);
const isDarkTheme = ref([configStore.theme]);
const currentTheme = vuetifyTheme.current.value.colors;

const props = defineProps({
  isDrawerOpen: {
    type: Boolean,
    required: true,
  },

  client_id: {
    type: String,
    required: true,
  },
});

const formData = ref({
  call_status: null,
  lead_prospect: "Cold",
  call_summary: "",
  visit_site: "no",
  visit_technician: "",
  next_call_time: "",
  visit_time: "",
});
const refForm = ref(false);

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

const searchcall_status = ref("");

const call_statuss = ref([
  { id: uuid(), value: "Call Picked", text: "Call Picked" },
  { id: uuid(), value: "Switched off", text: "Switched off" },
  { id: uuid(), value: "Not Reachable", text: "Not Reachable" },
  { id: uuid(), value: "Not Answering", text: "Lead Answering" },
  { id: uuid(), value: "Number does not exist", text: "Number does not exist" },
  { id: uuid(), value: "Lead Decline", text: "Lead Decline" },
]);

const lead_prospects = ref([
  { id: uuid(), value: "Hot", text: "Hot" },
  { id: uuid(), value: "Warm", text: "Warm" },
  { id: uuid(), value: "Cold", text: "Cold" },
]);

const employee = ref();
const dropdownUserList = async () => {
  try {
    const response = await $api(`/dropdown-user-list`);
    employee.value = response.data || [];
  } catch (error) {
    console.error("dropdownUserList:", error);
  }
};

const followUpHistory = ref([]);
let isSubmitting = false;
const isLoading = ref(false);

const onSubmit = async () => {
  if (isSubmitting) return;
  isSubmitting = true;
  isLoading.value = true;
  let { valid, errors } = await refForm.value.validate();
  if (!valid) {
    isSubmitting = false;
    isLoading.value = false;
    return false;
  }

  formData.value = {
    ...formData.value,
    client_id: props.client_id,
  };

  try {
    // Make the API call
    const res = await $api(`/client-follow-up`, {
      method: "POST",
      body: formData.value,
    });
    console.log("response", res);

    if (res?.success) {
      toast.success(res?.message);

      // Close the modal and reset form
      emit("submit");
      emit("update:isDrawerOpen", false);
      // closeNavigationDrawer();
      await new Promise((resolve) => setTimeout(resolve, 200));

      // Reset form data
      formData.value = {
        call_status: null,
        lead_prospect: null,
        call_summary: "",
        visit_site: "no",
        visit_technician: "",
        next_call_time: "",
        visit_time: "",
      };

      await nextTick(() => {
        refForm.value?.reset();
        refForm.value?.resetValidation();
      });
      isLoading.value = false;
    } else {
      // Show error message
      toast.error(res?.message || "Something went wrong");
      isLoading.value = false;
    }
  } catch (err) {
    // Handle errors and show toast
    console.error("Error:", err);
    isLoading.value = false;
    toast.error(err?._data?.message || "An unexpected error occurred");
  } finally {
    // Always unlock submitting state
    isSubmitting = false;
    isLoading.value = false;
  }
};

const filteredcall_statuss = computed(() => {
  if (!searchcall_status.value) {
    return call_statuss.value;
  }
  return call_statuss.value.filter((item) =>
    item.text.toLowerCase().includes(searchcall_status.value.toLowerCase())
  );
});

const lead_search = ref();
const leadType_search = ref();
const getleadvalue = (event) => {
  lead_search.value = event.target.value;
};

const getleadTypevalue = (event) => {
  leadType_search.value = event.target.value;
};

// console.log('selectedTheme --', selectedTheme);
const backgroundColor = ref("");
const textColorDate = ref("");

const SetDateRangeColor = () => {
  if (selectedTheme.value == "dark") {
    backgroundColor.value = "#222222";
    textColorDate.value = "#c3cddf";
  } else {
    backgroundColor.value = "#c3cddf";
    textColorDate.value = "#222222";
  }
};

const currentDateTime = new Date();

// Format function to `YYYY-MM-DDTHH:MM` for `<input type="datetime-local">`
const formatDateTime = (date) => {
  return date.toISOString().slice(0, 16);
};

// Set the minimum date-time (current date & time)
const minDateTime = ref(formatDateTime(currentDateTime));

// Watch for date selection and disable past times when selecting today
const updateMinTime = computed(() => {
  if (!formData.value.next_call_time) return minDateTime.value;

  const selectedDate = new Date(formData.value.next_call_time);
  const today = new Date();

  // If selected date is today, keep min time constraint
  return selectedDate.toDateString() === today.toDateString()
    ? formatDateTime(today)
    : "";
});

onMounted(() => {
  dropdownUserList();
});
</script>

<template>
  <div>
    <div v-if="isDrawerOpen" class="backdrop"></div>
    <VNavigationDrawer permanent :width="700" location="end" class="scrollable-content"
      :model-value="props.isDrawerOpen" @update:model-value="handleDrawerModelValueUpdate">
      <AppDrawerHeaderSection title="Add New Follow Up" @cancel="closeNavigationDrawer" />
      <VDivider class="mb-3" />
      <PerfectScrollbar :options="{ wheelPropagation: false }">
        <VForm class="pa-5" ref="refForm" @submit.prevent="onSubmit">
          <VRow>
            <VCol cols="12">
              <v-autocomplete :items="filteredcall_statuss" v-model="formData.call_status" item-title="text"
                item-value="value" label="Call Status *" :search-input.sync="searchcall_status"
                @input="getleadTypevalue($event)" dense chips small-chips>
                <template v-slot:no-data>
                  <v-btn v-if="leadType_search" @click="
                    filteredcall_statuss.push(leadType_search);
                  formData.call_status = leadType_search;
                  leadType_search = '';
                  ">
                    Add: {{ leadType_search }}
                  </v-btn>
                </template>
              </v-autocomplete>
            </VCol>

            <VCol cols="12">
              <v-autocomplete :items="lead_prospects" v-model="formData.lead_prospect" item-title="text"
                item-value="value" label="Client Prospect *" @input="getleadvalue($event)" dense chips small-chips>
                <template v-slot:no-data>
                  <v-btn v-if="lead_search" @click="
                    lead_prospects.push(lead_search);
                  formData.lead_prospect = lead_search;
                  lead_search = '';
                  ">
                    Add: {{ lead_search }}
                  </v-btn>
                </template>
              </v-autocomplete>

              <!-- <v-select :items="lead_prospects" v-model="formData.lead_prospect" item-title="text" item-value="value"
                  label="Lead Prospect *" /> -->
            </VCol>

            <VCol cols="12">
              <v-textarea v-model="formData.call_summary" label="Call Summary *" rows="1" auto-grow />
            </VCol>

            <VCol cols="12">
              <AppTextField v-model="formData.next_call_time" type="datetime-local" class="w-100"
                placeholder="Next Call Time *" :min="minDateTime" />
            </VCol>

            <VCol cols="12">
              <div class="align-center d-flex">
                <span>Need To Visit Site</span>
                <VRadioGroup v-model="formData.visit_site" inline class="ml-5">
                  <VRadio label="Yes" value="yes" />
                  <VRadio label="No" value="no" />
                </VRadioGroup>
              </div>
            </VCol>

            <VCol cols="12" v-if="formData.visit_site == 'yes'">
              <v-select :items="employee" v-model="formData.visit_technician" item-title="name" item-value="id"
                label="Select employee to Visit" />
            </VCol>

            <VCol cols="12" v-if="formData.visit_site == 'yes'">
              <AppTextField v-model="formData.visit_time" type="datetime-local" class="w-100"
                placeholder="Visit Scheduling Time *" :min="minDateTime" />
            </VCol>
          </VRow>

          <VRow>
            <VCol class="d-flex align-center gap-2" cols="12">
              <VBtn type="submit" color="primary">Submit</VBtn>
            </VCol>
          </VRow>
        </VForm>
      </PerfectScrollbar>
    </VNavigationDrawer>
  </div>
</template>
<style>
.el-date-picker.el-picker-panel {
  background: #25293c !important;
  color: #c3cddf !important;
}

.el-picker-panel__footer {
  background: #25293c !important;
  color: #c3cddf !important;
}

.el-input {
  --el-input-text-color: #c3cddf !important;
  --el-input-border-color: #c3cddf !important;
  --el-input-bg-color: #25293c !important;
}

.el-button {
  --el-button-bg-color: #25293c !important;
  --el-button-text-color: #c3cddf !important;
}
</style>
