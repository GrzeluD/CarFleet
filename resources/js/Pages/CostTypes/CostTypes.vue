<template>
    <Layout>
        <template #header>
            <Heading>Rodzaje kosztów</Heading>
        </template>

        <TextFieldSearchBox class="max-w-[360px] w-full my-8" v-model="searchQuery" />

        <div class="overflow-x-auto border border-gray-200 rounded-md shadow-sm bg-white">
            <table class="w-full text-sm text-gray-600">
                <thead>
                <tr class="bg-white text-gray-700">
                    <th class="p-2 font-medium text-left">Nazwa Kosztu</th>
                    <th class="p-2 font-medium text-center">Akcje</th>
                </tr>
                </thead>
                <tbody>
                <tr
                    v-for="costType in filteredTypes"
                    :key="costType.id"
                    class="hover:bg-gray-50 transition-colors border-t"
                >
                    <td class="p-2 whitespace-nowrap">{{ costType.cost_type_name }}</td>
                    <td class="p-2 text-center space-x-2">
                        <button
                            @click="openModal(true, costType)"
                            class="p-1 text-gray-600 hover:text-blue-500 transition"
                            title="Edytuj"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4h2a2 2 0 012 2v.586a1 1 0 01-.293.707l-6 6a1 1 0 01-.707.293H8a2 2 0 01-2-2v-2a1 1 0 01.293-.707l6-6A1 1 0 0111 4z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7l-4 4M7 17h10" />
                            </svg>
                        </button>
                        <button
                            @click="deleteCost(costType.cost_type_id)"
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


        <ModalWrapper modal-styles="min-w-[360px]" v-if="showModal" :top-bar-desc="isEditMode ? 'Edytuj koszt' : 'Dodaj koszt'" @close="closeModal">
            <form @submit.prevent="submitForm">
                <TextField class="mt-0 mb-4" label="Nazwa kosztu" id="cost-type-name" is-label-inside="true" v-model="form.cost_type_name" />
                <button type="submit" class="button min-w-20 w-full py-2 rounded-lg flex items-center justify-center px-4 cursor-auto transition-colors cursor-pointer h-max bg-arris-btn-success xl:hover:bg-arris-btn-successHover text-arris-btn-textPrimary">{{ isEditMode ? 'Zaktualizuj' : 'Dodaj' }}</button>
            </form>
        </ModalWrapper>

        <Btn :is-small="true" class="w-40 mt-8" @click="openModal(false)">Dodaj koszt</Btn>
    </Layout>
</template>

<script setup>
import {computed, ref} from 'vue';
import { usePage, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Pages/Layout.vue";
import Heading from "@/Components/Heading.vue";
import Btn from "@/Components/Btn.vue";
import ModalWrapper from "@/Components/modals/ModalWrapper.vue";
import TextField from "@/Components/inputs/TextField.vue";
import TextFieldSearchBox from "@/Components/inputs/TextFieldSearchBox.vue";

const { props } = usePage();
const costTypes = ref(props.costTypes || []);
const searchQuery = ref('');
const showModal = ref(false);
const isEditMode = ref(false);
const form = useForm({
    cost_type_name: '',
});

const filteredTypes = computed(() => {
    if (!searchQuery.value) {
        return costTypes.value;
    }
    return costTypes.value.filter(costType => costType.cost_type_name.toLowerCase().includes(searchQuery.value.toLowerCase()));
});

const openModal = (editMode, costType = null) => {
    isEditMode.value = editMode;
    if (editMode && costType) {
        form.cost_type_id = costType.cost_type_id;
        form.cost_type_name = costType.cost_type_name;
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
        axios.put(`/cost-types/${form.cost_type_id}`, form).then(response => {
            router.visit(route('cost-types.index'));
            closeModal();
        }).catch(error => {
            console.error(error);
        });
    } else {
        axios.post(`/cost-types`, form).then(response => {
            router.visit(route('cost-types.index'));
            closeModal();
        }).catch(error => {
            console.error(error);
        });
    }
};

const deleteCost = (id) => {
    if (confirm('Czy na pewno chcesz usunąć ten koszt?')) {
        axios.delete(`/cost-types/${id}`)
            .then(response => {
                router.visit(route('cost-types.index'));
            }).catch(error => {
            console.error(error);
        });
    }
};
</script>

<style>
</style>
