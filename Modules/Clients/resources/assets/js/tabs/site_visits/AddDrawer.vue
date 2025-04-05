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

import dayjs from 'dayjs';
import { watch } from 'vue';

const visit_time = ref('')
const menu = ref(false)

// Optional: watch and format the value when date changes
watch(visit_time, (val) => {
  if (val) {
    visit_time.value = dayjs(val).format('YYYY-MM-DD')
  }
})


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
  name: "",
  contact_person: "",
  email: "",
  phone: "",
  assign_to: "Unassigned",
});

onMounted(() => {
  if (props.currentClient?.name) {
    client.value = _.cloneDeep(props.currentClient);
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
      ...client.value,
      addresses: customer.value.addresses,
    };

    const res = await $api(
      props.currentClient
        ? `api/clients/${props.currentClient.id}?_method=PUT`
        : `api/clients`,
      {
        method: "POST",
        body: payload,
      }
    );
    // console.log("response", res);

    if (res?.status === 200) {
      toast.success(res?.message);

      // Close the modal and reset form
      emit("submit");
      emit("update:isDrawerOpen", false);

      await new Promise((resolve) => setTimeout(resolve, 500));

      // Reset form data
      client.value = {
        name: "",
        contact_person: "",
        email: "",
        phone: "",
        assign_to: "Unassigned",
      };

      // Reset form validation
      await nextTick(() => {
        refForm.value?.reset();
        refForm.value?.resetValidation();
      });
      isLoading.value = false;
    } else {
      toast.error(res?.message);
      isLoading.value = false;
    }
  } catch (err) {
    // Handle errors and show toast
    console.error("Error:", err);
    isLoading.value = false;
    toast.error(err?._data.message || "An unexpected error occurred");
  } finally {
    // Always unlock submitting state
    isSubmitting = false;
    isLoading.value = false;
  }

  isSubmitting = false;
};


const statusOptions = [
  { text: "Active", value: "active" },
  { text: "Inactive", value: "inactive" },
  { text: "Pending", value: "pending" },
  // Add more options as needed
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
</script>

<template>
  <div>
    <div v-if="isDrawerOpen" class="backdrop"></div>
    <VNavigationDrawer permanent :width="500" location="end" class="scrollable-content"
      :model-value="props.isDrawerOpen" @update:model-value="handleDrawerModelValueUpdate">
      <AppDrawerHeaderSection :title="currentClient ? 'Edit Sites Visit' : 'Add Sites Visit'"
        @cancel="closeNavigationDrawer" />
      <VDivider />
      <PerfectScrollbar :options="{ wheelPropagation: false }">
        <VCard class="department_card">
          <VForm ref="refForm" v-model="valid" @submit.prevent="onSubmit">
            <VRow>
              <VCol cols="12">
                <VMenu v-model="menu" :close-on-content-click="false" transition="scale-transition" offset-y
                  min-width="auto">
                  <template #activator="{ props }">
                    <AppTextField v-model="visit_time" label="Visit Time" placeholder="Visit Time" v-bind="props"
                      readonly prepend-inner-icon="tabler-calendar" />
                  </template>

                  <VDatePicker v-model="visit_time" @input="menu = false" />
                </VMenu>
              </VCol>
              <VCol cols="12">
                <VSelect v-model="client.visit_assignee" :items="userOptions" label="Visit Assignee"
                  placeholder="visit_assignee *" item-title="name" item-value="id" clearable>
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
                <AppTextField v-model="visit_time" label="Visit Notes" placeholder=" Visit Notes" autofocus />
              </VCol>
              <VCol cols="12">
                <VSelect v-model="client.client_id" :items="userOptions" label="Client" placeholder="Select Client"
                  item-title="name" item-value="id" clearable>
                  <!-- Simplified item template -->
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


              <!-- <VCol cols="12">
                <VSelect v-model="client.assigned_user" :items="userOptions" label="Assigned User"
                  placeholder="Select Assigned User *" item-title="name" item-value="id" clearable>
                  <template #selection="{ item }">
                    <span>{{ item.title }}</span>
                  </template>
                  <template #item="{ props, item }">
                    <VListItem v-bind="props" :title="item.raw.name" :subtitle="item.raw.email"></VListItem>
                  </template>
                </VSelect>
              </VCol> -->

              <VCol cols="12">
                <VBtn type="submit" class="me-3">
                  {{ currentClient ? "Update" : "Submit" }}
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
