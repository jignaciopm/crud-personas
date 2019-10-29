<template>
    <section>
        <button class="button is-primary is-medium"
            @click="newPosition">
            Nueva Persona
        </button>

        <b-modal 
            :active.sync="showModalNew"
            has-modal-card
            trap-focus
            aria-role="dialog"
            aria-modal>
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>{{isUpdate ? 'Actualizar' : 'Nueva'}} persona</div>
                        </div>

                        <div class="card-body">
                            <store-component 
                                @created="refresh" 
                                :update="isUpdate"
                                :attributes="attributes"
                                :url='`api/persons`'/>
                        </div>
                    </div>
                </div>
            </div>
        </b-modal>

        <confirmation-component 
            title="Eliminar Persona" 
            body="Esta seguro que desea eliminar esta persona?"
            :show="showConfirmationRemove"
            @confirmed="confirmedRemove" />

        <b-field grouped group-multiline>
            <b-select v-model="perPage" v-if="isPaginated">
                <option value="5">5 por pagina</option>
                <option value="10">10 por pagina</option>
                <option value="15">15 por pagina</option>
                <option value="20">20 por pagina</option>
            </b-select>
        </b-field>

        <b-table
            :data="data"
            :paginated="isPaginated"
            :per-page="perPage"
            :current-page.sync="currentPage"
            :pagination-simple="isPaginationSimple"
            :pagination-position="paginationPosition"
            :default-sort-direction="defaultSortDirection"
            :sort-icon="sortIcon"
            :sort-icon-size="sortIconSize"
            :loading="loading"
            default-sort="name"
            aria-next-label="Next page"
            aria-previous-label="Previous page"
            aria-page-label="Page"
            aria-current-label="Current page">

            <template slot-scope="props">
                <b-table-column field="id" label="ID" width="40" sortable numeric>
                    {{ props.row.id }}
                </b-table-column>

                <b-table-column field="name" label="Nombre" sortable>
                    {{ props.row.name }}
                </b-table-column>

                <b-table-column field="email" label="Correo" sortable>
                    {{ props.row.email }}
                </b-table-column>

                <b-table-column field="identification" label="Cedula" sortable>
                    {{ props.row.identification }}
                </b-table-column>

                <b-table-column field="position_id" label="Cargo" sortable>
                    {{ myPosition(props.row.position_id) }}
                </b-table-column>

                <b-table-column field="created_at" label="Creacion" sortable centered>
                    <span class="tag is-success">
                        {{ new Date(props.row.created_at).toLocaleDateString() }}
                    </span>
                </b-table-column>

                <b-table-column field="actions" label="Acciones" width="80" sortable centered>
                    <span>
                        <b-button type="is-success" icon-right="pencil" size="is-small" @click="editPosition(props.row)"/>
                        <b-button 
                            type="is-danger" icon-right="delete" size="is-small" @click="idSelectionRemove(props.row.id)"/>
                    </span>
                </b-table-column>
            </template>
            <template slot="empty">
                <section class="section">
                    <div class="content has-text-grey has-text-centered">
                        <p>
                            <b-icon
                                icon="emoticon-sad"
                                size="is-large">
                            </b-icon>
                        </p>
                        <p>No se encontraron registros.</p>
                    </div>
                </section>
            </template>
        </b-table>
    </section>
</template>

<script>
    export default {
        data() {
            return {
                data: [],
                isPaginated: false,
                isPaginationSimple: false,
                paginationPosition: 'bottom',
                defaultSortDirection: '',
                sortIcon: 'arrow-up',
                sortIconSize: 'is-small',
                currentPage: 1,
                perPage: 5,
                loading: false,
                loadingRemove: false,
                showModalNew: false,
                showConfirmationRemove: false,
                idSelected: null,
                isUpdate: false,
                attributes: [],
                attributesStore: [
                    {
                        name: 'name',
                        placeholder: "Nombre"
                    },
                    {
                        name: 'email',
                        placeholder: "Correo"
                    },
                    {
                        name: 'identification',
                        placeholder: "Cedula"
                    },
                    {
                        name: 'position_id',
                        placeholder: "Cargo",
                        isSelect: true,
                        value: [],
                        loading: true
                    }
                ],
                attributesUpdate: [
                    {
                        name: 'id',
                        placeholder: "ID",
                        value: '',
                        disabled: true
                    },
                    {
                        name: 'name',
                        placeholder: "Nombre",
                        value: ''
                    },
                    {
                        name: 'email',
                        placeholder: "Correo"
                    },
                    {
                        name: 'identification',
                        placeholder: "Cedula"
                    }
                ],
                positions: []
            }
        },
        methods: {
            getPositions() {
                axios.get(`api/positions`)
                    .then(res => {
                        console.log(res)
                        this.attributesStore.forEach(function(attribute){
                            if(attribute.name == 'position_id') {
                                attribute.loading = false
                                attribute.value = res.data
                            }
                        })
                        
                        this.positions = res.data
                    })
                    .catch(() => {
                        alert('Error')
                    })
            },
            getData() {
                this.loading = true
                axios.get(`api/persons?limit=${this.perPage}`)
                    .then(res => {
                        this.loading = false
                        this.data = res.data.data
                    })
                    .catch(() => {
                        this.loading = false
                        alert('Error')
                    })
            },
            idSelectionRemove(id) {
                this.showConfirmationRemove = true 
                this.idSelected = id
            },
            remove(id) {
                this.loading = true
                axios.delete(`api/persons/${id}`)
                    .then(res => {
                        this.loading = false
                        this.getData()
                    })
                    .catch(() => {
                        this.loading = false
                        alert('Error')
                    })
            },
            confirmedRemove(confirmed) {
                this.showConfirmationRemove = false
                if(confirmed)
                    this.remove(this.idSelected)
            },
            refresh() {
                this.showModalNew = false
                this.getData()
            },
            newPosition() {
                this.showModalNew = true
                this.isUpdate = false
                this.attributes = this.attributesStore.slice(0)
            },
            editPosition(position) {
                this.showModalNew = true
                this.isUpdate = true
                this.attributes = this.attributesUpdate.slice(0)
                this.attributes.forEach(function(attribute) {
                    attribute.value = position[attribute.name]
                })
            },
            myPosition(id) {
                var position = this.positions.filter(function(position) {
                    return position.id = id
                })

                return position[0].name
            }
        },
        mounted() {
            this.getData()
            this.getPositions() 
        }
    }
</script>
