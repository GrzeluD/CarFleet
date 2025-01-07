<template>
    <Layout>
        <template #header>
            <Heading>Ewidencja przebiegu</Heading>
        </template>
        <div class="mb-4 flex space-x-4 justify-center mt-8">
            <TextField class="m-0 w-52" label="Data filtrowania od" id="date" is-label-inside="true" v-model="filterStartDate" input-type="date" />
            <TextField class="m-0 w-52" label="Data filtrowania do" id="date" is-label-inside="true" v-model="filterEndDate" input-type="date" />
            <FilterDropdownSingleUser :users="drivers" v-model="filterUserId" />
            <FilterDropdownSingleVehicle :vehicles="vehicles" v-model="filterVehicleId" />
        </div>
        <div class="flex justify-center gap-2 mb-8">
            <Btn :is-small="true" class="w-52" @click="applyFilters">Filtruj</Btn>
            <Btn btn-type="warning" :is-small="true" class="w-52" @click="resetFilters">Resetuj</Btn>
        </div>

        <div class="overflow-x-auto border border-gray-200 rounded-md shadow-sm bg-white">
            <table class="w-full text-sm text-gray-600">
                <thead>
                <tr class="bg-white text-gray-700">
                    <th class="p-2 font-medium text-left">Pojazd</th>
                    <th class="p-2 font-medium text-left">Kierowca</th>
                    <th class="p-2 font-medium text-left">Data</th>
                    <th class="p-2 font-medium text-left">Początkowy przebieg</th>
                    <th class="p-2 font-medium text-left">Końcowy przebieg</th>
                    <th class="p-2 font-medium text-left">Opis trasy</th>
                    <th class="p-2 font-medium text-left">Dystans (km)</th>
                    <th class="p-2 font-medium text-center">Akcje</th>
                </tr>
                </thead>
                <tbody>
                <tr
                    v-for="record in vehicleMileages"
                    :key="record.mileage_id"
                    class="hover:bg-gray-50 transition-colors border-t"
                >
                    <td class="p-2 whitespace-nowrap">{{ getVehicleName(record.vehicle_id) }}</td>
                    <td class="p-2 whitespace-nowrap">{{ getDriverName(record.user_id) }}</td>
                    <td class="p-2 whitespace-nowrap">{{ formatDate(record.date) }}</td>
                    <td class="p-2">{{ record.start_mileage }}</td>
                    <td class="p-2">{{ record.end_mileage }}</td>
                    <td class="p-2 truncate max-w-xs">{{ record.route_description }}</td>
                    <td class="p-2">{{ record.distance_traveled }}</td>
                    <td class="p-2 text-center space-x-2">
                        <button
                            @click="openModal(true, record)"
                            class="p-1 text-gray-600 hover:text-blue-500 transition"
                            title="Edytuj"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4h2a2 2 0 012 2v.586a1 1 0 01-.293.707l-6 6a1 1 0 01-.707.293H8a2 2 0 01-2-2v-2a1 1 0 01.293-.707l6-6A1 1 0 0111 4z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7l-4 4M7 17h10" />
                            </svg>
                        </button>

                        <button
                            @click="deleteRecord(record.mileage_id)"
                            class="p-1 text-gray-600 hover:text-red-500 transition"
                            title="Usuń"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <ModalWrapper modal-styles="min-w-[360px]" v-if="showModal" :top-bar-desc="isEditMode ? 'Edytuj wpis' : 'Dodaj wpis'" @close="closeModal">
            <form @submit.prevent="submitForm">
                <div class="dropdown relative h-max mb-4" :class="{'flex items-center gap-4': inline }">
                    <div class="relative mt-1.5">
                        <select v-model="form.vehicle_id" id="vehicle_id" class="bg-arris-inputBox-textFieldBackground border-arris-textfield-border p-2 rounded-md text-flotte-text w-full px-4 cursor-pointer h-[44px]" required>
                            <option value="" disabled selected>Wybierz pojazd</option>
                            <option v-for="vehicle in vehicles" :key="vehicle.vehicle_id" :value="vehicle.vehicle_id">
                                {{ vehicle.brand }} - {{ vehicle.license_plate }}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="dropdown relative h-max mb-4" :class="{'flex items-center gap-4': inline }">
                    <div class="relative mt-1.5">
                        <select v-model="form.user_id" id="cost-type-id" class="bg-arris-inputBox-textFieldBackground border-arris-textfield-border p-2 rounded-md text-flotte-text w-full px-4 cursor-pointer h-[44px]" required>
                            <option value="" disabled selected>Wybierz kierowcę</option>
                            <option v-for="driver in drivers" :key="driver.id" :value="driver.id">
                                {{ driver.name }}
                            </option>
                        </select>
                    </div>
                </div>

                <TextField class="mt-0 mb-4" label="Podaj datę ewidencji" id="date" is-label-inside="true" v-model="form.date" input-type="date" />

                <div class="flex gap-4">
                    <TextField class="mt-0 mb-4" label="Początkowy przebieg" id="start-mileage" is-label-inside="true" v-model="form.start_mileage" input-type="number" @modify-input="calculateDistance" />
                    <TextField class="mt-0 mb-4" label="Końcowy przebieg" id="end-mileage" is-label-inside="true" v-model="form.end_mileage" input-type="number" @modify-input="calculateDistance" />
                </div>
                <div class="flex gap-4">
                    <TextField class="mt-0 mb-4" label="Dystans (km)" id="distance-traveled" is-label-inside="true" v-model="form.distance_traveled" input-type="number" @modify-input="calculateDistance" />
                </div>

                <TextField class="mt-0 mb-4" label="Miejsce początkowe" id="location-start" is-label-inside="true" v-model="form.location_start" />
                <TextField class="mt-0 mb-4" label="Miejsce końcowe" id="location-end" is-label-inside="true" v-model="form.location_end" />
                <TextArea v-model="form.route_description" class="mt-0 mb-4" label="Opis trasy"></TextArea>

                <button type="submit" class="button min-w-20 w-full py-2 rounded-lg flex items-center justify-center px-4 cursor-auto transition-colors cursor-pointer h-max bg-arris-btn-success xl:hover:bg-arris-btn-successHover text-arris-btn-textPrimary">
                    {{ isEditMode ? 'Zaktualizuj' : 'Dodaj' }}
                </button>
            </form>
        </ModalWrapper>

        <div class="flex justify-between">
            <Btn :is-small="true" class="w-52 mt-8" @click="openModal(false)">Dodaj ewidencje przebiegu</Btn>
            <Btn btn-type="secondary" :is-small="true" class="w-52 mt-8" @click="generateCSV">Generuj CSV</Btn>
        </div>
    </Layout>
</template>

<script setup>
import {computed, ref} from 'vue';
import {usePage, useForm, router} from '@inertiajs/vue3';
import Layout from '@/Pages/Layout.vue';
import Heading from "@/Components/Heading.vue";
import Btn from "@/Components/Btn.vue";
import TextField from "@/Components/inputs/TextField.vue";
import TextArea from "@/Components/inputs/TextArea.vue";
import ModalWrapper from "@/Components/modals/ModalWrapper.vue";
import Select from "@/Components/inputs/Select.vue";
import FilterDropdownVehicle from "@/Components/FilterDropdownVehicle.vue";
import FilterDropdownCosts from "@/Components/FilterDropdownCosts.vue";
import FilterDropdownSingleVehicle from "@/Components/FilterDropdownSingleVehicle.vue";
import FilterDropdownSingleUser from "@/Components/FilterDropdownSingleUser.vue";

const { props } = usePage();
const vehicleMileages = ref(props.vehicleMileages || []);
const vehicles = ref(props.vehicles || []);
const drivers = ref(props.drivers || []);
const showModal = ref(false);
const isEditMode = ref(false);

const filterStartDate = ref('');
const filterEndDate = ref('');
const filterVehicleId = ref('');
const filterUserId = ref('');

const form = useForm({
    vehicle_id: '',
    user_id: '',
    date: '',
    start_mileage: '',
    end_mileage: '',
    location_start: '',
    location_end: '',
    route_description: '',
    distance_traveled: '',
});

const generateCSV = () => {
    const params = {
        start_date: filterStartDate.value,
        end_date: filterEndDate.value,
        vehicle_id: filterVehicleId.value,
        user_id: filterUserId.value,
    };

    axios.get('/vehicle-mileages/csv', { params, responseType: 'blob' })
        .then(response => {
            const url = window.URL.createObjectURL(new Blob([response.data]));
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', 'vehicle_mileages.csv');
            document.body.appendChild(link);
            link.click();
        })
        .catch(console.error);
};

const applyFilters = () => {
    console.log(filterVehicleId.value)

    axios.get(route('vehicle-mileages.index'), {
        params: {
            start_date: filterStartDate.value,
            end_date: filterEndDate.value,
            vehicle_id: filterVehicleId.value,
            user_id: filterUserId.value,
        }
    }).then(response => {
        vehicleMileages.value = response.data.vehicleMileages;
    }).catch(console.error);
};

const resetFilters = () => {
    filterStartDate.value = '';
    filterEndDate.value = '';
    filterVehicleId.value = '';
    filterUserId.value = '';
    applyFilters();
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('pl-PL', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
};

const calculateDistance = () => {
    if (form.start_mileage && form.end_mileage && form.end_mileage >= form.start_mileage) {
        form.distance_traveled = (form.end_mileage - form.start_mileage).toFixed(0);
    } else {
        form.distance_traveled = 0;
    }
};

const getVehicleName = (vehicleId) => {
    const vehicle = vehicles.value.find(v => v.vehicle_id === vehicleId);
    return vehicle ? `${vehicle.brand} - ${vehicle.license_plate}` : 'Nieznany pojazd';
};

const getDriverName = (driverId) => {
    const driver = drivers.value.find(d => d.id === driverId);
    return driver ? driver.name : 'Nieznany kierowca';
};

const openModal = (editMode, record = null) => {
    isEditMode.value = editMode;
    if (editMode && record) {
        form.mileage_id = record.mileage_id;
        form.vehicle_id = record.vehicle_id;
        form.user_id = record.user_id;
        form.date = record.date.split(' ')[0];
        form.start_mileage = record.start_mileage;
        form.end_mileage = record.end_mileage;
        form.location_start = record.location_start;
        form.location_end = record.location_end;
        form.route_description = record.route_description;
        form.distance_traveled = record.distance_traveled;
    } else {
        form.reset();
    }
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
};

const submitForm = () => {
    if (isEditMode.value) {
        axios.put(`/vehicle-mileages/${form.mileage_id}`, form).then(() => {
            router.visit(route('vehicle-mileages.index'), {
                preserveScroll: true
            });
            closeModal();
        }).catch(console.error);
    } else {
        axios.post('/vehicle-mileages', form).then(() => {
            router.visit(route('vehicle-mileages.index'), {
                preserveScroll: true
            });
            closeModal();
        }).catch(console.error);
    }
};

const deleteRecord = (id) => {
    if (confirm('Czy na pewno chcesz usunąć ten przebieg?')) {
        axios.delete(`/vehicle-mileages/${id}`).then(() => {
            location.reload();
        }).catch(console.error);
    }
};
</script>

<style>
</style>
