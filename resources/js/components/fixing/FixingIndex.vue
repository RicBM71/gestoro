
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
                                v-show="items.length > 0 && hasConsultas"
                                v-on="on"
                                color="white"
                                icon
                                @click="goExcel()"
                            >
                                <v-avatar size="32px">
                                    <img class="img-fluid" src="/assets/excel.png">
                                </v-avatar>
                            </v-btn>
                        </template>
                        <span>Exportar a Excel</span>
                    </v-tooltip>

                    <menu-ope></menu-ope>
                </v-card-title>
            </v-card>
            <v-card v-show="filtro">
                <!-- <filtro-caja :filtro.sync="filtro"
                     :reg.sync="items"
                ></filtro-caja> -->
            </v-card>
            <v-card>
                <v-container>
                    <v-layout row wrap>
                        <v-flex xs12>
                            <v-data-table
                            :pagination="pagination"
                            :headers="headers"
                            :items="items"
                            :search="search"
                            rows-per-page-text="Registros por página"
                            >
                                <template slot="items" slot-scope="props">
                                    <td>{{ formatDate(props.item.fecha) }}</td>
                                    <td>{{ props.item.importe | currency('€', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false })}}</td>
                                    <td>{{ props.item.username+" "+formatDateUpdated(props.item.updated_at) }}</td>

                                    <td class="justify-center layout px-0">
                                        <v-icon
                                            small
                                            class="mr-2"
                                            @click="editItem(props.item.id)"
                                        >
                                            edit
                                        </v-icon>
                                       <v-icon
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
import MenuOpe from './MenuOpe'
// import FiltroCaja from './FiltroCaja'
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
        // 'filtro-caja': FiltroCaja
    },
    data () {
      return {
        expand: false,
        titulo: "Fixing diario",
        search:"",
        pagination:{
            descending: true,
            page: 1,
            rowsPerPage: 10,
            sortBy: 'fecha',
        },
        headers: [
            {
                text: 'Fecha',
                align: 'left',
                value: 'fecha',
                width: '2%'
            },
            {
                text: 'Importe',
                align: 'left',
                value: 'importe',
                width: "8%"
            },
            {
                text: 'Usuario',
                align: 'left',
                value: 'username',
                width: '15%'
            },
            {
                text: 'Acciones',
                align: 'Center',
                value: 'id',
                width: '1%'
            }
        ],
        items:[],
        status: false,
		registros: false,
        dialog: false,
        registro_id: 0,
        show_loading: true,
        url: "/mto/fixings",
        ruta: "fixing",

        ejercicio: new Date().toISOString().substr(0, 4),
        filtro: false,
        saldo: 0,
      }
    },
    mounted()
    {

        axios.get(this.url)
            .then(res => {
                this.items = res.data;
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
            'hasConsultas',
        ])
    },
    methods:{
        formatDate(f){

            if (f == null) return null;
            moment.locale('es');
            return moment(f).format('DD/MM/YYYY');
        },
        formatDateUpdated(f){


            if (f == null) return null;
            moment.locale('es');
            return moment(f).format('DD/MM/YYYY HH:mm:ss');
        },
        create(){
            this.$router.push({ name: this.ruta+'.create'})
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
                    this.items = res.data;
                }
                })
            .catch(err => {
                this.status = true;
                var msg = err.response.data.message;
                this.$toast.error(msg);

            });

        },
        goExcel(){
            this.show_loading = true;
            axios({
                        url: this.url+"/excel",
                        method: 'POST',
                        responseType: 'blob',
                        data:{ data: this.items }
                        })
                    .then(response => {

                        let blob = new Blob([response.data])
                        let link = document.createElement('a')
                        link.href = window.URL.createObjectURL(blob)

                        link.download = "Caja."+new Date().getFullYear()+(new Date().getMonth()+1)+(new Date().getDate())+'.xlsx';

                        document.body.appendChild(link);
                        link.click()
                        document.body.removeChild(link);

                        this.$toast.success('Download OK! '+link.download);

                        this.show_loading = false;

                    })
                    .catch(err => {
                        if (err.response.status == 403)
                            this.$toast.error("No autorizado!");
                        else
                            this.$toast.error(err.response.data.message);
                        this.show_loading = false;
                    });
        },
        // filtrar(){

        //     this.$validator.validateAll().then((result) => {
        //             if (result){
        //                 this.show_loading = true;
        //                 axios.post(this.url+'/filtrar',
        //                         {
        //                             ejercicio: this.ejercicio,
        //                         }
        //                     )
        //                     .then(res => {

        //                         this.pagination.page = 1;
        //                         this.filtro = false;

        //                         this.items = res.data;
        //                         this.show_loading = false;

        //                     })
        //                     .catch(err => {

        //                         this.show_loading = false;
        //                         if (err.response.status != 419)
        //                             this.$toast.error(err.response.data.message);
        //                         else
        //                             this.$toast.error("Sesión expirada!");

        //                     });
        //             }
        //     });

        // },

    }
  }
</script>

<style scoped>
  .tachado{text-decoration:line-through;}
</style>
