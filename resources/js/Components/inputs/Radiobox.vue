<template>
    <div class="relative">
        <input :id="uuid" type="radio" :value="value" :name="name" :checked="isSelected" @change="handleChange" class="opacity-0 w-0 h-0 absolute top-0 left-0"/>
        <label :for="uuid" class="flex items-center gap-2.5 cursor-pointer" :class="[inactive && 'opacity-[0.5] pointer-events-none']">
            <div class="flex-shrink-0 w-5 h-5 rounded-full border border-arris-inputBox-radioboxBorder flex items-center justify-center">
                <div class="w-2.5 h-2.5 bg-arris-inputBox-radioboxFill rounded-full transition-opacity" :class="[isSelected ? 'opacity-1' : 'opacity-0']"></div>
            </div>
            <slot></slot>
            <p class="text-text-base-small rounded-2px">{{ label }}</p>
        </label>
    </div>
</template>

<script setup>
import {computed, ref} from "vue";

const props = defineProps({
    inactive: {
        type: Boolean,
        default: false
    },
    label: {
        type: String,
        default: ''
    },
    name: {
        type: String,
        default: '',
    },
    value: {
        type: String,
        default: ''
    },
    selectedValue: {
        type: String,
        default: ''
    },
    id: {
        type: String,
        default: ''
    },
});
const isSelected = computed(() => props.value == props.selectedValue);
const emit = defineEmits(['update:selectedValue'])

const handleChange = () => {
    emit('update:selectedValue', props.value);
};

let uuid = ref(props.id ? props.id : getUuid())

function getUuid() {
    const randomNumber = Math.random();
    const hexString = (randomNumber).toString(16).slice(2, 17);
    return hexString
}

</script>
