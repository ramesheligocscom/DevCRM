<script setup>
import AppTextField from "@/@core/components/app-form-elements/AppTextField.vue";
import _ from "lodash";
import { onMounted, ref } from "vue";
import { PerfectScrollbar } from "vue3-perfect-scrollbar";
import { toast } from "vue3-toastify";
import { VForm } from "vuetify/components/VForm";
import { VBtn } from "vuetify/lib/components/index.mjs";
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

const fetchMultipleAddress = async (id) => {
  const data = await $api(`/clients/multiple-address/${id}`);
  console.log(data.data);
  if (data?.data) {
    customer.value.addresses = Object.values(data.data).map((addr) => ({
      address: addr,
    }));
  }
};
watch(
  () => props.currentClient,
  async (newValue) => {
    if (newValue) {
      console.log(newValue.id);
      client.value = _.cloneDeep(newValue);
      await fetchMultipleAddress(newValue.id);
      // console.log(response);
      // if (response) {
      //   customer.value.addresses = Object.values(response).map(addr => ({ address: addr }));
      // }
    } else {
      client.value = {
        name: "",
      };
    }
  },
  { immediate: true } // Trigger immediately on mount
);

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

const formatAddress = () => {
  if (client.value.address != "") {
    const words = client.value?.address?.split(/\s+/);
    const lines = [];

    for (let i = 0; i < words?.length; i += 4) {
      lines.push(words.slice(i, i + 4).join(" "));
    }
    client.value.address = lines.join("\n");
  }
};

const addAddress = () => {
  customer.value.addresses.push({ address: "" });
};

const removeAddress = (index) => {
  customer.value.addresses.splice(index, 1);
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
                <VSelect v-model="client.assigned_user" :items="userOptions" :rules="requiredRule" label="Assigned User"
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
                <div v-for="(address, index) in customer.addresses" :key="index" class="mb-4 d-flex align-center">
                  <app-textarea v-model="customer.addresses[index].address" label="Address *" :rules="requiredRule"
                    rows="1" auto-grow class="small-textarea" placeholder="Address" />
                  <v-btn icon @click="addAddress" v-if="index === customer.addresses.length - 1" class="mt-5 ml-2"
                    variant="tonal" size="small">
                    <v-icon icon="tabler-plus" />
                  </v-btn>
                  <v-btn icon @click="removeAddress(index)" v-if="customer.addresses.length > 1" class="mt-5 mx-3"
                    variant="tonal" size="small">
                    <v-icon icon="tabler-minus" />
                  </v-btn>
                </div>
              </VCol>
              <VCol cols="12">
                <VBtn type="submit" class="me-3">
                  {{ currentClient ? "Update" : "Submit" }}
                </VBtn>

                <!-- <VBtn v-if="
                  ((!currentClient && $can('client', 'create-client')) ||
                    (currentClient && $can('client', 'edit-client'))) &&
                  !isLoading
                " type="submit" class="me-3">
                  {{ currentClient ? "Update" : "Submit" }}
                </VBtn> -->
                <!-- <VBtn class="me-3" v-else>
                  <v-progress-circular color="light" :width="4" :size="20" indeterminate
                    class="mr-2"></v-progress-circular>{{ currentClient ? "Update" : "Submit" }}
                </VBtn> -->
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
