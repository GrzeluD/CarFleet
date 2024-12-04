<template>
    <Layout>
        <template #header>
            <h1 class="text-xl">Lista pojazdów</h1>
        </template>

        <div class="mb-4">
            <input
                v-model="searchQuery"
                type="text"
                placeholder="Szukaj pojazdu..."
                class="input w-full"
            />
        </div>

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
            <tr v-for="vehicle in filteredVehicles" :key="vehicle.id">
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
    </Layout>
</template>

<script setup>
import {computed, ref} from 'vue';
import { usePage, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Pages/Layout.vue";

const { props } = usePage();
const vehicles = ref(props.vehicles || []);
const searchQuery = ref('');
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

const filteredVehicles = computed(() => {
    if (!searchQuery.value) {
        return vehicles.value;
    }
    return vehicles.value.filter(vehicle =>
        vehicle.brand.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
        vehicle.model.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
        vehicle.license_plate.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
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
        axios.put(`/vehicles/${form.vehicle_id}`, form).then(response => {
            router.visit(route('vehicles.index'), {
                preserveScroll: true
            });
            closeModal();
        }).catch(error => {
            console.error(error);
        });
    } else {
        axios.post('/vehicles', form).then(response => {
            router.visit(route('vehicles.index'), {
                preserveScroll: true
            });
            closeModal();
        }).catch(error => {
            console.error(error);
        });
    }
};

const deleteVehicle = (id) => {
    if (confirm('Czy na pewno chcesz usunąć ten pojazd?')) {
        axios.delete(`/vehicles/${id}`)
            .then(response => {
                router.visit(route('vehicles.index'));
            }).catch(error => {
            console.error(error);
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
