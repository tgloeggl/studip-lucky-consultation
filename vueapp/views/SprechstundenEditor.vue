<template>
    <div>
        <InfoField></InfoField>

        <form class="default" @submit.prevent>
        <div>
            <h1>Lospools</h1>

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
                        <td>
                            <span>
                                <select
                                    v-model="currentPool.template"
                                >
                                    <option value="PP">PP</option>
                                    <option value="KJP">KJP</option>
                                </select>
                            </span>
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
            <MessageBox v-if="datesSaved" type="success" @hide="datesSaved = false">
                Termine wurden gespeichert.
            </MessageBox>

            <h1>Freigegebene Termine und Auslastung</h1>

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

            <sprechstunden-date-table
                :dates="approvedDates"
                :pools="pools"
                :edit-mode="editMode"
                :date-validation="dateValidation"
                move-icon="edit"
                move-label="Auswahl in Entwurfsmodus verschieben"
                empty-message="Es sind noch keine freigegebenen Termine vorhanden."
                @delete-date="deleteDate"
                @delete-selected="deleteSelectedDates"
                @delete-user-from-date="deleteUserFromDate"
                @edit-all="editMode = true"
                @move-selected="moveSelectedDates($event, true)"
                @remove-date="removeDateFromList"
                @set-date-description="setDateDescription"
                @store-dates="storeDates"
            />
        </div>

        <div v-if="pools && pools.length">
            <h1>Entwürfe und nicht freigegebene Termine</h1>

            <sprechstunden-date-table
                :dates="preliminaryDates"
                :pools="pools"
                :edit-mode="editMode"
                :date-validation="dateValidation"
                move-icon="accept"
                move-label="Auswahl freigeben"
                empty-message="Es sind keine Entwürfe vorhanden."
                @delete-date="deleteDate"
                @delete-selected="deleteSelectedDates"
                @delete-user-from-date="deleteUserFromDate"
                @edit-all="editMode = true"
                @move-selected="moveSelectedDates($event, false)"
                @remove-date="removeDateFromList"
                @set-date-description="setDateDescription"
                @store-dates="storeDates"
            />
        </div>
        </form>

        <MessageBox v-if="hasUnsavedChanges" type="warning">
            Es gibt ungespeicherte Änderungen.
        </MessageBox>

    </div>
</template>

<script>
import { mapGetters } from "vuex";

import StudipButton from '@/components/Studip/StudipButton';
import StudipIcon from '@/components/Studip/StudipIcon';
import StudipSelect from '@/components/Studip/StudipSelect';
import InfoField from '@/components/InfoField';
import MessageBox from '@/components/MessageBox';
import SprechstundenDateTable from '@/components/SprechstundenDateTable';

export default {
    name: "SprechstundenEditor",

    components: {
        StudipButton,
        StudipIcon,
        StudipSelect,
        InfoField,
        MessageBox,
        SprechstundenDateTable
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
            nextLocalDateId: 1,
            datesSaved: false
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

        hasUnsavedDateChanges() {
            if (this.deleteDates.length > 0) {
                return true;
            }

            return JSON.stringify(this.normalizeDates(this.datelist))
                !== JSON.stringify(this.normalizeDates(this.prepareDates(this.dates)));
        },

        hasUnsavedPoolChanges() {
            if (this.currentPool.id) {
                const pool = this.pools && this.pools.find(pool => pool.id == this.currentPool.id);

                if (!pool) {
                    return true;
                }

                return this.currentPool.name !== pool.attributes.name
                    || this.currentPool.date !== pool.attributes.date
                    || this.currentPool.template !== pool.attributes.template;
            }

            if (!this.addPool) {
                return false;
            }

            return this.currentPool.name.length > 0
                || this.currentPool.date !== null
                || this.currentPool.template.length > 0;
        },

        hasUnsavedChanges() {
            return this.hasUnsavedDateChanges || this.hasUnsavedPoolChanges;
        }
    },

    watch: {
        hasUnsavedChanges(hasUnsavedChanges) {
            if (hasUnsavedChanges) {
                this.datesSaved = false;
            }
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
                date: null,
                template: ''
            }

            this.addPool = false;
        },

        startAddPool() {
            this.cancelPoolEdit();
            this.addPool = true;
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
                this.editMode = false;
                this.datesSaved = true;
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

                if (use_date) {
                    new_date = JSON.parse(use_date);

                    delete new_date.id
                    delete new_date.attributes.id;
                    delete new_date.attributes.start
                    new_date.attributes.approved = approved ? 1 : 0;
                }
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
            this.deleteDates = [];
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

            this.datelist.splice(found, 1);
        },

        deleteSelectedDates(date_ids) {
            if (!date_ids.length) {
                return;
            }

            for (let date_id of date_ids) {
                this.removeDateFromList(date_id);
            }

            this.editMode = true;
        },

        isApproved(date) {
            return date.attributes.approved == 1;
        },

        dateKey(date) {
            return date.id || date._local_id;
        },

        moveSelectedDates(selected, fromApproved) {
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
        },

        prepareDates(dates) {
            const datelist = JSON.parse(JSON.stringify(dates || []));

            for (let date of datelist) {
                if (date.attributes.approved === undefined || date.attributes.approved === null) {
                    date.attributes.approved = 0;
                }
            }

            return datelist;
        },

        normalizeDates(dates) {
            return dates.map(date => {
                const normalized = JSON.parse(JSON.stringify(date));

                delete normalized._local_id;

                if (normalized.attributes) {
                    delete normalized.attributes.id;
                }

                return normalized;
            });
        },

        confirmDiscardUnsavedChanges() {
            return !this.hasUnsavedChanges
                || confirm('Es gibt ungespeicherte Änderungen. Möchten Sie die Seite wirklich verlassen?');
        },

        handleBeforeUnload(event) {
            if (!this.hasUnsavedChanges) {
                return;
            }

            event.preventDefault();
            event.returnValue = '';
        }
    },

    mounted() {
        this.$store.dispatch('loadCurrentUser');
        this.$store.dispatch('loadPools');
        this.$store.dispatch('loadDates')
            .then(() => {
                this.datelist = this.prepareDates(this.dates);
            });

        window.addEventListener('beforeunload', this.handleBeforeUnload);
    },

    beforeUnmount() {
        window.removeEventListener('beforeunload', this.handleBeforeUnload);
    },

    beforeRouteEnter (to, from) {
        if (!window.LuckyConsultationPlugin.PERMS) {
            return '/';
        }

        return true;
    },

    beforeRouteLeave() {
        return this.confirmDiscardUnsavedChanges();
    }

};
</script>
