<template>
    <div>
        <div v-if="props.isDialogVisible" class="backdrop"></div>
        <VNavigationDrawer permanent :width="700" location="end" :model-value="props.isDialogVisible"
            class="scrollable-content">
            <AppDrawerHeaderSection :title="currentInfo ? 'Edit User' : 'Add User'" @click="closeNavigationDrawer" />
            <VDivider />
            <PerfectScrollbar :options="{ wheelPropagation: false }">
                <v-container>
                    <VRow class="pa-2 mt-4">
                        <VCol cols="12">
                            <VForm ref="refForm" @submit.prevent="onSubmit">
                                <VRow>
                                    <VCol cols="6">
                                        <v-text-field v-model="formData.name"
                                            :rules="[...requiredRule, ...onlyAlphabetsRule]"
                                            @input="handleNameInput"><template v-slot:label>
                                                <span>Name <span style="color: red;">*</span></span>
                                            </template></v-text-field>
                                    </VCol>
                                    <VCol cols="6">
                                        <v-text-field v-model="formData.email" @input="clearFieldError('email')"
                                            :rules="requiredRule" :error-messages="errors.email"><template v-slot:label>
                                                <span>Email <span style="color: red;">*</span></span>
                                            </template></v-text-field>
                                    </VCol>

                                    <VCol cols="6">
                                        <!-- <VFileInput v-model="formData.profile" prepend-icon="tabler-camera"><template
                                                v-slot:label>
                                                <span>File input</span>
                                            </template></VFileInput> -->
                                        <VFileInput v-model="formData.profile" prepend-icon="tabler-camera"
                                            @change="handleFileChange">
                                            <template v-slot:label><span>File input</span></template>
                                        </VFileInput>
                                    </VCol>

                                    <VCol cols="6">
                                        <v-select :rules="requiredRule" :items="statusList" item-title="title"
                                            v-model="formData.status" item-value="value"><template v-slot:label>
                                                <span>Status <span style="color: red;">*</span></span>
                                            </template></v-select>
                                    </VCol>

                                    <VCol cols="6">
                                        <!-- readonly -->
                                        <v-text-field v-model="formData.user_name" :rules="requiredRule"
                                            @input="clearFieldError('user_name')"
                                            :error-messages="errors.user_name"><template v-slot:label>
                                                <span>Login User <span style="color: red;">*</span></span>
                                            </template> </v-text-field>
                                    </VCol>

                                    <VCol cols="6" v-if="!currentInfo">
                                        <v-text-field v-model="formData.password"
                                            :rules="!currentInfo ? requiredRule : []"
                                            hint="Enter your password"><template v-slot:label>
                                                <span>Password <span style="color: red;">*</span></span>
                                            </template></v-text-field>
                                    </VCol>

                                    <VCol cols="12">
                                        <v-select v-model="formData.roles" :rules="requiredRule" :items="roleList"
                                            item-title="name" item-value="id" placeholder="Select a Role" multiple>
                                            <template v-slot:label>
                                                <span>Select Role <span style="color: red;">*</span></span>
                                            </template></v-select>
                                    </VCol>
                                </VRow>
                                <VRow>
                                    <VCol v-if="$can('user', 'create') && !currentInfo"
                                        class="d-flex align-center gap-2 justify-start" cols="12">
                                        <VBtn type="submit" color="primary" :loading="isSubmitting"
                                            :disabled="isSubmitting">Save</VBtn>
                                    </VCol>
                                    <VCol v-if="$can('user', 'edit') && currentInfo"
                                        class="d-flex align-center gap-2 justify-start" cols="12">
                                        <VBtn type="submit" color="primary" :loading="isSubmitting"
                                            :disabled="isSubmitting">Update</VBtn>
                                    </VCol>
                                </VRow>
                            </VForm>
                        </VCol>
                    </VRow>
                </v-container>
            </PerfectScrollbar>
        </VNavigationDrawer>
    </div>
</template>
<script setup>
import { inputNumberRestrict, onlyAlphabetsRule, requiredRule } from '@/validations/validationRules';
import { onMounted } from 'vue';
import { PerfectScrollbar } from 'vue3-perfect-scrollbar';
import { toast } from 'vue3-toastify';
import { VForm, VSelect } from 'vuetify/lib/components/index.mjs';

const route = useRoute();
const props = defineProps({
    isDialogVisible: { type: Boolean, required: true },
    roleList: { type: Array, default: [] },
    currentInfo: { type: Object, default: null },
    peopleAdd: { type: String, required: true }
})

const refForm = ref(false)
const genderList = ref([
    { title: 'Male', value: 'Male' },
    { title: 'Female', value: 'Female' },
    { title: 'Transgender', value: 'Transgender' },
])
const previewImage = ref(null);
const selectedFile = ref(null);
const newEmpCode = ref(null);
const blood_groupList = ref([
    'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'
]);

const accountTypeList = ref(['Admin', 'User']);
const statusList = ref([
    { title: 'Active', value: 'Active' },
    { title: 'In-Active', value: 'In-Active' }
]);
const errors = ref({});

const formData = ref(
    {
        id: '',
        name: '',
        email: '',
        profile: [],
        status: '',
        user_name: '',
        password: '',
        roles: [],
        image: '',
    },
);

const emit = defineEmits([
    'update:isDialogVisible',
    'submit',
]);

// 👉 drawer close
const closeNavigationDrawer = () => {
    emit('update:isDialogVisible', false);
    nextTick(() => {
        refForm.value?.reset();
        refForm.value?.resetValidation();
    })
}
const handleNameInput = (event) => {
    formData.value.name = event.target.value.replace(/[^A-Za-z\s]/g, '');
};

const handleMobileInput = (event) => {
    formData.value.phone = inputNumberRestrict(event.target.value, 10);
};

const handleEmailUserNameInput = (event) => {
    formData.value.user_name = event.target.value;
};

const clearFieldError = (field) => {
    errors.value[field] = null;
};

const resetFiledInfo = () => {
    formData.value = {
        id: '',
        name: '',
        email: '',
        profile: [],
        status: '',
        user_name: '',
        password: '',
        roles: [],
    };
};

onMounted(() => {
    errors.value = {};
    if (props.currentInfo) {
        console.log(props.currentInfo);
        const { currentInfo } = props;
        const role_ids = currentInfo.roles?.map(role => role.id) || [];

        formData.value = {
            id: currentInfo.id,
            name: currentInfo.name,
            email: currentInfo.email,
            status: currentInfo.status,
            roles: role_ids,
            profile: null,
            user_name: currentInfo.user_name,
            image: currentInfo.avatar,
        };
    } else {
        resetFiledInfo();
        formData.value.status = "Active";
    }
});

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

const removeImage = () => {
    previewImage.value = null;
    selectedFile.value = null;
    formData.value.image = null;
};

const isSubmitting = ref(false);

const onSubmit = async () => {
    if (isSubmitting.value) return;
    try {
        // Validate the form
        const { valid } = await refForm.value.validate();

        if (!formData.value.status) {
            toast.error("Status is required.");
            return;
        }

        if (!valid) {
            toast.error('Please Check Required Fields!');
            return;
        }

        isSubmitting.value = true;
        const formDataObject = new FormData();
        Object.entries(formData.value).forEach(([key, value]) => {
            if (typeof value === 'boolean') {
                formDataObject.append(key, value ? '1' : '0');
            } else if (value !== null && value !== undefined) {
                formDataObject.append(key, value);
            }
        });
        formDataObject.append('image', selectedFile.value);

        const url_pai = props.currentInfo ? '/user/update' : '/user/create';

        const response = await $api(url_pai,
            { method: 'POST', body: formDataObject },
            { headers: { 'Content-Type': 'multipart/form-data' } }
        );

        toast.success(response.message || 'Operation successful!');
        emit('update:isDialogVisible', false);
        emit('submit', formData.value);

        // Reset form data
        resetFiledInfo();
        nextTick(() => {
            refForm.value?.reset();
            refForm.value?.resetValidation();
        });
    } catch (error) {
        let errorMessage = error._data.message ?? "Error occurred while processing the request.";
        toast.error(errorMessage);
        if (errorMessage === "The email has already been taken.") {
            errors.value.email = "Email is already in use.";
        } else if (errorMessage === "The phone has already been taken.") {
            errors.value.phone = "Phone is already in use.";
        } else if (errorMessage === "The user name has already been taken.") {
            errors.value.user_name = "User Name is already in use.";
        }
    } finally {
        isSubmitting.value = false;
    }
};
</script>
