<script setup>
const props = defineProps({
  InfoData: {
    type: null,
    required: true,
  },
})

const clientName = computed(() => props.InfoData.client?.name || `-`)
const quotationName = computed(() => props.InfoData.quotation?.quotation_number || `-`)
const contractName = computed(() => props.InfoData.contract?.title || `-`)
const invoiceName = computed(() => props.InfoData.invoice?.invoice_number || `-`)

</script>


<template>
  <VRow>
    <VCol cols="12">
      <VCard v-if="props.InfoData">
        <VCardText>
          <!-- SECTION Invoice Info -->
          <h5 class="text-h5 mb-4">Invoice Details</h5>

          <VRow dense>
            <VCol cols="12" md="4" lg="4">
              <div class="d-flex align-center gap-x-2 mt-1">
                <strong>Name:</strong>
                <span>{{ props.InfoData.name || '-' }}</span>
              </div>
            </VCol>

            <VCol cols="12" md="4" lg="4">
              <div class="d-flex align-center gap-x-2 mt-1">
                <strong>Email:</strong>
                <span>{{ props.InfoData.email || '-' }}</span>
              </div>
            </VCol>

            <VCol cols="12" md="4" lg="4">
              <div class="d-flex align-center gap-x-2 mt-1">
                <strong>Phone:</strong>
                <span>{{ props.InfoData.phone || '-' }}</span>
              </div>
            </VCol>

            <VCol cols="12" md="4" lg="4">
              <div class="d-flex align-center gap-x-2 mt-1">
                <strong>Address:</strong>
                <span>{{ props.InfoData.address || '-' }}</span>
              </div>
            </VCol>

            <VCol cols="12" md="4" lg="4">
              <div class="d-flex align-center gap-x-2 mt-1">
                <strong> Contact Person:</strong>
                <span>{{ props.InfoData.contact_person || '-' }}</span>
              </div>
            </VCol>

            <VCol cols="12" md="4" lg="4">
              <div class="d-flex align-center gap-x-2 mt-1">
                <strong>Contact Role:</strong>
                <span>{{ props.InfoData.contact_person_role || '-' }}</span>
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
                <strong>Source:</strong>
                <span>{{ props.InfoData.source || '-' }}</span>
              </div>
            </VCol>

            <VCol cols="12" md="4" lg="4">
              <div class="d-flex align-center gap-x-2 mt-1">
                <strong> Assigned User:</strong>
                <span>{{ props.InfoData.assigned_user || '-' }}</span>
              </div>
            </VCol>

            <VCol cols="12" md="4" lg="4">
              <div class="d-flex align-center gap-x-2 mt-1">
                <strong>Note:</strong>
                <span>{{ props.InfoData.note || '-' }}</span>
              </div>
            </VCol> 

            <VCol cols="12" md="4" lg="4">
              <div class="d-flex align-center gap-x-2 mt-1">
                <strong>Visit Assignee:</strong>
                <span>{{ props.InfoData.visit_assignee || '-' }}</span>
              </div>
            </VCol> 

            <VCol cols="12" md="4" lg="4">
              <div class="d-flex align-center gap-x-2 mt-1">
                <strong>Visit Time:</strong>
                <span>{{ props.InfoData.visit_time || '-' }}</span>
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

            <VCol cols="12" md="4" lg="4">
              <div class="d-flex align-center gap-x-2 mt-1">
                <strong>Client:</strong>
                <span>{{ clientName }}</span>
              </div>
            </VCol>

            <VCol cols="12" md="4" lg="4">
              <div class="d-flex align-center gap-x-2 mt-1">
                <strong>Contract:</strong>
                <span>{{ contractName }}</span>
              </div>
            </VCol>

            <VCol cols="12" md="4" lg="4">
              <div class="d-flex align-center gap-x-2 mt-1">
                <strong>Quotation:</strong>
                <span>{{ quotationName }}</span>
              </div>
            </VCol>

            <VCol cols="12" md="4" lg="4">
              <div class="d-flex align-center gap-x-2 mt-1">
                <strong>Invoice:</strong>
                <span>{{ invoiceName }}</span>
              </div>
            </VCol>
          </VRow>
        </VCardText>
      </VCard>
    </VCol>
  </VRow>
</template>

<style scoped>
.card-list {
  --v-card-list-gap: 0.5rem;
}
</style>
