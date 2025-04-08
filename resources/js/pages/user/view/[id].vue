<template>
    <div>
        <BaseSpinner class="d-flex" v-if="loading" />
        <VRow v-else-if="userData">
            <VCol cols="12" md="5" lg="4">
                <UserBioPanel :currentUser="userData" @submit="getUserList" />
            </VCol>

            <VCol cols="12" md="7" lg="8">
                <div class="d-flex justify-space-between">
                    <VTabs v-model="userTab" class="v-tabs-pill">
                        <VTab v-for="tab in filterTabs" :key="tab.icon">
                            <VIcon :size="18" :icon="tab.icon" class="me-1" />
                            <span>{{ tab.title }}</span>
                        </VTab>
                    </VTabs>
                    <RouterLink to="/user/list">
                        <VBtn color="secondary" variant="tonal">Back</VBtn>
                    </RouterLink>
                </div>

                <VWindow v-model="userTab" class="mt-6 disable-tab-transition" :touch="false">
                    <!-- User Lead List  -->
                    <VWindowItem>
                        <UserTabLead v-if="$can('leads', 'view')" />
                        <!-- <UserTabAccount /> -->
                    </VWindowItem>

                    <!-- User Client List -->
                    <VWindowItem>
                        <UserTabClient v-if="$can('client', 'view')" />
                    </VWindowItem>

                    <!-- User Quotation List -->
                    <VWindowItem>
                        <UserTabQuotation v-if="$can('quotation', 'view')" />
                    </VWindowItem>

                    <!-- User Contract List -->
                    <VWindowItem>
                        <UserTabContract v-if="$can('contract', 'view')" />
                    </VWindowItem>

                    <!-- User Invoice List -->
                    <VWindowItem>
                        <UserTabInvoice v-if="$can('invoice', 'view')" />
                    </VWindowItem>
                </VWindow>
            </VCol>
        </VRow>
        <div v-else>
            <VAlert type="error" variant="tonal">
                Invoice with ID {{ route.params.id }} not found!
            </VAlert>
        </div>
    </div>
</template>

<script setup>
import { computed, defineAsyncComponent, getCurrentInstance, onMounted, ref } from 'vue';
import { useRoute } from 'vue-router';
import UserBioPanel from './UserProfile.vue';

const instance = getCurrentInstance();
const $can = instance?.proxy?.$can;
const route = useRoute()

const userTab = ref(null)
const userData = ref();
const loading = ref(true);

// Load all available module components dynamically
const modules = import.meta.glob('/Modules/**/resources/assets/js/list/index.vue')

const getComponentByModule = (moduleName) => {
    const matchPath = Object.keys(modules).find(path => path.includes(`/Modules/${moduleName}/`));
    return matchPath ? defineAsyncComponent(modules[matchPath]) : null;
}

// Components will be null if not found
const UserTabLead = getComponentByModule('Leads')
const UserTabClient = getComponentByModule('Clients')
const UserTabQuotation = getComponentByModule('Quotations')
const UserTabContract = getComponentByModule('Contracts')
const UserTabInvoice = getComponentByModule('Invoices')

const tabs = [
    {
        icon: 'tabler-users',
        title: 'Leads',
        action: 'leads',
        slug: 'view',
        component: UserTabLead,
    },
    {
        icon: 'tabler-users',
        title: 'Clients',
        action: 'client',
        slug: 'view',
        component: UserTabClient,
    },
    {
        icon: 'tabler-users',
        title: 'Quotations',
        action: 'quotation',
        slug: 'view',
        component: UserTabQuotation,
    },
    {
        icon: 'tabler-users',
        title: 'Contracts',
        action: 'contract',
        slug: 'view',
        component: UserTabContract,
    },
    {
        icon: 'tabler-users',
        title: 'Invoices',
        action: 'invoice',
        slug: 'view',
        component: UserTabInvoice,
    },
]

const filterTabs = computed(() => {
    return tabs.filter(item => {
        const hasPermission = $can?.(item.action, item.slug);
        const hasExtraPermission = item.extraPermissions?.some(extra => $can?.(item.action, extra));
        return (hasPermission || hasExtraPermission) && item.component;
    });
});

const getUserData = async () => {
    try {
        loading.value = true;
        const response = await $api(`user/${route.params.id}`);
        if (response) {
            userData.value = response.data;
        }
    } catch (error) {
        console.error(error._data.message);
    } finally {
        loading.value = false;
    }
}

onMounted(async () => {
    await getUserData();
});
</script>
