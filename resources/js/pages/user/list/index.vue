<template>
    <section v-if="$can('user', 'view')">
        <VCard title="User List" class="mb-6">
            <VDivider />
            <VCardText>
                <div class="d-flex justify-space-between flex-wrap gap-y-4">
                    <AppTextField v-model="searchQuery" style="max-inline-size: 280px; min-inline-size: 280px;"
                        placeholder="Search Name" />
                    <div class="d-flex flex-row gap-4 align-center flex-wrap">
                        <VBtn v-if="$can('user', 'export-list')" prepend-icon="tabler-upload" variant="tonal"
                            color="secondary">
                            Export
                        </VBtn>
                        <VBtn v-if="$can('user', 'create')" prepend-icon="tabler-plus"
                            @click="isDialogVisible = !isDialogVisible; currentUser = null">
                            Add New
                        </VBtn>

                        <!-- Filter Header Btn FilterHeaderTableBtn -->
                        <FilterHeaderTableBtn :slug="tableHeaderSlug" @filterHeaderValue="getFilteredHeaderValue" />
                    </div>
                </div>
            </VCardText>
            <VDivider />

            <BaseSpinner class="d-flex" v-if="loading" />
            <VCardText v-else class="px-0">
                <VDataTableServer v-model:items-per-page="pagination.per_page" :items="userList"
                    :items-length="userList.length" :headers="headers.filter((header) => header.checked)"
                    class="text-no-wrap" mobile-breakpoint="600" @update:options="updateTableSort">
                    <!-- Name -->
                    <template #item.name="{ item }">
                        <div class="d-flex align-center gap-x-4">
                            <VAvatar size="34" :variant="!item.avatar ? 'tonal' : undefined"
                                @click="getBigImagePreview(item.avatar, item.name)">
                                <VImg v-if="item.avatar" :src="BASE_URL + `/` + item?.avatar" />
                                <span v-else>{{ item.name.charAt(0) }}</span>
                            </VAvatar>
                            <div class="d-flex flex-column">
                                <h6 class="text-base">
                                    {{ item.name }}
                                </h6>
                                <div class="text-sm">{{ item.phone }}</div>
                            </div>
                        </div>
                    </template>


                    <!-- Action -->
                    <template #item.action="{ item }">
                        <IconBtn v-if="$can('user', 'edit')" @click="editUser(item)">
                            <VIcon icon="tabler-pencil" />
                        </IconBtn>

                        <Router-link v-if="$can('user', 'view')"
                            :to="{ name: 'user-view-id', params: { id: item.uuid } }">
                            <IconBtn>
                                <VIcon icon="tabler-eye" />
                            </IconBtn>
                        </Router-link>

                        <v-tooltip location="top">
                            <template v-slot:activator="{ props }">
                                <IconBtn v-bind="props" v-if="$can('user', 'update-password')"
                                    @click="updatePassword(item)">
                                    <VIcon icon="tabler-brand-samsungpass" />
                                </IconBtn>
                            </template>
                            Update Password
                        </v-tooltip>

                        <!-- Restore Button -->
                        <IconBtn v-if="$can('user', 'restore') && item.deleted_at"
                            @click="openDeleteDialog(item, 'restore')" v-tooltip="'Restore User'">
                            <VIcon icon="tabler-reload" />
                        </IconBtn>

                        <!-- Delete Button -->
                        <IconBtn v-if="$can('user', 'delete')"
                            @click="openDeleteDialog(item, item.deleted_at ? 'force_delete' : 'delete')"
                            v-tooltip="item.deleted_at ? 'Permanently Delete User' : 'Delete User'">
                            <VIcon v-if="item.deleted_at" icon="tabler-database-x" />
                            <VIcon v-else icon="tabler-trash" />
                        </IconBtn>
                    </template>

                    <template #bottom>
                        <div class="d-flex align-center justify-space-between flex-wrap gap-3 px-6 py-3">
                            <p class="text-disabled mb-0"> Showing {{ pagination.from }} to {{ pagination.to }} of {{
                                pagination.total }} entries </p>
                            <div class="d-flex flex-wrap gap-2 align-center">
                                <AppSelect :model-value="pagination.per_page" :items="[10, 25, 50, 100]"
                                    @update:model-value="val => { pagination.per_page = val; getUserList(); }"
                                    style="inline-size: 6.25rem;" />

                                <v-pagination v-model="pagination.current_page" :length="pagination.last_page"
                                    :total-visible="5" />
                            </div>
                        </div>
                    </template>
                </VDataTableServer>
            </VCardText>
        </VCard>

        <!-- 👉 Delete Dialog -->
        <DeleteDialog v-model:isDialogVisible="isDeleteDialogOpen" :confirm-title="confirm_title"
            :confirmation-question="title" :currentItem="currentInfo" @submit="getUserList"
            :endpoint="`/user/${currentInfo?.uuid}`" :action="btn_action ?? 'force_delete'"
            @close="isDeleteDialogOpen = false" />

        <!-- 👉 Preview Image Dialog -->
        <PreviewImageDialog v-model:isDialogVisible="bigPreviewImageVisibleDialog" :currentInfo="currentInfo" />

        <!-- 👉 Password update Dialog -->
        <UpdatePassword v-model:isDialogVisible="updatePasswordVisibleDialog"
            :user_id="currentInfo ? currentInfo.uuid : null" />

        <!-- 👉 Create Edit Dialog -->
        <CreateEditDialog v-if="isDialogVisible" :roleList="roleList" @submit="getUserList" :currentInfo="currentInfo"
            v-model:isDialogVisible="isDialogVisible" :peopleAdd="'User'" />
    </section>
</template>
<script setup>
import { onMounted, ref, watch } from 'vue';
import { toast } from "vue3-toastify";
import { VCardText } from 'vuetify/lib/components/index.mjs';
import CreateEditDialog from '../dialog/CreateEditDialog.vue';
import PreviewImageDialog from '../dialog/PreviewImageDialog.vue';
import UpdatePassword from '../dialog/UpdatePassword.vue';

const searchQuery = ref('');
const loading = ref(true);

const tableHeaderSlug = ref('user-list');
const headers = ref([]);
const getFilteredHeaderValue = async (headerList) => { headers.value = headerList; };

const userList = ref([]);
const pagination = ref({ current_page: 1, last_page: 1, total: 0, per_page: 10, from: 0, to: 0 });
const sortBy = ref();
const orderBy = ref();

const bigPreviewImageVisibleDialog = ref(false)
const updatePasswordVisibleDialog = ref(false);
const BASE_URL = window.location.origin;

const isDialogVisible = ref(false);
const isDeleteDialogOpen = ref(false)
const currentInfo = ref(null);

// Update table sort options
const updateTableSort = (options) => {
    sortBy.value = options.sortBy[0]?.key || '';
    orderBy.value = options.sortBy[0]?.order || '';
};

const roleList = ref([]);
const getOptionList = async () => {
    try {
        const response = await $api(`/user/option-list`, { method: 'POST', body: {}, });
        roleList.value = response.data.roles;
    } catch (err) {
        console.error('Error fetching employee list:', err.message);
    }
};

const getUserList = async () => {
    loading.value = true;
    try {
        const params = {
            search: searchQuery.value || '',
            page: pagination.value.current_page,
            sort_key: sortBy.value || '',
            sort_order: orderBy.value || '',
            per_page: pagination.value.per_page,
        };

        const response = await $api('/user', { params });
        const { data, ...paginationData } = response.data;

        userList.value = data.map((user) => {
            const role_ids = Array.isArray(user.roles) ? user.roles.map(role => role.id) : [];

            return {
                ...user,
                roleSelectOptionShow: role_ids.length > 0 ? false : true,
                role_ids,
            };
        });

        pagination.value = { ...paginationData };
    } catch (error) {
        console.error('Error fetching user list:', error);
        toast.error(error?.response?.data?.message || 'Error fetching user list.');
    } finally {
        loading.value = false;
    }
};

const title = ref('');
const btn_action = ref('');
const confirm_title = ref('');
const openDeleteDialog = (item, type) => {
    btn_action.value = type;
    switch (type) {
        case 'delete':
            title.value = 'Are you sure you want to delete this user?';
            confirm_title.value = 'Delete!';
            break;
        case 'restore':
            title.value = 'Are you sure you want to restore this user?';
            confirm_title.value = 'Restore';
            break;
        case 'force_delete':
            title.value = 'Are you sure you want to permanently delete this user?';
            confirm_title.value = 'Permanently Delete';
            break;
        default:
            return;
    }

    currentInfo.value = item;
    isDeleteDialogOpen.value = true;
}

const editUser = (item) => {
    currentInfo.value = item;
    isDialogVisible.value = true;
};

const dbClickShowRoleOption = (item) => {
    item.roleSelectOptionShow = true;
    if (!item.role_ids) {
        item.role_ids = item.roles.map(role => role.id);
    }
};

const userRoleUpdate = async (item) => {
    if (!item.role_ids || item.role_ids.length === 0) {
        return toast.error("Please select at least one role before updating.");
    }

    const payload = {
        user_id: item.id,
        role_ids: item.role_ids,
    };

    try {
        const response = await $api(`/user/role-update`, {
            method: 'POST',
            body: payload,
        });

        toast.success(response.message);

        // Update the user in the list
        const updatedItemIndex = userList.value.findIndex(obj => obj.id === item.id);
        if (updatedItemIndex !== -1) {
            userList.value[updatedItemIndex] = response.data;
            item.role_ids = response.data.roles.map(role => role.id);
            item.roles = response.data.roles;
        }

        item.roleSelectOptionShow = false;
    } catch (error) {
        const errorMessage = error?.data?.message || error?._data?.message || "An error occurred while assigning roles.";
        toast.error(errorMessage);
    }
};

const getBigImagePreview = (image, name) => {
    bigPreviewImageVisibleDialog.value = true;
    currentInfo.value = item;
};

const updatePassword = (item) => {
    updatePasswordVisibleDialog.value = true;
    currentInfo.value = item;
};

// Watchers to handle pagination updates dynamically
watch([() => pagination.value.current_page, () => pagination.value.per_page, () => searchQuery.value,],
    (newValues, oldValues) => {
        const hasChanged = newValues.some((val, index) => val !== oldValues[index]);
        if (hasChanged) {
            getUserList();
        }
    }
);

onMounted(() => { getOptionList(), getUserList(); });
</script>
