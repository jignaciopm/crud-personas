<template>
    <b-modal 
        :active.sync="show"
        has-modal-card
        trap-focus
        aria-role="dialog"
        aria-modal>
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>{{title}}</div>
                    </div>

                    <div class="card-body">
                        <p>{{body}}</p>
                    </div>

                    <div class="card-footer d-flex justify-content-between align-items-center">
                        <b-button type="is-primary" @click="confirmation(true)">Si</b-button>
                        <b-button type="is-danger" @click="confirmation(false)">No</b-button>
                    </div>
                </div>
            </div>
        </div>
    </b-modal>
</template>

<script>
    export default {
        props: {
            title: {
                type: String,
                default: 'Confirmacion'
            },
            body: {
                type: String,
                default: 'Esta seguro que desea continuar?'
            },
            show: {
                type: Boolean,
                default: false
            }
        },
        methods: {
            confirmation(confirmed) {
                this.$emit('confirmed',confirmed)
            },
        }
    }
</script>
