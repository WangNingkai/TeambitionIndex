import 'mdui'
import 'mdui/dist/css/mdui.css'
import {createApp} from 'vue'
import App from './App.vue'
import './assets/index.css'
import './assets/notosans.css'
import router from './router/index'

const app = createApp(App)

app.use(router)

app.mount('#app')
