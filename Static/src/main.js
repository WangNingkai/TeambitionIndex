import 'default-passive-events'
import 'mdui'
import 'mdui/dist/css/mdui.css'
import {createApp} from 'vue'
import App from './App.vue'
import './assets/index.css'
import './assets/notosans.css'
import router from './router/index'
import store from './store/index'
import 'default-passive-events'
const app = createApp(App)

app.use(router)
app.use(store)

app.mount('#app')
