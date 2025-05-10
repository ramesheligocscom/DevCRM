<template>
    <VDialog :width="$vuetify.display.smAndDown ? 'auto' : 900" :model-value="props.isDialogVisible"
        @update:model-value="dialogModelValueUpdate" scrollable persistent>
        <!-- Dialog close btn -->
        <DialogCloseBtn @click="dialogModelValueUpdate(false)" />

        <VCard class="pa-sm-10 pa-2">
            <VCardText>
                <VImg v-if="props.currentInfo && props.currentInfo.avatar"
                    :src="BASE_URL + `/` + props.currentInfo.avatar" />
                <span v-else class="text-5xl font-weight-medium">
                    {{ avatarText(props.currentInfo ? props.currentInfo.name : '') }}
                </span>
            </VCardText>
        </VCard>
    </VDialog>
</template>
<script setup>
const props = defineProps({
    currentInfo: { type: Object, required: false, },
    isDialogVisible: { type: Boolean, required: true, },
})
const BASE_URL = window.location.origin;

const emit = defineEmits(['submit', 'update:isDialogVisible'])

const dialogModelValueUpdate = val => { emit('update:isDialogVisible', val) }
</script>
