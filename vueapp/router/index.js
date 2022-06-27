import Vue from "vue";
import Router from "vue-router";

Vue.use(Router);

export default new Router({
    routes: [
        {
            path: "/",
            name: "index",
            component: () => import("@/views/Sprechstunden")
        },

        {
            path: "/editor",
            name: "index",
            component: () => import("@/views/SprechstundenEditor")
        }
    ]
});
