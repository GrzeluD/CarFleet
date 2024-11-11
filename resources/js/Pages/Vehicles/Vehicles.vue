<template>
    <div>
        <h1 class="text-2xl font-bold mb-4">Lista Pojazdów</h1>
        <button @click="openModal(false)" class="btn btn-primary mb-2">Dodaj Pojazd</button>

        <table class="w-full border-collapse border">
            <thead>
            <tr>
                <th class="border p-2">Marka</th>
                <th class="border p-2">Model</th>
                <th class="border p-2">Rok produkcji</th>
                <th class="border p-2">Akcje</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="vehicle in vehicles" :key="vehicle.id">
                <td class="border p-2">{{ vehicle.brand }}</td>
                <td class="border p-2">{{ vehicle.model }}</td>
                <td class="border p-2">{{ vehicle.production_year }}</td>
                <td class="border p-2">
                    <button @click="openModal(true, vehicle)" class="btn btn-sm btn-warning">Edytuj</button>
                    <button @click="deleteVehicle(vehicle.vehicle_id)" class="btn btn-sm btn-danger">Usuń</button>
                </td>
            </tr>
            </tbody>
        </table>

        <div v-if="showModal" class="modal">
            <div class="modal-content">
                <h2>{{ isEditMode ? 'Edytuj Pojazd' : 'Dodaj Pojazd' }}</h2>
                <form @submit.prevent="submitForm">
                    <div class="mb-2">
                        <label for="brand">Marka</label>
                        <input v-model="form.brand" type="text" id="brand" class="input" required />
                    </div>

                    <div class="mb-2">
                        <label for="model">Model</label>
                        <input v-model="form.model" type="text" id="model" class="input" required />
                    </div>

                    <div class="mb-2">
                        <label for="vin">vin</label>
                        <input v-model="form.vin" type="text" id="vin" class="input" required />
                    </div>

                    <div class="mb-2">
                        <label for="production-year">Rok produkcji</label>
                        <input v-model="form.production_year" type="text" id="production-year" class="input" required />
                    </div>

                    <div class="mb-2">
                        <label for="license-plate">Numer rejestracyjny</label>
                        <input v-model="form.license_plate" type="text" id="license-plate" class="input" required />
                    </div>

                    <div class="mb-2">
                        <label for="insurance-expiry">Data końca ubezpieczenia</label>
                        <input v-model="form.insurance_expiry" type="date" id="insurance-expiry" class="input" required />
                    </div>

                    <div class="mb-2">
                        <label for="license-plate">Data następnego przeglądu</label>
                        <input v-model="form.inspection_due" type="date" id="license-plate" class="input" required />
                    </div>
                    <button type="submit" class="btn btn-primary">{{ isEditMode ? 'Zaktualizuj' : 'Dodaj' }}</button>
                    <button @click="closeModal" type="button" class="btn">Anuluj</button>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { usePage, useForm, router } from '@inertiajs/vue3';

const { props } = usePage();
const vehicles = ref(props.vehicles || []);
const showModal = ref(false);
const isEditMode = ref(false);
const form = useForm({
    brand: '',
    model: '',
    vin: '',
    production_year: '',
    license_plate: '',
    insurance_expiry: '',
    inspection_due: '',
});

const formatDate = (dateString) => {
    return dateString.split(' ')[0];
};

const openModal = (editMode, vehicle = null) => {
    isEditMode.value = editMode;
    if (editMode && vehicle) {
        form.vehicle_id = vehicle.vehicle_id;
        form.brand = vehicle.brand;
        form.model = vehicle.model;
        form.vin = vehicle.vin;
        form.production_year = vehicle.production_year;
        form.license_plate = vehicle.license_plate;
        form.insurance_expiry = formatDate(vehicle.insurance_expiry);
        form.inspection_due = formatDate(vehicle.inspection_due);
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
        form.put(`/vehicles/${form.vehicle_id}`, {
            onSuccess: () => {
                router.visit(route('vehicles.index', {vehicles: props.vehicles}), {
                    preserveScroll: true
                });
                closeModal();
            },
        });
    } else {
        form.post('/vehicles', {
            onSuccess: () => {
                router.visit(route('vehicles.index', {vehicles: props.vehicles}), {
                    preserveScroll: true
                });
                closeModal();
            },
        });
    }
};

const deleteVehicle = (id) => {
    if (confirm('Czy na pewno chcesz usunąć ten pojazd?')) {
        router.delete(`/vehicles/${id}`, {
            onSuccess: () => {
                alert('Pojazd został usunięty.');
            },
        });
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
