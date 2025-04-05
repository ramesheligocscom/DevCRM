<script setup>

const props = defineProps({
  LeadData: {
    type: null,
    required: true,
  },
})

const clientName = computed(() => props.LeadData.client?.name || `ID: ${props.LeadData.client_id || 'N/A'}`)
const quotationName = computed(() => props.LeadData.quotation?.name || `ID: ${props.LeadData.quotation_id || 'N/A'}`)
const contractName = computed(() => props.LeadData.contract?.name || `ID: ${props.LeadData.contract_id || 'N/A'}`)
const invoiceName = computed(() => props.LeadData.invoice?.name || `ID: ${props.LeadData.invoice_id || 'N/A'}`)

</script>


<template>
  <VRow>
    <!-- SECTION Lead Details -->
    <VCol cols="12">
      <VCard v-if="props.LeadData">
        <!-- 👉 Lead Details -->
        <VCardText>
          <h5 class="text-h5">
            Details
          </h5>

          <VDivider class="my-4" />
          <VList class="card-list mt-2">
            <VListItem>
              <h6 class="text-h6">
                Lead:
                <span class="text-body-1 d-inline-block">
                  {{ props.LeadData.name }}
                </span>
              </h6>
            </VListItem>

            <VListItem>
              <h6 class="text-h6">
                Email:
                <span class="text-body-1 d-inline-block">
                  {{ props.LeadData.email }}
                </span>
              </h6>
            </VListItem>

            <VListItem>
              <h6 class="text-h6">
                Phone:
                <span class="text-body-1 d-inline-block">
                  {{ props.LeadData.phone }}
                </span>
              </h6>
            </VListItem>

            <VListItem>
              <h6 class="text-h6">
                Address:
                <span class="text-body-1 d-inline-block">
                  {{ props.LeadData.address }}
                </span>
              </h6>
            </VListItem>

            <VListItem>
              <h6 class="text-h6">
                Contact Person:
                <span class="text-body-1 d-inline-block">
                  {{ props.LeadData.contact_person }}
                </span>
              </h6>
            </VListItem>

            <VListItem>
              <h6 class="text-h6">
                Contact Role:
                <span class="text-body-1 d-inline-block">
                  {{ props.LeadData.contact_person_role }}
                </span>
              </h6>
            </VListItem>

            <VListItem>
              <div class="d-flex gap-x-2 align-center">
                <h6 class="text-h6">Status:</h6>
                <VChip label :color="props.LeadData.status === 'active' ? 'success' : 'default'" size="small">
                  {{ props.LeadData.status }}
                </VChip>
              </div>
            </VListItem>

            <VListItem>
              <h6 class="text-h6">
                Source:
                <span class="text-body-1 d-inline-block">
                  {{ props.LeadData.source }}
                </span>
              </h6>
            </VListItem>

            <VListItem>
              <h6 class="text-h6">
                Assigned User:
                <span class="text-body-1 d-inline-block">
                  {{ props.LeadData.assigned_user }}
                </span>
              </h6>
            </VListItem>

            <VListItem>
              <h6 class="text-h6">
                Note:
                <span class="text-body-1 d-inline-block">
                  {{ props.LeadData.note }}
                </span>
              </h6>
            </VListItem>

            <VListItem>
              <h6 class="text-h6">
                Visit Assignee:
                <span class="text-body-1 d-inline-block">
                  {{ props.LeadData.visit_assignee }}
                </span>
              </h6>
            </VListItem>

            <VListItem>
              <h6 class="text-h6">
                Visit Time:
                <span class="text-body-1 d-inline-block">
                  {{ props.LeadData.visit_time }}
                </span>
              </h6>
            </VListItem>

            <VListItem>
              <h6 class="text-h6">
                Client:
                <span class="text-body-1 d-inline-block">
                  {{ clientName }}
                </span>
              </h6>
            </VListItem>

            <VListItem>
              <h6 class="text-h6">
                Quotation:
                <span class="text-body-1 d-inline-block">
                  {{ quotationName }}
                </span>
              </h6>
            </VListItem>

            <VListItem>
              <h6 class="text-h6">
                Contract:
                <span class="text-body-1 d-inline-block">
                  {{ contractName }}
                </span>
              </h6>
            </VListItem>

            <VListItem>
              <h6 class="text-h6">
                Invoice:
                <span class="text-body-1 d-inline-block">
                  {{ invoiceName }}
                </span>
              </h6>
            </VListItem>
          </VList>

        </VCardText>


      </VCard>
    </VCol>
    <!-- !SECTION -->

  </VRow>
</template>

<style lang="scss" scoped>
.card-list {
  --v-card-list-gap: 0.5rem;
}

.current-plan {
  background: linear-gradient(45deg, rgb(var(--v-theme-primary)) 0%, #9e95f5 100%);
  color: #fff;
}
</style>
