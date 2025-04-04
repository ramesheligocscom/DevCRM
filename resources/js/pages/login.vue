<!-- ❗Errors in the form are set on line 60 -->
<script setup>
import { useGenerateImageVariant } from '@core/composable/useGenerateImageVariant'
import authV2LoginIllustrationBorderedDark from '@images/pages/auth-v2-login-illustration-bordered-dark.png'
import authV2LoginIllustrationBorderedLight from '@images/pages/auth-v2-login-illustration-bordered-light.png'
import authV2LoginIllustrationDark from '@images/pages/auth-v2-login-illustration-dark.png'
import authV2LoginIllustrationLight from '@images/pages/auth-v2-login-illustration-light.png'
import authV2MaskDark from '@images/pages/misc-mask-dark.png'
import authV2MaskLight from '@images/pages/misc-mask-light.png'
import { themeConfig } from '@themeConfig'
import { toast } from 'vue3-toastify'
import { VForm } from 'vuetify/components/VForm'

const authThemeImg = useGenerateImageVariant(authV2LoginIllustrationLight, authV2LoginIllustrationDark, authV2LoginIllustrationBorderedLight, authV2LoginIllustrationBorderedDark, true)
const authThemeMask = useGenerateImageVariant(authV2MaskLight, authV2MaskDark)

definePage({
    meta: {
        layout: 'blank',
        unauthenticatedOnly: true,
    },
})
const ability_list = ref([])
const isPasswordVisible = ref(false)
const route = useRoute()
const router = useRouter()
const ability = useAbility()
const refVForm = ref()
const loading = ref(false)
const errors = ref({})
const credentials = ref({
    email: 'test@example.com',
    password: 'password',
})

const routeList = ref([
    { route: '/admin/dashboard', action: 'dashboard', slug: 'view-dashboard' },
    { route: '/admin/clients', action: 'client', slug: 'view-client' },
    { route: '/admin/calendar', action: 'calendar', slug: 'view-calendar' },
    { route: '/admin/quotations', action: 'quotation', slug: 'view-quotations' },
    { route: '/admin/contracts', action: 'contract', slug: 'view-contract' },
    { route: '/admin/invoices-list', action: 'invoice', slug: 'view-invoice' },
    { route: '/admin/contract-schedulings', action: 'schedule', slug: 'view-schedule' },
]);

const rememberMe = ref(false)

const login = async () => {
    loading.value = true;
    try {
        const response = await $api(`api/login`, { method: 'POST', body: { email: credentials.value.email, password: credentials.value.password, remember_me: rememberMe.value }, });
        if (response.status) {
            useCookie('userData').value = response.data.user
            useCookie('accessToken').value = response.data.access_token

            const abilityRules = response.data.permissions.map((p) => {
                let obj = { action: p.action, subject: p.slug };
                ability_list.value.push(obj);
                return obj;
            });

            const user = response.data.user;

            // Store in localStorage
            localStorage.setItem('user_id', user.id);
            localStorage.setItem('user_name', user.name);

            localStorage.setItem('permission_list', JSON.stringify(ability_list.value)) ?? [];

            // Helper function to check if all cookies are set
            const areCookiesSet = () => useCookie('userData').value && useCookie('accessToken').value;
            let attempts = 0;
            await new Promise((resolve) => {
                const interval = setInterval(() => {
                    attempts++;

                    if (areCookiesSet()) {
                        clearInterval(interval);
                        resolve();
                    }
                }, 100);
            });

            const userPermissions = JSON.parse(localStorage.getItem('permission_list')) || [];
            // Find the first allowed route
            const allowedRoute = routeList.value.find(({ slug }) =>
                userPermissions.some(permission => permission.subject === slug)
            );

            if (allowedRoute) {
                window.location.href = allowedRoute.route;
            } else {
                router.push("/dashboards/crm");
            }
        } else {
            toast.error(response.message);
            loading.value = false;
        }
    } catch (error) {
    loading.value = false;
    toast.error(error?.response?._data?.message || error?.data?.message || error?.message || 'An error occurred.');
    }
}

const onSubmit = () => {
    refVForm.value?.validate().then(({ valid: isValid }) => {
        if (isValid)
            login()
    })
}
</script>

<template>
    <RouterLink to="/">
        <div class="auth-logo d-flex align-center gap-x-3">
            <!-- <VNodeRenderer :nodes="themeConfig.app.logo"/> -->
            <h1 class="auth-title">
                {{ themeConfig.app.title }}
            </h1>
        </div>
    </RouterLink>

    <VRow no-gutters class="auth-wrapper bg-surface">
        <VCol md="8" class="d-none d-md-flex">
            <div class="position-relative bg-background w-100 me-0">
                <div class="d-flex align-center justify-center w-100 h-100" style="padding-inline: 6.25rem;">
                    <VImg max-width="613" :src="authThemeImg" class="auth-illustration mt-16 mb-2" />
                </div>

                <img class="auth-footer-mask" :src="authThemeMask" alt="auth-footer-mask" height="280" width="100">
            </div>
        </VCol>

        <VCol cols="12" md="4" class="auth-card-v2 d-flex align-center justify-center">
            <VCard flat :max-width="500" class="mt-12 mt-sm-0 pa-4">
                <VCardText>
                    <h4 class="text-h4 mb-1">
                        Welcome to <span class="text-capitalize"> {{ themeConfig.app.title }} </span>! 👋🏻
                    </h4>
                    <p class="mb-0">
                        Please sign-in to your account and start the adventure.
                    </p>
                </VCardText>
                <VCardText>
                    <VForm ref="refVForm" @submit.prevent="onSubmit">
                        <VRow>
                            <!-- 👉 email -->
                            <VCol cols="12">
                                <AppTextField v-model="credentials.email" label="Email" placeholder="johndoe@email.com"
                                    type="email" autofocus :rules="[requiredValidator, emailValidator]"
                                    :error-messages="errors?.email" />
                            </VCol>

                            <!-- 👉 password -->
                            <VCol cols="12">
                                <AppTextField v-model="credentials.password" label="Password" placeholder="············"
                                    :rules="[requiredValidator]" :type="isPasswordVisible ? 'text' : 'password'"
                                    :error-messages="errors?.password"
                                    :append-inner-icon="isPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"
                                    @click:append-inner="isPasswordVisible = !isPasswordVisible" />

                                <div class="d-flex align-center flex-wrap justify-space-between my-6">
                                    <VCheckbox v-model="rememberMe" label="Remember me" />
                                    <RouterLink class="text-primary ms-2 mb-1" :to="{ name: 'forgot-password' }">
                                        Forgot Password?
                                    </RouterLink>
                                </div>

                                <VBtn block type="submit" :loading="loading">
                                    Login
                                </VBtn>
                            </VCol>
                            <!-- <VCol cols="12" class="text-center text-base">
                                <span class="d-inline-block">New To Nidhi ?</span>
                                <RouterLink class="text-primary ms-1 d-inline-block" :to="{ name: 'register' }">
                                    Sign Up
                                </RouterLink>
                            </VCol> -->
                        </VRow>
                    </VForm>
                </VCardText>
            </VCard>
        </VCol>
    </VRow>
</template>

<style lang="scss">
@use "@core-scss/template/pages/page-auth";
</style>
