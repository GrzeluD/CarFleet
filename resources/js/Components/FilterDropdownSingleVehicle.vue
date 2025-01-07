<template>
    <div class="relative inline-block w-64">
        <button
            @click="toggleDropdown"
            :class="[
                'px-4 py-2 rounded-full shadow-md border flex items-center justify-center w-40 transition-colors',
                selectedValue !== ''
                    ? 'bg-arris-btn-primary xl:hover:bg-arris-btn-primaryHover text-white border-blue-500'
                    : 'bg-white xl:hover:bg-gray-50 text-gray-700 border-gray-300'
            ]"
        >
            Wybierz pojazd
        </button>

        <div v-if="isOpen" class="absolute mt-2 w-full bg-white border border-gray-300 rounded-md shadow-lg z-10 max-h-60 overflow-auto">
            <div
                v-for="vehicle in vehicles"
                :key="vehicle.vehicle_id"
                @click="selectValue(vehicle.vehicle_id)"
                class="flex items-center px-4 py-2 hover:bg-gray-100 cursor-pointer"
            >
                <div
                    class="w-5 h-5 border-2 rounded-md mr-3 flex items-center justify-center"
                    :class="{'bg-blue-500 border-blue-500': selectedValue === vehicle.vehicle_id}"
                >
                    <svg v-if="selectedValue === vehicle.vehicle_id" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <label class="cursor-pointer">{{ vehicle.brand }} - {{ vehicle.license_plate }}</label>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from "vue";

const props = defineProps({
    vehicles: {
        type: Array,
        required: true
    },
    modelValue: {
        type: [String, Number, null],
        default: null
    }
});

const emit = defineEmits(['update:modelValue']);

const isOpen = ref(false);
const selectedValue = ref(props.modelValue);

const toggleDropdown = () => {
    isOpen.value = !isOpen.value;
};

const selectValue = (vehicleId) => {
    selectedValue.value = vehicleId;
    emit('update:modelValue', vehicleId);
    isOpen.value = false;
};

watch(() => props.modelValue, (newValue) => {
    selectedValue.value = newValue;
});
</script>

<style scoped>
</style>
