import { createRouter, createWebHistory } from "vue-router";

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: "/", redirect: "/contacts" },
    {
      path: "/contacts",
      name: "contacts",
      component: () => import("@/modules/contacts/views/List.vue"),
    },
    {
      path: "/contacts/new",
      name: "contact-new",
      component: () => import("@/modules/contacts/views/Form.vue"),
    },
    {
      path: "/contacts/:id/edit",
      name: "contact-edit",
      component: () => import("@/modules/contacts/views/Form.vue"),
      props: true,
    },
  ],
});

export default router;
