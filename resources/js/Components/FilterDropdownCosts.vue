<template>
    <div class="relative inline-block w-52">
        <button
            @click="toggleDropdown"
            :class="[
                'px-4 py-2 rounded-full shadow-md border flex items-center justify-center w-52 transition-colors',
                selectedValues.length > 0
                    ? 'bg-arris-btn-primary xl:hover:bg-arris-btn-primaryHover text-white border-blue-500'
                    : 'bg-white xl:hover:bg-gray-50 text-gray-700 border-gray-300'
            ]"
        >
            Wybierz typy kosztów
        </button>

        <div v-if="isOpen" class="absolute mt-2 w-full bg-white border border-gray-300 rounded-md shadow-lg z-10 max-h-60 overflow-auto">
            <div
                v-for="cost in costTypes"
                :key="cost.cost_type_id"
                @click="toggleSelection(cost.cost_type_id)"
                class="flex items-center px-4 py-2 hover:bg-gray-100 cursor-pointer"
            >
                <div
                    class="w-5 h-5 border-2 rounded-md mr-3 flex items-center justify-center"
                    :class="{'bg-blue-500 border-blue-500': selectedValues.includes(cost.cost_type_id)}"
                >
                    <svg v-if="selectedValues.includes(cost.cost_type_id)" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <label class="cursor-pointer text-sm">{{ cost.cost_type_name }}</label>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from "vue";

const props = defineProps({
    costTypes: {
        type: Array,
        required: true
    },
    modelValue: {
        type: Array,
        default: () => []
    }
});

const emit = defineEmits(['update:modelValue']);

const isOpen = ref(false);
const selectedValues = ref([]);

selectedValues.value = [...props.modelValue];

const toggleDropdown = () => {
    isOpen.value = !isOpen.value;
};

const toggleSelection = (vehicleId) => {
    if (selectedValues.value.includes(vehicleId)) {
        selectedValues.value = selectedValues.value.filter(id => id !== vehicleId);
    } else {
        selectedValues.value.push(vehicleId);
    }
    emit('update:modelValue', [...selectedValues.value]);
};

watch(() => props.modelValue, (newValues) => {
    if (JSON.stringify(newValues) !== JSON.stringify(selectedValues.value)) {
        selectedValues.value = [...newValues];
    }
});
</script>

<style scoped>
</style>
