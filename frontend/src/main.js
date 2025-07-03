import "./assets/main.css";
import axios from "axios";

import { createApp } from "vue";
import App from "./App.vue";
import router from "./router";

axios.defaults.baseURL = import.meta.env.BACKEND_URL;
axios.defaults.headers.common["Authorization"] = localStorage.getItem("authToken");
axios.defaults.headers.common["Content-Type"] = "application/json";

const app = createApp(App);

app.use(router);

app.mount("#app");
