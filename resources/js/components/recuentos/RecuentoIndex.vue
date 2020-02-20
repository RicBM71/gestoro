<template>
    <div>
        <loading :show_loading="show_loading"></loading>
            <div v-if="registros">
                <my-dialog :dialog.sync="dialog" registro="registro"></my-dialog>
                <v-card>
                    <v-card-title>
                        <h2>{{titulo}}</h2>
                        <v-spacer></v-spacer>
                        <v-spacer></v-spacer>
                        <v-tooltip bottom>
                            <template v-slot:activator="{ on }">
                                <v-btn
                                    v-on="on"
                                    color="white"
                                    icon
                                    @click="filtro = !filtro"
                                >
                                    <v-icon color="primary">filter_list</v-icon>
                                </v-btn>
                            </template>
                            <span>Filtros</span>
                        </v-tooltip>
                        <menu-ope></menu-ope>
                    </v-card-title>
                </v-card>
                <v-card v-show="filtro">
                    <filtro-rec :filtro.sync="filtro" :items.sync="items"></filtro-rec>
                </v-card>
                <v-card>
                    <v-container>
                        <v-layout row wrap>
                            <v-flex xs6></v-flex>
                            <v-flex xs6>
                                <v-spacer></v-spacer>
                                <v-text-field
                                    v-model="search"
                                    append-icon="search"
                                    label="Buscar"
                                    single-line
                                    hide-details
                                ></v-text-field>
                            </v-flex>
                        </v-layout>
                        <br/>
                        <v-layout row wrap>
                            <v-flex xs12>
                                <v-data-table
                                    :headers="headers"
                                    :items="items"
                                    :search="search"
                                    @update:pagination="updateEventPagina"
                                    :pagination.sync="pagination"
                                    rows-per-page-text="Registros por página"
                                >
                                    <template slot="items" slot-scope="props">
                                        <td v-if="props.item.producto != null">{{props.item.producto.referencia }}</td>
                                        <td v-else>ID:{{ props.item.producto_id}}</td>
                                        <td v-if="props.item.producto != null">{{ props.item.producto.nombre }}</td>
                                        <td v-else>empresa: {{props.item.destino_empresa_id}}</td>
                                        <td v-if="props.item.estado != null">{{ props.item.estado.nombre }}</td>
                                        <td v-else>{{props.item.estado_id}}</td>
                                        <td v-if="props.item.rfid != null">{{ props.item.rfid.nombre }}</td>
                                        <td v-else>{{props.item.rfid_id}}</td>
                                        <td class="justify-center layout px-0">
                                            <v-icon
                                                small
                                                class="mr-2"
                                                @click="goProducto(props.item)"
                                            >
                                                local_offer
                                            </v-icon>
                                            <v-icon
                                                v-if="props.item.rfid_id == 3"
                                                small
                                                class="mr-2"
                                                @click="update(props.item)"
                                            >
                                                warning
                                            </v-icon>
                                            <v-icon
                                                v-if="props.item.rfid_id > 10"
                                                small
                                                class="mr-2"
                                                @click="update(props.item)"
                                            >
                                                location_off
                                            </v-icon>
                                        </td>
                                    </template>
                                    <template slot="pageText" slot-scope="props">
                                        Registros {{ props.pageStart }} - {{ props.pageStop }} de {{ props.itemsLength }}
                                    </template>
                                </v-data-table>
                            </v-flex>
                        </v-layout>
                    </v-container>
                </v-card>
            </div>
    </div>
</template>
<script>
import {mapGetters} from 'vuex'
import {mapActions} from "vuex";
import moment from 'moment'
import MyDialog from '@/components/shared/MyDialog'
import Loading from '@/components/shared/Loading'
import MenuOpe from './MenuOpe'
import FiltroRec from './FiltroRec'
  export default {
    components: {
        'my-dialog': MyDialog,
        'menu-ope': MenuOpe,
        'loading': Loading,
        'filtro-rec': FiltroRec
    },
    data () {
      return {
        titulo:"Recuentos",
        search:"",
        paginaActual:{},
        pagination:{
            model: "recuentos",
            descending: false,
            page: 1,
            rowsPerPage: 10,
            sortBy: "id",
            search: ""
        },
        headers: [
            {
            text: 'Referencia',
            align: 'left',
            value: 'producto.referencia'
            },
            {
            text: 'Producto',
            align: 'left',
            value: 'producto.nombre'
            },
            {
            text: 'Estado',
            align: 'left',
            value: 'estado.nombre'
            },
            {
            text: 'Situación',
            align: 'left',
            value: 'rfid.nombre'
            },
            {
            text: 'Acciones',
            align: 'Center',
            value: ''
            }
        ],
        filtro: false,
        item_destroy: {},
        items:[],
        status: false,
		registros: false,
        dialog: false,
        show_loading: true,
        editedIndex: 0
      }
    },
    beforeMount(){

    },
    mounted()
    {

        if (this.getPagination.model == this.pagination.model){
            this.updatePosPagina(this.getPagination);
        }
        else
            this.unsetPagination();

        axios.get('/mto/recuentos')
            .then(res => {
                this.items = res.data;
            })
            .catch(err =>{
                this.$toast.error(err.response.data.message);
                this.$router.push({ name: 'dash' })
            })
            .finally(()=> {
                this.show_loading = false;
                this.registros = true;
            });
    },
    computed: {
        ...mapGetters([
            'getPagination',
            'isRoot',
            'isAdmin',
            'isSupervisor',
        ]),
    },
    methods:{
        ...mapActions([
            'setPagination',
            'unsetPagination'
        ]),
        updateEventPagina(obj){

            this.paginaActual = obj;

        },
        updatePosPagina(pag){

            this.pagination.page = pag.page;
            this.pagination.descending = pag.descending;
            this.pagination.rowsPerPage= pag.rowsPerPage;
            this.pagination.sortBy = pag.sortBy;

        },
        goProducto(item) {

            this.setPagination(this.paginaActual);

            this.$router.push({ name: 'producto.edit', params: { id: item.producto_id } })
        },
        update(item) {

            this.editedIndex = this.items.indexOf(item)
            //this.editedItem = Object.assign({}, item)

                    axios.put("/mto/recuentos/"+item.id, {rfid_id : item.rfid_id})
                        .then(res => {

                            Object.assign(this.items[this.editedIndex], res.data.recuento)

                            this.$toast.success(res.data.message);
                            this.loading = false;

                        })
                        .catch(err => {
                            this.$toast.error(err.response.data.message);
                        })
                        .finally(()=> {
                            this.show_loading = false;
                        });

            },
    }
  }
</script>
<style scoped>
.tachado{
    text-decoration: line-through
}
</style>
