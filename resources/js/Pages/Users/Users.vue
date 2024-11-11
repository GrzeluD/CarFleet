<template>
    <div>
        <h1 class="text-2xl font-bold mb-4">Lista Użytkowników</h1>
        <button @click="openModal(false)" class="btn btn-primary mb-2">Dodaj Użytkownika</button>

        <table class="w-full border-collapse border">
            <thead>
            <tr>
                <th class="border p-2">ID</th>
                <th class="border p-2">Nazwa</th>
                <th class="border p-2">E-mail</th>
                <th class="border p-2">Rola</th>
                <th class="border p-2">Akcje</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="user in users" :key="user.id">
                <td class="border p-2">{{ user.id }}</td>
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

        <div v-if="showModal" class="modal">
            <div class="modal-content">
                <h2>{{ isEditMode ? 'Edytuj Użytkownika' : 'Dodaj Użytkownika' }}</h2>
                <form @submit.prevent="submitForm">
                    <div class="mb-2">
                        <label for="name">Nazwa</label>
                        <input v-model="form.name" type="text" id="name" class="input" required />
                    </div>

                    <div class="mb-2">
                        <label for="email">E-mail</label>
                        <input v-model="form.email" type="email" id="email" class="input" required />
                    </div>

                    <div class="mb-2">
                        <label for="role">Rola</label>
                        <select v-model="form.role" id="role" class="input" required>
                            <option value="admin">Admin</option>
                            <option value="driver">Kierowca</option>
                        </select>
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
const users = ref(props.users || []);
const showModal = ref(false);
const isEditMode = ref(false);
const form = useForm({
    name: '',
    email: '',
    role: '',
});

const openModal = (editMode, user = null) => {
    isEditMode.value = editMode;
    if (editMode && user) {
        form.name = user.name;
        form.email = user.email;
        form.role = user.role;
        form.id = user.id; // Ustawianie ID przy edycji
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
