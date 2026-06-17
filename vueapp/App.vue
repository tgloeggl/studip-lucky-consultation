<template>
    <div class="container" id="luckyconsultation">
        <Error />

        <Teleport to="#sidebar-navigation">
            <NavigationWidget>
            </NavigationWidget>
        </Teleport>

        <Teleport v-if="hasActionWidget" to="#action-widget">
            <ActionsWidget
                @create-pool="callActiveView('startAddPool')"
                @create-approved-date="callActiveView('addDate', true)"
                @create-preliminary-date="callActiveView('addDate', false)"
            />
        </Teleport>

        <div class="content">
            <router-view v-slot="{ Component }">
                <component :is="Component" ref="activeView" />
            </router-view>
        </div>
        <div class="clearfix"></div>
        <p></p>
    </div>

</template>

<script>
import Error from "@/components/Error";
import NavigationWidget from "@/components/NavigationWidget";
import ActionsWidget from "@/components/ActionsWidget";

export default {
    name: "App",

    components: {
        Error,
        NavigationWidget,
        ActionsWidget
    },

    data() {
        return {
            hasActionWidget: false
        }
    },

    methods: {
        callActiveView(method, ...args) {
            const activeView = this.$refs.activeView;

            if (activeView && typeof activeView[method] === 'function') {
                activeView[method](...args);
            }
        }
    },

    beforeMount() {
        const placeholder = document.getElementById('sidebar-navigation');
        placeholder.innerHTML = '';

        const actionWidget = document.getElementById('action-widget');
        if (actionWidget) {
            actionWidget.innerHTML = '';
            this.hasActionWidget = true;
        }
    }
};
</script>
