<template>
    <div class="sidebar-widget-content">
        <ul class="widget-list widget-links sidebar-navigation">
            <li :class="{
                    active: fragment == 'index'
                }"
                v-if="!hasPerms"
            >
                <router-link :to="{ name: 'index' }">
                    Sprechstunden
                </router-link>
            </li>

            <li :class="{
                    active: fragment == 'editor'
                }"
                v-if="hasPerms"
            >
                <router-link :to="{ name: 'editor' }">
                    Sprechstunden
                </router-link>
            </li>

            <li :class="{
                    active: fragment == 'templates'
                }"
                v-if="hasPerms"
            >
                <router-link :to="{ name: 'templates' }">
                    Globale Vorlagen
                </router-link>
            </li>
        </ul>
    </div>
</template>

<script>
import StudipIcon from '@studip/StudipIcon.vue';

import { mapGetters } from "vuex";

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
        ...mapGetters(['cid', 'currentUser']),

        fragment() {
            return this.$route.name;
        },

        hasPerms()
        {
            if (!this.currentUser) {
                return false;
            }

            return (this.currentUser.status == 'root' || this.currentUser.admin == true);
        }
    },

    beforeMount()
    {
        this.$store.dispatch('loadCurrentUser');
    }
}
</script>
