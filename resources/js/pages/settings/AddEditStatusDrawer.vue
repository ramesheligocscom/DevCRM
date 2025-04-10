
<template>
  <div>
      <div v-if="isDialogVisible" class="backdrop"></div>
      <VNavigationDrawer permanent :width="500" location="end" class="scrollable-content"
          :model-value="props.isDialogVisible" @update:model-value="handleDrawerModelValueUpdate">
          <AppDrawerHeaderSection :title="currentInfo ? 'Edit Status' : 'Add Status'"
              @cancel="closeNavigationDrawer" />

          <VDivider />

          <PerfectScrollbar :options="{ wheelPropagation: false }">
              <VCard class="department_card">
                  <VForm ref="refForm" v-model="valid" @submit.prevent="onSubmit">
                      <VRow>
                          <VCol cols="12">
                              <AppTextField v-model="page_status.status_text" :rules="requiredRule" label="Name *"
                                  placeholder="Name" autofocus />
                          </VCol>
                          <VCol cols="12">
                              <AppSelect :items="pageList" v-model="page_status.status_for" placeholder="Select pages"
                                  label="Select Page" class="mb-6" multiple chips closable-chips />
                          </VCol>
                          <VCol cols="12">
                              <VBtn type="submit" class="me-3">
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
import { onMounted, ref } from "vue";
import { PerfectScrollbar } from "vue3-perfect-scrollbar";
import { VForm } from "vuetify/components/VForm";

const valid = ref(true);
const refForm = ref(false);
const errors = ref({});

const props = defineProps({
  isDialogVisible: { type: Boolean, required: true, },
  currentInfo: { type: Object, default: null, },
});

const page_status = ref({
      id : null,
      status_for : null,
      status_text : '',
      status : '',
      position : '',
      invoice_footer_text : '',
      contract_footer_text : '',
});

onMounted(() => {
    errors.value = {};
    if (props.currentInfo) {
        const { currentInfo } = props;
        page_status.value = {
            id: currentInfo.id,
            status_for: currentInfo.status_for,
            status_text: currentInfo.status_text,
            status : currentInfo.position > 0 ? true : false,  // true -> active , false -> in-active
            position : currentInfo.position,
            invoice_footer_text : currentInfo.invoice_footer_text,
            contract_footer_text : currentInfo.contract_footer_text,
        };
    } else {
        resetFiledInfo();
        page_status.value.status = true;
    }
    fetchPageList();
});

const pageList = ref([]);

const fetchPageList = async () => { 
  try {
        const params = { type:'list', };
        const response = await $api('/settings/page', { params });
        console.log('fetchPageList ', response.data);
        pageList.value = response.data;
    } catch (error) {
        console.error('Error fetching status list:', error);
        toast.error(error?.response?.data?.message || 'Error fetching status list.');
    }  
 }
 
const resetFiledInfo = () => {
  page_status.value = {
        id: null,
        status_for : null,
      status_text : '',
      status : '',
      position : '',
      invoice_footer_text : '',
      contract_footer_text : '',
    };
};

// onMounted(() => {
//     console.log(props.currentInfo);
//     // return;
//     if (props.currentInfo?.status) {
//         page_status.value = _.cloneDeep(props.currentInfo);
//     }
//     fetchPageList();
// });

const emit = defineEmits(["update:isDialogVisible", "submit"]);

// 👉 drawer close
const closeNavigationDrawer = () => {
    emit("update:isDialogVisible", false);
    nextTick(() => {
        refForm.value?.reset();
        refForm.value?.resetValidation();
    });
};

const handleDrawerModelValueUpdate = (val) => {
    emit("update:isDialogVisible", val);
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
    // try {
    //     const res = await $api(props.currentInfo ? `/settings/status/${props.currentInfo.id}?_method=PUT` : `/settings/status`, {
    //         method: 'POST',
    //         body: page_status.value,
    //     })
    //     console.log("response", res);

    //     if (res?.response == 'success') {
    //         toast.success(res?.message);
    //         // Close the modal and reset form
    //         emit('submit');
    //         emit('update:isDialogVisible', false);

    //         await new Promise(resolve => setTimeout(resolve, 500));
    //         // Reset form validation
    //         await nextTick(() => {
    //             refForm.value?.reset();
    //             refForm.value?.resetValidation();
    //         });

    //         isSubmitting = false;
    //     }
    // } catch (err) {
    //     // Handle errors and show toast
    //     console.error("Error:", err);
    //     isLoading.value = false;
    //     toast.error(err?._data?.message || "An unexpected error occurred");
    // } finally {
    //     // Always unlock submitting state
    //     isSubmitting = false;
    //     isLoading.value = false;
    // }
};
</script>


<style scoped>
.department_card { padding: 20px; }
</style>
