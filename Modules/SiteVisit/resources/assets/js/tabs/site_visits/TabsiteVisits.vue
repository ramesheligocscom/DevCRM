<script setup>
import SiteVisitTable from './TabList.vue';
</script>

<template>
  <VRow>
    <VCol cols="12" md="6">
      <VCard>
        <VCardText class="d-flex gap-y-2 flex-column">
          <VAvatar variant="tonal" color="primary" icon="tabler-currency-dollar" rounded />
          <h5 class="text-h5">
            Account Balance
          </h5>
          <div>
            <h5 class="text-h5 text-primary mb-1">
              $7480
              <span class="text-body-1 d-inline-block">Credit Left</span>
            </h5>
            <p class="mb-0">
              Account balance for next purchase
            </p>
          </div>
        </VCardText>
      </VCard>
    </VCol>

    <VCol cols="12" md="6">
      <VCard>
        <VCardText class="d-flex gap-y-2 flex-column">
          <VAvatar variant="tonal" color="success" icon="tabler-gift" rounded />
          <h5 class="text-h5">
            Loyalty Program
          </h5>
          <div>
            <VChip color="success" class="mb-2" label size="small">
              Platinum member
            </VChip>
            <p class="mb-0">
              3000 points to next tier
            </p>
          </div>
        </VCardText>
      </VCard>
    </VCol>
    <VCol>
      <SiteVisitTable />
    </VCol>
  </VRow>
</template>
