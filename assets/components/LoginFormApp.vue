<template>
  <div class="register-container">
    <h1>Sign In</h1>

    <div class="register-form">
      <div class="form-group">
        <label for="email">Email</label>
        <input
            v-model="email"
            type="email"
            placeholder="Enter your email"
            :class="isEmptyEmail ? 'empty-required': ''"
            @input="isEmptyEmail = false;"
        />
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input
            v-model="password"
            type="password"
            placeholder="Enter your password"
            :class="isEmptyPassword ? 'empty-required': ''"
            @input="isEmptyPassword = false;"
        />
      </div>
      <p v-if="errors" class="error">{{ errors }}</p>

      <div @click="submitForm" class="submit-button">Sign In</div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      email: '',
      password: '',
      errors: '',
      isEmptyEmail: false,
      isEmptyPassword: false
    };
  },
  methods: {
    async submitForm() {
      this.errors = '';

      this.isEmptyEmail = this.email === '';
      this.isEmptyPassword = this.password === '';

      if (this.isEmptyEmail || this.isEmptyPassword) {
        return;
      }

      try {
        const response = await axios.post('/api/login', {email: this.email, password: this.password});

        if (response.data) {
          this.$router.push('/');
        }
      } catch (error) {
        this.errors = 'Invalid email or password. Please try again.';
      }
    }
  }
}
</script>
