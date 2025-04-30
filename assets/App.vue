<template>
  <header class="site-header">
    <div class="header-left">
      <router-link to="/">Home</router-link>
      <router-link to="/ticket">Tickets</router-link>
    </div>
    <div class="header-right">
      <span class="user-email"></span>
      <a @click="logout()" class="logout-button">Logout</a>
    </div>
  </header>

  <router-view />
</template>

<script>

import axios from "axios";

export default {
  name: 'App',
  methods: {
    async logout() {
      await axios.post('/api/logout');
      this.$router.push('/');
    }
  }
}
</script>