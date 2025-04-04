<script setup>
import { getCurrentInstance } from "vue";
import ClientFollowUpTab from "@/views/apps/ecommerce/customer/view/ClientFollowUpTab.vue";
import ClientSiteRiskTab from "@/views/apps/ecommerce/customer/view/ClientSiteRiskTab.vue";
import ClientQuotationTab from "@/views/apps/ecommerce/customer/view/ClientQuotationTab.vue";
import ClientContractTab from "@/views/apps/ecommerce/customer/view/ClientContractTab.vue";
import { VDivider } from "vuetify/lib/components/index.mjs";
import { $api } from "@/utils/api";
import { useRouter, useRoute } from "vue-router";
const instance = getCurrentInstance();
const $can = instance?.proxy?.$can;
const loading = ref(true);

const route = useRoute();
const router = useRouter();
const client_id = route.params.id;
const isLeadinfoVisible = ref(true);
// const activePanel = ref("3");

const userTab = ref(localStorage.getItem("ClientActiveTab") || "0");

watch(userTab, (newTab) => {
  localStorage.setItem("ClientActiveTab", newTab);
});

// Retrieve stored tab index when the component is mounted
onMounted(() => {
  userTab.value = localStorage.getItem("ClientActiveTab") || "0";
});

const tabs = [
  {
    title: "Follow Up",
    icon: "tabler-bell",
    slug: "follow-up",
    extraPermissions: ["activity-timeline"],
  },
  // {
  //   title: "Site Risk Management",
  //   icon: "tabler-augmented-reality",
  //   slug: 'view-site-risk-management',
  // },
  {
    title: "Quotation",
    icon: "tabler-brand-asana",
    slug: "follow-up",
  },
  {
    title: "Contracts",
    icon: "tabler-file-stack",
    slug: "view-contract",
  },
];

const filterTabs = computed(() => {
  return tabs.filter((item) => {
    const hasPermission = $can?.("client", item.slug);
    const hasExtraPermission = item.extraPermissions?.some((extra) =>
      $can?.("client", extra)
    );
    return hasPermission || hasExtraPermission;
  });
});

const beforeEnter = (el) => {
  // You can set initial styles before the transition starts
  el.style.opacity = 0;
};

const enter = (el, done) => {
  // Animation logic for entering
  el.offsetHeight; // Trigger reflow to apply transition
  el.style.transition = "opacity 0.5s ease-in-out";
  el.style.opacity = 1;

  // Call done after the transition ends
  el.addEventListener("transitionend", done, { once: true });
};

const leave = (el, done) => {
  // Animation logic for leaving
  el.style.transition = "opacity 0.5s ease-out";
  el.style.opacity = 0;

  // Call done after the transition ends
  el.addEventListener("transitionend", done, { once: true });
};

const clientEmail = ref("");
const clientContact = ref("");
const clientStatus = ref("");
const clientContactPerson = ref("");
const clientName = ref("");
const specificClientContractList = ref([]);
const siteRiskData = ref([]);

const fetchClientInfo = async () => {
  const data = await $api(`/clients/${client_id}`, {
    method: "GET",
  });

  // console.log(data);
  clientEmail.value = data.data.email;
  clientContact.value = data.data.phone;
  clientStatus.value = data.data.status;
  clientName.value = data.data.name;
  clientContactPerson.value = data.data.contact_person;
  loading.value = false;
};

const fetchClientContracts = async () => {
  const data = await $api(`/contract/client-contract/${client_id}`);
  if (data.status == 201) {
    specificClientContractList.value = data.data;
  } else {
  }
};
onMounted(() => {
  fetchClientInfo();
  fetchClientContracts();
  setSiteRiskData();
  setQuotationData();
});

const visibleDetails = ref(false);
const invisibleDetail = () => {
  visibleDetails.value = !visibleDetails.value;
};
const visibleDetails1 = ref(false);
const invisibleDetail1 = () => {
  visibleDetails1.value = !visibleDetails1.value;
};

const followUpData = ref([]);

const setSiteRiskData = async () => {
  const data = await $api(`/visit-sites/client/${client_id}`);
  siteRiskData.value = data.data;
};

const quotationData = ref([]);
const setQuotationData = async () => {
  const data = await $api(`/quotation/client/${client_id}`);
  // console.log(data.data);
  if (data.status == 201) {
    quotationData.value = data.data;
  } else {
  }
};
// const contractData = ref([specificClientContractList.value]);
const resolveStatusVariant = (status) => {
  if (status === "Active") return { color: "success", text: "Active" };
  else if (status === "In Active") return { color: "error", text: "In Active" };
  else return { color: "success", text: "Active" };
};
</script>

<template>
  <div v-if="$can('client', 'show-client')">
    <VContainer>
      <VRow>
        <transition :css="false" @before-enter="beforeEnter" @enter="enter" @leave="leave">
          <VCol v-if="isLeadinfoVisible" cols="12" md="4">
            <VCard class="pa-8">
              <VCardTitle class="rounded pa-5">

                <div class="d-flex justify-center">
                  <VAvatar rounded="" size="x-large" color='primary' variant="tonal">
                    <h4 class="text-h4">{{ avatarText(clientName).slice(0, 2) }}</h4>
                  </VAvatar>
                </div>
                <small>Details</small>
                <VDivider class="mb-4 mt-2" />

                <ul class="list-unstyled my-0 py-1" v-if="clientStatus">
                  <li class="d-flex align-items-center mb-2">
                    <VIcon size="20" icon="tabler-user" />
                    <span class="fetchLeadText mx-2">Name:</span>
                    <span class="fetchLeadText">{{
                      clientName.split(" - (")[0]
                    }}</span>
                  </li>
                  <li class="align-items-center mb-2 d-flex flex-wrap gap-2">
                    <div>
                      <VIcon size="20" icon="tabler-mail" />
                      <span class="fetchLeadText mx-2">Email:</span>
                    </div>

                    <span class="fetchLeadText text-wrap " style="width: fit-content;">{{ clientEmail }}</span>
                  </li>
                  <li class="d-flex align-items-center mb-2 d-flex flex-wrap gap-2">
                    <VIcon size="20" icon="tabler-phone" />
                    <span class="fetchLeadText">Contact:</span>
                    <span class="fetchLeadText">{{ clientContact }}</span>
                  </li>
                  <li class="d-flex align-items-center mb-2 d-flex flex-wrap gap-2">
                    <VIcon size="20" icon="tabler-brand-bitbucket" />
                    <span class="fetchLeadText">Organisation :</span>
                    <span class="fetchLeadText text-wrap">{{
                      clientContactPerson
                    }}</span>
                  </li>
                  <li class="d-flex align-items-center mb-2">
                    <VIcon size="20" icon="tabler-progress" />
                    <span class="fetchLeadText mx-2">Status:</span>
                    <VChip :color="resolveStatusVariant(clientStatus).color" class="font-weight-medium" size="small"
                      style="
                        justify-content: center !important;
                        min-width: 10% !important;
                      ">
                      {{ resolveStatusVariant(clientStatus).text }}
                    </VChip>
                  </li>
                </ul>
                <ul v-else>
                  <BaseSpinner class="d-flex" v-if="loading" />
                </ul>
              </VCardTitle>
            </VCard>
          </VCol>
        </transition>
        <VCol class="leadsActivityHide" cols="12" md="8">
          <VRow>
            <VCol class="leadsActivityHide d-flex gap-2 align-center justify-lg-space-between align-sm-end" cols="12"
              md="12">
              <div class="d-flex gap-2">
                <IconBtn color="primary" variant="tonal">
                  <VIcon icon="tabler-user" />
                </IconBtn>

                <div class="d-flex gap-3 justify-space-between">
                  <VTabs v-model="userTab" class="v-tabs-pill disable-tab-transition">
                    <VTab v-for="tab in filterTabs" :key="tab.title">
                      <VIcon size="20" start :icon="tab.icon" />
                      {{ tab.title }}
                    </VTab>
                  </VTabs>
                </div>
              </div>

              <VBtn @click="router.back()" class="ml-3">
                <VIcon icon="tabler-arrow-back-up" />
                Back
              </VBtn>

            </VCol>

            <VCol cols="12" md="12">
              <VWindow v-model="userTab" :touch="false">
                <VWindowItem v-for="(tab, index) in tabs" :key="index" :value="index">
                  <ClientFollowUpTab v-if="
                    tab.title == 'Follow Up' && $can('client', 'follow-up')
                  " :siteRisk="followUpData" />
                  <ClientSiteRiskTab v-if="
                    tab.title == 'Site Risk Management' &&
                    $can('client', 'view-site-risk-management')
                  " :siteRisk="siteRiskData" />
                  <ClientQuotationTab v-if="
                    tab.title == 'Quotation' &&
                    $can('client', 'view-quotation')
                  " :quotation="quotationData" />
                  <ClientContractTab v-if="
                    tab.title == 'Contracts' &&
                    $can('client', 'view-contract')
                  " :contract="specificClientContractList" />
                </VWindowItem>
              </VWindow>
            </VCol>
          </VRow>
        </VCol>
      </VRow>
    </VContainer>
  </div>
</template>

<style scoped>
.leadDataText {
  font-size: 16px;
  font-weight: 500;
}

.highlighted-card {
  /* box-shadow: 0px 0px 20px rgba(14, 118, 255, 0.726) !important; */
  border: 2px solid rgb(var(--v-theme-primary)) !important;
}
</style>
