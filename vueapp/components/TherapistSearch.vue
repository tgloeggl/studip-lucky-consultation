<template>
    <span :class="'quicksearch_container' + (containerclass ? ' ' + containerclass : '')">
        <input type="hidden"
               :name="name"
               :value="returnValue"
               v-if="!autocomplete && name">
        <input type="text"
               :name="autocomplete ? name : null"
               v-model="inputValue"
               autocomplete="off"
               @keydown.up="selectUp"
               @keydown.down="selectDown"
               @keydown.enter.prevent="selectByKey"
               @keyup="search"
               v-bind="$attrs">
        <div class="dropdownmenu">
            <ul class="autocomplete__results" v-if="isVisible">
                <li class="autocomplete__result"
                    v-for="(result, index) in results"
                    :key="index"
                    :class="index === selected ? 'autocomplete__result--selected' : ''"
                    @click="select(result)"
                >
                    <studip-icon shape="person"></studip-icon>
                    {{ result.name }}
                </li>
                <li v-if="errorMessage !== null">{{errorMessage}}</li>
            </ul>
        </div>
    </span>
</template>

<script>
import { mapGetters } from "vuex";

import StudipIcon from '@/components/Studip/StudipIcon';

export default {
    name: 'therapistsearch',

    components: {
        StudipIcon
    },

    props: {
        name: {
            type: String,
            required: false
        },
        value: {
            type: String,
            required: false,
            default: ''
        },
        needle: {
            type: String,
            required: false,
            default: ''
        },
        autocomplete: {
            type: Boolean,
            required: false,
            default: false
        },
        containerclass: {
            type: String,
            required: false,
            default: ''
        },
        dateId: {
            type: Number,
            required: false,
            default: 0
        }
    },

    inheritAttrs: false,

    data () {
        return {
            searching: false,
            debounceTimeout: null,
            selected: null,
            errorMessage: null,
            inputValue: null,
            returnValue: null,
            initialValue: null,
            results: []
        };
    },

    computed: {
        ...mapGetters(['search_users']),

        isVisible() {
            return this.results.length > 0 || this.errorMessage !== null;
        }
    },

    methods: {
        initialize (value, displayname) {
            this.initialValue = value;
            this.inputValue = value;
            this.returnValue = value;
        },

        search (event)
        {
            let needle = event.target.value;

            this.$emit('date-input', {
                'value': null,
                'name' : needle
            }, this.dateId);

            if (needle.length < 3) {
                return;
            }

            clearTimeout(this.debounceTimeout);

            let view = this;

            this.debounceTimeout = setTimeout(() => {
                view.searching = true;
                view.$store.dispatch('searchUsers', needle)
                .then((data) => {
                    view.searching = false;
                    view.results = view.search_users;
                });
            }, 500);
        },

        select (result) {
            this.inputValue = result.name;
            this.initialValue = this.inputValue;
            this.returnValue = result.user_id;
            this.results = [];

            this.$emit('date-input', result, this.dateId);
        },

        selectUp () {
            if (this.selected > 0) {
                this.selected -= 1;
            } else if (this.selected === null) {
                this.selected = this.results.length - 1;
            } else {
                this.selected = null;
            }
        },

        selectDown () {
            if (this.selected === null) {
                this.selected = 0;
            } else if (this.selected < this.results.length - 1) {
                this.selected += 1;
            } else {
                this.selected = null;
            }
        },

        selectByKey () {
            if (this.selected !== null) {
                this.select(this.results[this.selected]);
            }
            return false;
        },

        reset (clear = false) {
            setTimeout(() => {
                this.results = [];
                this.selected = null;

                if (clear) {
                    this.returnValue = this.initialValue;
                    this.inputValue = this.initialValue;
                }
            }, clear ? 0 : 200);
        }
    },

    created () {
        this.initialize(
            this.value,
            this.autocomplete ? this.value : this.needle
        );
    },

    watch: {
        value (val) {
            this.reset(true);
            this.initialize(val);
        },

        // inputValue (needle, oldneedle) {
        //     if (oldneedle !== null && (oldneedle !== needle) && needle.length > 2) {
        //         this.search(needle);
        //     }
        // }
    }
}
</script>
