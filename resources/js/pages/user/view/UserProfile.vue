<template>
    <VRow>
        <!-- SECTION User Details -->
        <VCol cols="12">
            <VCard v-if="props.currentUser">
                <VCardText class="text-center pt-12">
                    <!-- 👉 Avatar -->
                    <VAvatar class="py-2 px-1" rounded :size="100" color="primary" variant="tonal">
                        <VImg v-if="props.currentUser.avatar" :src="BASE_URL + `/` + props?.currentUser?.avatar" />
                        <span v-else class="text-5xl font-weight-medium">
                            {{ avatarText(props.currentUser.name) }}
                        </span>
                    </VAvatar>

                    <!-- 👉 User fullName -->
                    <h5 class="text-h5 mt-4">
                        {{ props.currentUser.name }}
                    </h5>

                    <!-- 👉 Role chip -->
                    <VChip v-for="role in props.currentUser.roles" :key="role.id" :color="'success'" variant="tonal">
                        {{ role.name }}
                    </VChip>
                </VCardText>

                <VCardText>
                    <div class="d-flex justify-space-around gap-x-6 gap-y-2 flex-wrap mb-6">
                    </div>
                    <!-- 👉 Details -->
                    <h5 class="text-h5">Details</h5>

                    <VDivider class="my-4" />

                    <!-- 👉 User Details list -->
                    <VList class="card-list mt-2">
                        <VListItem>
                            <VListItemTitle>
                                <h6 class="text-h6">
                                    Name:
                                    <div class="d-inline-block text-body-1">
                                        {{ props.currentUser.name }}
                                    </div>
                                </h6>
                            </VListItemTitle>
                        </VListItem>
                        <VListItem>
                            <VListItemTitle>
                                <h6 class="text-h6">
                                    User Name:
                                    <div class="d-inline-block text-body-1">
                                        {{ props.currentUser.user_name }}
                                    </div>
                                </h6>
                            </VListItemTitle>
                        </VListItem>
                        <VListItem>
                            <VListItemTitle>
                                <span class="text-h6">
                                    Email:
                                </span>
                                <span class="text-body-1">
                                    {{ props.currentUser.email }}
                                </span>
                            </VListItemTitle>
                        </VListItem>

                        <VListItem>
                            <VListItemTitle>
                                <h6 class="text-h6">
                                    Status:
                                    <VChip :color="props.currentUser.status = true ? 'success' : 'error'"
                                        :text="props.currentUser.status = true ? 'Active' : 'Inactive'" />
                                </h6>
                            </VListItemTitle>
                        </VListItem>
                        <!-- 
                        <VListItem>
                            <VListItemTitle>
                                <h6 class="text-h6">
                                    Member Since:
                                    <div class="d-inline-block text-body-1 text-capitalize">
                                        {{ getMetaInfo(props.currentUser, 'join_date') }}
                                    </div>
                                </h6>
                            </VListItemTitle>
                        </VListItem> -->

                        <!-- <VListItem>
                            <VListItemTitle>
                                <h6 class="text-h6">
                                    Monthly Salary:
                                    <div class="d-inline-block text-capitalize text-body-1">
                                        {{ formatToIndianNumberRupee(getMetaInfo(props.currentUser, 'monthly_salary'))
                                            ??
                                            '0' }}
                                    </div>
                                </h6>
                            </VListItemTitle>
                        </VListItem> -->

                        <!-- <VListItem>
                            <VListItemTitle>
                                <h6 class="text-h6">
                                    Aadhar:
                                    <div class="d-inline-block text-capitalize text-body-1">
                                        {{ getMetaInfo(props.currentUser, 'aadhar_no') }}
                                    </div>
                                </h6>
                            </VListItemTitle>
                        </VListItem> -->

                        <!-- <VListItem>
                            <VListItemTitle>
                                <h6 class="text-h6">
                                    PAN:
                                    <div class="d-inline-block text-capitalize text-body-1">
                                        {{ getMetaInfo(props.currentUser, 'pan_no') }}
                                    </div>
                                </h6>
                            </VListItemTitle>
                        </VListItem> -->

                        <!-- <VListItem>
                            <VListItemTitle>
                                <h6 class="text-h6">
                                    Date of Birth
                                    <div class="d-inline-block text-body-1">
                                        {{ getMetaInfo(props.currentUser, 'date_of_birth') }}
                                    </div>
                                </h6>
                            </VListItemTitle>
                        </VListItem> -->
                        <!-- <VListItem>
                            <VListItemTitle>
                                <h6 class="text-h6">
                                    Contact:
                                    <div class="d-inline-block text-body-1">
                                        {{ formatToIndianNumber(props.currentUser.phone) || 'N/A' }}
                                    </div>
                                </h6>
                            </VListItemTitle>
                        </VListItem> -->
                        <!-- 
                        <VListItem>
                            <VListItemTitle>
                                <h6 class="text-h6">
                                    Address:
                                    <div class="d-inline-block text-wrap text-body-1">
                                        {{ props.currentUser.address }}
                                    </div>
                                </h6>
                            </VListItemTitle>
                        </VListItem> -->

                        <VListItem>
                            <VListItemTitle>
                                <h6 class="text-h6">
                                    Language:
                                    <div class="d-inline-block text-body-1">
                                        English, Hindi
                                    </div>
                                </h6>
                            </VListItemTitle>
                        </VListItem>

                        <VListItem>
                            <VListItemTitle>
                                <h6 class="text-h6">
                                    Country:
                                    <div class="d-inline-block text-body-1">
                                        India
                                    </div>
                                </h6>
                            </VListItemTitle>
                        </VListItem>
                    </VList>
                </VCardText>

                <!-- 👉 Edit and Suspend button -->
                <VCardText class="d-flex justify-center gap-x-4">
                    <VBtn variant="elevated" @click="isDialogVisible = true">
                        Edit
                    </VBtn>
                </VCardText>
            </VCard>
        </VCol>
    </VRow>

    <!-- 👉 Edit user info dialog -->
    <!-- <UserInfoEditDialog v-model:isDialogVisible="isDialogVisible" :user-data="props.currentUser" /> -->

    <!-- 👉 Create Edit Dialog -->
    <CreateEditDialog v-if="isDialogVisible" :roleList="roleList ?? []" @submit="getUsers"
        :currentInfo="props.currentUser" v-model:isDialogVisible="isDialogVisible" :peopleAdd="'User'" />
</template>
<script setup>
import { onMounted } from 'vue';
import CreateEditDialog from '../dialog/CreateEditDialog.vue';

const props = defineProps({
    currentUser: {
        type: Object,
        required: true,
    },
})

const BASE_URL = window.location.origin;
const isDialogVisible = ref(false)

// const getMetaInfo = (item, key) => {
//     const getValue = (metaArray) => {
//         const meta = metaArray?.find(meta => meta.meta_key === key);
//         return meta?.meta_value || "";
//     };

//     return getValue(item.meta) || getValue(item.employee?.employee_meta);
// };

const roleList = ref([]);
const getOptionList = async () => {
    try {
        const response = await $api(`/user/option-list`, { method: 'POST', body: {}, });
        roleList.value = response.data.roles;
    } catch (err) {
        console.error('Error fetching employee list:', err.message);
    }
};
onMounted(() => {
    getOptionList();
    console.log('props.user-data', props.currentUser)
})
</script>

<style lang="scss" scoped>
.card-list {
    --v-card-list-gap: 0.5rem;
}

.text-capitalize {
    text-transform: capitalize !important;
}
</style>
