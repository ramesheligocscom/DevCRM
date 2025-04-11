<script setup>
import { ref, watch } from "vue";
import { useRoute } from "vue-router";
import { PerfectScrollbar } from "vue3-perfect-scrollbar";
import { toast } from "vue3-toastify";
import { VForm } from "vuetify/components/VForm";
import { VBtn } from "vuetify/lib/components/index.mjs";

const valid = ref(true);
const refForm = ref(false);
const route = useRoute();

const props = defineProps({
  isDrawerOpen: {
    type: Boolean,
    required: true,
  },
  currentItem: {
    type: Object,
    required: true,
  },
  type: {
    type: String,
    default: null,
    validator: (value) => ['lead', 'client'].includes(value)
  }
});
 
const followupItem = ref({
  lead_prospect: "",
  call_status: "",
  call_summary: "",
  lead_id: "",
  client_id: "",
});

const resetForm = () => {
  followupItem.value = {
    lead_prospect: "",
    call_status: "",
    call_summary: "",
    lead_id: props.type === 'lead' ? route.params.id : "",
    client_id: props.type === 'client' ? route.params.id : "",
  }
}


watch(
  () => props.isDrawerOpen,
  (val) => {
    if (val) {
      if (props.currentItem?.id) {
        followupItem.value = JSON.parse(JSON.stringify(props.currentItem))
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
watch(() => followupItem.value.call_status, (newStatus) => {
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
      lead_prospect: followupItem.value.lead_prospect,
      call_status: followupItem.value.call_status,
      call_summary: followupItem.value.call_summary,
      lead_id: followupItem.value.lead_id,
      client_id: followupItem.value.client_id,
    };

    const res = await $api(
      props.currentItem
        ? `/followup/${props.currentItem.id}`
        : `/followup`,
      {
        method: props.currentItem ? "PUT" : "POST",
        body: payload,
      }
    );

    if (res?.status === 200) {
      toast.success(res?.message || (props.currentItem ? "Follow Up updated successfully" : "Follow Up created successfully"));

      // Emit submit event with the response data for reload
      emit("submit", res.data);
      
      // Close the drawer first
      emit("update:isDrawerOpen", false);

      // Only reset form if it's a new entry, not an update
      if (!props.currentItem) {
        followupItem.value = {
          lead_prospect: "",
          call_status: "",
          call_summary: "",
          lead_id: "",
          client_id: "",
        };

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

  { text: "Pending", value: "pending" },
  { text: "Completed", value: "completed" },
  { text: "No answer", value: "no_answer" },
  { text: "Busy", value: "busy" },
  { text: "Failed", value: "failed" },
];
 
</script>

<template>
  <div>
    <div v-if="isDrawerOpen" class="backdrop"></div>
    <VNavigationDrawer permanent :width="500" location="end" class="scrollable-content"
      :model-value="props.isDrawerOpen" @update:model-value="handleDrawerModelValueUpdate">
      <AppDrawerHeaderSection :title="props.currentItem ? 'Edit Follow Up' : 'Add Follow Up'"
        @cancel="closeNavigationDrawer" />
      <VDivider />
      <PerfectScrollbar :options="{ wheelPropagation: false }">
        <VCard class="department_card">
          <VForm ref="refForm" v-model="valid" @submit.prevent="onSubmit">
            <VRow>
              <VCol cols="12">
                <VSelect v-model="followupItem.call_status" :items="statusOptions" :rules="[requiredValidator]" label="Call Status" placeholder="Select Status *"
                  item-title="text" item-value="value" clearable />
              </VCol>
              <VCol cols="12">
                <AppTextField v-model="followupItem.lead_prospect" label="Lead Prospect" />
              </VCol>
              <VCol cols="12">
                <AppTextField v-model="followupItem.call_summary" label="Call Summary" placeholder="Call Summary" autofocus />
              </VCol>
              <!-- <VCol cols="12">
                <VSelect v-model="followupItem.client_id" :items="[]" label="Client" placeholder="Select Client"
                  item-title="name" item-value="id" clearable>
                  <template #item="{ item }">
                    <VListItem :title="item.raw.name" :subtitle="item.raw.email" />
                  </template>
                </VSelect>
              </VCol>
              <VCol cols="12">
                <VSelect v-model="followupItem.lead_id" :items="[]" label="Lead" placeholder="Select Lead"
                  item-title="name" item-value="id" clearable>
                  <template #selection="{ item }">
                    <span>{{ item.title }}</span>
                  </template>
                  <template #item="{ props, item }">
                    <VListItem v-bind="props" :title="item.raw.name" :subtitle="item.raw.email"></VListItem>
                  </template>
                </VSelect>
              </VCol> -->

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
