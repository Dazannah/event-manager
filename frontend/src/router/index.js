import { createRouter, createWebHistory } from "vue-router";

function isAuthenticated() {
  return !!localStorage.getItem("token");
}

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: "/",
      redirect: "/event"
    },
    {
      path: "/login",
      name: "login",
      component: () => import("../views/LoginView.vue"),
      beforeEnter: (to, from, next) => {
        if (isAuthenticated()) next({ name: "events" });
        else next();
      }
    },
    {
      path: "/forgot-password",
      name: "forgotPassword",
      component: () => import("../views/ForgotPasswordView.vue")
    },
    {
      path: "/reset-password",
      name: "resetPassword",
      component: () => import("../views/ResetPasswordView.vue")
    },
    {
      path: "/event",
      name: "event",
      component: () => import("../views/EventsView.vue"),
      meta: { requiresAuth: true }
    },
    {
      path: "/helpdesk-page",
      name: "helpdeskPage",
      component: () => import("../views/HelpdeskPageView.vue"),
      meta: { requiresAuth: true }
    }
  ]
});

router.beforeEach((to, from, next) => {
  if (to.meta.requiresAuth && !isAuthenticated()) {
    next({ name: "login" });
  } else {
    next();
  }
});

export default router;
