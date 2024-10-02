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
                            Mailvorlage
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
                                <span v-if="pool.attributes.lots_drawn == 1">
                                    (wurde gelost)
                                </span>
                            </span>
                        </td>
                        <td>
                            <span v-if="currentPool.id == pool.id">
                                <select
                                    v-model="currentPool.template"
                                >
                                    <option value="PP">PP</option>
                                    <option value="KJP">KJP</option>
                                </select>
                            </span>

                            <span v-else>
                                {{  pool.attributes.template }}
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
            <studip-button icon="add" @click="addDate()">
                Neuer Sprechstundentermin
            </studip-button>

            <span v-if="editMode">
                <studip-button icon="accept" @click.stop="storeDates">
                    Alles speichern
                </studip-button>

                <studip-button icon="cancel" @click.stop="cancelEdit">
                    Abbrechen
                </studip-button>
            </span>

            <span v-else>
                <studip-button icon="edit" @click.stop="editMode = true">
                    Alles bearbeiten
                </studip-button>
            </span>

             <table class="default" v-if="(datelist && datelist.length) ">
                <colgroup>
                    <col v-if="editMode" width="1%">
                    <col width="25%">
                    <col width="15%">
                    <col width="5%">
                    <col width="8%">
                    <col width="8%">
                    <col width="8%">
                    <col width="9%">
                    <col width="8%">
                    <col v-if="!editMode" width="20%">
                    <col v-if="!editMode" width="5%">
                    <col width="20%">
                </colgroup>
                <thead>
                    <tr>
                        <th v-if="editMode"></th>
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
                            <abbr title="Fallseminar Startdaum">
                                FS Start
                            </abbr>
                        </th>
                        <th>
                            <abbr title="Fallseminar Zeitfenster / Uhrzeit">
                                FS Slot
                            </abbr>
                        </th>
                        <th>
                            <abbr title="Fallseminar Zeitfenster">
                                FS Raum
                            </abbr>
                        </th>
                        <th>
                            <abbr title="Kick-Off-Sitzung Datum und Uhrzeit">
                                KO Datum
                            </abbr>
                        </th>
                        <th>
                            <abbr title="Kick-Off-Sitzung Raum">
                                KO Raum
                            </abbr>
                        </th>
                        <th v-if="!editMode">
                            Zugeordnete Person
                        </th>
                        <th v-if="!editMode">
                            Losliste
                        </th>
                        <th>
                            Aktionen
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="date in datelist" :key="date.id">
                        <td v-if="editMode">
                            <a href="#" @click.prevent="removeDateFromList(date.id)">
                                <studip-icon shape="trash"/>
                            </a>
                        </td>

                        <td>
                            <span v-if="editMode">
                                <therapist-search
                                    :class="{ invalid: dateValidation.description == date.id }"
                                    :value="date.id ? date.attributes.description : ''"
                                    :dateId="date.id"
                                    @date-input="setDateDescription"
                                    :placeholder="$gettext('Therapeut/in')">
                                </therapist-search>
                            </span>

                            <span v-else>
                                <studip-icon v-if="date.attributes.therapist_id" shape="person"></studip-icon>
                                {{ date.attributes.description }}
                            </span>
                        </td>

                        <td>
                            <span v-if="editMode">
                                <input :class="{
                                        invalid: dateValidation.start == date.id
                                    }"
                                    type="datetime-local"
                                    v-model="date.attributes.start"
                                >
                            </span>

                            <span v-else>
                                {{ $filters.datetime(date.attributes.start) }}
                            </span>
                        </td>

                         <td>
                            <span v-if="editMode">
                                <select
                                    :class="{
                                        invalid: dateValidation.pool == date.id
                                    }"
                                    v-model="date.attributes.pool"
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
                            <template v-if="!editMode">
                                {{ date.attributes.fs_start }}
                            </template>

                            <input v-else type="text"
                                v-model="date.attributes.fs_start"
                            >
                        </td>

                        <td>
                            <template v-if="!editMode">
                                {{ date.attributes.fs_slot }}
                            </template>

                            <input v-else type="text"
                                v-model="date.attributes.fs_slot"
                            >
                        </td>

                        <td>
                            <template v-if="!editMode">
                                {{ date.attributes.fs_room }}
                            </template>

                            <input v-else type="text"
                                v-model="date.attributes.fs_room"
                            >
                        </td>

                        <td>
                            <template v-if="!editMode">
                                {{ date.attributes.ko_date }}
                            </template>

                            <input v-else type="text"
                                v-model="date.attributes.ko_date"
                            >
                        </td>

                        <td>
                            <template v-if="!editMode">
                                {{ date.attributes.ko_room }}
                            </template>

                            <input v-else type="text"
                                v-model="date.attributes.ko_room"
                            >
                        </td>

                        <td v-if="!editMode">
                            <a target="_blank" :href="getUserLink(date.attributes.username)">
                                {{ date.attributes.fullname }}
                            </a>
                            <a v-if="date.attributes.username" href="#" @click.prevent="deleteUserFromDate(date)">
                                <studip-icon shape="trash"/>
                            </a>
                        </td>

                        <td v-if="!editMode">
                            {{ date.attributes.waiting }}

                            <div data-tooltip class="tooltip" v-if="date.attributes.waiting">
                                <span class="tooltip-content" style="display: none">
                                    <template v-for="user in date.attributes.waitinglist">
                                        {{ user.fullname }} <br/>
                                    </template>
                                </span>

                                <studip-icon shape="info" role="clickable" :size="16"/>
                            </div>
                        </td>

                        <td class="actions">
                            <span v-if="editMode">
                                <studip-button icon="accept" @click="storeDates">
                                    Alles speichern
                                </studip-button>
                            </span>

                            <span v-else>
                                <a href="#" @click.prevent="editMode = true">
                                    <studip-icon shape="edit"/> Alles bearbeiten
                                </a>

                                <a href="#" @click.prevent="deleteDate(date)">
                                    <studip-icon shape="trash"/> Löschen
                                </a>
                            </span>
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
import StudipSelect from '@/components/Studip/StudipSelect';
import InfoField from '@/components/InfoField';
import TherapistSearch from '@/components/TherapistSearch'

export default {
    name: "SprechstundenEditor",

    components: {
        StudipButton,
        StudipIcon,
        StudipSelect,
        InfoField,
        TherapistSearch
    },

    data() {
        return {
            addPool: false,
            currentPool: {
                name: '',
                date: null,
                template: ''
            },
            poolValidation: {
                name: true,
                date: true
            },

            currentDate: {
                description: '',
                start      : null,
                pool       : null
            },
            dateValidation: {
                description: true,
                start      : true,
                pool       : true
            },
            editMode: false,
            datelist: [],
            deleteDates: []
        }
    },

    computed: {
        ...mapGetters(['cid', 'pools', 'dates', 'search_users']),
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
                template: pool.attributes.template
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

        setDateDescription(returnValue, date_id) {
            for (let id in this.datelist) {
                if (this.datelist[id].id == date_id) {
                    this.datelist[id].attributes.description  = returnValue.name;
                    this.datelist[id].attributes.therapist_id = returnValue.value;
                    return;
                }
            }
        },

        storeDates()
        {
            let validated = true;

            for (let date of this.datelist) {

                if (date.attributes.description.length == 0) {
                    this.dateValidation.description = date.id;
                    validated = false;
                }

                if (date.attributes.start == null) {
                    this.dateValidation.start = date.id;
                    validated = false;
                }

                if (!date.attributes.pool) {
                    this.dateValidation.pool = date.id;
                    validated = false;
                }

                if (!validated) {
                    return;
                }
            }

            this.$store.dispatch('editDates', {
                'dates': this.datelist,
                'delete': this.deleteDates
            }).then(() => {
                this.datelist = JSON.parse(JSON.stringify(this.dates));
                this.deleteDates = [];
                this.editMode = false;
            });
        },

        deleteDate(date) {
            if (confirm('Sind sie sicher, dass sie den Zeiteintrag "' + date.attributes.description + '" löschen möchten?')) {
                this.$store.dispatch('deleteDate', date.id)
                    .then(() => {
                        this.datelist = JSON.parse(JSON.stringify(this.dates));
                    });
            }
        },

        deleteUserFromDate(date) {
            if (confirm('Sind sie sicher, dass sie den/die Nutzer/in "' + date.attributes.fullname + '" aus dem Zeiteintrag "' + date.attributes.description + '" löschen möchten?')) {
                this.$store.dispatch('deleteUserFromDate', date.id);
            }
        },

        addDate()
        {
            let new_date = {
                attributes: {
                    description: '',
                    start      : null,
                    pool       : null
                }
            }
            // get last entry in array, if any
            if (this.datelist && this.datelist.length) {
                new_date = JSON.parse(JSON.stringify(
                    this.datelist[this.datelist.length - 1]
                ));
                delete new_date.id
                delete new_date.attributes.id;
                delete new_date.attributes.start
            } else {
                this.datelist = [];
            }

            this.datelist.push(new_date);
            this.editMode = true;
        },

        cancelEdit()
        {
            this.datelist = JSON.parse(JSON.stringify(this.dates));
            this.editMode = false;
        },

        removeDateFromList(date_id)
        {
            let found = -1;

            for (let i = 0; i < this.datelist.length; i++) {
                if (this.datelist[i].id == date_id) {
                    found = i;
                }
            }

            if (this.datelist[found].id) {
                this.deleteDates.push(this.datelist[found].id);
            }

            if (found > -1) {
                this.datelist.splice(found, 1);
            }
        },

        getUserLink(username) {
            return STUDIP.URLHelper.getURL('dispatch.php/profile/index/?username=' + username, { cid: null });
        }
    },

    mounted() {
        this.$store.dispatch('loadCurrentUser');
        this.$store.dispatch('loadPools');
        this.$store.dispatch('loadDates')
            .then(() => {
                this.datelist = JSON.parse(JSON.stringify(this.dates));
            });
    },

    beforeRouteEnter (to, from) {
        if (!window.LuckyConsultationPlugin.PERMS) {
            return '/';
        }

        return true;
    }

};
</script>
