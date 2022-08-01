<template>
    <div>
        <span class="infotext" v-html="infotext"></span>

        <form class="default" @submit.prevent>
        <div v-if="pools && pools.length && mydates && mydates.length">
            <h1>Meine Termine</h1>

             <table class="default">
                <colgroup>
                    <col width="40%">
                    <col width="40%">
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
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="mydate in mydates" :key="mydate.id">
                        <td>
                            {{ mydate.attributes.description }}
                        </td>

                        <td>
                            {{ $filters.datetime(mydate.attributes.start) }}
                        </td>

                        <td>
                            {{ getPool(mydate.attributes.pool).attributes.name }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-if="pools && pools.length && dates && dates.length">
            <h1>Vorhandene Lostermine</h1>

            <table class="default">
                <colgroup>
                    <col width="5%">
                    <col width="25%">
                    <col width="20%">
                    <col width="10%">
                    <col width="20%">
                    <col width="20%">
                </colgroup>
                <thead>
                    <tr>
                        <th></th>
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
                            Losdatum
                        </th>
                        <th>
                            Aktionen
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="date in dates" :key="date.id">
                        <td>
                            <studip-icon
                                v-if="onWaitingList(date)"
                                shape="date" role="accept"
                                title="Sie sind auf der Losliste für diesen Termin"
                            />
                        </td>
                        <td>
                            {{ date.attributes.description }}
                        </td>

                        <td>
                            {{ $filters.datetime(date.attributes.start) }}
                        </td>

                        <td>
                            {{ getPool(date.attributes.pool).attributes.name }}
                        </td>

                        <td>
                            {{ $filters.datetime(getPool(date.attributes.pool).attributes.date) }}
                        </td>

                        <td class="actions">
                            <span v-if="onWaitingList(date)">
                                <a href="#" @click.prevent="removeFromWaitingList(date)">
                                    <studip-icon shape="remove"/> Eintragung entfernen
                                </a>
                            </span>

                            <span v-else>
                                <a href="#" @click.prevent="addToWaitingList(date)">
                                    <studip-icon shape="add"/> Auf Losliste eintragen
                                </a>
                            </span>



                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        </form>

        <div v-if="(pools && !pools.length) || (dates && !dates.length)">
            <MessageBox type="info">
                 Es sind momentan keine Termine vorhanden.
            </MessageBox>
        </div>
    </div>
</template>

<script>
import { mapGetters } from "vuex";

import StudipButton from '@/components/Studip/StudipButton';
import StudipIcon from '@/components/Studip/StudipIcon';
import MessageBox from '@/components/MessageBox';


export default {
    name: "Sprechstunden",

    components: {
        StudipButton,   StudipIcon,     MessageBox
    },

    computed: {
        ...mapGetters(['cid', 'pools', 'dates', 'waitinglist', 'mydates', 'infotext']),

        waitinglist_ids() {
            let list = {};

            for (let id in this.waitinglist) {
                list[this.waitinglist[id].attributes.dates_id] = this.waitinglist[id].attributes;
            }

            return list;
        }
    },

    methods: {
        getPool(id) {
            for (let pool_id in this.pools) {
                if (this.pools[pool_id].id == id) {
                    return this.pools[pool_id];
                }
            }

            return '-';
        },

        onWaitingList(date) {
            return (this.waitinglist_ids[date.id] !== undefined) ? true : false;
        },

        addToWaitingList(date) {
            this.$store.dispatch('addToWaitingList', date.id);
        },

        removeFromWaitingList(date) {
            if (confirm('Sind sie sicher, dass sie ihren Eintrag von der Losliste für diesen Termin entfernen möchten?')) {
                this.$store.dispatch('removeFromWaitingList', date.id);
            }
        }
    },

    mounted() {
        this.$store.dispatch('loadCurrentUser');
        this.$store.dispatch('loadPools');
        this.$store.dispatch('loadDates');
        this.$store.dispatch('loadMyDates');
        this.$store.dispatch('loadWaitingList');
        this.$store.dispatch('loadInfotext');
    }
};
</script>
