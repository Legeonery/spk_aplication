import { createApp } from 'vue'
import App from './App.vue'
import router from './router/router.js'
import './assets/main.css'
import 'vue-multiselect/dist/vue-multiselect.min.css'

const app = createApp(App)
app.use(router) // Используем router перед монтированием
app.mount('#app')
