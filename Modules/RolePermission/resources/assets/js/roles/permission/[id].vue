<template>
    <VRow>
        <VCol cols="12">
            <VCard title="Update Permissions">
                <VCardText class="d-flex align-center justify-space-between">
                    <!-- <div class="d-flex gap-2 align-center"></div> -->
                    <div class="d-flex flex-wrap gap-4">
                        <div class="d-flex align-center flex-wrap gap-4">
                            <AppTextField v-model="searchQuery" placeholder="Search role"
                                style="inline-size: 15.625rem;" @click:append="clearSearch"
                                @keyup="searchQuery.length === 0 ? getPermission() : ''" />
                            <VBtn depressed v-if="searchQuery.length !== 0" color="primary" @click="getPermission">
                                Search
                            </VBtn>
                        </div>
                        <VIcon id="refresh" class="ml-2 my-2" @click="clearSearch" size="25" color="darker">
                            mdi-sync
                        </VIcon>
                    </div>
                    <RouterLink to="/admin/roles">
                        <VBtn color="secondary" variant="tonal">Back</VBtn>
                    </RouterLink>
                </VCardText>
                <VDivider />
            </VCard>
        </VCol>
        <BaseSpinner class="d-flex" v-if="spinnerLoading" />
        <VCol v-else cols="12">
            <!-- <VCard class="pa-5 mt-3" v-for="permission in expandedList.permissions" :key="permission.id"> -->
            <Panel v-for="permission in expandedList.permissions" :key="permission.id" class="permission_panel"
                toggleable>
                <template #header>
                    <div class="flex items-center gap-2">
                        <!-- <VBtn color="primary">{{ permission.name }}</VBtn> -->
                        <VChip class="permission_hdng">{{ permission.name }}</VChip>
                        <!-- <h4 class="permission_hdng">{{ permission.name }}</h4> -->
                    </div>
                </template>
                <template #icons>
                    <!-- <Button icon="pi pi-cog" severity="secondary" rounded text @click="toggle" /> -->
                </template>
                <VDataTableServer :headers="headers" :items="permission.permissions_group" :loading="loading"
                    class="my-card mb-0 permission_table" :hide-default-footer="true">

                    <template v-slot:top>
                        <div class="crud_permisson_div">
                            <p class="mb-0">Permissions</p>

                            <VCheckbox label="View" v-model="permission.view_all" :disabled="loading"
                                @click="allPermission('view', permission.view_all, permission.id)" />

                            <VCheckbox label="Create" v-model="permission.create_all"
                                :disabled="loading || permission.name == 'Dashboard'"
                                @click="allPermission('create', permission.create_all, permission.id)" />

                            <VCheckbox label="Update" v-model="permission.update_all"
                                :disabled="loading || permission.name == 'Dashboard'"
                                @click="allPermission('update', permission.update_all, permission.id)" />

                            <VCheckbox label="Delete" v-model="permission.delete_all"
                                :disabled="loading || permission.name == 'Dashboard'"
                                @click="allPermission('delete', permission.delete_all, permission.id)" />
                        </div>
                        <VDivider class="permision_divider" />
                    </template>

                    <template v-slot:[`item.view`]="{ item }">
                        <VCheckbox dense v-model="item.view_permission.view" :disabled="item.view_permission.disabled"
                            @click="updatePermission(item.view_permission.permission, item.view_permission.view)" />
                    </template>

                    <template v-slot:[`item.create`]="{ item }">
                        <VCheckbox dense v-model="item.create_permission.create"
                            :disabled="item.create_permission.disabled"
                            @click="updatePermission(item.create_permission.permission, item.create_permission.create)" />
                    </template>

                    <template v-slot:[`item.update`]="{ item }">
                        <VCheckbox dense v-model="item.update_permission.update"
                            :disabled="item.update_permission.disabled"
                            @click="updatePermission(item.update_permission.permission, item.update_permission.update)" />
                    </template>

                    <template v-slot:[`item.delete`]="{ item }">
                        <VCheckbox dense v-model="item.delete_permission.delete"
                            :disabled="item.delete_permission.disabled"
                            @click="updatePermission(item.delete_permission.permission, item.delete_permission.delete)" />
                    </template>
                </VDataTableServer>
            </Panel>
            <!-- </VCard> -->
        </VCol>
    </VRow>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { toast } from 'vue3-toastify';
import axios from 'axios';
import { debounce } from 'lodash';
import Panel from 'primevue/panel';
import { VBtn } from 'vuetify/lib/components/index.mjs';

const spinnerLoading = ref(true);
const route = useRoute();
const expandedList = ref([]);
const searchQuery = ref('');
const roleUuid = ref(route.params.id || null);
const loading = ref(false);
const isFetching = ref(false);
const totalItems = ref(0); // Total number of items from the server
const tableOptions = ref({
    page: 1,
    itemsPerPage: 10,
    sortBy: [],
    sortDesc: [],
});

const headers = [
    { text: 'Permissions', key: 'name', width: '200px' },
    { text: 'View', key: 'view', width: '200px' },
    { text: 'Add', key: 'create', width: '200px' },
    { text: 'Edit', key: 'update', width: '200px' },
    { text: 'Delete', key: 'delete', width: '200px' },
];

const allPermission = async (action, checkbox, groupType) => {
    if (loading.value) return;
    loading.value = true;
    const accessToken = useCookie('accessToken').value;

    try {
        await axios.post('/api/allPermission', {
            role_id: roleUuid.value,
            action,
            enable: checkbox,
            permission_name_id: groupType,
        }, { headers: { Authorization: `Bearer ${accessToken}` } });
        toast.success('Permissions updated successfully.');
        await getPermission();
    } catch (error) {
        console.error(error);
        toast.error('Failed to update permissions.');
    } finally {
        loading.value = false;
    }
};

const updatePermission = async (permission, action) => {
    if (loading.value) return;
    loading.value = true;
    const accessToken = useCookie('accessToken').value;
    try {
        const { data } = await axios.post('/api/updateRolePermissions', {
            role_id: roleUuid.value,
            action,
            permission,
        }, { headers: { Authorization: `Bearer ${accessToken}` } });
        toast.success(data.message);
        await getPermission();
    } catch (error) {
        console.error(error);
        toast.error('Failed to update permission.');
    } finally {
        loading.value = false;
    }
};

const getPermission = async () => {
    if (isFetching.value) return;
    isFetching.value = true;
    const accessToken = useCookie('accessToken').value;
    try {
        const { data } = await axios.post('/api/getPermissionsByRole', {
            search: searchQuery.value || '',
            role_id: roleUuid.value,
        }, { headers: { Authorization: `Bearer ${accessToken}` } });
        expandedList.value = data.data || [];
        totalItems.value = data.total || 0;
        spinnerLoading.value = false;
    } catch (error) {
        console.error(error);
        toast.error('Failed to fetch permissions. Please try again.');
    } finally {
        isFetching.value = false;
    }
};

const clearSearch = async () => {
    searchQuery.value = '';
    await getPermission();
};

const debouncedGetPermission = debounce(getPermission, 300);
watch(searchQuery, debouncedGetPermission);

onMounted(getPermission);
</script>

<style scoped>
.crud_permisson_div {
    display: flex;
    gap: 204px;
    align-items: center;
}

.permision_divider {
    margin: 20px 0px !important;
}
</style>