<template>
    <div>
        <VRow>
            <!-- 👉 Roles -->
            <VCol v-for="role in roleList" :key="role.id" cols="12" sm="6" lg="4">
                <VCard>
                    <VCardText class="d-flex align-center pb-4">
                        <div class="text-body-1">
                            <VChip variant="tonal" color="primary">
                                {{ role.users.length }} {{ role.users.length === 1 ? 'user' : 'users' }}
                            </VChip>
                        </div>
                        <VSpacer />
                        <div class="v-avatar-group">
                            <template v-for="(user, index) in role.users" :key="user.id+'-'+index">
                                <v-tooltip location="top">
                                    <template v-slot:activator="{ props }">
                                        <VAvatar v-if="role.users.length > 4 && role.users.length !== 4 && index < 3"
                                            v-bind="props" size="40" :image="user.image" />
                                        <VAvatar v-else v-bind="props" size="40" :image="user.image" />
                                    </template>
                                    {{ user.name }}
                                </v-tooltip>
                            </template>
                            <VAvatar v-if="role.users.length > 4"
                                :color="$vuetify.theme.current.dark ? '#373B50' : '#EEEDF0'">
                                <span> +{{ role.users.length - 3 }} </span>
                            </VAvatar>
                        </div>
                    </VCardText>

                    <VCardText>
                        <div class="d-flex justify-space-between align-center">
                            <div class="text-left">
                                <h5 class="text-h5"> {{ role.name }} </h5>
                            </div>
                            <div class="text-right">
                                <v-tooltip location="top">
                                    <template v-slot:activator="{ props }">
                                        <Router-link v-bind="props" :to="`/role-permission/edit/${role.id}`"
                                            v-if="$can('role', 'edit')">
                                            <VIcon icon="tabler-edit" class="text-high-emphasis" color="primary"
                                                variant="text" />
                                        </Router-link>
                                    </template>
                                    Edit Role
                                </v-tooltip>
                                <v-tooltip location="top">
                                    <template v-slot:activator="{ props }">
                                        <IconBtn v-bind="props" @click="openDuplicateDialog(role)"
                                            v-if="$can('role', 'create')">
                                            <VIcon icon="tabler-copy" class="text-high-emphasis" color='info'
                                                variant="text" />
                                        </IconBtn>
                                    </template>
                                    Duplicate Role
                                </v-tooltip>
                                <v-tooltip location="top" v-if="role.slug != 'super-admin' && $can('role', 'delete')">
                                    <template v-slot:activator="{ props }">
                                        <IconBtn v-if="$can('role', 'delete')" v-bind="props"
                                            @click="openDeleteDialog(role)">
                                            <VIcon icon="tabler-trash" class="text-high-emphasis" color='error'
                                                variant="text" />
                                        </IconBtn>
                                    </template>
                                    Delete Role
                                </v-tooltip>
                            </div>
                        </div>
                    </VCardText>
                </VCard>
            </VCol>
        </VRow>

        <!-- 👉 Delete Dialog -->
        <DeleteDialog v-model:isDialogVisible="isDeleteDialogOpen" confirm-title="Delete!"
            confirmation-question="Are you sure want to delete role?" :currentItem="currentInfo" @submit="fetchRoleList"
            :endpoint="`/role/${currentInfo?.id}`" @close="isDeleteDialogOpen = false" />

        <!-- 👉 DuplicateDialogOpen Dialog -->
        <VDialog max-width="500" :model-value="duplicateDialogOpen" @update:model-value="updateDuplicateModelValue"
            persistent scrollable>
            <VCard class="text-center px-10 py-6">
                <VCardText>
                    <VBtn icon variant="outlined" color="warning" class="my-4"
                        style=" block-size: 88px;inline-size: 88px; pointer-events: none;">
                        <span class="text-5xl">!</span>
                    </VBtn>

                    <h6 class="text-lg font-weight-medium">
                        Duplicate Role
                    </h6>

                    <h6 class="text-lg font-weight-medium">
                        Are you sure you Create {{ currentInfo ? currentInfo.name : '' }} Duplicate Role
                    </h6>

                </VCardText>

                <VCardText class="d-flex align-center justify-center gap-2">
                    <VBtn variant="elevated" @click="duplicateRoleCreate">
                        Confirm
                    </VBtn>
                    <VBtn color="secondary" variant="tonal" @click="duplicateOnCancel"> Cancel </VBtn>
                </VCardText>
            </VCard>
        </VDialog>
    </div>
</template>
<script setup>
import { toast } from 'vue3-toastify';
import { useTheme } from 'vuetify';
import DeleteDialog from './dialog/DeleteDialog.vue';

const vuetifyTheme = useTheme()
const currentTheme = vuetifyTheme.current.value.colors
const isDeleteDialogOpen = ref(false)
const currentInfo = ref(null);
const duplicateDialogOpen = ref(false);

const props = defineProps({ roleList: { type: Array, default: [] }, });

const emit = defineEmits(['refreshRoleList']);

const fetchRoleList = async (response) => {
    emit('refreshRoleList', response.data);
};

const openDuplicateDialog = (item) => {
    currentInfo.value = item;
    duplicateDialogOpen.value = true;
}

const updateDuplicateModelValue = val => {
    duplicateDialogOpen.value = val;
}

const duplicateRoleCreate = async () => {
    if (currentInfo.value) {
        try {
            const response = await $api(`/api/role/duplicate/${currentInfo.value.id}`, {
                method: 'GET',
            });

            toast.success(response.message ?? "Error occurred while assigning roles.");
            emit('refreshRoleList', response.data);
        } catch (error) {
            console.log('duplicateRoleCreate error ', error);
            toast.error(error?._data?.message ?? "Error occurred while assigning roles.");
        }
    }
}

const duplicateOnCancel = () => {
    currentInfo.value = null;
    duplicateDialogOpen.value = false;
}

const openDeleteDialog = (item) => {
    currentInfo.value = item;
    isDeleteDialogOpen.value = true;
}

</script>
