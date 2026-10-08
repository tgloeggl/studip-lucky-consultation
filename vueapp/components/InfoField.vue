<template>
    <div v-if="infotext !== null">
        <span  v-if="edit" class="infotext">
            <studip-wysiwyg
                name="info" class="wysiwyg"
                :value="local_infotext"
                @input="updateInfotext"
            ></studip-wysiwyg>
        </span>

        <span v-if="!edit && infotext" class="infotext" v-html="infotext">
        </span>

        <studip-button v-if="!edit" icon="edit" @click.prevent="startEditing">
            Infotext bearbeiten
        </studip-button>

        <studip-button v-if="edit" icon="edit" @click.prevent="saveInfotext">
            Speichern
        </studip-button>

        <studip-button v-if="edit" icon="edit" @click.prevent="cancelEditing">
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
        startEditing() {
            this.local_infotext = this.infotext || '';
            this.edit = true;
        },

        cancelEditing() {
            this.edit = false;
            this.local_infotext = '';
        },

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
