<template>
    <VRow v-if="$can('role', 'view')">
        <VCol cols="12">
            <VCard class="mb-5">
                <VCardText>
                    <div class="d-flex justify-sm-space-between flex-wrap align-center justify-start">
                        <h1>Roles</h1>
                        <div class="d-flex gap-2 flex-wrap align-center">
                            <AppTextField v-model="searchQuery" placeholder="Search role"
                                style="inline-size: 15.625rem;" />
                            <Router-link :to="{ path: '/role-permission-create' }" v-if="$can('role', 'create')">
                                <VBtn class="float-right" rounded="" icon="tabler-plus" />
                            </Router-link>
                            <VBtn v-show="false" color="primary" @click="rollAssignPermissionList()"> Role Assign
                                Permission </VBtn>
                        </div>
                    </div>
                </VCardText>
            </VCard>
            <BaseSpinner class="d-flex" v-if="loading" size="1" />
            <RoleCards v-else-if="$can('role', 'view')" :roleList="roleList" @refreshRoleList="getRoleList" />
        </VCol>
    </VRow>
</template>

<script setup>
import { onMounted, ref, watch } from 'vue';
import { toast } from 'vue3-toastify';
import RoleCards from './RoleCard.vue';

const roleList = ref([]);
const loading = ref(false);
const searchQuery = ref('');
const pagination = ref({ current_page: 1, last_page: 1, total: 0, per_page: 10, from: 0, to: 0 });

onMounted(async () => {
    console.log('onMounted getRoleList 1');
    await getRoleList();
});

// Watch for changes in pagination or search query
watch([() => pagination.value.current_page, () => pagination.value.per_page, () => searchQuery.value,],
    (newValues, oldValues) => {
        const hasChanged = newValues.some((val, index) => val !== oldValues[index]);
        if (hasChanged) {
            getRoleList();
        }
    }
);

const rollAssignPermissionList = async () => {
    const response = await $api('/role/roll-assign-permission-list');
};

const getRoleList = async () => {
    loading.value = true;
    try {
        const params = {
            search: searchQuery.value || '',
            page: pagination.value.current_page,
            per_page: 1000 ?? pagination.value.per_page,
        };

        const response = await $api('/role', { params });
        roleList.value = response.data.data;

        pagination.value = {
            current_page: response.data.current_page,
            last_page: response.data.last_page,
            total: response.data.total,
            per_page: response.data.per_page,
            from: response.data.from,
            to: response.data.to,
        };
    } catch (error) {
        console.log('getRoleList error ', error);
        toast.error(error?._data?.message ?? "Error occurred while assigning roles.");
    } finally {
        loading.value = false;
    }
};
</script>
