<template>
    <div class="relative">
        <TextField v-model="searchPhrase" @focus="handleFocus" @blur="handleBlur" :label="label" class="relative z-[1]" isLabelInside inputBgColor="bg-[#FFFFFF]" :isLabelMoved="chosenOption"/>
        <div class="fixed w-screen h-screen inset-0 bg-[transparent]" @click="isHintBoxOpen=false" v-if="isHintBoxOpen"></div>
        <div v-if="chosenOption && !isInputFocused" class="flex items-center gap-2 absolute top-2.5 left-2 z-[2] px-2 py-1 rounded-lg bg-arris-searchbox-chosenOption">
            <div class="w-5 h-5">
                <img :src="chosenOption.pictureUrl" class="w-full h-full object-cover"/>
            </div>
            <p class="text-[12px] leading-[14px]">{{chosenOption.name_surname}}</p>
            <svg class="cursor-pointer"
                    width="14"
                    height="14"
                    viewBox="0 0 14 14"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                    @click="chosenOption=null">
                <path d="M1 1L13 13" stroke="#E10001" stroke-width="2" stroke-linecap="round"/>
                <path d="M1 13L13 1" stroke="#E10001" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </div>
        <div class="absolute top-12 z-[999] w-full flex flex-col gap-2 transition-[max-height] h-max bg-arris-white shadow-2xl" :class="[isHintBoxOpen ? 'max-h-[500px]' : 'border-transparent overflow-hidden max-h-[0]']">
            <div v-for="item in props.hints.slice(0, 5)" :key="item.id" class="flex items-center gap-2 cursor-pointer m-2 p-2 rounded-[4px] bg-arris-searchbox-option-bg hover:bg-arris-searchbox-option-bg-hover transition-[background-color]" @click="handleItemChosen(item)">
                <div class="w-12 h-12">
                    <img :src="item.pictureUrl" class="w-full h-full object-cover"/>
                </div>
                <p>{{item.name_surname}}</p>
            </div>
        </div>
    </div>
</template>
<script setup>
import TextField from "@/components/inputs/TextField.vue";
import {ref, watch} from "vue";
const searchPhrase = defineModel();
const isInputFocused = ref(false);

const props = defineProps({
    hints: {
        type: Array,
        default() {
            return []
        }
    },
    label: {
        type: String,
        default: 'Wyszukaj..'
    }
})

const isHintBoxOpen = ref(false);

const chosenOption = ref(null);

function handleFocus() {
    isHintBoxOpen.value = props.hints.length>0;
    // chosenOption.value = null;
    isInputFocused.value = true;
}

function handleBlur() {
    isInputFocused.value = false;
}

function handleItemChosen(item) {
    chosenOption.value = item;
    isHintBoxOpen.value = false;
    // searchPhrase.value = '';
}

watch(
    () => props.hints,
    (newValue, oldValue) => {
        isHintBoxOpen.value = props.hints.length>0;
    },
    { deep: true }
)

</script>
<style scoped>

</style>
