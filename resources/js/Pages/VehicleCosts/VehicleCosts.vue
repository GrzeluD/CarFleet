<template>
    <Layout>
        <template #header>
            <h1 class="text-xl">Lista kosztów</h1>
        </template>

        <button @click="openModal(false)" class="btn btn-primary mb-2">Dodaj Koszt</button>

        <table class="w-full border-collapse border">
            <thead>
            <tr>
                <th class="border p-2">Pojazd</th>
                <th class="border p-2">Typ Kosztu</th>
                <th class="border p-2">Kwota Brutto</th>
                <th class="border p-2">Data</th>
                <th class="border p-2">Faktura</th>
                <th class="border p-2">Akcje</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="cost in vehicleCosts" :key="cost.cost_id">
                <td class="border p-2">
                    {{ getVehicleName(cost.vehicle_id) }}
                </td>
                <td class="border p-2">
                    {{ getCostTypeName(cost.cost_type_id) }}
                </td>
                <td class="border p-2">{{ cost.amount_gross }}</td>
                <td class="border p-2">{{ cost.date.split(' ')[0] }}</td>
                <td class="border p-2">
                    <button v-if="cost.invoice_path" @click="previewInvoice(cost.invoice_path)" class="btn btn-sm btn-info">Podgląd</button>
                    <span v-else>Brak faktury</span>
                </td>
                <td class="border p-2">
                    <button @click="openModal(true, cost)" class="btn btn-sm btn-warning">Edytuj</button>
                    <button @click="deleteCost(cost.cost_id)" class="btn btn-sm btn-danger">Usuń</button>
                </td>
            </tr>
            </tbody>
        </table>


        <div v-if="showModal" class="modal">
            <div class="modal-content">
                <h2>{{ isEditMode ? 'Edytuj Koszt' : 'Dodaj Koszt' }}</h2>
                <form @submit.prevent="submitForm" enctype="multipart/form-data">
                    <div class="mb-2">
                        <label for="vehicle_id">Wybierz pojazd</label>
                        <select v-model="form.vehicle_id" id="vehicle_id" class="input" required>
                            <option v-for="vehicle in vehicles" :key="vehicle.vehicle_id" :value="vehicle.vehicle_id">
                                {{ vehicle.brand }} - {{ vehicle.license_plate }}
                            </option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label for="cost_type_id">Wybierz typ kosztu</label>
                        <select v-model="form.cost_type_id" id="cost_type_id" class="input" required>
                            <option v-for="costType in costTypes" :key="costType.cost_type_id" :value="costType.cost_type_id">
                                {{ costType.cost_type_name }}
                            </option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label for="date">Data</label>
                        <input v-model="form.date" type="date" id="date" class="input" required />
                    </div>
                    <div class="mb-2">
                        <label for="description">Opis</label>
                        <textarea v-model="form.description" id="description" class="input"></textarea>
                    </div>
                    <div class="mb-2">
                        <label for="amount_gross">Kwota Brutto</label>
                        <input v-model.number="form.amount_gross" @input="calculateNetAmount" type="number" step="0.01" id="amount_gross" class="input" required />
                    </div>
                    <div class="mb-2">
                        <label for="vat_rate">Stawka VAT (%)</label>
                        <input v-model.number="form.vat_rate" @input="calculateNetAmount" type="number" step="0.01" id="vat_rate" class="input" required />
                    </div>
                    <div class="mb-2">
                        <label for="amount_net">Kwota Netto</label>
                        <input v-model="form.amount_net" type="number" step="0.01" id="amount_net" class="input" readonly />
                    </div>
                    <div class="mb-2">
                        <label for="vat_amount">Kwota VAT</label>
                        <input v-model="form.vat_amount" type="number" step="0.01" id="vat_amount" class="input" readonly />
                    </div>
                    <div class="mb-2">
                        <label for="invoice_path">Dodaj Fakturę (PDF, JPG, PNG)</label>
                        <input @change="handleFileUpload" type="file" id="invoice_path" class="input" accept=".pdf,.jpg,.png" />
                    </div>
                    <button type="submit" class="btn btn-primary">{{ isEditMode ? 'Zaktualizuj' : 'Dodaj' }}</button>
                    <button @click="closeModal" type="button" class="btn">Anuluj</button>
                </form>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import {ref} from 'vue';
import {usePage, useForm} from '@inertiajs/vue3';
import axios from 'axios';
import Layout from "@/Pages/Layout.vue";

const {props} = usePage();
const vehicleCosts = ref(props.vehicleCosts || []);
const vehicles = ref(props.vehicles || []);
const costTypes = ref(props.costTypes || []);
const showModal = ref(false);
const isEditMode = ref(false);

const form = useForm({
    vehicle_id: '',
    cost_type_id: '',
    date: '',
    description: '',
    amount_gross: '',
    vat_rate: '',
    amount_net: '',
    vat_amount: '',
});

const previewInvoice = (path) => {
    const fullPath = `/${path}`;
    window.open(fullPath, '_blank');
};

const getVehicleName = (vehicleId) => {
    const vehicle = vehicles.value.find(v => v.vehicle_id === vehicleId);
    return vehicle ? `${vehicle.brand} - ${vehicle.license_plate}` : 'Nieznany pojazd';
};

const getCostTypeName = (costTypeId) => {
    const costType = costTypes.value.find(c => c.cost_type_id === costTypeId);
    return costType ? costType.cost_type_name : 'Nieznany typ kosztu';
};

const calculateNetAmount = () => {
    if (form.amount_gross && form.vat_rate) {
        const vatMultiplier = 1 + form.vat_rate / 100;
        form.amount_net = (form.amount_gross / vatMultiplier).toFixed(2);
        form.vat_amount = (form.amount_gross - form.amount_net).toFixed(2);
    } else {
        form.amount_net = 0;
        form.vat_amount = 0;
    }
};

const handleFileUpload = (event) => {
    form.invoice_path = event.target.files[0];
};

const openModal = (editMode, cost = null) => {
    isEditMode.value = editMode;
    if (editMode && cost) {
        form.cost_id = cost.cost_id;
        form.vehicle_id = cost.vehicle_id;
        form.cost_type_id = cost.cost_type_id;
        form.date = cost.date.split(' ')[0];
        form.description = cost.description;
        form.amount_gross = cost.amount_gross;
        form.vat_rate = cost.vat_rate;
        form.amount_net = cost.amount_net;
        form.vat_amount = cost.vat_amount;
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
        console.log(form)
        axios.post(`/vehicle-costs/${form.cost_id}`, form, {
            headers: {
                'Content-Type': 'multipart/form-data',
            }
        })
            .then(() => {
                alert('Koszt został zaktualizowany.');
                closeModal();
            })
            .catch(console.error);
    } else {
        axios.post('/vehicle-costs', form, {
            headers: {
                'Content-Type': 'multipart/form-data',
            }
        })
            .then(() => {
                alert('Koszt został dodany.');
                closeModal();
            })
            .catch(console.error);
    }
};
const deleteCost = (id) => {
    if (confirm('Czy na pewno chcesz usunąć ten koszt?')) {
        axios.delete(`/vehicle-costs/${id}`)
            .then(() => {
                alert('Koszt został usunięty.');
            })
            .catch(console.error);
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
