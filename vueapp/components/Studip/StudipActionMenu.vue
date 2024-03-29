<template>
    <nav v-if="shouldCollapse" class="action-menu">
        <a class="action-menu-icon" :title="'Aktionen'" aria-expanded="false" :aria-label="'Aktionsmenü'" href="#">
            <div></div>
            <div></div>
            <div></div>
        </a>
        <div class="action-menu-content">
            <div class="action-menu-title">
                {{ 'Aktionen' }}
            </div>
            <ul class="action-menu-list">
                <li v-for="item in navigationItems" :key="item.id" class="action-menu-item">
                    <a v-if="item.type === 'link'" v-bind="linkAttributes(item)" v-on="linkEvents(item)">
                        <studip-icon v-if="item.icon !== false" :shape="item.icon.shape" :role="item.icon.role"></studip-icon>
                        <span v-else class="action-menu-no-icon"></span>

                        {{ item.label }}
                    </a>
                </li>
            </ul>
        </div>
    </nav>
    <nav v-else>
        <a v-for="item in navigationItems" :key="item.id" v-bind="linkAttributes(item)" v-on="linkEvents(item)">
            <studip-icon :title="item.label" :shape="item.icon.shape" :role="item.icon.role" :size="20"></studip-icon>
        </a>
    </nav>
</template>

<script>
import StudipIcon from '@studip/StudipIcon.vue';
export default {
  components: { StudipIcon },
    name: 'studip-action-menu',
    props: {
        items: Array,
        collapseAt: {
            default: true,
        }
    },
    data () {
        return {
            open: false
        };
    },
    methods: {
        linkAttributes (item) {
            let attributes = item.attributes;
            attributes.class = item.classes;

            if (item.disabled) {
                attributes.disabled = true;
            }

            if (item.url) {
                attributes.href = item.url;
            }

            return attributes;
        },
        linkEvents (item) {
            let events = {};
            if (item.emit) {
                events.click = () => {
                    this.$emit.apply(this, [item.emit].concat(item.emitArguments));
                    this.close();
                };
            }
            return events;
        },
        close () {
            STUDIP.ActionMenu.closeAll();
        }
    },
    computed: {
        navigationItems () {
            return this.items.map((item) => {
                let classes = item.classes || '';
                if (item.disabled) {
                    classes += " action-menu-item-disabled";
                }
                return {
                    label: item.label,
                    url: item.url || false,
                    emit: item.emit || false,
                    emitArguments: item.emitArguments || [],
                    icon: item.icon ? {
                        shape: item.icon,
                        role: item.disabled ? 'inactive' : 'clickable'
                    } : false,
                    type: item.type || 'link',
                    classes: classes.trim(),
                    attributes: item.attributes || {},
                    disabled: item.disabled,
                };
            });
        },
        shouldCollapse () {
            if (this.collapseAt === false) {
                return false;
            }
            if (this.collapseAt === true) {
                return true;
            }
            return Number.parseInt(this.collapseAt) <= this.items.length;
        }
    }
}
</script>
