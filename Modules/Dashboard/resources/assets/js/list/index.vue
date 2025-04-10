<script setup>
import { toast } from 'vue3-toastify'
const dashboardInfo = ref({})
const getDashboard = async () => {
  try {
    const response = await $api(
      `/dashboard`
    )

    dashboardInfo.value = response.data
  } catch (err) {
    console.error('Failed to fetch Dashboard:', err)
    toast.error('Failed to load Dashboard')
  }
} 

getDashboard();

</script>


<template>
  <VRow>
    <VCol
      cols="12"
      md="3"
      sm="6"
    >
      <div>
        <VCard
          class="logistics-card-statistics cursor-pointer"
           :style="`border-block-end-color: rgba(var(--v-theme-warning),0.38)`"
        >
          <VCardText>
            <div class="d-flex align-center gap-x-4 mb-1">
              <h4 class="text-h4">
                {{ dashboardInfo.clients }}
              </h4>
            </div>
            <div class="text-body-1 mb-1">
              Clients
            </div>
          </VCardText>
        </VCard>
      </div>
    </VCol>
    <VCol
      cols="12"
      md="3"
      sm="6"
    >
      <div>
        <VCard
          class="logistics-card-statistics cursor-pointer"
          :style="`border-block-end-color: rgba(var(--v-theme-primary),0.38)`"
          
        >
          <VCardText>
            <div class="d-flex align-center gap-x-4 mb-1">
              <h4 class="text-h4">
                {{ dashboardInfo.leads }}
              </h4>
            </div>
            <div class="text-body-1 mb-1">
              Leads
            </div>
          </VCardText>
        </VCard>
      </div>
    </VCol>
  </VRow>
</template>

<style lang="scss" scoped>
@use "@core-scss/base/mixins" as mixins;

.logistics-card-statistics {
  border-block-end-style: solid;
  border-block-end-width: 2px;

  &:hover {
    border-block-end-width: 3px;
    margin-block-end: -1px;

    @include mixins.elevation(8);

    transition: all 0.1s ease-out;
  }
}

.skin--bordered {
  .logistics-card-statistics {
    border-block-end-width: 2px;

    &:hover {
      border-block-end-width: 3px;
      margin-block-end: -2px;
      transition: all 0.1s ease-out;
    }
  }
}
</style>
