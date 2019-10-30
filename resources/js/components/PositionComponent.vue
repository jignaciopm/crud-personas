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
                Nueva Posicion
            </button>
        </div>

        <b-tabs v-model="activeTab">
            <b-tab-item label="Listado">
                <b-table
                    :data="data"
                    :default-sort-direction="defaultSortDirection"
                    :sort-icon="sortIcon"
                    :sort-icon-size="sortIconSize"
                    :loading="loading"
                    default-sort="user.first_name"
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
                    @created="refresh('Se ha creado la posicion correctamente!')"
                    :attributes="attributesStore"
                    :url='`api/positions`'/>
            </b-tab-item>

            <b-tab-item label="Editar" disabled>
                <update-component 
                    @updated="refresh('Se ha actualizado la posicion correctamente!')"
                    :id="idSelected"
                    :attributes="attributesStore"
                    :url='`api/positions`'/>
            </b-tab-item>
        </b-tabs>

        <confirmation-component 
            title="Eliminar Posicion" 
            body="Esta seguro que desea eliminar esta posicion?"
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
                defaultSortDirection: '',
                sortIcon: 'arrow-up',
                sortIconSize: 'is-small',
                loading: false,
                showConfirmationRemove: false,
                idSelected: '',
                attributesStore: [
                    {
                        name: 'name',
                        placeholder: "Nombre"
                    }
                ],
            }
        },
        methods: {
            getData() {
                this.loading = true
                axios.get(`api/positions?limit=${this.perPage}`)
                    .then(res => {
                        this.loading = false
                        this.data = res.data
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
                axios.delete(`api/positions/${id}`)
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
        }
    }
</script>
