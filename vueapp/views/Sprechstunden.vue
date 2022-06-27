<template>
    <div>
        <div>
            <h1>Lospools</h1>
            <studip-button icon="add" @click="cancelPoolEdit();addPool = true ">
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
                    <tr v-for="pool in pools" :key="pool.id">
                        <td>
                            <span v-if="currentPool.id == pool.id">
                                <input :class="{ 
                                        invalid: !validation.name
                                    }" 
                                    type="text" 
                                    placeholder="PP, KJP, ..." 
                                    v-model="currentPool.name"
                                >
                            </span>
    
                            <span v-else>
                                {{ pool.attributes.name }}
                            </span>
                        </td>
                        <td>
                            <span v-if="currentPool.id == pool.id">
                                <input :class="{ 
                                        invalid: !validation.date
                                    }"
                                    type="datetime-local" 
                                    v-model="currentPool.date"
                                >
                            </span>
    
                            <span v-else>
                                {{ pool.attributes.date | datetime }}
                            </span>
                        </td>
                        <td class="actions">
                            <span v-if="currentPool.id == pool.id">
                                <studip-button icon="accept" @click="storePool">
                                    Speichern
                                </studip-button>

                                <studip-button icon="cancel" @click="cancelPoolEdit">
                                    Abbrechen
                                </studip-button>
                            </span>
    
                            <span v-else>
                                <a href="#" @click.prevent="editPool(pool)">
                                    <studip-icon shape="edit"/> Bearbeiten
                                </a>

                                <a href="#" @click.prevent="deletePool(pool)">
                                    <studip-icon shape="trash"/> Löschen
                                </a>
                            </span>
                        </td>
                    </tr>

                    <tr v-if="addPool && !currentPool.id">
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
                        <td class="actions">
                            <studip-button icon="accept" @click="storePool">
                                Speichern
                            </studip-button>

                            <studip-button icon="cancel" @click="cancelPoolEdit">
                                Abbrechen
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
import StudipIcon from '@/components/Studip/StudipIcon';

export default {
    name: "Sprechstunden",

    components: {
        StudipButton,   StudipIcon
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
        storePool() {
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

            let promise;
            if (this.currentPool.id) {
                promise = this.$store.dispatch('editPool', this.currentPool);
            } else {
                promise = this.$store.dispatch('addPool', this.currentPool);
            }

            promise.then(() => {
                this.cancelPoolEdit();
            })
        },

        deletePool(pool) {
            if (confirm('Sind sie sicher, dass sie den Pool "' + pool.attributes.name + '" löschen möchten?')) {
                this.$store.dispatch('deletePool', pool.id);
            }
        },

        editPool(pool) {
            this.currentPool = {
                id  : pool.id, 
                name: pool.attributes.name,
                date: pool.attributes.date,
            }
        },

        cancelPoolEdit() {
            this.currentPool = {
                name: '',
                date: null
            }

            this.addPool = false;
        }
    },

    mounted() {
        this.$store.dispatch('loadCurrentUser');
        this.$store.dispatch('loadPools');
    }
};
</script>
