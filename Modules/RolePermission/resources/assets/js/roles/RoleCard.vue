<template>
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
                                    <IconBtn v-bind="props" @click="duplicateRoleCreate(role)"
                                        v-if="$can('role', 'create')">
                                        <VIcon icon="tabler-copy" class="text-high-emphasis" color='info'
                                            variant="text" />
                                    </IconBtn>
                                </template>
                                Duplicate Role
                            </v-tooltip>
                            <v-tooltip location="top" v-if="role.slug != 'super-admin'">
                                <template v-slot:activator="{ props }">
                                    <IconBtn v-bind="props" @click="deleteUser(role.id, 'force_delete')"
                                        v-if="$can('role', 'delete')">
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
</template>
<script setup>
import Swal from 'sweetalert2';
import { onMounted } from 'vue';
import { toast } from 'vue3-toastify';
import { useTheme } from 'vuetify';

const vuetifyTheme = useTheme()
const currentTheme = vuetifyTheme.current.value.colors
const props = defineProps({
    roleList: { type: Array, default: [] },
});
const emit = defineEmits(['refreshRoleList']);

const duplicateRoleCreate = async (role) => {
    let title = 'Are you sure you Create Duplicate Role';
    let text = 'Duplicate Role';
    let confirmButtonText = 'Duplicate Role';

    const result = await Swal.fire({
        title: title,
        text: text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: confirmButtonText,
        allowOutsideClick: false,
    });

    if (result.isConfirmed) {
        try {
            const response = await $api(`/api/role/duplicate/${role.id}`, {
                method: 'GET',
            });

            toast.success(response.message ?? "Error occurred while assigning roles.");
            console.log('duplicateRoleCreate getRoleList 3');
            emit('refreshRoleList', response.data);
        } catch (error) {
            console.log('duplicateRoleCreate error ', error);
            toast.error(error?._data?.message ?? "Error occurred while assigning roles.");
        }
    }
}

const deleteUser = async (role_id, action) => {
    let title = '';
    let text = '';
    let confirmButtonText = '';

    switch (action) {
        case 'delete':
            title = 'Are you sure you want to delete this user?';
            text = "You won't be able to revert this!";
            confirmButtonText = 'Yes, delete it!';
            break;
        case 'restore':
            title = 'Are you sure you want to restore this user?';
            text = "This will bring the user back!";
            confirmButtonText = 'Yes, restore it!';
            break;
        case 'force_delete':
            title = 'Are you sure you want to permanently delete this Role?';
            text = "This action cannot be undone!";
            confirmButtonText = 'Yes, permanently delete it!';
            break;
        default:
            return;
    }

    const result = await Swal.fire({
        title: title,
        text: action == 'restore' ? text : '',
        html: action == 'restore' ? '' : `
            <div style="display: grid; grid-template-columns: repeat(12, 1fr); gap: 10px;">
                <input id="swal-input-comment" class="swal2-input" style="grid-column: span 12;" placeholder="Type 'Delete' to confirm" required />
            </div>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: confirmButtonText,
        allowOutsideClick: false,
        didOpen: () => {
            // Add event listener to input to enable/disable confirm button
            if (action === 'force_delete' || action === 'delete') {
                const input = document.getElementById('swal-input-comment');
                const confirmButton = Swal.getConfirmButton();

                confirmButton.disabled = true;

                input.addEventListener('input', () => {
                    confirmButton.disabled = input.value.trim().toLowerCase() !== 'delete';
                });
            }
        },
        preConfirm: () => {
            const comment = action == 'force_delete' || action === 'delete' ? document.getElementById('swal-input-comment').value : '';
            // const type = document.getElementById('swal-input-action').value;
            if ((action == 'force_delete' || action === 'delete') && comment !== 'delete') {
                Swal.showValidationMessage('Delete Comment is required');
                return false;
            }
            return { comment };
            // return { comment, type };
        }
    });

    if (result.isConfirmed) {
        try {
            const response = await $api(`/role/${role_id}`, {
                method: 'DELETE',
                body: { delete_text: formValues.comment, action: action },
            });
            toast.success(response.message);
            console.log('deleteUser getRoleList 4');
            emit('refreshRoleList', response.data);
        } catch (error) {
            console.error('Error deleting user:', error);
            toast.error(error?._data?.message ?? "Error occurred while assigning roles.");
        }
    }
};

onMounted(() => {
    //console.log("currentTheme", currentTheme);
});

</script>
