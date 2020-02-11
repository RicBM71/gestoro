<template>
    <div>
        <loading :show_loading="show_loading"></loading>
            <div v-if="registros">
                <my-dialog :dialog.sync="dialog" registro="registro" @destroyReg="destroyReg"></my-dialog>
                <v-card>
                    <v-card-title>
                        <h2>{{titulo}}</h2>
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
                            <span>Filtrar</span>
                        </v-tooltip>
                        <menu-ope></menu-ope>
                    </v-card-title>
                </v-card>
                <v-card v-show="filtro">
                    <filtro-cli :filtro.sync="filtro" :reg.sync="arr_reg"></filtro-cli>
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
                                :items="arr_reg"
                                :search="search"
                                @update:pagination="updateEventPagina"
                                :pagination.sync="pagination"
                                rows-per-page-text="Registros por página"
                                >
                                    <template slot="items" slot-scope="props">
                                        <td :class="baja(props.item.fecha_baja)">{{ props.item.razon }}</td>
                                        <td>{{ getDni(props.item.dni) }}</td>
                                        <td>{{ props.item.direccion }}</td>
                                        <td>{{ props.item.cpostal }}</td>
                                        <td>{{ props.item.poblacion }}</td>
                                        <td class="justify-center layout px-0">
                                            <v-icon
                                                small
                                                class="mr-2"
                                                @click="editItem(props.item.id)"
                                            >
                                                edit
                                            </v-icon>
                                            &nbsp;
                                            <v-icon
                                                v-show="isRoot"
                                                small
                                                @click="openDialog(props.item)"
                                            >
                                                delete
                                            </v-icon>
                                            &nbsp;
                                            <v-icon
                                                small
                                                @click="goCreateVenta(props.item.id)"
                                            >
                                                credit_card
                                            </v-icon>
                                            &nbsp;
                                            &nbsp;
                                            <v-icon
                                                small
                                                @click="goCreateCompra(props.item.id)"
                                            >
                                                add_shopping_cart
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
import moment from 'moment'
import MyDialog from '@/components/shared/MyDialog'
import Loading from '@/components/shared/Loading'
import MenuOpe from './MenuOpe'
import FiltroCli from './FiltroCli'
import {mapGetters} from 'vuex'
import {mapActions} from "vuex";
  export default {
    components: {
        'my-dialog': MyDialog,
        'menu-ope': MenuOpe,
        'loading': Loading,
        'filtro-cli': FiltroCli
    },
    data () {
      return {
        titulo:"Clientes/Proveedores",
        search:"",
        paginaActual:{},
        pagination:{
            model: "cliente",
            descending: false,
            page: 1,
            rowsPerPage: 10,
            sortBy: "razon",
            search: ""
        },
        headers: [
            {
            text: 'Razón',
            align: 'left',
            value: 'razon'
            },
            {
            text: 'DNI/NIE/CIF',
            align: 'left',
            value: 'dni'
            },
            {
            text: 'Dirección',
            align: 'left',
            value: 'direccion'
            },
            {
            text: 'C. Postal',
            align: 'left',
            value: 'cpostal'
            },
            {
            text: 'Poblacion',
            align: 'left',
            value: 'poblacion'
            },
            {
            text: 'Acciones',
            align: 'Center',
            value: ''
            }
        ],
        item_destroy: {},
        arr_reg:[],
        status: false,
		registros: false,
        dialog: false,
        show_loading: true,
        filtro: true,
        fbaja: 'A',
        sino: [
            {text:'Alta','value':'A'},
            {text:'Baja','value':'B'},
            {text:'Todos','value':'T'}
        ]
      }
    },
    mounted()
    {
        this.pagination.model="cliente"+this.empresaActiva;
        if (this.getPagination.model == this.pagination.model){
            this.updatePosPagina(this.getPagination);
        }
        else
            this.unsetPagination();

        axios.get('/mto/clientes')
            .then(res => {

                this.arr_reg = res.data;
                this.registros = true;
                this.show_loading = false;
            })
            .catch(err =>{
                this.$toast.error(err.response.data.message);
                this.$router.push({ name: 'dash' })
            })
    },
    computed: {
        ...mapGetters([
            'isRoot',
            'isAdmin',
            'isSupervisor',
            'getPagination'
        ]),
    },
    methods:{
        ...mapActions([
            'setPagination',
            'unsetPagination'
        ]),
        getDni(dni){
            return this.isSupervisor ? dni : "******"+dni.substr(-4);
        },
        getFecha(newValue) {

            moment.locale('es');
            return newValue ? moment(newValue).format('DD/MM/YYYY') : '';
        },

        baja(fecha){

            return (fecha == null) ? '' : 'red--text tachado';

        },
        updateEventPagina(obj){

            this.paginaActual = obj;

        },
        updatePosPagina(pag){

            this.pagination.page = pag.page;
            this.pagination.descending = pag.descending;
            this.pagination.rowsPerPage= pag.rowsPerPage;
            this.pagination.sortBy = pag.sortBy;

        },
        create(){
            this.$router.push({ name: 'cliente.create'})
        },
        editItem (id) {
            this.setPagination(this.paginaActual);
            // if (this.isSupervisor)
                this.$router.push({ name: 'cliente.edit', params: { id: id } })
            // else
            //     this.$router.push({ name: 'cliente.show', params: { id: id } })
        },
        openDialog (item){
            this.dialog = true;
            this.item_destroy = item;
        },
        destroyReg () {

            this.dialog = false;

            axios.post('/mto/clientes/'+this.item_destroy.id,{_method: 'delete'})
                .then(response => {

                const index = this.arr_reg.indexOf(this.item_destroy)
                this.arr_reg.splice(index, 1)

                this.arr_reg = response.data;
                this.$toast.success('Cliente eliminado!');


            })
            .catch(err => {
                this.status = true;

                var msg = err.response.data.message;
                this.$toast.error(msg);

            });

        },
        goCreateCompra(id){
            this.$router.push({ name: 'compra.create', params: { cliente_id: id }  })
        },
        goCreateVenta(id){
            this.$router.push({ name: 'albaran.create', params: { cliente_id: id }  })
        },
        filtrar(){

            this.$validator.validateAll().then((result) => {
                    if (result){
                        this.show_loading = true;
                        axios.post('/mto/clientes/filtrar',
                                {
                                    fbaja: this.fbaja,
                                }
                            )
                            .then(res => {
                                this.pagination.page = 1;
                                this.filtro = false;

                                this.clientes   = res.data;
                                this.show_loading = false;

                            })
                            .catch(err => {

                                this.show_loading = false;
                                if (err.response.status != 419)
                                    this.$toast.error(err.response.data.message);
                                else
                                    this.$toast.error("Sesión expirada!");

                            });
                    }
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
