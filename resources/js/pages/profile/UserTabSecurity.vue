<template>
  <VRow>
    <VCol cols="12">
      <VCard title="Change Password">
        <VCardText>
          <VAlert
            closable
            variant="tonal"
            color="warning"
            class="mb-4"
            title="Ensure that these requirements are met"
            text="Minimum 8 characters long, with at least 1 uppercase letter & 1 symbol"
          />

          <VForm v-model="valid" @submit.prevent="onSubmit">
            <VRow>
              <VCol cols="12" md="6">
                <AppTextField
                  v-model="credentials.password"
                  label="New Password"
                  placeholder="············"
                  :type="isNewPasswordVisible ? 'text' : 'password'"
                  :append-inner-icon="isNewPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"
                  :rules="[...requiredRule, ...minLengthRule(8)]"
                  :error-messages="errors.password"
                  @click:append-inner="isNewPasswordVisible = !isNewPasswordVisible"
                />
              </VCol>

              <VCol cols="12" md="6">
                <AppTextField
                  v-model="credentials.confirmPassword"
                  label="Confirm Password"
                  placeholder="············"
                  :type="isConfirmPasswordVisible ? 'text' : 'password'"
                  :append-inner-icon="isConfirmPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"
                  :rules="[...requiredRule, ...minLengthRule(8), ...confirmPasswordMatchRule(credentials.password)]"
                  :error-messages="errors.confirmPassword"
                  @click:append-inner="isConfirmPasswordVisible = !isConfirmPasswordVisible"
                />
              </VCol>

              <VCol cols="12">
                <VBtn type="submit" :loading="loading" :disabled="loading">
                  Change Password
                </VBtn>
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
      </VCard>
    </VCol>

        <!-- 👉 Two step verification -->
    <!-- <VCol cols="12">
      <VCard
        title="Two-steps verification"
        subtitle="Keep your account secure with authentication step."
      >
        <VCardText>
          <div class="text-h6 mb-1">
            SMS
          </div>
          <AppTextField placeholder="+1(968) 819-2547">
            <template #append>
              <IconBtn color="secondary">
                <VIcon
                  icon="tabler-edit"
                  size="22"
                />
              </IconBtn>
              <IconBtn color="secondary">
                <VIcon
                  icon="tabler-user-plus"
                  size="22"
                />
              </IconBtn>
            </template>
          </AppTextField>

          <p class="mb-0 mt-4">
            Two-factor authentication adds an additional layer of security to your account by requiring more than just a password to log in. <a
              href="javascript:void(0)"
              class="text-decoration-none"
            >Learn more</a>.
          </p>
        </VCardText>
      </VCard>
    </VCol> -->

    <!-- Login Log Component -->
    <VCol cols="12">
      <LoginLog />
    </VCol>
  </VRow>

  <!-- Two-Factor Auth Dialog (if needed later) -->
  <TwoFactorAuthDialog
    v-model:isDialogVisible="isTwoFactorDialogOpen"
    :sms-code="smsVerificationNumber"
  />
</template>

<script setup>
import { confirmPasswordMatchRule, minLengthRule, requiredRule } from '@/validations/validationRules';
import { ref } from 'vue';
import { useRoute } from 'vue-router';
import { toast } from 'vue3-toastify';
import LoginLog from './login-log.vue';

// State
const route = useRoute();
const valid = ref(true);
const loading = ref(false);
const isNewPasswordVisible = ref(false);
const isConfirmPasswordVisible = ref(false);
const isTwoFactorDialogOpen = ref(false);
const smsVerificationNumber = ref('+91 98765 43210'); 

const credentials = ref({
  password: '',
  confirmPassword: '',
});

const errors = ref({
  password: [],
  confirmPassword: [],
});

// Submit handler
const onSubmit = async () => {
  loading.value = true;
  errors.value = { password: [], confirmPassword: [] };

  if (credentials.value.password !== credentials.value.confirmPassword) {
    errors.value.confirmPassword = ['Password and confirm password should match'];
    toast.error('Password and confirm password should match');
    loading.value = false;
    return;
  }

  try {
    const { data } = await $api(`/update-password/${route.params.id}`, { method: 'POST', body: credentials.value });

    if (data.status) {
      toast.success(data.message || 'Password changed successfully');
    } else {
      toast.error(data.message || 'Failed to update password');
    }
  } catch (error) {
    toast.error('An error occurred while updating the password');
    console.error(error);
  } finally {
    loading.value = false;
  }
};
</script>
