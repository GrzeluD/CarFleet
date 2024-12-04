<template>
    <Layout>
        <template #header>
            <h1 class="text-xl">Ewidencja Przebiegów</h1>
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

        <button @click="openModal(false)" class="btn btn-primary mb-2">Dodaj Przebieg</button>

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

        <button @click="generateCSV" class="btn btn-secondary mt-4">Generuj CSV</button>

        <div v-if="showModal" class="modal">
            <div class="modal-content">
                <h2>{{ isEditMode ? 'Edytuj Przebieg' : 'Dodaj Przebieg' }}</h2>
                <form @submit.prevent="submitForm">
                    <div class="mb-2">
                        <label for="vehicle_id">Wybierz pojazd</label>
                        <select v-model="form.vehicle_id" id="vehicle_id" class="input" required>
                            <option v-for="vehicle in vehicles" :key="vehicle.vehicle_id" :value="vehicle.vehicle_id">
                                {{ vehicle.brand }} - {{ vehicle.license_plate }}
                            </option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label for="driver_id">Wybierz kierowcę</label>
                        <select v-model="form.user_id" id="driver_id" class="input" required>
                            <option v-for="driver in drivers" :key="driver.id" :value="driver.id">
                                {{ driver.name }}
                            </option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label for="date">Data</label>
                        <input v-model="form.date" type="date" id="date" class="input" required />
                    </div>
                    <div class="mb-2">
                        <label for="start_mileage">Początkowy przebieg</label>
                        <input v-model.number="form.start_mileage" @input="calculateDistance" type="number" id="start_mileage" class="input" required />
                    </div>
                    <div class="mb-2">
                        <label for="end_mileage">Końcowy przebieg</label>
                        <input v-model.number="form.end_mileage" @input="calculateDistance" type="number" id="end_mileage" class="input" required />
                    </div>
                    <div class="mb-2">
                        <label for="location_start">Miejsce początkowe</label>
                        <input v-model="form.location_start" type="text" id="location_start" class="input" required />
                    </div>
                    <div class="mb-2">
                        <label for="location_end">Miejsce końcowe</label>
                        <input v-model="form.location_end" type="text" id="location_end" class="input" required />
                    </div>
                    <div class="mb-2">
                        <label for="route_description">Opis trasy</label>
                        <textarea v-model="form.route_description" id="route_description" class="input"></textarea>
                    </div>
                    <div class="mb-2">
                        <label for="distance_traveled">Dystans (km)</label>
                        <input v-model="form.distance_traveled" type="number" id="distance_traveled" class="input" readonly />
                    </div>
                    <button type="submit" class="btn btn-primary">{{ isEditMode ? 'Zaktualizuj' : 'Dodaj' }}</button>
                    <button @click="closeModal" type="button" class="btn">Anuluj</button>
                </form>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import {computed, ref} from 'vue';
import {usePage, useForm, router} from '@inertiajs/vue3';
import Layout from '@/Pages/Layout.vue';

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
