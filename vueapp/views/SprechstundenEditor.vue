<template>
    <div>
        <InfoField></InfoField>

        <form class="default" @submit.prevent>
        <div>
            <h1>Lospools</h1>
            <studip-button icon="add" @click="cancelPoolEdit();addPool = true">
                Neuer Lospool
            </studip-button>

            <table class="default" v-if="addPool || (pools && pools.length) ">
                <colgroup>
                    <col width="25%">
                    <col width="55%">
                    <col width="20%">
                </colgroup>
                <thead>
                    <tr>
                        <th>
                            Name des Pools
                        </th>
                        <th>
                            Losen am
                        </th>
                        <th>
                            Aktionen
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="pool in pools" :key="pool.id">
                        <td>
                            <span v-if="currentPool.id == pool.id">
                                <input :class="{
                                        invalid: !poolValidation.name
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
                                        invalid: !poolValidation.date
                                    }"
                                    type="datetime-local"
                                    v-model="currentPool.date"
                                >
                            </span>

                            <span v-else>
                                {{ $filters.datetime(pool.attributes.date) }}
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
                                    invalid: !poolValidation.name
                                }"
                                type="text"
                                placeholder="PP, KJP, ..."
                                v-model="currentPool.name"
                            >
                        </td>
                        <td>
                            <input :class="{
                                    invalid: !poolValidation.date
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

        <div v-if="pools && pools.length">
            <h1>Vorhandene Termine und Auslastung</h1>
            <studip-button icon="add" @click="cancelDateEdit();addDate = true">
                Neuer Sprechstundentermin
            </studip-button>

             <table class="default" v-if="addDate || (dates && dates.length) ">
                <colgroup>
                    <col width="25%">
                    <col width="25%">
                    <col width="5%">
                    <col width="20%">
                    <col width="5%">
                    <col width="20%">
                </colgroup>
                <thead>
                    <tr>
                        <th>
                            Beschreibung
                        </th>
                        <th>
                            Zeitpunkt
                        </th>
                        <th>
                            Lospool
                        </th>
                        <th>
                            Zugeordnete Person
                        </th>
                        <th>
                            Losliste
                        </th>
                        <th>
                            Aktionen
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="date in dates" :key="date.id">
                        <td>
                            <span v-if="currentDate.id == date.id">
                                <input :class="{
                                        invalid: !dateValidation.description
                                    }"
                                    type="text"
                                    placeholder="Therapeut/in"
                                    v-model="currentDate.description"
                                >
                            </span>

                            <span v-else>
                                {{ date.attributes.description }}
                            </span>
                        </td>

                        <td>
                            <span v-if="currentDate.id == date.id">
                                <input :class="{
                                        invalid: !dateValidation.start
                                    }"
                                    type="datetime-local"
                                    v-model="currentDate.start"
                                >
                            </span>

                            <span v-else>
                                {{ $filters.datetime(date.attributes.start) }}
                            </span>
                        </td>

                         <td>
                            <span v-if="currentDate.id == date.id">
                                <select
                                    :class="{
                                        invalid: !dateValidation.pool
                                    }"
                                    v-model="currentDate.pool"
                                >
                                    <option v-for="pool in pools" :key="pool.id" :value="pool.id">
                                        {{ pool.attributes.name }}
                                    </option>
                                </select>
                            </span>

                            <span v-else>
                                {{ getPoolName(date.attributes.pool) }}
                            </span>
                        </td>

                        <td>
                            <a target="_blank" :href="getUserLink(date.attributes.username)">
                                {{ date.attributes.fullname }}
                            </a>
                        </td>

                        <td>
                            {{ date.attributes.waiting }}
                        </td>

                        <td class="actions">
                            <span v-if="currentDate.id == date.id">
                                <studip-button icon="accept" @click="storeDate">
                                    Speichern
                                </studip-button>

                                <studip-button icon="cancel" @click="cancelDateEdit">
                                    Abbrechen
                                </studip-button>
                            </span>

                            <span v-else>
                                <a href="#" @click.prevent="editDate(date)">
                                    <studip-icon shape="edit"/> Bearbeiten
                                </a>

                                <a href="#" @click.prevent="deleteDate(date)">
                                    <studip-icon shape="trash"/> Löschen
                                </a>
                            </span>
                        </td>
                    </tr>

                    <tr v-if="addDate && !currentDate.id">
                        <td>
                            <input :class="{
                                    invalid: !dateValidation.description
                                }"
                                type="text"
                                placeholder="Therapeut/in"
                                v-model="currentDate.description"
                            >
                        </td>
                        <td>
                            <input :class="{
                                    invalid: !dateValidation.start
                                }"
                                type="datetime-local"
                                v-model="currentDate.start"
                            >
                        </td>

                        <td>
                            <select
                                :class="{
                                    invalid: !dateValidation.pool
                                }"
                                v-model="currentDate.pool"
                            >
                                <option v-for="pool in pools" :key="pool.id" :value="pool.id">
                                    {{ pool.attributes.name }}
                                </option>
                            </select>
                        </td>

                        <td class="actions" colspan="3">
                            <studip-button icon="accept" @click="storeDate">
                                Speichern
                            </studip-button>

                            <studip-button icon="cancel" @click="cancelDateEdit">
                                Abbrechen
                            </studip-button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        </form>
    </div>
</template>

<script>
import { mapGetters } from "vuex";

import StudipButton from '@/components/Studip/StudipButton';
import StudipIcon from '@/components/Studip/StudipIcon';
import InfoField from '@/components/InfoField';

export default {
    name: "Sprechstunden",

    components: {
        StudipButton,   StudipIcon,     InfoField
    },

    data() {
        return {
            addPool: false,
            currentPool: {
                name: '',
                date: null
            },
            poolValidation: {
                name: true,
                date: true
            },

            addDate: false,
            currentDate: {
                description: '',
                start      : null,
                pool       : null
            },
            dateValidation: {
                description: true,
                start      : true,
                pool       : true
            }
        }
    },

    computed: {
        ...mapGetters(['cid', 'pools', 'dates'])
    },

    methods: {
        storePool() {
            this.poolValidation = {
                name: true,
                date: true
            }

            let validated = true;

            if (this.currentPool.name.length == 0) {
                this.poolValidation.name = false;
                validated = false;
            }

            if (this.currentPool.date == null) {
                this.poolValidation.date = false;
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
        },

        getPoolName(id) {
            for (let pool_id in this.pools) {
                if (this.pools[pool_id].id == id) {
                    return this.pools[pool_id].attributes.name;
                }
            }

            return '-';
        },

        storeDate() {
            this.dateValidation = {
                description: true,
                start      : true,
                pool       : true
            }

            let validated = true;

            if (this.currentDate.description.length == 0) {
                this.dateValidation.description = false;
                validated = false;
            }

            if (this.currentDate.start == null) {
                this.dateValidation.start = false;
                validated = false;
            }

            if (!this.currentDate.pool) {
                this.dateValidation.pool = false;
                validated = false;
            }

            if (!validated) {
                return;
            }

            let promise;

            if (this.currentDate.id) {
                promise = this.$store.dispatch('editDate', this.currentDate);
            } else {
                promise = this.$store.dispatch('addDate', this.currentDate);
            }

            promise.then(() => {
                this.cancelDateEdit();
            })
        },

        deleteDate(date) {
            if (confirm('Sind sie sicher, dass sie den Zeiteintrag "' + date.attributes.description + '" löschen möchten?')) {
                this.$store.dispatch('deleteDate', date.id);
            }
        },

        editDate(date) {
            this.currentDate = {
                id         : date.id,
                description: date.attributes.description,
                start      : date.attributes.start,
                pool       : date.attributes.pool,
            }
        },

        cancelDateEdit() {
            this.currentDate = {
                description: '',
                start      : null,
                pool       : null
            }

            this.addDate = false;
        },

        getUserLink(username) {
            console.log(window.STUDIP);
            return STUDIP.URLHelper.getURL('dispatch.php/profile/index/?username=' + username, { cid: null });
        }
    },

    mounted() {
        this.$store.dispatch('loadCurrentUser');
        this.$store.dispatch('loadPools');
        this.$store.dispatch('loadDates');
    }
};
</script>
