<template>
    <Layout>
        <template #header>
            <Heading>Ewidencja przebiegu</Heading>
        </template>

        <div class="mb-4 flex space-x-4">
            <div>
                <label for="filter-start-date">Data początkowa</label>
                <input v-model="filterStartDate" type="date" id="filter-start-date" class="input" />
            </div>
            <div>
                <label for="filter-end-date">Data końcowa</label>
                <input v-model="filterEndDate" type="date" id="filter-end-date" class="input" />
            </div>
            <div>
                <label for="filter-vehicle">Pojazd</label>
                <select v-model="filterVehicleId" id="filter-vehicle" class="input">
                    <option value="">Wszystkie pojazdy</option>
                    <option v-for="vehicle in vehicles" :key="vehicle.vehicle_id" :value="vehicle.vehicle_id">
                        {{ vehicle.brand }} - {{ vehicle.license_plate }}
                    </option>
                </select>
            </div>
            <button @click="applyFilters" class="btn btn-primary">Filtruj</button>
            <button @click="resetFilters" class="btn btn-secondary">Resetuj</button>
        </div>

        <table class="w-full border-collapse border">
            <thead>
            <tr>
                <th class="border p-2">Pojazd</th>
                <th class="border p-2">Kierowca</th>
                <th class="border p-2">Data</th>
                <th class="border p-2">Początkowy przebieg</th>
                <th class="border p-2">Końcowy przebieg</th>
                <th class="border p-2">Opis trasy</th>
                <th class="border p-2">Dystans (km)</th>
                <th class="border p-2">Akcje</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="record in filteredMileages" :key="record.mileage_id">
                <td class="border p-2">{{ getVehicleName(record.vehicle_id) }}</td>
                <td class="border p-2">{{ getDriverName(record.user_id) }}</td>
                <td class="border p-2">{{ formatDate(record.date) }}</td>
                <td class="border p-2">{{ record.start_mileage }}</td>
                <td class="border p-2">{{ record.end_mileage }}</td>
                <td class="border p-2">{{ record.route_description }}</td>
                <td class="border p-2">{{ record.distance_traveled }}</td>
                <td class="border p-2">
                    <button @click="openModal(true, record)" class="btn btn-sm btn-warning">Edytuj</button>
                    <button @click="deleteRecord(record.mileage_id)" class="btn btn-sm btn-danger">Usuń</button>
                </td>
            </tr>
            </tbody>
        </table>

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

const { props } = usePage();
const vehicleMileages = ref(props.vehicleMileages || []);
const vehicles = ref(props.vehicles || []);
const drivers = ref(props.drivers || []);
const showModal = ref(false);
const isEditMode = ref(false);

const filterStartDate = ref('');
const filterEndDate = ref('');
const filterVehicleId = ref('');

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

const filteredMileages = computed(() => {
    return vehicleMileages.value.filter(mileage => {
        const date = new Date(mileage.date.split(' ')[0]);
        const startDate = filterStartDate.value ? new Date(filterStartDate.value) : null;
        const endDate = filterEndDate.value ? new Date(filterEndDate.value) : null;
        const vehicleMatch = !filterVehicleId.value || mileage.vehicle_id === filterVehicleId.value;

        return (!startDate || date >= startDate) && (!endDate || date <= endDate) && vehicleMatch;
    });
});

const applyFilters = () => {
    const params = {
        start_date: filterStartDate.value,
        end_date: filterEndDate.value,
        vehicle_id: filterVehicleId.value,
    };

    axios.get('/vehicle-mileages', { params })
        .then(response => {
            vehicleMileages.value = response.data.vehicleMileages;
        })
        .catch(console.error);
};

const resetFilters = () => {
    filterStartDate.value = '';
    filterEndDate.value = '';
    filterVehicleId.value = '';
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
.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
}
.modal-content {
    background-color: #fff;
    padding: 20px;
    border-radius: 5px;
    width: 500px;
}
</style>
