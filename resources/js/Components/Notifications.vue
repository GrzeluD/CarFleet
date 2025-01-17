<template>
    <div class="relative dropdown__container">
        <button @click="toggleDropdown" class="notification-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11c0-3.071-1.574-5.64-4-6.326V4a1 1 0 00-2 0v.674C7.574 5.36 6 7.929 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            <span v-if="unreadCount > 0" class="badge">{{ unreadCount }}</span>
        </button>
        <div v-if="showDropdown" class="dropdown">
            <ul>
                <li v-for="notification in unreadNotifications" :key="notification.id" class="p-2 border-b">
                    {{ notification.data.message }}
                    <button @click="markAsRead(notification.id)" class="text-blue-500 hover:underline">
                        Oznacz jako przeczytane
                    </button>
                </li>
                <li v-if="unreadNotifications.length === 0" class="p-2 text-gray-500">
                    Brak nieprzeczytanych powiadomień
                </li>
            </ul>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const notifications = ref([])
const showDropdown = ref(false)

const unreadNotifications = computed(() => {
    return notifications.value.filter(notification => !notification.read_at)
})

const unreadCount = computed(() => {
    return unreadNotifications.value.length
})

const fetchNotifications = async () => {
    try {
        const response = await axios.get('/notifications')
        notifications.value = response.data
    } catch (error) {
        console.error('Błąd podczas pobierania powiadomień:', error)
    }
}

const markAsRead = async (notificationId) => {
    try {
        await axios.post(`/notifications/${notificationId}/mark-as-read`)
        await fetchNotifications()
    } catch (error) {
        console.error('Błąd podczas oznaczania powiadomienia jako przeczytane:', error)
    }
}

const toggleDropdown = () => {
    showDropdown.value = !showDropdown.value
}

onMounted(() => {
    fetchNotifications()
})
</script>

<style>
.notification-icon {
    position: relative;
    background: none;
    border: none;
    cursor: pointer;
    outline: none;
}

.badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background-color: red;
    color: white;
    border-radius: 50%;
    padding: 0.2em 0.6em;
    font-size: 0.5rem;
}

.dropdown__container .dropdown {
    position: absolute;
    top: 2.5rem;
    right: 0;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 0.375rem;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    z-index: 100;
    min-width: 250px;
}
</style>
