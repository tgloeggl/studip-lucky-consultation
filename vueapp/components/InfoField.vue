<template>
    <div v-if="infotext !== null">
        <span  v-if="edit" class="infotext">
            <studip-wysiwyg
                name="info" class="wysiwyg"
                :value="infotext"
                @input="updateInfotext"
            ></studip-wysiwyg>
        </span>

        <span v-if="!edit && infotext" class="infotext" v-html="infotext">
        </span>

        <studip-button v-if="!edit" icon="edit" @click.prevent="edit = true">
            Infotext bearbeiten
        </studip-button>

        <studip-button v-if="edit" icon="edit" @click.prevent="saveInfotext">
            Speichern
        </studip-button>

        <studip-button v-if="edit" icon="edit" @click.prevent="edit = false">
            Abbrechen
        </studip-button>
    </div>
</template>

<script>
import StudipWysiwyg from '@/components/Studip/StudipWysiwyg';
import StudipButton from './Studip/StudipButton.vue';
import { mapGetters } from 'vuex';

export default {
    name: "InfoField",

    components: {
        StudipWysiwyg,
        StudipButton
    },

    data() {
        return {
            edit: false,
            local_infotext: ''
        }
    },

    computed: {
        ...mapGetters(['infotext'])
    },

    methods: {
        updateInfotext(new_text) {
            this.local_infotext = new_text;
        },

        saveInfotext() {
            this.edit = false;
            this.$store.dispatch('updateInfotext', this.local_infotext)
            this.local_infotext = '';
        }
    },

    mounted() {
        this.$store.dispatch('loadInfotext');
    }
}
</script>
