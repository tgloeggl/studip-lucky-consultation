<template>
    <div class="sidebar-widget " id="sidebar-navigation">
        <div class="sidebar-widget-header" v-translate>
            Navigation
        </div>
        <div class="sidebar-widget-content">
            <ul class="widget-list widget-links sidebar-navigation">
                <li :class="{
                    active: fragment == 'index'
                }">
                    <router-link :to="{ name: 'index' }">
                        Sprechstunden
                    </router-link>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
import StudipIcon from '@studip/StudipIcon.vue';

export default {
    name: 'NavigationWidget',
    components: {
        StudipIcon
    },
    data() {
        return {
            showAddDialog: false
        }
    },

    computed: {
        fragment() {
            return this.$route.name;
        }
    }
}
</script>
