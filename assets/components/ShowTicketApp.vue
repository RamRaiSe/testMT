<template>
  <div class="ticket-page">
    <div class="ticket-details">
      <div v-if="ticket.status !== 1"><button class="btn btn-primary" @click="closeTicket">Close Ticket</button></div>
      <div class="meta">
        <span><strong>Created:</strong> {{ formatDate(ticket.createdAt) }}</span>
        <span><strong>Creator:</strong> {{ ticket.creator.email }}</span>
        <span><strong>Status:</strong> <span :class="'ticket-status ' + getStatusString(ticket.status)">{{ getStatusString(ticket.status) }}</span></span>
      </div>
      <h2>Ticket #{{ ticket.id }} — {{ ticket.title }}</h2>
      <p class="description">{{ ticket.description }}</p>
    </div>

    <div class="discussion">
      <h3>Discussion</h3>
      <div class="messages">
        <div v-for="message in messages" :key="message.id" class="message">
          <div class="author">{{ message.creator.email }}</div>
          <div class="text">{{ message.content }}</div>
          <div class="timestamp">{{ formatDate(message.createdAt) }}</div>
          <div v-if="ticket.status !== 1" @click="deleteMessage(message.id)" style="text-decoration: underline; margin-top: 6px; cursor: pointer; color: darkred">delete</div>
        </div>
      </div>

      <div class="new-message" v-if="ticket.status !== 1">
        <textarea v-model="newMessage" placeholder="Write a message..."></textarea>
        <button @click="sendMessage" :disabled="!newMessage">Send</button>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'ShowTicketApp',
  data() {
    return {
      ticket: {
        id: this.$route.params.id,
        title: '',
        description: '',
        createdAt: null,
        status: 0,
        creator: {
          email: '',
        },
      },
      messages: [],
      newMessage: '',
    }
  },
  methods: {
    async fetchTicket() {
      const response = await axios.get(`/api/ticket/${this.ticket.id}`);
      this.ticket = response.data;
    },
    async fetchMessages() {
      const response = await axios.get(`/api/ticket/${this.ticket.id}/messages`);
      this.messages = response.data;
    },
    async sendMessage() {
      if (!this.newMessage.trim()) return;

      await axios.post(`/api/ticket/${this.ticket.id}/messages`, {
        content: this.newMessage,
      });

      this.newMessage = '';
    },
    formatDate(date) {
      return new Date(date).toLocaleString();
    },
    getStatusString(status) {
      if (status === 0) {
        return 'open';
      }
      if (status === 1) {
        return 'closed';
      }
    },
    subscribe() {
      const eventSource = new EventSource(
          `http://localhost:3000/.well-known/mercure?topic=ticket-${this.ticket.id}`, {withCredentials: true}
      );

      eventSource.onmessage = (event) => {
        const data = JSON.parse(event.data);

        if (data.messageId) {
          const index = this.messages.findIndex((message) => {
            return message.id === data.messageId
          });

          this.messages.splice(index, 1);
        } else {
          this.messages.push(data);
        }
      };

      eventSource.onerror = (error) => {
        console.error("Error with EventSource:", error);
      };
    },
    closeTicket() {
      axios.post(`/api/ticket/${this.ticket.id}/close`);
      this.$router.push('/ticket');
    },
    deleteMessage(messageId) {
      axios.delete(`/api/ticket/${this.ticket.id}/messages/${messageId}`);
    }
  },
  mounted() {
    this.fetchTicket().catch(()=> {this.$router.push('/ticket')});
    this.fetchMessages();
    this.subscribe();
  },
}
</script>