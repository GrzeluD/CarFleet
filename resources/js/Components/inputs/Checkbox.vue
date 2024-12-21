<template>
    <div class="custom_checkbox flex items-center relative" :class="[inactive && 'opacity-[0.5] pointer-events-none']">
        <input type="checkbox" class="opacity-0 w-0 h-0 absolute top-0 left-0" :id="uuid" v-model="value" @change="(event) => change(event)">
        <label class="cursor-pointer flex items-center" :for="uuid">
            <div class="flex-shrink-0 checkbox_dummy mr-2 min-w-[20px] min-h-[20px] border border-arris-inputBox-border flex justify-center items-center rounded-[2px]" :class="{'error border-arris-general-error': errorMessage!==undefined && errorMessage!==''}">
                <div class="dummy_box w-3 h-3 border bg-arris-inputBox-fill transition-[opacity] transition-duration-300" :class="{'opacity-0': !value, 'opacity-1': value }"></div>
            </div>
            <slot></slot>
            <p class="text-text-base-small rounded-2px" :class="[errorMessage !== undefined && errorMessage !=='' && 'text-arris-general-error']">{{ label }}{{isRequired ? '*' : ''}}</p>
        </label>
        <p class="absolute text-[11px] leading-[14px] -bottom-[14px] text-arris-general-error whitespace-nowrap">{{ errorMessage }}</p>
    </div>
</template>

<script setup>
import {ref, watch} from "vue";

const props = defineProps({
    errorMessage: {
        type: String,
        default: ''
    },
    errorName: {
        type: String,
        default: ''
    },
    id: {
        type: String,
        default: ''
    },
    inactive: {
        type: Boolean,
        default: false
    },
    isRequired: {
        type: Boolean,
        default: false
    },
    label: {
        type: String,
        default: ''
    },
    modelValue: {
        type: [Boolean],
        default: false,
    },
})

const value = ref(props.modelValue);
let uuid = ref(props.id ? props.id : getUuid())

function getUuid() {
    const randomNumber = Math.random();
    const hexString = (randomNumber).toString(16).slice(2, 17);
    return hexString
}

const emit = defineEmits(['update:modelValue'])

function change(event) {
    emit('update:modelValue', event.target.checked)
}

watch(() => props.modelValue, (val) => {
    value.value = val;
});

</script>

<style scoped>
</style>
