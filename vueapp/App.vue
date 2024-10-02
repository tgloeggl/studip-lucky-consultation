<template>
    <div class="container" id="luckyconsultation">
        <Error />

        <Teleport to="#sidebar-navigation">
            <NavigationWidget>
            </NavigationWidget>
        </Teleport>

        <div class="content">
            <router-view></router-view>
        </div>
        <div class="clearfix"></div>
        <p></p>
    </div>

</template>

<script>
import Error from "@/components/Error";
import NavigationWidget from "@/components/NavigationWidget";

export default {
    name: "App",

    components: {
        Error,  NavigationWidget
    },

    beforeMount() {
        const placeholder = document.getElementById('sidebar-navigation');
        placeholder.innerHTML = '';
    }
};
</script>
