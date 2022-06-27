import Vue from "vue";
import Router from "vue-router";

Vue.use(Router);

export default new Router({
    routes: [
        {
            path: "/course",
            component: () => import("@/views/RouterView"),

            children: [
                {
                    path: '/course',
                    component: () => import("@/views/Course"),

                    children: [
                        {
                            name: "index",
                            path: "index",
                            component: () => import("@/views/Sprechstunden")
                        },
                    ]
                }
            ]
        }
    ]
});
