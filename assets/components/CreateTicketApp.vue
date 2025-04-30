<template>
  <div class="ticket-form-container">
    <h1>Create New Ticket</h1>

    <div class="ticket-form">
      <div class="form-group">
        <label for="title">Title</label>
        <input
            v-model="ticket.title"
            type="text"
            id="title"
            placeholder="Enter ticket title"
            :class="errors.title ? 'empty-required': ''"
            @input="this.errors.title = '';"
        />
        <p v-if="errors.title" class="error">{{ errors.title }}</p>
      </div>

      <div class="form-group">
        <label for="description">Description</label>
        <textarea
            v-model="ticket.description"
            id="description"
            placeholder="Describe your issue"
            rows="5"
            :class="errors.description ? 'empty-required': ''"
            @input="this.errors.description = '';"
        ></textarea>
        <p v-if="errors.description" class="error">{{ errors.description }}</p>
      </div>

      <div class="submit-button" @click="submitTicket">
        Create Ticket
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      ticket: {
        title: '',
        description: ''
      },
      errors: {}
    };
  },
  methods: {
    async submitTicket() {
      this.errors = {};

      if (!this.ticket.title) {
        this.errors.title = 'Title is required.';
      }

      if (!this.ticket.description) {
        this.errors.description = 'Description is required.';
      }

      if (Object.keys(this.errors).length > 0) { return; }

      try {
        const response = await axios.post('/api/ticket', this.ticket);

        if (response.data) {
          this.$router.push('/ticket');
        }
      } catch (error) {
        this.message = 'Failed to create ticket.';
      }
    }
  }
};
</script>