import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useCompanyStore = defineStore('company', () => {
    const companyDetails = ref(null);
    const loading = ref(false);

    const fetchCompanyDetails = async () => {
        if (companyDetails.value) return;
        loading.value = true;
        try {
            console.log('Calling API...');
            const response = await $api('/settings');
            console.log('API response:', response);
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
