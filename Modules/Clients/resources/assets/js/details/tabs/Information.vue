<script setup>

const props = defineProps({
  InfoData: {
    type: null,
    required: true,
  },
})

</script>

<template>
  <VRow>
    <VCol cols="12">
      <VCard v-if="props.InfoData">
        <VCardText>
          <!-- SECTION Client Info -->
          <h5 class="text-h5 mb-4">Client Details</h5>

          <VRow dense>
            <VCol cols="12" md="4" lg="4">
              <div class="d-flex align-center gap-x-2 mt-1">
                <strong>Name:</strong>
                <span>{{props.InfoData.name }}</span>
              </div>
            </VCol>
            <VCol cols="12" md="4" lg="4">
              <div class="d-flex align-center gap-x-2 mt-1">
                <strong>Email:</strong>
                <span>{{props.InfoData.email }}</span>
              </div>
            </VCol>
 
            <VCol cols="12" md="4" lg="4">
              <div class="d-flex align-center gap-x-2 mt-1">
                <strong>Phone:</strong>
                <span>{{props.InfoData.phone }}</span>
              </div>
            </VCol>

            <VCol cols="12" md="4" lg="4">
              <div class="d-flex align-center gap-x-2 mt-1">
                <strong>Status:</strong>
                <VChip label :color="props.InfoData.status === 'active' ? 'success' : 'default'" size="small">
                  {{ props.InfoData.status }}
                </VChip>
              </div>
            </VCol>

            <VCol cols="12" md="4" lg="4">
              <div class="d-flex align-center gap-x-2 mt-1">
                <strong>Created By:</strong>
                <span>{{ props.InfoData.creator?.name || '-' }}</span>
              </div>
            </VCol>

            <VCol cols="12" md="4" lg="4">
              <div class="d-flex align-center gap-x-2 mt-1">
                <strong>Last Updated By:</strong>
                <span>{{ props.InfoData.updater?.name || '-' }}</span>
              </div>
            </VCol>
          </VRow>


         
        </VCardText>
      </VCard>
      <VAlert v-else type="info" variant="tonal" class="mt-4">
            No items found for this client.
      </VAlert>
    </VCol>

  </VRow>
</template>

<style scoped>
.card-list {
  --v-card-list-gap: 0.5rem;
}
</style>
