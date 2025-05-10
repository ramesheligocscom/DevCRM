<template>
  <div>
    <VNavigationDrawer :permanent="true" :width="500" location="end" class="scrollable-content"
      :model-value="props.isDialogVisible" @update:model-value="handleDrawerModelValueUpdate" :scrim="false"
      :close-on-back="false" disable-resize-watcher>
      <AppDrawerHeaderSection :title="currentInfo ? 'Edit Status' : 'Add Status'" @cancel="closeNavigationDrawer" />
      <VDivider />

      <PerfectScrollbar :options="{ wheelPropagation: false }">
        <VCard class="department_card">
          <VForm ref="refForm" v-model="valid" @submit.prevent="onSubmit">
            <VRow>
              <!-- Name -->
              <VCol cols="12">
                <AppTextField v-model="page_status.status_text" :rules="requiredRule" label="Name *" placeholder="Name"
                  autofocus />
              </VCol>

              <!-- Pages -->
              <VCol cols="12">
                <AppSelect :items="pageList" v-model="page_status.status_for" label="Select Page"
                  placeholder="Select pages" class="" multiple chips closable-chips :rules="requiredRule"
                  :disabled="props.currentInfo !== null" />
              </VCol>

              <!-- Position -->
              <VCol cols="12">
                <AppTextField v-model="page_status.position" :rules="requiredRule" label="Position" type="number"
                  placeholder="Enter position" />
              </VCol>

              <!-- Color -->
              <VCol cols="12">
                <AppTextField v-model="page_status.status_color" :rules="requiredRule" label="Color" type="color" />
              </VCol>

              <!-- Submit -->
              <VCol cols="12">
                <VBtn type="submit" class="me-3" :loading="isLoading">
                  {{ currentInfo ? "Update" : "Submit" }}
                </VBtn>
              </VCol>
            </VRow>
          </VForm>
        </VCard>
      </PerfectScrollbar>
    </VNavigationDrawer>
  </div>
</template>
<script setup>
import AppSelect from "@/@core/components/app-form-elements/AppSelect.vue";
import AppTextField from "@/@core/components/app-form-elements/AppTextField.vue";
import { requiredRule } from "@/validations/validationRules";
import { nextTick, onMounted, ref } from "vue";
import { PerfectScrollbar } from "vue3-perfect-scrollbar";
import { toast } from "vue3-toastify";
import { VForm } from "vuetify/components/VForm";

const props = defineProps({
  isDialogVisible: { type: Boolean, required: true },
  currentInfo: { type: Object, default: null },
});

const emit = defineEmits(["update:isDialogVisible", "submit"]);

const refForm = ref(null);
const valid = ref(true);
const pageList = ref([]);
const isLoading = ref(false);
let isSubmitting = false;
const errors = ref({});

const page_status = ref({
  id: null,
  status_for: [],
  status_text: '',
  status: true,
  position: 1,
  invoice_footer_text: '',
  contract_footer_text: '',
  status_color: '#b7c8bd',
});

// // Watch dialog open/close to reset form
// watch(() => props.isDialogVisible, (val) => {
//   if (val) {
//     if (props.currentInfo) {
//       const { currentInfo } = props;
//       page_status.value = {
//         id: currentInfo.id,
//         status_for: currentInfo.status_for,
//         status_text: currentInfo.status_text,
//         status: currentInfo.position > 0,
//         position: currentInfo.position,
//         invoice_footer_text: currentInfo.invoice_footer_text,
//         contract_footer_text: currentInfo.contract_footer_text,
//         status_color: currentInfo.status_color || '#b7c8bd',
//       };
//     } else {
//       resetFieldInfo();
//     }
//   }
// });

onMounted(() => {
  errors.value = {};
  if (props.currentInfo) {
    const { currentInfo } = props;
    page_status.value = {
      id: currentInfo.id,
      status_for: [currentInfo.status_for],
      status_text: currentInfo.status_text,
      status: currentInfo.position > 0 ? true : false,  // true -> active , false -> in-active
      position: currentInfo.position,
      invoice_footer_text: currentInfo.invoice_footer_text,
      contract_footer_text: currentInfo.contract_footer_text,
    };
  } else {
    resetFieldInfo();
    page_status.value.status = true;
  }
  fetchPageList();
});

const fetchPageList = async () => {
  try {
    const { data } = await $api('/settings/page', { params: { type: 'list' } });
    pageList.value = data;
  } catch (error) {
    console.error(error);
    toast.error(error?.response?.data?.message || 'Failed to fetch pages');
  }
};

const resetFieldInfo = () => {
  page_status.value = {
    id: null,
    status_for: [],
    status_text: '',
    status: true,
    position: 1,
    invoice_footer_text: '',
    contract_footer_text: '',
    status_color: '#b7c8bd',
  };
  nextTick(() => {
    refForm.value?.reset();
    refForm.value?.resetValidation();
  });
};

const closeNavigationDrawer = () => {
  emit("update:isDialogVisible", false);
  resetFieldInfo();
};

const handleDrawerModelValueUpdate = (val) => {
  if (!val) return;
  emit("update:isDialogVisible", val);
};

const onSubmit = async () => {
  if (isSubmitting) return;
  isSubmitting = true;

  const { valid } = await refForm.value.validate();
  if (!valid) {
    isSubmitting = false;
    return;
  }

  isLoading.value = true;

  try {
    const url = props.currentInfo
      ? `/settings/page-status-update/${props.currentInfo.id}?_method=PUT`
      : `/settings/page-status-create`;

    const res = await $api(url, {
      method: 'POST',
      body: page_status.value,
    });

      toast.success(res.message);
      emit('submit');
      closeNavigationDrawer();
  } catch (err) {
    console.error(err);
    toast.error(err?._data?.message || 'An unexpected error occurred');
  } finally {
    isLoading.value = false;
    isSubmitting = false;
  }
};

onMounted(() => {
  fetchPageList();
});
</script>

<style scoped>
.department_card {
  padding: 20px;
}
</style>
