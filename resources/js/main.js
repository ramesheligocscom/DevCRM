import App from '@/App.vue'
import { registerPlugins } from '@core/utils/plugins'
import { createApp } from 'vue'

// Styles
import '@core-scss/template/index.scss'
import '@styles/styles.scss'

// Toast 
import Vue3Toastify from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'
// Create vue app
const app = createApp(App)
app.use(Vue3Toastify, {
    autoClose: 3000,
    position: "top-right",
});


// Register plugins
registerPlugins(app)

// Mount vue app
app.mount('#app')

// Use Tostify
