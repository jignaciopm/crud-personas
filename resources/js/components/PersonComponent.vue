<template>
    <section>
        <div class="d-flex justify-content-between align-items-center">
            <b-taglist attached class='m-0'>
                <b-tag type="is-dark" size="is-large">  
                    <b-icon
                        icon="view-dashboard"
                        size="is-medium">
                    </b-icon>
                </b-tag>
                <b-tag type="is-info" size="is-large">LOGO</b-tag>
            </b-taglist>
            <button class="button is-primary is-medium"
                @click="newPosition">
                Nueva Persona
            </button>
        </div>

        <b-tabs v-model="activeTab">
            <b-tab-item label="Listado">
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
                    :backend-pagination="true"
                    :total="total"
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
                            {{ props.row.identification != null ? props.row.identification : 'N/A' }}
                        </b-table-column>

                        <b-table-column field="position" label="Cargo" sortable>
                            {{ props.row.position != null ? props.row.position.name : 'N/A' }}
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
            </b-tab-item>

            <b-tab-item label="Nuevo" disabled>
                <store-component 
                    @created="refresh('Se ha creado la persona correctamente!')"
                    :attributes="attributesStore"
                    :url='`api/persons`'/>
            </b-tab-item>

            <b-tab-item label="Editar" disabled>
                <update-component 
                    @updated="refresh('Se ha actualizado la persona correctamente!')"
                    :id="idSelected"
                    :attributes="attributesStore"
                    :url='`api/persons`'/>
            </b-tab-item>
        </b-tabs>

        <confirmation-component 
            title="Eliminar Persona" 
            body="Esta seguro que desea eliminar esta persona?"
            :show="showConfirmationRemove"
            @confirmed="confirmedRemove" />
    </section>
</template>

<script>
    export default {
        data() {
            return {
                activeTab: 0,
                data: [],
                isPaginated: true,
                isPaginationSimple: false,
                paginationPosition: 'bottom',
                defaultSortDirection: '',
                sortIcon: 'arrow-up',
                sortIconSize: 'is-small',
                currentPage: 1,
                perPage: 10,
                total: 0,
                loading: false,
                showConfirmationRemove: false,
                idSelected: '',
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
                positions: []
            }
        },
        methods: {
            getPositions() {
                axios.get(`api/positions`)
                    .then(res => {
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
                axios.get(`api/persons?limit=${this.perPage}&page=${this.currentPage}`)
                    .then(res => {
                        this.loading = false
                        this.data = res.data.data
                        this.total = res.data.total
                        this.currentPage = res.data.current_page
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
                        this.$buefy.toast.open({
                            duration: 5000,
                            position: 'is-bottom',
                            message: 'Se ha eliminado el usuario correctamente!',
                            type: 'is-success'
                        })
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
            refresh(message) {
                this.activeTab = 0
                this.$buefy.toast.open({
                    duration: 5000,
                    position: 'is-bottom',
                    message: message,
                    type: 'is-success'
                })
                this.getData()
            },
            newPosition() {
                this.activeTab = 1
            },
            editPosition(position) {
                this.idSelected = position.id
                this.activeTab = 2
            }
        },
        mounted() {
            this.getData()
            this.getPositions() 
        },
        watch: {
            currentPage: function() {
                this.getData()
            },
            perPage: function() {
                this.getData()
            }
        }
    }
</script>
