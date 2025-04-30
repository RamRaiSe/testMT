<template>
  <div class="register-container">
    <h1>Registration</h1>

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
            v-model="plainPassword"
            type="password"
            placeholder="Enter your password"
            :class="isEmptyPassword ? 'empty-required': ''"
            @input="isEmptyPassword = false;"
        />
      </div>
      <p v-if="errors" class="error">{{ errors }}</p>

      <div @click="submitForm" class="submit-button">Sign Up</div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      email: '',
      plainPassword: '',
      errors: '',
      isEmptyEmail: false,
      isEmptyPassword: false
    };
  },
  methods: {
    async submitForm() {
      this.errors = '';

      this.isEmptyEmail = this.email === '';
      this.isEmptyPassword = this.plainPassword === '';

      if (this.isEmptyEmail || this.isEmptyPassword) {
        return;
      }

      try {
        const response = await axios.post('/api/registration', {email: this.email, plainPassword: this.plainPassword});

        if (response.data) {
          this.$router.push('/login');
        }
      } catch (error) {
        this.errors = 'Invalid email or password. Please try again.';
      }
    }
  }
}
</script>
