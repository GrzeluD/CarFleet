<template>
    <Layout>
        <template #header>
            <Heading>Rodzaje kosztów</Heading>
        </template>

        <TextFieldSearchBox class="max-w-[360px] w-full my-8" v-model="searchQuery" />

        <button @click="openModal(false)" class="btn btn-primary mb-2">Dodaj koszt</button>

        <table class="w-full border-collapse border">
            <thead>
            <tr>
                <th class="border p-2">Nazwa Kosztu</th>
                <th class="border p-2">Akcje</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="costType in filteredTypes" :key="costType.id">
                <td class="border p-2">{{ costType.cost_type_name }}</td>
                <td class="border p-2">
                    <button @click="openModal(true, costType)" class="btn btn-sm btn-warning">Edytuj</button>
                    <button @click="deleteCost(costType.cost_type_id)" class="btn btn-sm btn-danger">Usuń</button>
                </td>
            </tr>
            </tbody>
        </table>

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
