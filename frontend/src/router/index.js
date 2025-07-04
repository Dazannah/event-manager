import { createRouter, createWebHistory } from "vue-router";

function isAuthenticated() {
  return !!localStorage.getItem("token");
}

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: "/",
      redirect: "/dashboard"
    },
    {
      path: "/login",
      name: "login",
      component: () => import("../views/LoginView.vue"),
      beforeEnter: (to, from, next) => {
        if (isAuthenticated()) next({ name: "dashboard" });
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
      path: "/dashboard",
      name: "dashboard",
      component: () => import("../views/DashboardView.vue"),
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
