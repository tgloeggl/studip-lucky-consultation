<template>
    <div>
        <table class="default" v-if="dates.length">
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
                <tr v-for="date in dates" :key="dateKey(date)">
                    <td>
                        <input
                            type="checkbox"
                            :value="dateKey(date)"
                            v-model="selectedDates"
                        >
                    </td>
                    <td v-if="editMode">
                        <a href="#" @click.prevent="$emit('remove-date', dateKey(date))">
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
                        <a v-if="date.attributes.username" href="#" @click.prevent="$emit('delete-user-from-date', date)">
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
                        <span v-if="!editMode">
                            <a href="#" @click.prevent="$emit('edit-all')">
                                <studip-icon shape="edit"/> Alles bearbeiten
                            </a>

                            <a href="#" @click.prevent="$emit('delete-date', date)">
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
                            :icon="moveIcon"
                            :disabled="selectedDates.length === 0"
                            @click="moveSelectedDates"
                        >
                            {{ moveLabel }}
                        </studip-button>
                    </td>
                </tr>
            </tfoot>
        </table>

        <MessageBox v-else type="info">
            {{ emptyMessage }}
        </MessageBox>
    </div>
</template>

<script>
import StudipButton from '@/components/Studip/StudipButton';
import StudipIcon from '@/components/Studip/StudipIcon';
import MessageBox from '@/components/MessageBox';
import TherapistSearch from '@/components/TherapistSearch';

export default {
    name: "SprechstundenDateTable",

    components: {
        StudipButton,
        StudipIcon,
        MessageBox,
        TherapistSearch
    },

    props: {
        dates: {
            type: Array,
            required: true
        },
        pools: {
            type: Array,
            required: true
        },
        editMode: {
            type: Boolean,
            required: true
        },
        dateValidation: {
            type: Object,
            required: true
        },
        moveIcon: {
            type: String,
            required: true
        },
        moveLabel: {
            type: String,
            required: true
        },
        emptyMessage: {
            type: String,
            required: true
        }
    },

    emits: [
        'delete-date',
        'delete-user-from-date',
        'edit-all',
        'move-selected',
        'remove-date',
        'set-date-description',
        'store-dates'
    ],

    data() {
        return {
            selectedDates: []
        }
    },

    computed: {
        dateTableColumnCount() {
            return this.editMode ? 11 : 13;
        }
    },

    methods: {
        getPoolName(id) {
            for (let pool_id in this.pools) {
                if (this.pools[pool_id].id == id) {
                    return this.pools[pool_id].attributes.name;
                }
            }

            return '-';
        },

        getUserLink(username) {
            return STUDIP.URLHelper.getURL('dispatch.php/profile/index/?username=' + username, { cid: null });
        },

        dateKey(date) {
            return date.id || date._local_id;
        },

        setDateDescription(returnValue, dateId) {
            this.$emit('set-date-description', returnValue, dateId);
        },

        moveSelectedDates() {
            this.$emit('move-selected', this.selectedDates);
            this.selectedDates = [];
        }
    }
};
</script>
