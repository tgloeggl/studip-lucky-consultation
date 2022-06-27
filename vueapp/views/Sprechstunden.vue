<template>
    <div>
        <h1>Hello World!</h1>
    </div>
</template>

<script>
import { mapGetters } from "vuex";

import NavigationWidget from '../components/NavigationWidget';

export default {
    name: "Sprechstunden",

    components: {
        NavigationWidget
    },

    methods: {
        uploadVideo() {
            this.uploadDialog = true;
        }
    },

    mounted() {
        this.$store.dispatch('loadCurrentUser');
    }
};
</script>
