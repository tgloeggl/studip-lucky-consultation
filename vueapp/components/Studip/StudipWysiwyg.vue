<template>
    <textarea
        :value="value"
        @input="updateValue($event.target.value)"
        ref="studip_wysiwyg"
        class="studip-wysiwyg"
    />
</template>

<script>
import { toRaw } from "vue";

// need v-model to provide and get content -> <studip-wysiwyg v-model="content" />
export default {
    name: 'studip-wysiwyg',

    props: {
        value: String
    },

    data() {
        return {
            wysiwyg_editor: null,
        }
    },

    mounted() {
        this.initCKE();
    },

    methods: {
        initCKE() {
             if (typeof STUDIP?.wysiwyg?.replace !== 'function') {
                return false;
            }

            if (!this.wysiwyg_editor) {
                this.checkEditor();
            }

            return true;
        },

        updateValue(value) {
            this.$emit('input', value);
        },

        checkEditor()
        {
            let view = this;
            let textarea = this.$refs['studip_wysiwyg'];

            if (!STUDIP.wysiwyg.getEditor(textarea)) {
                STUDIP.wysiwyg.replace(textarea);
                setTimeout(() => {
                    view.checkEditor()
                }, 500);
                return;
            }

            this.wysiwyg_editor = STUDIP.wysiwyg.getEditor(textarea);

            console.log("getting data from editor");
            // using toRaw to remove Vue proxys. They do not work well with CKEditor
            const editor = toRaw(this.wysiwyg_editor);
            editor.ui.focusTracker.on('change:isFocused', () => {
                view.updateValue(editor.getData());
            });
        },

    },

}
</script>

<style>

</style>
