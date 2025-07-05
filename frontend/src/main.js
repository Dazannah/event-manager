import "./assets/main.css";
import axios from "axios";

import { createApp, ref } from "vue";
import App from "./App.vue";
import router from "./router";

import InputError from "./components/InputError.vue";
import SuccessMessage from "./components/SuccessMessage.vue";
import Loading from "./components/Loading.vue";
import EventFormPanel from "./components/forms/EventFormPanel.vue";
import Chat from "./components/Chat.vue";

axios.defaults.baseURL = import.meta.env.VITE_BACKEND_API_URL;
axios.defaults.headers.common["Authorization"] = `Bearer ${localStorage.getItem("token")}`;
axios.defaults.headers.common["Accept"] = "application/json";
axios.defaults.headers.common["Content-Type"] = "application/json";

const app = createApp(App);

app.config.globalProperties.axios = axios;

app.component("InputError", InputError);
app.component("SuccessMessage", SuccessMessage);
app.component("Loading", Loading);
app.component("EventFormPanel", EventFormPanel);
app.component("Chat", Chat);

const isAuthenticated = ref(!!localStorage.getItem("token"));
app.provide("isAuthenticated", isAuthenticated);

const isHelpdeskAgent = ref(localStorage.getItem("isHelpdeskAgent") == "true");
app.provide("isHelpdeskAgent", isHelpdeskAgent);

const userId = ref(localStorage.getItem("userId"));
app.provide("userId", userId);

app.use(router);

app.mount("#app");
