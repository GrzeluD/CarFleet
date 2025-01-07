<template>
    <Layout>
        <template #header>
            <Heading>Lista użytkowników</Heading>
        </template>

        <TextFieldSearchBox class="max-w-[360px] w-full my-8" v-model="searchQuery" />

        <div class="overflow-x-auto border border-gray-200 rounded-md shadow-sm bg-white">
            <table class="w-full text-sm text-gray-600">
                <thead>
                <tr class="bg-white text-gray-700">
                    <th class="p-2 font-medium text-left">Nazwa</th>
                    <th class="p-2 font-medium text-left">E-mail</th>
                    <th class="p-2 font-medium text-left">Rola</th>
                    <th class="p-2 font-medium text-center">Akcje</th>
                </tr>
                </thead>
                <tbody>
                <tr
                    v-for="user in filteredUsers"
                    :key="user.id"
                    class="hover:bg-gray-50 transition-colors border-t"
                >
                    <td class="p-2 whitespace-nowrap">{{ user.name }}</td>
                    <td class="p-2 whitespace-nowrap">{{ user.email }}</td>
                    <td class="p-2 whitespace-nowrap">{{ formatUserRole(user.role) }}</td>
                    <td class="p-2 text-center space-x-2">
                        <button
                            @click="openModal(true, user)"
                            class="p-1 text-gray-600 hover:text-blue-500 transition"
                            title="Edytuj"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4h2a2 2 0 012 2v.586a1 1 0 01-.293.707l-6 6a1 1 0 01-.707.293H8a2 2 0 01-2-2v-2a1 1 0 01.293-.707l6-6A1 1 0 0111 4z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7l-4 4M7 17h10" />
                            </svg>
                        </button>
                        <button
                            @click="deleteUser(user.id)"
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

const formatUserRole = userRole => {
    return userRole === 'admin' ? 'Administrator' : userRole === 'driver' ? 'Kierowca' : '';
}

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
</style>
