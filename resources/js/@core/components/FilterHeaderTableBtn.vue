<template>
  <VTooltip location="top">
    <template v-slot:activator="{ props }">
      <VBtn v-bind="props">
        <VIcon icon="tabler-columns-3" />
        <VMenu v-if="parsedHeaders.length" activator="parent" location="start">
          <VList>
            <!-- Sync Button -->
            <VListItem>
              <VBtn color="primary" block class="mb-2" @click="tableHeaderSync">
                <VIcon icon="tabler-refresh" class="mr-2" /> Sync Headers
              </VBtn>
            </VListItem>
            <VListItem v-for="(item, index) in parsedHeaders" :key="index"
              class="rounded-md hover:bg-gray-100 filterBtnListItem">
              <VCheckbox v-model="item.checked" :label="item.title" class="mr-2" @change="toggleHeader(item)" />
            </VListItem>
          </VList>
        </VMenu>
      </VBtn>
    </template>
    Choose columns to display
  </VTooltip>
</template>

<script setup>
import { defineEmits, defineProps, onMounted, ref } from "vue";
import { toast } from "vue3-toastify";

const props = defineProps({
  slug: { type: String, required: true },
});

const emit = defineEmits(["filterHeaderValue"]);

const headers = ref([]);

// Computed property to parse headers properly
const parsedHeaders = computed(() => {
  try {
    return typeof headers.value === "string" ? JSON.parse(headers.value) : headers.value;
  } catch (error) {
    return [];
  }
});

const fetchHeaders = async () => {
  try {
    const response = await $api("/table-header/get", { params: { slug: props.slug } });
    headers.value = response.data?.headers ?? [];
    emit("filterHeaderValue", parsedHeaders.value);
  } catch (error) {
    toast.error(error.response?.data?.message || "Failed to load headers.");
  }
};

const toggleHeader = async (header) => {
  try {
    let headerList = parsedHeaders.value.filter(item => {
      return item.key === header.key ? { ...item, checked: !item.checked } : item;
    });

    const response = await $api(`/table-header/save`, { method: 'POST', body: { slug: props.slug, header_list: headerList }, });
    headers.value = response.data?.headers ?? [];
    emit("filterHeaderValue", parsedHeaders.value);
  } catch (error) {
    toast.error(error.response?.data?.message || "Failed to update headers.");
  }
};

const tableHeaderSync = async () => {
  try {
    const response = await $api("/table-header/sync", { method: 'POST', body: { slug: props.slug } });
    headers.value = response.data?.headers ?? [];
    emit("filterHeaderValue", parsedHeaders.value);
  } catch (error) {
    toast.error(error.response?.data?.message || "Failed to load headers.");
  }
};

onMounted(fetchHeaders);
</script>
