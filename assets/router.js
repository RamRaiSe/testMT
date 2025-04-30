import TicketCreate from "./components/CreateTicketApp.vue";
import TicketIndexApp from "./components/TicketIndexApp.vue";
import {createRouter, createWebHistory} from "vue-router";
import RegistrationFormApp from "./components/RegistrationFormApp.vue";
import LoginFormApp from "./components/LoginFormApp.vue";
import HomeApp from "./components/HomeApp.vue";
import ShowTicketApp from "./components/ShowTicketApp.vue";

const routes = [
    { path: '/', component: HomeApp },
    { path: '/registration', component: RegistrationFormApp },
    { path: '/login', component: LoginFormApp },
    { path: '/ticket/create', component: TicketCreate },
    { path: '/ticket', component: TicketIndexApp },
    { path: '/ticket/:id/show', component: ShowTicketApp },
]

const router = createRouter({
    history: createWebHistory('/'),
    routes,
})

export default router