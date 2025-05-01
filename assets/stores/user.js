import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from "axios";

export const useUserStore = defineStore('user', () => {
    const user = ref(null)

    const isAuthenticated = computed(() => !!user.value)

    async function fetchUser() {
        try {
            const response = await axios.get('/api/user')
            user.value = response.data
        } catch (error) {
            user.value = null
        }
    }

    function setUser(currentUser) {
        user.value = currentUser;
    }

    return {
        user,
        isAuthenticated,
        setUser,
        fetchUser
    }
})