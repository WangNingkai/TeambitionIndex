import 'mdui'
import 'mdui/dist/css/mdui.min.css'
import {createApp} from 'vue'
import './assets/notosans.css'
import './assets/index.css'
import App from './App.vue'
import router from './router/index'

const app = createApp(App)

app.use(router)

app.mount('#app')
