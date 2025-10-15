import { createApp } from "vue";
import App from "./App.vue";

import PrimeVue from "primevue/config";
import Aura from "@primeuix/themes/aura";
import "primeicons/primeicons.css";

import "./style.css";
import { createPinia } from "pinia";
import router from "./router";
import i18n from "./plugins/i18n";

import ToastService from "primevue/toastservice";
import ConfirmationService from "primevue/confirmationservice";

const app = createApp(App);

app.use(PrimeVue, {
  theme: {
    preset: Aura,
    options: {
      prefix: "p",
      darkModeSelector: ".dark",
    },
  },
});

app.use(createPinia());
app.use(router);
app.use(i18n);
app.use(ToastService);
app.use(ConfirmationService);

app.mount("#app");
