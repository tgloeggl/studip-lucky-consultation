<template>
    <div>
        <div>
            <h1>Lospools</h1>
            <studip-button icon="add">
                Neuer Lospool
            </studip-button>
        </div>

        <div v-if="pools">
            <h1>Vorhandene Termine und Auslastung</h1>
            <studip-button icon="add">
                Neuer Sprechstundentermin
            </studip-button>
        </div>
    </div>
</template>

<script>
import { mapGetters } from "vuex";

import StudipButton from '@/components/Studip/StudipButton';

export default {
    name: "Sprechstunden",

    components: {
        StudipButton
    },

    computed: {
        ...mapGetters(['cid', 'pools', 'dates'])
    },

    methods: {

    },

    mounted() {
        this.$store.dispatch('loadCurrentUser');
    }
};
</script>
