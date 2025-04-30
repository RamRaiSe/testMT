<template>
  <div class="tickets-container">
    <div class="header">
      <h1>Tickets</h1>
      <router-link class="btn btn-primary" to="/ticket/create">Create New Ticket</router-link>
    </div>

    <table class="tickets-table">
      <thead>
      <tr>
        <th>Ticket ID</th>
        <th>Status</th>
        <th>Title</th>
        <th>Creator</th>
        <th>Created At</th>
        <th>Actions</th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="ticket in tickets" :key="ticket.id">
        <td>#{{ ticket.id }}</td>
        <td>
          <span :class="'ticket-status ' + getStatusString(ticket.status)">{{ getStatusString(ticket.status) }}</span>
        </td>
        <td>{{ ticket.title }}</td>
        <td>{{ ticket.creator.email }}</td>
        <td>{{ ticket.createdAt }}</td>
        <td>
          <router-link class="btn btn-details" :to="'/ticket/'+ticket.id+'/show'">View</router-link>
        </td>
      </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      tickets: []
    }
  },
  created() {
    this.getTickets();
  },
  methods: {
    async getTickets() {
      const response = await axios.get('/api/tickets');

      if (response.data) {
        this.tickets = response.data;
      }
    },
    getStatusString(status) {
      if (status === 0) {
        return 'open';
      }
      if (status === 1) {
        return 'closed';
      }
    }
  }
};
</script>