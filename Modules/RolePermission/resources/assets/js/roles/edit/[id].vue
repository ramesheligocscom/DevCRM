<template>
    <div>
        <VForm ref="refForm" @submit.prevent="onSubmit">
            <VCard class="pa-5">
                <div class="d-flex justify-space-between align-center mb-5">
                    <h2>Edit Role Permission </h2>
                    <div class="d-flex gap-2">
                        <VBtn v-if="$can('role', 'edit')" color="primary" type="submit" :disabled="role_name == 'Super Admin' ? true : false">
                            Save</VBtn>
                        <Router-link to="/roles">
                            <VBtn prepend-icon="tabler-arrow-back-up" variant="tonal" color="secondary">Back</VBtn>
                        </Router-link>
                    </div>
                </div>
                <VRow>
                    <VCol cols="12" md="4">
                        <v-text-field :rules="requiredRule" v-model="role_name" placeholder="Enter Role Name">
                            <template v-slot:label>
                                <span>Role Name <span style="color: red;">*</span></span>
                            </template></v-text-field>
                    </VCol>
                    <VCol cols="12" md="4">
                        <v-text-field v-model="position" type="text" step="any" inputmode="decimal" min="0"
                            @input="validateInput(position)"> <template v-slot:label>
                                <span>Role Position <span style="color: red;">*</span></span>
                            </template></v-text-field>
                    </VCol>
                    <VCol cols="12" md="4">
                        <v-select :items="roleStatus" item-title="title" item.value="value" v-model="status">
                            <template v-slot:label>
                                <span>Role status <span style="color: red;">*</span></span>
                            </template></v-select>
                    </VCol>
                    <VCol cols="12" md="12">
                        <v-textarea v-model="description" label="Description" rows="1" auto-grow />
                    </VCol>
                </VRow>
            </VCard>
            <BaseSpinner class="d-flex" v-if="loading" size="1" />
            <div v-else>
                <VCard title="Assign Permissions" prepend-icon="tabler-user-shield" class="mt-5">
                    <VTabs class="permissionTabs" v-model="currentTab">
                        <VTab v-for="(obj, index) in permissionList" :key="index" :prepend-icon="obj.icon">
                            {{ obj.name }}({{ obj.permissions_count }}/{{ obj.has_permission_count }})
                            <!-- <VChip :color="obj.has_permission_count === 0 ? 'error' : 'primary'" class="ml-2">
                                {{ obj.permissions_count }}/{{ obj.has_permission_count }}
                            </VChip> -->
                        </VTab>
                    </VTabs>
                </VCard>

                <VWindow v-model="currentTab">
                    <VWindowItem v-for="permissionType in permissionList" :value="permissionType.id"
                        :key="permissionType.id">
                        <VRow class="d-flex">
                            <VCol class="pb-0" v-for="permissionCategory in permissionType.permission_category"
                                :key="permissionCategory.id" cols="12" md="6" lg="6">
                                <VCard class="mt-4 dashboard_card pa-4">
                                    <div class="d-flex role_title justify-space-between align-center mb-2">
                                        <h3>{{ permissionCategory.name }}</h3>
                                        <VCheckbox v-model="permissionCategory.all_category_permission"
                                            @change="allPermissionCheckOrUnCheck(permissionCategory)" />
                                    </div>
                                    <div class="grid_checkbox  pa-3">
                                        <VCheckbox v-for="permission in permissionCategory.permissions"
                                            :key="permission.id" v-model="permission.check_permission"
                                            :label="permission.title"
                                            @change="singlePermissionCheckCategory(permissionCategory)" />
                                    </div>
                                </VCard>
                            </VCol>
                        </VRow>
                    </VWindowItem>
                </VWindow>
            </div>
        </VForm>
    </div>
</template>

<script setup>
import { useRoute } from 'vue-router';
import { toast } from 'vue3-toastify';
import { VBtn, VCheckbox, VForm, VSelect, VTabs, VWindowItem } from 'vuetify/lib/components/index.mjs';
import { requiredRule } from '../../../../../../../resources/js/validations/validationRules';

const route = useRoute();
const role_id = route.params.id;
const currentTab = ref(0);
const permissionList = ref([]);
const loading = ref(false);
const role_info = ref(null);
const role_name = ref("");
const description = ref("");
const position = ref(0);
const status = ref("Active");
const refForm = ref(false);

const roleStatus = [
    { title: 'Active', value: 'Active' },
    { title: 'In-Active', value: 'In-Active' },
]

onMounted(() => { getRoleInfo(); getPermissionList(); });

const getRoleInfo = async () => {
    try {
        const params = { role_id: role_id };
        const response = await $api(`/api/role/info`, { method: 'POST', body: params, });
        if (response.status === 200) {
            role_name.value = response.data ? response.data.name : '';
            description.value = response.data ? response.data.description : '';
            position.value = response.data ? response.data.position : 0;
            status.value = response.data ? response.data.status : 'Active';
            role_info.value = response.data;
        }
    } catch (error) {
        console.log('getRoleInfo error ', JSON.stringify(error));
        toast.error(error?._data?.message ?? "Error occurred while assigning roles.");
    } finally {
    }
};

const getPermissionList = async () => {
    loading.value = true;
    try {
        const params = {
            role_id: role_id,
            search: '',
        };
        const response = await $api('/permission', { params });
        permissionList.value = response.data.map(obj => ({
            ...obj,
            has_permission_count: obj.permission_category
                ?.reduce((count, category) =>
                    count + (category.permissions?.filter(permission => permission.check_permission === true).length || 0),
                    0)
        }));
    } catch (error) {
        console.log('getPermissionList error ', JSON.stringify(error));
        toast.error(error?._data?.message ?? "Error occurred while assigning roles.");
    } finally {
        loading.value = false;
    }
};

const validateInput = (value) => {
    const regex = /^[0-9]*(\.[0-9]+)?$/;
    if (!regex.test(value)) {
        value = value.slice(0, -1);
    }
};

const allPermissionCheckOrUnCheck = (permissionCategory) => {
    permissionCategory.permissions.forEach((permission) => {
        permission.check_permission = permissionCategory.all_category_permission;
    });
};

const singlePermissionCheckCategory = (permissionCategory) => {
    const totalPermissions = permissionCategory.permissions.length;
    const checkedPermissions = permissionCategory.permissions.filter(
        (permission) => permission.check_permission
    ).length;

    permissionCategory.all_category_permission = totalPermissions === checkedPermissions;
};

const isSubmitting = ref(false);
const onSubmit = async () => {
    if (isSubmitting.value) return;
    isSubmitting.value = true;
    try {
        const { valid } = await refForm.value.validate();
        if (!valid) {
            isSubmitting.value = false;
            return false;
        }
        const permission_list = [];
        permissionList.value.forEach(type => {
            type.permission_category.forEach(category => {
                category.permissions.forEach(permission => {
                    if (permission.check_permission) {
                        permission_list.push({
                            id: permission.id,
                            permission_category_id: permission.permission_category_id,
                            permission_type_id: permission.permission_type_id,
                            permission_type_id: permission.permission_type_id,
                            check_permission: permission.check_permission,
                        });
                    }
                });

            });
        });
        let formDataObject = {
            role_id: role_id,
            role_name: role_name.value,
            description: description.value,
            position: position.value,
            status: status.value,
            permission_list: permission_list,
        };
        const response = await $api('/role/update', { method: 'POST', body: formDataObject },);
        toast.success(response.message || 'Operation successful!');
        nextTick(() => {
            refForm.value?.reset();
            refForm.value?.resetValidation();
        });
        getRoleInfo();
        getPermissionList();
    } catch (error) {
        toast.error(error?._data.message ?? 'Error occurred while processing the request.');
    } finally {
        isSubmitting.value = false;
    }
};
</script>

<style scoped>
.grid_parent_checkbox {
  display: flex;
  flex-direction: column;
}

.grid_checkbox {
  display: grid;
  grid-template-columns: auto auto;
}
</style>
