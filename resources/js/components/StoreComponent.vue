<template>
    <section>
        <div v-for="attribute in attributes" v-bind:key="attribute.name">
            <b-field :label="attribute.placeholder" v-if="!attribute.disabled && !attribute.isSelect"
                :type="errors[attribute.name] != null ? 'is-danger' : ''"
                :message="errors[attribute.name] != null ? errors[attribute.name] : ''">
                <b-input :placeholder="attribute.placeholder" v-model="form[attribute.name]" v-if="!attribute.disabled"></b-input>
            </b-field>
            <b-field :label="attribute.placeholder" v-if="attribute.isSelect">
                <b-select placeholder="Seleccionar" v-model="form[attribute.name]" :loading="attribute.loading" expanded>
                    <option v-for="position in attribute.value"
                        v-bind:key="position.id" :value="position.id">{{position.name}}</option>
                </b-select>
            </b-field>
            <b-field />
        </div>

        <b-field>
            <b-button @click="store" :loading="loading" v-if="!update">Crear</b-button>
            <b-button @click="updating" :loading="loading" v-else>Actualizar</b-button>
        </b-field>
    </section>
</template>

<script>
    export default {
        props: {
            attributes: {
                type: Array,
                required: true
            },
            url: {
                type: String,
                required: true
            },
            update: {
                type: Boolean,
                default: false
            }
        },
        data() {
            return {
                form: {},
                errors: [],
                loading: false
            }
        },
        methods: {
            store() {
                this.loading = true
                axios.post(this.url,this.form)
                    .then(res => {
                        this.loading = false
                        this.$emit('created')
                    })
                    .catch(error => {
                        this.loading = false
                        this.errors = error.response.data.errors
                    }) 
            },
            updating() {
                this.loading = true
                const id = this.attributes.filter(function(attribute) {
                    return attribute.name == 'id'
                })[0].value

                axios.put(`${this.url}/${id}`,this.form)
                    .then(res => {
                        this.loading = false
                        this.$emit('created')
                    })
                    .catch(error => {
                        this.loading = false
                        this.errors = error.response.data.errors
                    })
            }
        },
        created() {
            if(this.update) {
                var form = this.form
                this.attributes.forEach(function(attribute){
                    form[attribute.name] = attribute.value
                })
            }
        }
    }
</script>
