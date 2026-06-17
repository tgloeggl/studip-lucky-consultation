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
            <h1>Freigegebene Termine und Auslastung</h1>
            <studip-button icon="add" @click="addDate(true)">
                Neuer freigegebener Sprechstundentermin
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

             <table class="default" v-if="approvedDates.length">
                <colgroup>
                    <col width="1%">
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
                    <col v-if="!editMode" width="5%">
                    <col width="20%">
                </colgroup>
                <thead>
                    <tr>
                        <th></th>
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
                        <th v-if="!editMode">
                            Historie
                        </th>
                        <th>
                            Aktionen
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="date in approvedDates" :key="dateKey(date)">
                        <td>
                            <input
                                type="checkbox"
                                :value="dateKey(date)"
                                v-model="selectedApprovedDates"
                            >
                        </td>
                        <td v-if="editMode">
                            <a href="#" @click.prevent="removeDateFromList(dateKey(date))">
                                <studip-icon shape="trash"/>
                            </a>
                        </td>

                        <td>
                            <span v-if="editMode">
                                <therapist-search
                                    :class="{ invalid: dateValidation.description == date.id }"
                                    :value="date.attributes.description"
                                    :dateId="dateKey(date)"
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

                        <td v-if="!editMode">
                            <div data-tooltip class="tooltip" v-if="date.attributes.history">
                                <span class="tooltip-content" style="display: none">
                                    <template v-for="(entries, date) in date.attributes.history">
                                        Loszeitpunkt: <br/>
                                        {{ date }} <br/>
                                        Personen auf der Losliste: <br/>
                                        <template v-for="(entry) in entries">
                                            {{ entry }}
                                        </template>
                                        <hr>
                                    </template>
                                </span>

                                <studip-icon shape="list" role="clickable" :size="16"/>
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
                <tfoot>
                    <tr>
                        <td :colspan="dateTableColumnCount">
                            <studip-button
                                icon="edit"
                                :disabled="selectedApprovedDates.length === 0"
                                @click="moveSelectedDates(true)"
                            >
                                Auswahl in Entwurfsmodus verschieben
                            </studip-button>
                        </td>
                    </tr>
                </tfoot>
            </table>

            <MessageBox v-else type="info">
                Es sind noch keine freigegebenen Termine vorhanden.
            </MessageBox>
        </div>

        <div v-if="pools && pools.length">
            <h1>Entwürfe und nicht freigegebene Termine</h1>
            <studip-button icon="add" @click="addDate(false)">
                Neuer Sprechstundentermin
            </studip-button>

             <table class="default" v-if="preliminaryDates.length">
                <colgroup>
                    <col width="1%">
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
                    <col v-if="!editMode" width="5%">
                    <col width="20%">
                </colgroup>
                <thead>
                    <tr>
                        <th></th>
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
                        <th v-if="!editMode">
                            Historie
                        </th>
                        <th>
                            Aktionen
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="date in preliminaryDates" :key="dateKey(date)">
                        <td>
                            <input
                                type="checkbox"
                                :value="dateKey(date)"
                                v-model="selectedPreliminaryDates"
                            >
                        </td>
                        <td v-if="editMode">
                            <a href="#" @click.prevent="removeDateFromList(dateKey(date))">
                                <studip-icon shape="trash"/>
                            </a>
                        </td>

                        <td>
                            <span v-if="editMode">
                                <therapist-search
                                    :class="{ invalid: dateValidation.description == date.id }"
                                    :value="date.attributes.description"
                                    :dateId="dateKey(date)"
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

                        <td v-if="!editMode">
                            <div data-tooltip class="tooltip" v-if="date.attributes.history">
                                <span class="tooltip-content" style="display: none">
                                    <template v-for="(entries, date) in date.attributes.history">
                                        Loszeitpunkt: <br/>
                                        {{ date }} <br/>
                                        Personen auf der Losliste: <br/>
                                        <template v-for="(entry) in entries">
                                            {{ entry }}
                                        </template>
                                        <hr>
                                    </template>
                                </span>

                                <studip-icon shape="list" role="clickable" :size="16"/>
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
                <tfoot>
                    <tr>
                        <td :colspan="dateTableColumnCount">
                            <studip-button
                                icon="accept"
                                :disabled="selectedPreliminaryDates.length === 0"
                                @click="moveSelectedDates(false)"
                            >
                                Auswahl freigeben
                            </studip-button>
                        </td>
                    </tr>
                </tfoot>
            </table>

            <MessageBox v-else type="info">
                Es sind keine Entwürfe vorhanden.
            </MessageBox>
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
import MessageBox from '@/components/MessageBox';

export default {
    name: "SprechstundenEditor",

    components: {
        StudipButton,
        StudipIcon,
        StudipSelect,
        InfoField,
        TherapistSearch,
        MessageBox
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
            deleteDates: [],
            selectedApprovedDates: [],
            selectedPreliminaryDates: [],
            nextLocalDateId: 1
        }
    },

    computed: {
        ...mapGetters(['cid', 'pools', 'dates', 'search_users']),

        approvedDates() {
            return this.datelist.filter(date => this.isApproved(date));
        },

        preliminaryDates() {
            return this.datelist.filter(date => !this.isApproved(date));
        },

        dateTableColumnCount() {
            return this.editMode ? 11 : 13;
        }
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
                if (this.dateKey(this.datelist[id]) == date_id) {
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
                this.datelist = this.prepareDates(this.dates);
                this.deleteDates = [];
                this.selectedApprovedDates = [];
                this.selectedPreliminaryDates = [];
                this.editMode = false;
            });
        },

        deleteDate(date) {
            if (confirm('Sind sie sicher, dass sie den Zeiteintrag "' + date.attributes.description + '" löschen möchten?')) {
                this.$store.dispatch('deleteDate', date.id)
                    .then(() => {
                        this.datelist = this.prepareDates(this.dates);
                    });
            }
        },

        deleteUserFromDate(date) {
            if (confirm('Sind sie sicher, dass sie den/die Nutzer/in "' + date.attributes.fullname + '" aus dem Zeiteintrag "' + date.attributes.description + '" löschen möchten?')) {
                this.$store.dispatch('deleteUserFromDate', date.id)
                    .then(() => {
                        this.datelist = this.prepareDates(this.dates);
                    });
            }
        },

        addDate(approved)
        {
            let new_date = {
                attributes: {
                    description: '',
                    start      : null,
                    pool       : null,
                    approved   : approved ? 1 : 0
                }
            }

            // get last entry in array, if any
            if (this.datelist && this.datelist.length) {
                // get date to copy

                let latest = new Date("2000-01-01");
                let use_date = null;
                for (let id in this.datelist) {
                    let d1 = new Date(this.datelist[id].attributes.chdate);

                    if (d1 > latest) {
                        latest = d1;
                        use_date = JSON.stringify(this.datelist[id]);
                    }
                }

                new_date = JSON.parse(use_date);

                delete new_date.id
                delete new_date.attributes.id;
                delete new_date.attributes.start
                new_date.attributes.approved = approved ? 1 : 0;
            } else {
                this.datelist = [];
            }

            new_date._local_id = this.nextLocalDateId++;
            this.datelist.push(new_date);
            this.editMode = true;
        },

        cancelEdit()
        {
            this.datelist = this.prepareDates(this.dates);
            this.selectedApprovedDates = [];
            this.selectedPreliminaryDates = [];
            this.editMode = false;
        },

        removeDateFromList(date_id)
        {
            let found = -1;

            for (let i = 0; i < this.datelist.length; i++) {
                if (this.dateKey(this.datelist[i]) == date_id) {
                    found = i;
                }
            }

            if (found === -1) {
                return;
            }

            if (this.datelist[found].id) {
                this.deleteDates.push(this.datelist[found].id);
            }

            this.selectedApprovedDates = this.selectedApprovedDates.filter(id => id != this.dateKey(this.datelist[found]));
            this.selectedPreliminaryDates = this.selectedPreliminaryDates.filter(id => id != this.dateKey(this.datelist[found]));
            this.datelist.splice(found, 1);
        },

        getUserLink(username) {
            return STUDIP.URLHelper.getURL('dispatch.php/profile/index/?username=' + username, { cid: null });
        },

        isApproved(date) {
            return date.attributes.approved == 1;
        },

        dateKey(date) {
            return date.id || date._local_id;
        },

        moveSelectedDates(fromApproved) {
            const selected = fromApproved ? this.selectedApprovedDates : this.selectedPreliminaryDates;
            const selectedMap = {};

            for (let id of selected) {
                selectedMap[id] = true;
            }

            for (let date of this.datelist) {
                if (selectedMap[this.dateKey(date)]) {
                    date.attributes.approved = fromApproved ? 0 : 1;
                }
            }

            this.editMode = true;

            if (fromApproved) {
                this.selectedApprovedDates = [];
            } else {
                this.selectedPreliminaryDates = [];
            }
        },

        prepareDates(dates) {
            const datelist = JSON.parse(JSON.stringify(dates || []));

            for (let date of datelist) {
                if (date.attributes.approved === undefined || date.attributes.approved === null) {
                    date.attributes.approved = 0;
                }
            }

            return datelist;
        }
    },

    mounted() {
        this.$store.dispatch('loadCurrentUser');
        this.$store.dispatch('loadPools');
        this.$store.dispatch('loadDates')
            .then(() => {
                this.datelist = this.prepareDates(this.dates);
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
