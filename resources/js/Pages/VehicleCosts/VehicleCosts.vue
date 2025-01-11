<template>
    <Layout>
        <template #header>
            <Heading>Ewidencja kosztów</Heading>
        </template>

        <div class="mb-4 flex justify-center mt-8 flex-wrap gap-2">
            <TextField class="m-0 w-52" label="Data filtrowania od" id="date" is-label-inside="true" v-model="filterStartDate" input-type="date" />
            <TextField class="m-0 w-52" label="Data filtrowania do" id="date" is-label-inside="true" v-model="filterEndDate" input-type="date" />
            <FilterDropdownCosts :cost-types="costTypes" v-model="filterCostTypeIds" />
            <FilterDropdownVehicle :vehicles="vehicles" v-model="filterVehicleIds" />
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
                    <th class="p-2 font-medium text-left">Typ Kosztu</th>
                    <th class="p-2 font-medium text-left">Kwota Brutto</th>
                    <th class="p-2 font-medium text-left">Kwota Netto</th>
                    <th class="p-2 font-medium text-left">Kwota VAT</th>
                    <th class="p-2 font-medium text-left">Data</th>
                    <th class="p-2 font-medium text-center">Faktura</th>
                    <th class="p-2 font-medium text-center">Akcje</th>
                </tr>
                </thead>
                <tbody>
                <tr
                    v-for="cost in vehicleCosts"
                    :key="cost.cost_id"
                    class="hover:bg-gray-50 transition-colors border-t"
                >
                    <td class="p-2 whitespace-nowrap">{{ getVehicleName(cost.vehicle_id) }}</td>
                    <td class="p-2 whitespace-nowrap">{{ getCostTypeName(cost.cost_type_id) }}</td>
                    <td class="p-2">{{ Number(cost.amount_gross).toFixed(2) }} zł</td>
                    <td class="p-2">{{ Number(cost.amount_net).toFixed(2) }} zł</td>
                    <td class="p-2">{{ Number(cost.vat_amount).toFixed(2) }} zł</td>
                    <td class="p-2 whitespace-nowrap">{{ formatDate(cost.date) }}</td>
                    <td class="p-2 text-center">
                        <button
                            v-if="cost.invoice_path"
                            @click="previewInvoice(cost.invoice_path)"
                            class="p-1 text-gray-600 hover:text-blue-500 transition"
                            title="Podgląd Faktury"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0zm-9-4v8" />
                            </svg>
                        </button>
                        <span v-else class="text-gray-400 text-xs">Brak faktury</span>
                    </td>
                    <td class="p-2 text-center space-x-2">
                        <button
                            @click="openModal(true, cost)"
                            class="p-1 text-gray-600 hover:text-blue-500 transition"
                            title="Edytuj"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4h2a2 2 0 012 2v.586a1 1 0 01-.293.707l-6 6a1 1 0 01-.707.293H8a2 2 0 01-2-2v-2a1 1 0 01.293-.707l6-6A1 1 0 0111 4z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7l-4 4M7 17h10" />
                            </svg>
                        </button>

                        <button
                            @click="deleteCost(cost.cost_id)"
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
                        <select v-model="form.cost_type_id" id="cost-type-id" class="bg-arris-inputBox-textFieldBackground border-arris-textfield-border p-2 rounded-md text-flotte-text w-full px-4 cursor-pointer h-[44px]" required>
                            <option value="" disabled selected>Wybierz rodzaj kosztu</option>
                            <option v-for="costType in costTypes" :key="costType.cost_type_id" :value="costType.cost_type_id">
                                {{ costType.cost_type_name }}
                            </option>
                        </select>
                    </div>
                </div>
                <TextField class="mt-0 mb-4" label="Podaj datę ewidencji" id="date" is-label-inside="true" v-model="form.date" input-type="date" />

                <div class="flex gap-4">

                    <TextField class="mt-0 mb-4" label="Kwota Brutto" id="amount-gross" is-label-inside="true" v-model="form.amount_gross" input-type="number" @modify-input="calculateNetAmount" input-step="0.01" />
                    <TextField class="mt-0 mb-4" label="Stawka VAT (%)" id="vat-rate" is-label-inside="true" v-model="form.vat_rate" input-type="number" @modify-input="calculateNetAmount" />
                </div>
                <div class="flex gap-4">
                    <TextField class="mt-0 mb-4" label="Kwota Netto" id="amount-net" is-label-inside="true" v-model="form.amount_net" input-type="number" input-step="0.01" is-read-only="true" />
                    <TextField class="mt-0 mb-4" label="Kwota VAT" id="vat-amount" is-label-inside="true" v-model="form.vat_amount" input-type="number" input-step="0.01" is-read-only="true" />
                </div>
                <div class="dropdown relative h-max mb-4 w-full max-w-[506px]" :class="{'flex items-center gap-4': inline }">
                    <div class="relative mt-1.5">
                        <input @change="handleFileUpload" accept=".pdf,.jpg,.png" id="invoice_path" class="bg-arris-inputBox-textFieldBackground border-arris-textfield-border p-2 rounded-md text-flotte-text w-full px-4 cursor-pointer h-[44px]" type="file">
                    </div>
                </div>
                <TextArea v-model="form.description" class="mt-0 mb-4" label="Opis"></TextArea>

                <button type="submit" class="button min-w-20 w-full py-2 rounded-lg flex items-center justify-center px-4 cursor-auto transition-colors cursor-pointer h-max bg-arris-btn-success xl:hover:bg-arris-btn-successHover text-arris-btn-textPrimary">
                    {{ isEditMode ? 'Zaktualizuj' : 'Dodaj' }}
                </button>
            </form>
        </ModalWrapper>

        <div class="flex justify-between flex-wrap pb-4">
            <Btn :is-small="true" class="w-52 mt-8" @click="openModal(false)">Dodaj ewidencje przebiegu</Btn>
            <Btn btn-type="secondary" :is-small="true" class="w-52 mt-2 lg:mt-8" @click="downloadPDF">Pobierz raport PDF</Btn>
        </div>
    </Layout>
</template>

<script setup>
import {ref} from 'vue';
import {usePage, useForm, router} from '@inertiajs/vue3';
import axios from 'axios';
import Layout from "@/Pages/Layout.vue";
import Heading from "@/Components/Heading.vue";
import Btn from "@/Components/Btn.vue";
import ModalWrapper from "@/Components/modals/ModalWrapper.vue";
import TextField from "@/Components/inputs/TextField.vue";
import Select from "@/Components/inputs/Select.vue";
import TextArea from "@/Components/inputs/TextArea.vue";
import FilterDropdownVehicle from "@/Components/FilterDropdownVehicle.vue";
import FilterDropdownCosts from "@/Components/FilterDropdownCosts.vue";

const {props} = usePage();
const vehicleCosts = ref(props.vehicleCosts || []);
const vehicles = ref(props.vehicles || []);
const costTypes = ref(props.costTypes || []);
const showModal = ref(false);
const isEditMode = ref(false);
const filterStartDate = ref('');
const filterEndDate = ref('');
const filterVehicleIds = ref([]);
const filterCostTypeIds = ref([]);

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

const applyFilters = () => {
    axios.get(route('vehicle-costs.index'), {
        params: {
            start_date: filterStartDate.value,
            end_date: filterEndDate.value,
            vehicle_ids: filterVehicleIds.value,
            cost_type_ids: filterCostTypeIds.value,
        }
    })
        .then(response => {
            vehicleCosts.value = response.data.vehicleCosts;
        })
        .catch(console.error);
};

const resetFilters = () => {
    filterStartDate.value = '';
    filterEndDate.value = '';
    filterVehicleIds.value = [];
    filterCostTypeIds.value = [];
    applyFilters();
};

const previewInvoice = (path) => {
    const fullPath = `/storage/invoices/${path}`;
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
        form.amount_net = (form.amount_gross / (1 + form.vat_rate / 100)).toFixed(2);
        form.vat_amount = (form.amount_gross - form.amount_net).toFixed(2);
    } else {
        form.amount_net = 0;
        form.vat_amount = 0;
    }
};
const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString.split(' ')[0]);
    return date.toLocaleDateString('pl-PL', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
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
        const formData = new FormData();

        for (const key in form) {
            formData.append(key, form[key]);
        }

        formData.append('_method', 'PUT');

        axios.post(`/vehicle-costs/${form.cost_id}`, formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            }
        })
            .then(() => {
                router.visit(route('vehicle-costs.index'), {
                    preserveScroll: true
                });
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
                router.visit(route('vehicle-costs.index'), {
                    preserveScroll: true
                });
                closeModal();
            })
            .catch(console.error);
    }
};
const deleteCost = (id) => {
    if (confirm('Czy na pewno chcesz usunąć ten koszt?')) {
        axios.delete(`/vehicle-costs/${id}`)
            .then(() => {
                router.visit(route('vehicle-costs.index'), {
                    preserveScroll: true
                });
            })
            .catch(console.error);
    }
};

const downloadPDF = () => {
    const params = new URLSearchParams();

    if (filterStartDate.value) params.append('start_date', filterStartDate.value);
    if (filterEndDate.value) params.append('end_date', filterEndDate.value);

    if (filterVehicleIds.value.length > 0) {
        filterVehicleIds.value.forEach(id => params.append('vehicle_ids[]', id));
    }

    if (filterCostTypeIds.value.length > 0) {
        filterCostTypeIds.value.forEach(id => params.append('cost_type_ids[]', id));
    }

    window.location.href = `/vehicle-costs/pdf?${params.toString()}`;
}
</script>

<style>
input[type=file]::file-selector-button {
    border: none;
    background: #9fcdff;
    border-radius: 4px;
    padding: 4px 4px;
}
</style>
