import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useCompanyStore = defineStore('company', () => {
    const companyDetails = ref(null);
    const loading = ref(false);

    const fetchCompanyDetails = async () => {
        if (companyDetails.value) return;
        loading.value = true;
        try {
            const response = await $api('/settings');
            companyDetails.value = response.data ?? null;
        } catch (error) {
            console.error('Failed to fetch company details:', error);
        } finally {
            loading.value = false;
        }
    };

    return {
        companyDetails,
        loading,
        fetchCompanyDetails,
    };
});
