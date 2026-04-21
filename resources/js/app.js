import { createApp } from 'vue'
import { createRouter, createWebHistory } from 'vue-router'
import App from './pages/Home.vue'
import axios from 'axios'

// CSRF token setup
axios.defaults.headers.common['X-CSRF-TOKEN'] =
    document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')

// Vue Router
const router = createRouter({
    history: createWebHistory(),
    routes: [
        { path: '/:pathMatch(.*)*', component: App }
    ]
})

const app = createApp(App)
app.use(router)
app.mount('#app')