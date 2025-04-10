<script setup>
import moment from 'moment';

const props = defineProps({
  InfoData: {
    type: null,
    required: true,
  },
})

const makeDateFormat = (date , onlyDate = false) => {
    if(onlyDate)
    return moment(date).format('DD-MM-Y');
    else
    return moment(date).format('LLLL');
};

</script>

<template>
  <VRow>
    <VCol cols="12">
      <VCard v-if="props.InfoData">
        <VCardText>
          <!-- SECTION Quotation Info -->
          <h5 class="text-h5 mb-4">Quotation Details</h5>

          <VRow dense>
            <VCol cols="12" md="4" lg="4">
              <div class="d-flex align-center gap-x-2 mt-1">
                <strong>title:</strong>
                <span>{{ props.InfoData.title || '-' }}</span>
              </div>
            </VCol>

            <VCol cols="12" md="4" lg="4">
              <div class="d-flex align-center gap-x-2 mt-1">
                <strong>Valid Uptill:</strong>
                <span>{{ props.InfoData.valid_uptill ? makeDateFormat(props.InfoData.valid_uptill , true) : '-' }}</span>
              </div>
            </VCol>

            <VCol cols="12" md="4" lg="4">
              <div class="d-flex align-center gap-x-2 mt-1">
                <strong>Quotation Type:</strong>
                <span>{{ props.InfoData.quotation_type || '-' }}</span>
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

            <VCol cols="12" md="12" lg="12">
              <div class="d-flex align-center gap-x-2 mt-1">
                <strong>Custom Header Text:</strong>
                <span>{{ props.InfoData.custom_header_text }}</span>
              </div>
            </VCol>
            <VCol cols="12" md="12" lg="12">
              <div class="d-flex align-center gap-x-2 mt-1">
                <strong>Payment Terms:</strong>
                <span>{{ props.InfoData.payment_terms }}</span>
              </div>
            </VCol>
            <VCol cols="12" md="12" lg="12">
              <div class="d-flex align-center gap-x-2 mt-1">
                <strong>Terms & Conditions:</strong>
                <span>{{ props.InfoData.terms_conditions }}</span>
              </div>
            </VCol>
          </VRow>

          <VDivider class="my-6" />

          <!-- SECTION Items -->
          <h5 class="text-h5 mb-2">Quotation Items</h5>

          <VRow v-if="props.InfoData.items?.length">
            <VCol v-for="(item, index) in props.InfoData.items" :key="index" cols="12" md="6" lg="6">
              <VCard class="mb-4" outlined>
                <VCardText>
                  <VRow dense>
                    <VCol cols="12">
                      <strong>Name:</strong> {{ item.name }}
                    </VCol>
                    <VCol cols="6">
                      <strong>Quantity:</strong> {{ item.quantity }}
                    </VCol>
                    <VCol cols="6">
                      <strong>Unit Price:</strong> ${{ Number(item.unit_price || 0).toFixed(2) }}
                    </VCol>
                    <VCol cols="6">
                      <strong>Tax Rate:</strong> {{ item.tax_rate }}%
                    </VCol>
                    <VCol cols="6">
                      <strong>Subtotal:</strong> ${{ Number(item.subtotal || 0).toFixed(2) }}
                    </VCol>
                    <VCol cols="6">
                      <strong>Discount:</strong> ${{ Number(item.discount_amount || 0).toFixed(2) }}
                    </VCol>
                    <VCol cols="6">
                      <strong>Total:</strong> ${{ Number(item.total || 0).toFixed(2) }}
                    </VCol>
                    <VCol cols="12">
                      <strong>Description:</strong> {{ item.description || '—' }}
                    </VCol>

                  </VRow>
                </VCardText>
              </VCard>
            </VCol>
          </VRow>



          <VAlert v-else type="info" variant="tonal" class="mt-4">
            No items found for this quotation.
          </VAlert>
        </VCardText>
      </VCard>
    </VCol>
    <VCol cols="12" md="8">
    </VCol>
   <!-- Summary Card -->
   <VCol cols="12" md="4">
      <VCard>
        <VCardText>
          <h5 class="text-h5 mb-4">Summary</h5>

          <VRow dense>
            <VCol cols="12">
              <div class="d-flex justify-space-between">
                <span>Subtotal:</span>
                <strong>${{ Number(props.InfoData.sub_total ?? 0).toFixed(2) }}</strong>
              </div>
            </VCol>

            <VCol cols="12">
              <div class="d-flex justify-space-between">
                <span>Total Tax:</span>
                <strong>${{ Number(props.InfoData.tax ?? 0).toFixed(2) }}</strong>
              </div>
            </VCol>

            <VCol cols="12">
              <div class="d-flex justify-space-between">
                <span>Total Discount:</span>
                <strong>-${{ Number(props.InfoData.discount ?? 0).toFixed(2) }}</strong>
              </div>
            </VCol>

            <VDivider class="my-2" />

            <VCol cols="12">
              <div class="d-flex justify-space-between">
                <span><strong>Total:</strong></span>
                <strong>${{ Number(props.InfoData.total ?? 0).toFixed(2) }}</strong>
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
