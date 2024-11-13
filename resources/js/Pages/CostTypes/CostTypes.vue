<template>
    <Layout>
        <template #header>
            <h1 class="text-xl">Rodzaje kosztów</h1>
        </template>
        <button @click="openModal(false)" class="btn btn-primary mb-2">Dodaj koszt</button>

        <table class="w-full border-collapse border">
            <thead>
            <tr>
                <th class="border p-2">Nazwa Kosztu</th>
                <th class="border p-2">Akcje</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="costType in costTypes" :key="costType.id">
                <td class="border p-2">{{ costType.cost_type_name }}</td>
                <td class="border p-2">
                    <button @click="openModal(true, costType)" class="btn btn-sm btn-warning">Edytuj</button>
                    <button @click="deleteCost(costType.cost_type_id)" class="btn btn-sm btn-danger">Usuń</button>
                </td>
            </tr>
            </tbody>
        </table>

        <div v-if="showModal" class="modal">
            <div class="modal-content">
                <h2>{{ isEditMode ? 'Edytuj ' : 'Dodaj koszt' }}</h2>
                <form @submit.prevent="submitForm">
                    <div class="mb-2">
                        <label for="brand">Nazwa kosztu</label>
                        <input v-model="form.cost_type_name" type="text" id="brand" class="input" required />
                    </div>

                    <button type="submit" class="btn btn-primary">{{ isEditMode ? 'Zaktualizuj' : 'Dodaj' }}</button>
                    <button @click="closeModal" type="button" class="btn">Anuluj</button>
                </form>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import { ref } from 'vue';
import { usePage, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Pages/Layout.vue";

const { props } = usePage();
const costTypes = ref(props.costTypes || []);
const showModal = ref(false);
const isEditMode = ref(false);
const form = useForm({
    cost_type_name: '',
});

const formatDate = (dateString) => {
    return dateString.split(' ')[0];
};

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
