import { createRouter, createWebHashHistory } from 'vue-router';

export default createRouter({
    history: createWebHashHistory(),
    base: window.location.pathname,
    routes: [
        {
            path: "/",
            name: "index",
            component: () => import("@/views/Sprechstunden")
        },

        {
            path: "/editor",
            name: "editor",
            component: () => import("@/views/SprechstundenEditor")
        },

        {
            path: "/templates",
            name: "templates",
            component: () => import("@/views/SprechstundenTemplates")
        }
    ]
});
