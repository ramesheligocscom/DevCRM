<script setup>
import { ref, watch } from "vue";
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
  currentItem: {
    type: Object,
    required: true,
  },
});
 

const siteVisitItem = ref({
  visit_time: "",
  visit_assignee: "",
  status: "",
  visit_notes: "",
  lead_id: "",
  client_id: "",
});

const resetForm = () => {
  siteVisitItem.value = {
    visit_time: "",
    visit_assignee: "",
    status: "",
    visit_notes: "",
    lead_id: "",
    client_id: "",
  }
}


watch(
  () => props.isDrawerOpen,
  (val) => {
    if (val) {
      if (props.currentItem?.id) {
        siteVisitItem.value = JSON.parse(JSON.stringify(props.currentItem))
        date.value = siteVisitItem.value.visit_time;
      } else {
        resetForm()
      }
    }
  }
)

 

const emit = defineEmits(["update:isDrawerOpen", "submit", "statusChange"]);

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

// Add watch on status changes
watch(() => siteVisitItem.value.status, (newStatus) => {
  if (newStatus) {
    // Emit status change event to parent
    emit("statusChange", newStatus);
  }
});

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
      visit_assignee: siteVisitItem.value.visit_assignee,
      status: siteVisitItem.value.status,
      visit_notes: siteVisitItem.value.visit_notes,
      lead_id: siteVisitItem.value.lead_id,
      client_id: siteVisitItem.value.client_id,
    };

    const res = await $api(
      props.currentItem
        ? `/sitevisit/${props.currentItem.id}`
        : `/sitevisit`,
      {
        method: props.currentItem ? "PUT" : "POST",
        body: payload,
      }
    );

    if (res?.status === 200) {
      toast.success(res?.message || (props.currentItem ? "Site visit updated successfully" : "Site visit created successfully"));

      // Emit submit event with the response data for reload
      emit("submit", res.data);
      
      // Close the drawer first
      emit("update:isDrawerOpen", false);

      // Only reset form if it's a new entry, not an update
      if (!props.currentItem) {
        siteVisitItem.value = {
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
  { text: "Scheduled", value: "scheduled" },
  { text: "Completed", value: "completed" },
  { text: "Canceled", value: "canceled" },
  { text: "Rescheduled", value: "rescheduled" },
];
 
const date = ref('')
</script>

<template>
  <div>
    <div v-if="isDrawerOpen" class="backdrop"></div>
    <VNavigationDrawer permanent :width="500" location="end" class="scrollable-content"
      :model-value="props.isDrawerOpen" @update:model-value="handleDrawerModelValueUpdate">
      <AppDrawerHeaderSection :title="props.currentItem ? 'Edit Site Visit' : 'Add Site Visit'"
        @cancel="closeNavigationDrawer" />
      <VDivider />
      <PerfectScrollbar :options="{ wheelPropagation: false }">
        <VCard class="department_card">
          <VForm ref="refForm" v-model="valid" @submit.prevent="onSubmit">
            <VRow>
              <VCol cols="12">
                <AppDateTimePicker v-model="date" label="Date & Time" placeholder="Select date and time"
                :config="{ enableTime: true, dateFormat: 'Y-m-d H:i' }"
                 :rules="[requiredValidator]" />
              </VCol>
              <VCol cols="12">
                <VSelect v-model="siteVisitItem.status" :items="statusOptions" :rules="[requiredValidator]" label="Status" placeholder="Select Status *"
                  item-title="text" item-value="value" clearable />
              </VCol>
              <VCol cols="12">
                <VSelect v-model="siteVisitItem.visit_assignee" :items="[]" label="Visit Assignee"
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
                <AppTextField v-model="siteVisitItem.visit_notes" label="Visit Notes" placeholder="Visit Notes" autofocus />
              </VCol>
              <VCol cols="12">
                <VSelect v-model="siteVisitItem.client_id" :items="[]" label="Client" placeholder="Select Client"
                  item-title="name" item-value="id" clearable>
                  <template #item="{ item }">
                    <VListItem :title="item.raw.name" :subtitle="item.raw.email" />
                  </template>
                </VSelect>
              </VCol>
              <VCol cols="12">
                <VSelect v-model="siteVisitItem.lead_id" :items="[]" label="Lead" placeholder="Select Lead"
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
                  {{ props.currentItem ? "Update" : "Submit" }}
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
  padding: 4px;
  cursor: pointer;
}
</style>
