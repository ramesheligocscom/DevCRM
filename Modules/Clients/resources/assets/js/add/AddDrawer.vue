<script setup>
import _ from "lodash";
import { onMounted, ref } from "vue";
import { PerfectScrollbar } from "vue3-perfect-scrollbar";
import { toast } from "vue3-toastify";
import {
  emailRule,
  inputNumberRestrict,
  onlyAlphabetsRule,
  optionalRequiredRule,
  requiredRule,
  validateMobileNumber,
} from "../validations/validationRules";
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
    };

    const res = await $api(
      props.currentClient
        ? `/clients/${props.currentClient.id}?_method=PUT`
        : `/clients`,
      {
        method: "POST",
        body: payload,
      }
    );

    if (res?.status === 200) {
      toast.success(res?.message);

      // Close the modal and reset form
      emit("update:isDrawerOpen", false);

      // Add a delay before emitting submit to ensure backend processing
      await new Promise((resolve) => setTimeout(resolve, 1000));
      emit("submit");

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

const handleMobileInput = (event) => {
  client.value.phone = inputNumberRestrict(event.target.value, 10);
};

const convertToLowerCase = (event) => {
  client.value.email = event.target.value.toLowerCase();
};

const clientName = computed({
  get: () => (client.value.name ? client.value.name.split(" - (")[0] : ""), // Extract company name safely
  set: (newValue) => (client.value.name = newValue), // Allow editing and updating client.name
});

const statusOptions = [
  { text: "Active", value: "active" },
  { text: "Inactive", value: "inactive" },
  { text: "Pending", value: "pending" },
  // Add more options as needed
];

</script>

<template>
  <div>
    <div v-if="isDrawerOpen" class="backdrop"></div>
    <VNavigationDrawer permanent :width="500" location="end" class="scrollable-content"
      :model-value="props.isDrawerOpen" @update:model-value="handleDrawerModelValueUpdate">
      <AppDrawerHeaderSection :title="currentClient ? 'Edit Client' : 'Add Client'" @cancel="closeNavigationDrawer" />

      <VDivider />

      <PerfectScrollbar :options="{ wheelPropagation: false }">
        <VCard class="department_card">
          <VForm ref="refForm" v-model="valid" @submit.prevent="onSubmit">
            <VRow>
              <VCol cols="12">
                <AppTextField v-model="clientName" :rules="[...requiredRule]" label="Name" placeholder="Name"
                  autofocus />
              </VCol>
              <VCol cols="12">
                <AppTextField :rules="onlyAlphabetsRule" v-model="client.contact_person" label="Contact Person *"
                  placeholder="Contact Person" />
              </VCol>
              <VCol cols="12">
                <AppTextField v-model="client.contact_person_role" label="Contact Person Role"
                  placeholder="Contact Person Role" autofocus />
              </VCol>
              <VCol cols="12">
                <AppTextField v-model="client.email" :rules="emailRule" @input="convertToLowerCase" label="Email"
                  placeholder="Email" type="email" />
              </VCol>
              <VCol cols="12">
                <AppTextField v-model="client.phone" :rules="[optionalRequiredRule, ...validateMobileNumber]"
                  @input="handleMobileInput" label="Phone *" placeholder="Phone" />
              </VCol>
              <VCol cols="12">
                <VSelect v-model="client.status" :items="statusOptions" :rules="requiredRule" label="Status"
                  placeholder="Select Status *" item-title="text" item-value="value" clearable />
              </VCol>
              <VCol cols="12">
                <VSelect v-model="client.assigned_user" :items="[]" label="Assigned User"
                  placeholder="Select Assigned User *" item-title="name" item-value="id" clearable>
                  <template #selection="{ item }">
                    <span>{{ item.title }}</span>
                  </template>
                  <template #item="{ props, item }">
                    <VListItem v-bind="props" :title="item.raw.name" :subtitle="item.raw.email"></VListItem>
                  </template>
                </VSelect>
              </VCol>
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
</style>
