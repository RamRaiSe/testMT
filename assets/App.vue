<template>
  <header class="site-header">
    <div class="header-left" v-if="user">
<!--      <router-link to="/">Home</router-link>-->
      <router-link to="/ticket">Tickets</router-link>
    </div>
    <div class="header-right" v-if="user">
      <span class="user-email">{{user.email}}</span>
      <a @click="logout()" class="logout-button">Logout</a>
    </div>
    <div class="header-right" v-else>
      <router-link to="/login">Log In</router-link>
      <router-link to="/registration">Registration</router-link>
    </div>
  </header>

  <router-view />
</template>

<script>

import axios from "axios";
import {useUserStore} from "./stores/user";
import {mapActions, mapState} from "pinia";

export default {
  name: 'App',
  async created() {
    if (!this.user) {
      await this.fetchUser();
      if (!this.user) {
        this.$router.push('/login');
      }
    }
  },
  computed: {
    ...mapState(useUserStore, {
      user: 'user',
    })
  },
  methods: {
    ...mapActions(useUserStore, {
      setUser: 'setUser',
      fetchUser: 'fetchUser'
    }),
    async logout() {
      await axios.post('/api/logout');
      this.setUser(null);
      this.$router.push('/login');
    }
  }
}
</script>