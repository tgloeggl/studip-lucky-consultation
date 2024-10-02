<template>
    <div>
        <form class="default" @submit.prevent>
            <fieldset>
                <legend>
                    Globale Mailvorlagen (gelten für ALLE Kurse!)
                </legend>

                <table class="default lc--templates--preview">
                    <colgroup>
                        <col width="50%">
                        <col width="50%">
                    </colgroup>
                    <tbody>
                        <tr>
                            <td>
                                <label>
                                    Vorlage PP
                                    <textarea rows="15" v-model="templates.PP.template"></textarea>
                                </label>
                            </td>
                            <td>
                                <label>
                                    Vorschau
                                    <p v-html="previewPP"></p>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label>
                                    Vorlage KJP
                                    <textarea rows="15" v-model="templates.KJP.template"></textarea>
                                </label>
                            </td>
                            <td>
                                <label>
                                    Vorschau
                                    <p v-html="previewKJP"></p>
                                </label>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>

            <footer>
                <studip-button icon="accept" @click="storeTemplates">
                    Speichern
                </studip-button>
            </footer>
        </form>

        <br>

        <table class="default">
            <caption>
                Verfügbare Variablen
            </caption>
            <thead>
                <tr>
                    <th>
                        Variable
                    </th>
                    <th>
                        Funktion
                    </th>
                </tr>
            </thead>

            <tbody>
                <tr
                    v-for="variable in variables"
                    v-bind:key="variable.name"
                >
                    <td>
                        {{ variable.name }}
                    </td>
                    <td>
                        {{ variable.description }}
                    </td>
                </tr>
            </tbody>
        </table>

    </div>
</template>

<script>
import { mapGetters } from "vuex";

import StudipButton from '@/components/Studip/StudipButton';
import StudipIcon from '@/components/Studip/StudipIcon';

export default {
    name: "SprechstundenTemplates",

    components: {
        StudipButton,   StudipIcon
    },

    data()
    {
        return {
            variables: [
                {
                    name: '##fullname##',
                    description: 'Kompletter Name des Studierenden inklusive Titel'
                },
                {
                    name: '##therapist##',
                    description: 'Beschreibung des Termins, üblicherweise ist das der Therapeut/innen-Name'
                },
                {
                    name: '##date##',
                    description: 'Datum (ohne Uhrzeit) des Therapiestarts'
                },
                {
                    name: '##time##',
                    description: 'Uhrzeit des Therapiestarts'
                },
                {
                    name: '##weekday##',
                    description: 'Werktag des Therapiestarts'
                },
                {
                    name: '##fs_start##',
                    description: 'Erstes Datum des Fallseminars'
                },
                {
                    name: '##fs_slot##',
                    description: 'Zeitfenster des Fallseminars'
                },
                {
                    name: '##fs_room##',
                    description: 'Raum des Fallseminars'
                },
                {
                    name: '##ko_date##',
                    description: 'Datum mit Uhrzeit des Kick-Off-Termins'
                },
                {
                    name: '##ko_room##',
                    description: 'Raum des Kick-Off-Termins'
                }
            ]
        };
    },

    computed: {
        ...mapGetters(['cid', 'templates']),

        previewPP() {
            if (!this.templates.PP.template) {
                return '';
            }

            return this.replaceTemplateWithSamples(this.templates.PP.template);
        },

        previewKJP() {
            if (!this.templates.PP.template) {
                return '';
            }

            return this.replaceTemplateWithSamples(this.templates.KJP.template);
        }
    },

    methods: {
        storeTemplates() {
            this.$store.dispatch('storeTemplates', this.templates);
        },

        replaceTemplateWithSamples(template)
        {
            let data = {
                '##fullname##' : 'Maxime Muster',
                '##therapist##': 'Dr. Gerome Müller',
                '##date##'     : '25.08.2025',
                '##time##'     : '10:00',
                '##weekday##'  : 'Montag',
                '##fs_start##' : '16.08.2024 und 23.08.2024',
                '##fs_slot##'  : '08.30 – 10.00',
                '##fs_room##'  : '139',
                '##ko_date##'  : 'Dienstag, den 06.08.2024 um 09:00',
                '##ko_room##'  : '179',
            }

            for (let tag in data) {
                template = template.replace(tag, '<b>' + data[tag] + '</b>');
            }

            template = template.replace(/(?:\r\n|\r|\n)/g, '<br>');

            return template;
        }
    },

    mounted() {
        this.$store.dispatch('loadCurrentUser');
        this.$store.dispatch('loadTemplates');
    },

    beforeRouteEnter (to, from) {
        if (!window.LuckyConsultationPlugin.PERMS) {
            return '/';
        }

        return true;
    }

};
</script>
