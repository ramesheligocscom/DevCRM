import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useCompanyStore = defineStore('company', () => {
    const companyDetails = ref(null);
    const loading = ref(false);

    const fetchCompanyDetails = async () => {
        if (companyDetails.value) return; // Prevent duplicate API calls
        loading.value = true;

        try {
            const response = await $api('/settings');
            companyDetails.value = response.data?.settings ?? null;
        } catch (error) {
            console.error('Failed to fetch company details:', error);
        } finally {
            loading.value = false;
        }
    };

    const getPaymentTerms = () => companyDetails.value?.Payment_Details || "";
    const getTermsConditions = () => companyDetails.value?.Terms_Condition || "";
    const getStarterTerms = () => companyDetails.value?.Greeting_Message || "";

    return {
        companyDetails,
        loading,
        fetchCompanyDetails,
        getPaymentTerms,
        getTermsConditions,
        getStarterTerms,
    };
});
