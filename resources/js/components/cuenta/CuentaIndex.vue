
<template>
    <div>
        <loading :show_loading="show_loading"></loading>
        <v-container v-if="registros">
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
                            :items="this.cuentas"
                            :search="search"
                            @update:pagination="updateEventPagina"
                            :pagination.sync="pagination"
                            rows-per-page-text="Registros por página"
                            >
                                <template slot="items" slot-scope="props">
                                    <td>{{ props.item.nombre }}</td>
                                    <td v-if="props.item.defecto">Si</td>
                                    <td v-else>No</td>
                                    <td v-if="props.item.activa">Si</td>
                                    <td v-else>No</td>
                                    <td>{{ datosIBAN(props.item.iban,props.item.bic) }}</td>
                                    <td class="justify-center layout px-0">
                                        <v-icon
                                            small
                                            class="mr-2"
                                            @click="editItem(props.item.id)"
                                        >
                                            edit
                                        </v-icon>


                                        <v-icon v-if="isAdmin"
                                        small
                                        @click="openDialog(props.item.id)"
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
        </v-container>
    </div>
</template>
<script>
import MyDialog from '@/components/shared/MyDialog'
import Loading from '@/components/shared/Loading'
import MenuOpe from './MenuOpe'
import {mapGetters} from 'vuex'
import {mapActions} from "vuex";
  export default {
    components: {
        'my-dialog': MyDialog,
        'menu-ope': MenuOpe,
        'loading': Loading
    },
    data () {
      return {
        titulo: "Cuentas",
        paginaActual:{},
        pagination:{
            model: "cuenta",
            descending: false,
            page: 1,
            rowsPerPage: 10,
            sortBy: "id",
        },
        search:"",
        headers: [
            {
                text: 'Nombre',
                align: 'left',
                value: 'nombre'
            },
            {
                text: 'Defecto',
                align: 'left',
                value: 'rebu'
            },
            {
                text: 'Activa',
                align: 'left',
                value: 'importe'
            },
            {
                text: 'IBAN',
                align: 'left',
                value: 'iban'
            },

            {
                text: 'Acciones',
                align: 'Center',
                value: ''
            }
        ],
        cuentas:[],
        status: false,
		registros: false,
        dialog: false,
        cuenta_id: 0,
        show_loading: true

      }
    },
    mounted()
    {

        if (this.getPagination.model == this.pagination.model)
            this.updatePosPagina(this.getPagination);
        else
            this.unsetPagination();

        axios.get('/mto/cuentas')
            .then(res => {

                this.cuentas = res.data;
                this.registros = true;
                this.show_loading = false;
            })
            .catch(err =>{
               ;
                this.$toast.error(err.response.data.message);
                this.$router.push({ name: 'dash' })
            })
    },
    computed: {
        ...mapGetters([
            'isAdmin',
            'getPagination'
        ])
    },
    methods:{
        ...mapActions([
            'setPagination',
            'unsetPagination'
        ]),
        datosIBAN(iban,bic){

            if (iban == null) return '';

            var iban_f= iban.substring(0,4)+" "+ iban.substring(4,8) + " " + iban.substring(8,12) + " "+ iban.substring(12,16) +" "+ iban.substring(16,20)+" "+ iban.substring(20,24);

            return (this.isAdmin) ? "IBAN: "+iban_f+" - BIC: "+bic :  iban.substring(0,4)+" "+ iban.substring(4,8) + " **** **** **** *" + iban.substring(21,24);
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
            this.$router.push({ name: 'cuenta.create'})
        },
        editItem (id) {
            this.setPagination(this.paginaActual);
            this.$router.push({ name: 'cuenta.edit', params: { id: id } })
        },
        openDialog (id){
            this.dialog = true;
            this.cuenta_id = id;
        },
        destroyReg () {
            this.dialog = false;

            axios.post('/mto/cuentas/'+this.cuenta_id,{_method: 'delete'})
                .then(response => {

                if (response.status == 200){
                    this.$toast.success('cuenta eliminado!');
                    this.cuentas = response.data;
                }
                })
            .catch(err => {
                this.status = true;

                var msg = err.response.data.message;
                this.$toast.error(msg);

            });

        }
    }
  }
</script>

<style scoped>
  .tachado{text-decoration:line-through;}
</style>
