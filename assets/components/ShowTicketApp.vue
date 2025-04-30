<template>
  <div class="ticket-show-container">
    <div class="ticket-details">
      <h2>{{ ticket.title }}</h2>
      <p class="description">{{ ticket.description }}</p>
    </div>

    <div class="comments-section">
      <h3>Discussion</h3>
      <div class="comments-list">
        <div v-for="comment in comments" :key="comment.id" class="comment">
          <strong>{{ comment.author }}</strong>: {{ comment.content }}
        </div>
      </div>

      <div class="comment-form">
        <textarea v-model="newComment" placeholder="Write a comment..." />
        <button @click="submitComment" :disabled="newComment === ''">Send</button>
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
      },
      comments: [],
      newComment: '',
      intervalId: null,
    }
  },
  methods: {
    async fetchTicket() {
      const res = await axios.get(`/api/ticket/${this.ticket.id}`);
      this.ticket = res.data;
    },
    async fetchComments() {
      const res = await axios.get(`/api/ticket/${this.ticket.id}/comments`);
      this.comments = res.data;
    },
    async submitComment() {
      if (!this.newComment.trim()) return;

      await axios.post(`/api/ticket/${this.ticket.id}/comments`, {
        content: this.newComment,
      });

      this.newComment = '';
      this.fetchComments();
    }
  },
  mounted() {
    this.fetchTicket()
    this.fetchComments()
    this.intervalId = setInterval(this.fetchComments, 3000) // simulate real-time
  },
  beforeUnmount() {
    clearInterval(this.intervalId)
  }
}
</script>