<template>
    <b-modal id="create_directory" centered ref="modal" :title="langNewDirectory">
        <b-form @submit.prevent="doMkdir">
            <b-form-group label-for="new_directory_name">
                <template #label>
                    <translate key="lang_new_directory_name">Directory Name</translate>
                </template>
                <b-input type="text" id="new_directory_name" v-model="$v.newDirectory.$model"
                         :state="$v.newDirectory.$dirty ? !$v.newDirectory.$error : null" autofocus></b-input>
                <b-form-invalid-feedback key="lang_new_directory_invalid" v-translate>
                    This field is required.
                </b-form-invalid-feedback>
            </b-form-group>
        </b-form>
        <template #modal-footer>
            <b-button variant="default" @click="close" key="lang_btn_close" v-translate>
                Close
            </b-button>
            <b-button variant="primary" @click="doMkdir" key="lang_btn_create" :disabled="$v.$invalid" v-translate>
                Create Directory
            </b-button>
        </template>
    </b-modal>
</template>
<script>
import {validationMixin} from 'vuelidate';
import {required} from 'vuelidate/dist/validators.min.js';

export default {
    name: 'NewDirectoryModal',
    mixins: [validationMixin],
    props: {
        currentDirectory: String,
        mkdirUrl: String
    },
    data() {
        return {
            newDirectory: null
        };
    },
    validations: {
        newDirectory: {
            required
        }
    },
    computed: {
        langNewDirectory () {
            return this.$gettext('New Directory');
        }
    },
    methods: {
        close () {
            this.newDirectory = null;
            this.$v.$reset();
            this.$refs.modal.hide();
        },
        doMkdir () {
            this.$v.$touch();
            if (this.$v.$anyError) {
                return;
            }

            this.$wrapWithLoading(
                this.axios.post(this.mkdirUrl, {
                    'currentDirectory': this.currentDirectory,
                    'name': this.newDirectory
                })
            ).then(() => {
                this.$notifySuccess(this.$gettext('New directory created.'));
            }).finally(() => {
                this.$emit('relist');
                this.close();
            });
        }
    }
};
</script>
