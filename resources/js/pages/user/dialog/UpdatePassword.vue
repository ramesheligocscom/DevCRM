<template>
    <VDialog :width="$vuetify.display.smAndDown ? 'auto' : 900" :model-value="props.isDialogVisible"
        @update:model-value="dialogModelValueUpdate" scrollable persistent>
        <!-- Dialog close btn -->
        <DialogCloseBtn @click="dialogModelValueUpdate(false)" />

        <VCard class="pa-sm-10 pa-2">
            <VForm ref="refForm" v-model="valid" @submit.prevent="onSubmit">
                <VRow>
                    <VCol cols="12">
                        <AppTextField v-model="credentials.password" label="Password" placeholder="············"
                            :rules="[...requiredRule, ...minLengthRule(8)]"
                            :type="isPasswordVisible ? 'text' : 'password'" :error-messages="errors?.password"
                            :append-inner-icon="isPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"
                            @click:append-inner="isPasswordVisible = !isPasswordVisible" />

                    </VCol>

                    <VCol cols="12">
                        <AppTextField v-model="credentials.confirmPassword" label="Confirm Password"
                            placeholder="············"
                            :rules="[...requiredRule, ...minLengthRule(8), ...confirmPasswordMatchRule(credentials.password)]"
                            :type="isPasswordVisible ? 'text' : 'password'" :error-messages="errors?.password"
                            autocomplete="on" :append-inner-icon="isPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"
                            @click:append-inner="isPasswordVisible = !isPasswordVisible" />
                    </VCol>

                    <VBtn block type="submit" :loading="loading">
                        Save
                    </VBtn>
                </VRow>
            </VForm>
        </VCard>
    </VDialog>
</template>
<script setup>
import { confirmPasswordMatchRule, minLengthRule, requiredRule } from '@/validations/validationRules';
import { toast } from 'vue3-toastify';

const props = defineProps({
    user_id: { type: Object, required: false, },
    isDialogVisible: { type: Boolean, required: true, },
});

const valid = ref(true);
const isPasswordVisible = ref(false);

const credentials = ref({
    password: '',
    confirmPassword: '',
});

const errors = ref({});
const loading = ref(false);

const emit = defineEmits([
    'submit',
    'update:isDialogVisible',
])

const onSubmit = async () => {
    loading.value = true;
    if (credentials.value.password != credentials.value.confirmPassword) {
        loading.value = false;
        return toast.error('Password and confirm password should matched');
    } else {
        if (props.user_id) {
            const data = await $api(`/update-password/${props.user_id}`, {
                method: 'POST',
                body: credentials.value,
            });
            if (data.status) {
                toast.success(`${data.message}`);
            }
            dialogModelValueUpdate(false);
            loading.value = false;
            emit('submit', props.user_id)
        }
    }
}

const dialogModelValueUpdate = val => {
    credentials.value = {
        password: '',
        confirmPassword: '',

    };
    emit('update:isDialogVisible', val)
}
</script>
