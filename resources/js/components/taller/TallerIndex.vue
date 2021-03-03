<template>
    <div>
        <loading :show_loading="show_loading"></loading>
            <div v-if="registros">
                <my-dialog :dialog.sync="dialog" registro="registro" @destroyReg="destroyReg"></my-dialog>
                <v-card>
                    <v-card-title>
                        <h2>{{titulo}}</h2>
                        <v-spacer></v-spacer>
                        <menu-ope></menu-ope>
                    </v-card-title>
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
                                @update:pagination="updateEventPagina"
                                :pagination.sync="pagination"
                                :search="search"
                                rows-per-page-text="Registros por página"
                                >
                                    <template slot="items" slot-scope="props">
                                        <td :class="baja(props.item.deleted_at)">{{ props.item.razon }}</td>
                                        <td>{{ getDni(props.item.dni) }}</td>
                                        <td>{{ props.item.direccion }}</td>
                                        <td>{{ props.item.telefono1 }}</td>
                                        <td>{{ props.item.tfmovil }}</td>
                                        <td class="justify-center layout px-0">
                                            <v-icon
                                                small
                                                class="mr-2"
                                                @click="editItem(props.item.id)"
                                            >
                                                edit
                                            </v-icon>


                                            <v-icon
                                            v-show="isRoot"
                                            small
                                            @click="openDialog(props.item)"
                                            >
                                            delete
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
import {mapGetters} from 'vuex'
import {mapActions} from "vuex";
  export default {
    components: {
        'my-dialog': MyDialog,
        'menu-ope': MenuOpe,
        'loading': Loading,
    },
    data () {
      return {
        titulo:"Talleres",
        search:"",
        paginaActual:{},
        pagination:{
            model: "talleres",
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
            text: 'Telefono',
            align: 'left',
            value: 'telefono1'
            },
            {
            text: 'Movil',
            align: 'left',
            value: 'tfmovil'
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
      }
    },
    mounted()
    {

        if (this.getPagination.model == this.pagination.model)
            this.updatePosPagina(this.getPagination);
        else
            this.unsetPagination();

        axios.get('/mto/talleres')
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
            'hasEdtCli',
            'getPagination'
        ]),
    },
    methods:{
        ...mapActions([
            'setPagination',
            'unsetPagination'
        ]),
        getDni(dni){
            if (dni == null) return dni;
            if (dni.length >= 4)
                return this.hasEdtCli ? dni : "******"+dni.substr(-4);
            else
                return dni;
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
           // if (this.hasEdtCli)
                this.$router.push({ name: 'taller.edit', params: { id: id } })
            // else
            //     this.$router.push({ name: 'taller.show', params: { id: id } })
        },
        openDialog (item){
            this.dialog = true;
            this.item_destroy = item;
        },
        destroyReg () {

            this.dialog = false;

            axios.post('/mto/talleres/'+this.item_destroy.id,{_method: 'delete'})
                .then(response => {

                const index = this.arr_reg.indexOf(this.item_destroy)
                this.arr_reg.splice(index, 1)

                this.arr_reg = response.data.taller;
                this.$toast.success(response.data.msg);


            })
            .catch(err => {
                this.status = true;

                var msg = err.response.data.message;
                this.$toast.error(msg);

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
