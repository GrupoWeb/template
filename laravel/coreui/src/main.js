import 'core-js/stable'
import Vue from 'vue'
import App from './App'
import router from './router'
import CoreuiVue from '@coreui/vue'
import { iconsSet as icons } from './assets/icons/icons.js'
import store from './store'
import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';
import VueSweetalert2 from 'vue-sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';



Vue.use(VueSweetalert2)
// Vue.prototype.$apiAdress = 'http://127.0.0.1:8000'
Vue.prototype.$apiAdress = 'https://dev.diaco.gob.gt'
Vue.config.performance = true
Vue.use(CoreuiVue)


new Vue({
  el: '#app',
  router,
  store,
  icons,
  template: '<App/>',
  components: {
    App
  },
})
