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
          <!-- SECTION Product/Service Info -->
          <h5 class="text-h5 mb-4">Product/Service Details</h5>

          <VRow dense>
            <VCol cols="12" md="4" lg="4">
              <div class="d-flex align-center gap-x-2 mt-1">
                <strong>Name:</strong>
                <span>{{props.InfoData.name }}</span>
              </div>
            </VCol>
            <VCol cols="12" md="4" lg="4">
              <div class="d-flex align-center gap-x-2 mt-1">
                <strong>Price:</strong>
                <span>${{props.InfoData.price }}</span>
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

          <VDivider class="my-6" />

          <!-- SECTION ATTRIBUTES -->
          <h5 class="text-h5 mb-2">Product/Service Attributes</h5>

          <VRow v-if="props.InfoData.attributes?.length">
            <VCol v-for="(attribute, index) in props.InfoData.attributes" :key="index" cols="12" md="6" lg="6">
              <VCard class="mb-4" outlined>
                <VCardText>
                  <VRow dense>
                    <VCol cols="12">
                      <strong>Key:</strong> {{ attribute.key }}
                    </VCol>
                    <VCol cols="6">
                      <strong>Type:</strong> {{ attribute.type }}
                    </VCol>
                    <VCol cols="6">
                      <strong>Value:</strong> {{ attribute.value }}
                    </VCol>
                  </VRow>
                </VCardText>
              </VCard>
            </VCol>
          </VRow>



          <VAlert v-else type="info" variant="tonal" class="mt-4">
            No Attributes found for this Product/Service.
          </VAlert>
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
