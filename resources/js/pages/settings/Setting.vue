

<template>
  <!-- 👉 Profile Information -->
  <VForm @submit.prevent="handleSubmitForm" ref="form" v-model="valid">
    <VCard title="General Settings" class="mb-6">
      <VCardText>
        <VRow>
          <VCol cols="12" md="6">
            <AppTextField label="Company name" placeholder="Pixinvent" v-model="formData.name" :rules="requiredRule" />
          </VCol>
          <VCol cols="12" md="6">
            <AppTextField label="Phone" placeholder="+(123) 456-7890" v-model="formData.phone"
              :rules="[...requiredRule, ...minLengthRule(10)]" @input="filterInput(10, 'phone', $event)" />
          </VCol>
          <VCol cols="12" md="6">
            <app-textarea @input="formatAddress" label="Address" placeholder="Pixinvent" :rules="requiredRule"
              v-model="formData.address" rows="1" auto-grow />
          </VCol>
        
          <!-- File Input -->
          <VCol cols="12" md="6">
            <VFileInput label="Upload Images" prepend-icon="tabler-camera" @change="handleFileChange" />
          </VCol>

          <!-- Image Preview -->
          <VCol cols="12" md="6">
            <div v-if="previewImage" class="d-flex">
              <img :src="previewImage" alt="Preview" class="preview-img" style=" border-radius: 10px;inline-size: 150px;" />
              <div class="cutBtn" @click="removeImage">X</div>
            </div>
          </VCol>
        </VRow>
        <!-- 👉 Save button -->
        <div class="d-flex justify-end gap-x-4 mt-4">
          <VBtn type="submit">Save Changes</VBtn>
        </div>
      </VCardText>
    </VCard>
  </VForm>
</template>

<script setup>
import { useCompanyStore } from "@/stores/companyStore";
import { minLengthRule, requiredRule } from "@/validations/validationRules";
import { ref } from "vue";
import { toast } from "vue3-toastify";
const companyStore = useCompanyStore();

const valid = ref(false);
const form = ref(false);
const BASE_URL = window.location.origin;

const formData = ref({
  name: "",
  phone: "",
  address: "",
  image: "",
});
const selectedFile = ref(null);
const previewImage = ref(null);

const companyDetails = computed(() => companyStore.companyDetails || {});

console.log(companyDetails.value);
formData.value.name = companyDetails.value.Company_name;
formData.value.phone = companyDetails.value.Phone;
formData.value.address = companyDetails.value.Address;
formData.value.image = companyDetails.value.company_logo;
previewImage.value = BASE_URL + `/` + companyDetails.value.company_logo;

const baseUrl = import.meta.env.VITE_API_BASE_URL;
console.log("Base URL:", baseUrl)

// 👉 Update Profile
const handleFileChange = async (event) => {
  const file = event.target.files[0];
  if (file) {
    selectedFile.value = await convertToBase64(file);
    previewImage.value = URL.createObjectURL(file);
  }
};

// Convert File to Base64
const convertToBase64 = (file) => {
  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = () => resolve(reader.result);
    reader.onerror = (error) => reject(error);
  });
};

const handleSubmitForm = async () => {
  let { valid } = await form.value.validate();
  if (!valid) return false;

  try {
    let payload = {
      Company_name: formData.value.name,
      Phone: formData.value.phone,
      Address: formData.value.address,
      image: selectedFile.value,
    };

    const data = await $api(`/settings`, {
      method: "PUT",
      body: JSON.stringify(payload),
      headers: { "Content-Type": "application/json" },
    });

    console.log(data);
    await companyStore.fetchCompanyDetails();
    await nextTick(() => { toast.success(data?.message); });
  } catch (error) {
    console.log(error);
    toast.error("Something went wrong");
  }
};

const filterInput = (maxLength, field, event) => {
  let value = event.target.value;
  let filteredValue = value.replace(/[^0-9]/g, "");
  if (filteredValue.length > maxLength) {
    filteredValue = filteredValue.slice(0, maxLength);
  }
  formData.value[field] = filteredValue;
};

const formatAddress = () => {
  if (formData.value.address != "") {
    const words = formData.value?.address?.split(/\s+/);
    const lines = [];

    for (let i = 0; i < words?.length; i += 5) {
      lines.push(words.slice(i, i + 5).join(" "));
    }
    formData.value.address = lines.join("\n");
  }
};

const removeImage = () => {
  previewImage.value = null;
  selectedFile.value = null;
};

onMounted(() => {
  fetchStates();
  formatAddress();
});
</script>

<style scoped>
.cutBtn {
  border-radius: 20%;
  background-color: #db3838;
  block-size: min-content;
  color: white;
  cursor: pointer;
  margin-inline-start: 4px;
  padding-block: 2px;
  padding-inline: 7px;
}
</style>
