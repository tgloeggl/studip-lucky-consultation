<template>
    <div>
        <div>
            <h1>Lospools</h1>
            <studip-button icon="add" @click="addPool = true">
                Neuer Lospool
            </studip-button>

            <table class="default" v-if="addPool || pools">
                <thead>
                    <tr>
                        <th>
                            Name des Pools
                        </th>
                        <th>
                            Losen am:
                        </th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="addPool">
                        <td>
                            <input :class="{ 
                                    invalid: !validation.name
                                }" 
                                type="text" 
                                placeholder="PP, KJP, ..." 
                                v-model="currentPool.name"
                            >
                        </td>
                        <td>
                            <input :class="{ 
                                    invalid: !validation.date
                                }"
                                type="datetime-local" 
                                v-model="currentPool.date"
                            >
                        </td>
                        <td>
                            <studip-button icon="accept" @click="storeNewPool">
                                Speichern
                            </studip-button>
                        </td>
                    </tr>
                </tbody>
            </table>
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

    data() {
        return {
            addPool: false,
            currentPool: {
                name: '',
                date: null
            },
            validation: {
                name: true,
                date: true
            }
        }
    },

    computed: {
        ...mapGetters(['cid', 'pools', 'dates'])
    },

    methods: {
        storeNewPool() {
            this.validation = {
                name: true,
                date: true
            }

            let validated = true;
            
            if (this.currentPool.name.length == 0) {
                this.validation.name = false;
                validated = false;
            }
            
            if (this.currentPool.date == null) {
                this.validation.date = false;
                validated = false;
            }

            if (!validated) {
                return;
            }

            this.$store.dispatch('addPool', this.currentPool);
        }
    },

    mounted() {
        this.$store.dispatch('loadCurrentUser');
    }
};
</script>
