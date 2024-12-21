<template>
    <Layout>
        <template #header>
            <Heading>Lista pojazdów</Heading>
        </template>

        <TextFieldSearchBox class="max-w-[360px] w-full my-8" v-model="searchQuery" />

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

        <ModalWrapper modal-styles="min-w-[360px]" v-if="showModal" :top-bar-desc="isEditMode ? 'Edytuj pojazd' : 'Dodaj pojazd'" @close="closeModal">
            <form @submit.prevent="submitForm">
                <TextField class="mt-0 mb-4" label="Marka" id="brand" is-label-inside="true" v-model="form.brand" />
                <TextField class="my-4" label="Model" id="model" is-label-inside="true" v-model="form.model" />
                <TextField class="my-4" label="Vin" id="vin" is-label-inside="true" v-model="form.vin" />
                <TextField class="my-4" label="Rok produkcji" id="production-year" is-label-inside="true" v-model="form.production_year" input-type="number" />
                <TextField class="my-4" label="Numer rejestracyjny" id="license-plate" is-label-inside="true" v-model="form.license_plate" />
                <TextField class="my-4" label="Data końca ubezpieczenia" id="insurance-expiry" is-label-inside="true" v-model="form.insurance_expiry" input-type="date" />
                <TextField class="my-4" label="Data następnego przeglądu" id="inspection-due" is-label-inside="true" v-model="form.inspection_due" input-type="date"  />

                <button type="submit" class="button min-w-20 w-full py-2 rounded-lg flex items-center justify-center px-4 cursor-auto transition-colors cursor-pointer h-max bg-arris-btn-success xl:hover:bg-arris-btn-successHover text-arris-btn-textPrimary">
                    {{ isEditMode ? 'Zaktualizuj' : 'Dodaj' }}
                </button>
            </form>
        </ModalWrapper>

        <Btn :is-small="true" class="w-40 mt-8" @click="openModal(false)">Dodaj pojazd</Btn>
    </Layout>
</template>

<script setup>
import {computed, ref} from 'vue';
import { usePage, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Pages/Layout.vue";
import Btn from "@/Components/Btn.vue";
import TextField from "@/Components/inputs/TextField.vue";
import ModalWrapper from "@/Components/modals/ModalWrapper.vue";
import TextFieldSearchBox from "@/Components/inputs/TextFieldSearchBox.vue";
import Heading from "@/Components/Heading.vue";

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
