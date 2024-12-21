<template>
    <Layout>
        <template #header>
            <Heading>Lista użytkowników</Heading>
        </template>

        <TextFieldSearchBox class="max-w-[360px] w-full my-8" v-model="searchQuery" />

        <table class="w-full border-collapse border">
            <thead>
            <tr>
                <th class="border p-2">Nazwa</th>
                <th class="border p-2">E-mail</th>
                <th class="border p-2">Rola</th>
                <th class="border p-2">Akcje</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="user in filteredUsers" :key="user.id">
                <td class="border p-2">{{ user.name }}</td>
                <td class="border p-2">{{ user.email }}</td>
                <td class="border p-2">{{ user.role }}</td>
                <td class="border p-2">
                    <button @click="openModal(true, user)" class="btn btn-sm btn-warning">Edytuj</button>
                    <button @click="deleteUser(user.id)" class="btn btn-sm btn-danger">Usuń</button>
                </td>
            </tr>
            </tbody>
        </table>

        <ModalWrapper modal-styles="min-w-[360px]" v-if="showModal" :top-bar-desc="isEditMode ? 'Edytuj użytkownika' : 'Dodaj użytkownika'" @close="closeModal">
            <form @submit.prevent="submitForm">
                <TextField class="mt-0 mb-4" label="Imię i nazwisko" id="name" is-label-inside="true" v-model="form.name" />
                <TextField class="mt-0 mb-4" label="Email" id="email" is-label-inside="true" v-model="form.email" />
                <div class="dropdown relative h-max mb-4" :class="{'flex items-center gap-4': inline }">
                    <div class="relative mt-1.5">
                        <select v-model="form.role" id="role" class="bg-arris-inputBox-textFieldBackground border-arris-textfield-border p-2 rounded-md text-flotte-text w-full px-4 cursor-pointer h-[44px]" required>
                            <option value="" disabled selected>Wybierz role</option>
                            <option value="admin">Admin</option>
                            <option value="driver">Kierowca</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="button min-w-20 w-full py-2 rounded-lg flex items-center justify-center px-4 cursor-auto transition-colors cursor-pointer h-max bg-arris-btn-success xl:hover:bg-arris-btn-successHover text-arris-btn-textPrimary">{{ isEditMode ? 'Zaktualizuj' : 'Dodaj' }}</button>
            </form>
        </ModalWrapper>

        <Btn :is-small="true" class="w-40 mt-8" @click="openModal(false)">Dodaj Użytkownika</Btn>
    </Layout>
</template>

<script setup>
import {computed, ref} from 'vue';
import { usePage, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Pages/Layout.vue";
import Heading from "@/Components/Heading.vue";
import TextFieldSearchBox from "@/Components/inputs/TextFieldSearchBox.vue";
import Btn from "@/Components/Btn.vue";
import ModalWrapper from "@/Components/modals/ModalWrapper.vue";
import TextField from "@/Components/inputs/TextField.vue";
import Select from "@/Components/inputs/Select.vue";

const { props } = usePage();
const users = ref(props.users || []);
const showModal = ref(false);
const isEditMode = ref(false);
const searchQuery = ref('');
const form = useForm({
    name: '',
    email: '',
    role: '',
});
const selectOptions = ["Admin","Kierowca"];

const openModal = (editMode, user = null) => {
    isEditMode.value = editMode;
    if (editMode && user) {
        form.name = user.name;
        form.email = user.email;
        form.role = user.role;
        form.id = user.id;
    } else {
        form.reset();
    }
    showModal.value = true;
};

const filteredUsers = computed(() => {
    if (!searchQuery.value) {
        return users.value;
    }
    return users.value.filter(user =>
        user.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
        user.email.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
        user.role.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
});

const closeModal = () => {
    showModal.value = false;
};

const submitForm = () => {
    if (isEditMode.value) {
        form.put(`/users/${form.id}`, {
            onSuccess: () => {
                alert('Użytkownik został zaktualizowany.');
                closeModal();
            },
        });
    } else {
        form.post('/users', {
            onSuccess: () => {
                alert('Użytkownik został dodany.');
                closeModal();
            },
        });
    }
};

const deleteUser = (id) => {
    if (confirm('Czy na pewno chcesz usunąć tego użytkownika?')) {
        router.delete(`/users/${id}`, {
            onSuccess: () => {
                alert('Użytkownik został usunięty.');
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
