<template>
    <ul class="widget-list widget-links sidebar-navigation">
        <li>
            <a href="#" @click.prevent="$emit('create-pool')">
                <studip-icon shape="add" />
                Neuer Lospool
            </a>
        </li>
        <li>
            <a href="#" @click.prevent="$emit('create-approved-date')">
                <studip-icon shape="add" />
                Neuer freigegebener Sprechstundentermin
            </a>
        </li>
        <li>
            <a href="#" @click.prevent="$emit('create-preliminary-date')">
                <studip-icon shape="add" />
                Neuer Sprechstundentermin
            </a>
        </li>
    </ul>
</template>

<script>
import StudipIcon from '@/components/Studip/StudipIcon';

import { mapGetters } from "vuex";

export default {
    name: 'ActionsWidget',
    components: {
        StudipIcon
    },

    emits: [
        'create-approved-date',
        'create-pool',
        'create-preliminary-date'
    ]
}
</script>
