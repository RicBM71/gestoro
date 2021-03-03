
<template>
    <div>
        <loading :show_loading="show_loading"></loading>
        <v-container v-if="registros">
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
                            <span>Filtros</span>
                        </v-tooltip>
                    <menu-ope></menu-ope>
                </v-card-title>
            </v-card>
            <v-card v-show="filtro">
                <filtro-tras :filtro.sync="filtro" :arr_reg.sync="arr_reg"></filtro-tras>
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
                            :pagination.sync="pagination"
                            rows-per-page-text="Registros por página"
                            >
                                <template slot="items" slot-scope="props">
                                    <td>{{ formatDate(props.item.fecha) }}</td>
                                    <td>{{ props.item.empresa.nombre }}</td>
                                    <td>{{ props.item.proveedora.nombre }}</td>
                                    <td>{{ props.item.importe_solicitado | currency('€', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false })}}</td>
                                    <td>{{ props.item.importe_entregado | currency('€', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false })}}</td>
                                    <td>{{ estado(props.item.situacion_id) }}</td>
                                    <td>{{ props.item.username }}</td>
                                    <td class="justify-center layout px-0">
                                        <v-icon
                                            small
                                            class="mr-2"
                                            @click="editItem(props.item.id)"
                                        >
                                            edit
                                        </v-icon>


                                        <v-icon
                                            v-show="isAdmin"
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
import moment from 'moment'
import MyDialog from '@/components/shared/MyDialog'
import Loading from '@/components/shared/Loading'
import FiltroTras from './FiltroTras'
import MenuOpe from './MenuOpe'
import {mapGetters} from 'vuex'
import {mapActions} from "vuex";
  export default {
    $_veeValidate: {
        validator: 'new'
    },
    components: {
        'my-dialog': MyDialog,
        'menu-ope': MenuOpe,
        'loading': Loading,
        'filtro-tras': FiltroTras
    },
    data () {
      return {
        titulo: "Traspasos de efectivo",
        paginaActual:{},
        pagination:{
            model: "caja",
            descending: true,
            page: 1,
            rowsPerPage: 10,
            sortBy: "id",
        },
        search:"",
        headers: [
            {
                text: 'Fecha',
                align: 'left',
                value: 'fecha',
                width: '2%'
            },
            {
                text: 'Solicitante',
                align: 'left',
                value: 'solicitante_user_id',
                width: '5%'
            },
            {
                text: 'Proveedora',
                align: 'left',
                value: 'proveedora_empresa_id',
                width: '5%'
            },
            {
                text: 'Importe Solicitado',
                align: 'left',
                value: 'importe',
                width: "2%"
            },
            {
                text: 'Importe Entregado',
                align: 'left',
                value: 'importe',
                width: "2%"
            },
            {
                text: 'Estado',
                align: 'left',
                value: 'situacion_id',
                width: '2%'
            },
            {
                text: 'Usuario',
                align: 'left',
                value: 'username',
                width: '2%'
            },
            {
                text: 'Acciones',
                align: 'Center',
                value: '',
                width: '1%'
            }
        ],
        situaciones: [
            'Solicitado',
            'Autorizado',
            'Entregado'
        ],
        arr_reg:[],
        status: false,
		registros: false,
        dialog: false,
        registro_id: 0,
        show_loading: true,
        url: "/mto/traspasos",
        ruta: "traspaso",

        ejercicio: new Date().toISOString().substr(0, 4),
        filtro: false,
        saldo: 0,
        fecha_saldo: ""

      }
    },
    mounted()
    {

        axios.get(this.url)
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
            'isAdmin',
            'userName',
        ])
    },
    methods:{
        estado(value){
            return this.situaciones[value - 1];
        },
        formatDate(f){
            if (f == null) return null;
            moment.locale('es');
            return moment(f).format('DD/MM/YYYY');
        },
        editItem (id) {
            this.$router.push({ name: this.ruta+'.edit', params: { id: id } })
        },
        openDialog (id){
            this.dialog = true;
            this.registro_id = id;
        },
        destroyReg () {
            this.dialog = false;

            axios.post(this.url+'/'+this.registro_id,{_method: 'delete'})
                .then(res => {

                if (res.status == 200){
                    this.$toast.success('Registro eliminado!');
                    this.arr_reg = res.data;
                }
                })
            .catch(err => {
                this.status = true;
                var msg = err.response.data.message;
                this.$toast.error(msg);

            });

        },
        filtrar(){

            this.$validator.validateAll().then((result) => {
                    if (result){
                        this.show_loading = true;
                        axios.post(this.url+'/filtrar',
                                {
                                    ejercicio: this.ejercicio,
                                }
                            )
                            .then(res => {

                                this.pagination.page = 1;
                                this.filtro = false;

                                this.arr_reg = res.data;
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
  .tachado{text-decoration:line-through;}
</style>
