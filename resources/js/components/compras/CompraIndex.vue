
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
                                v-show="items.length > 0 && hasExcel"
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
                    <menu-ope :compra="compra"></menu-ope>
                </v-card-title>
            </v-card>
            <v-card v-show="filtro">
                 <filtro-com :filtro.sync="filtro" :reg.sync="items"></filtro-com>
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
                            :expand="expand"
                            @update:pagination="updateEventPagina"
                            :pagination.sync="pagination"
                            rows-per-page-text="Registros por página"
                            >
                                <template slot="items" slot-scope="props">
                                    <td>{{ getDni(props.item.cliente.dni) }}</td>
                                    <td>{{ props.item.cliente.razon }}</td>
                                    <td>{{ props.item.alb_ser }}</td>
                                    <td>{{ formatDate(props.item.fecha_compra) }} {{ props.item.tipo.nombre[0] }}</td>
                                    <td class="text-xs-right">{{ props.item.importe | currency('€', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false })}}</td>
                                    <td v-if="(parametros.doble_interes == true && props.item.tipo_id == 1)" class="text-xs-center">{{ props.item.interes | currency('', 0, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false })}}/{{ props.item.interes_recuperacion | currency('', 0, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false })}}</td>
                                    <td v-if="(parametros.doble_interes == false && props.item.tipo_id == 1)" class="text-xs-center">{{ props.item.interes | currency('', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false })}}</td>
                                    <td v-if="props.item.tipo_id != 1" class="text-xs-center">-</td>
                                    <td v-if="props.item.fase_id == 4">{{ props.item.retraso }}</td>
                                    <td v-else>-</td>
                                    <td :class="props.item.fase.color">{{ props.item.fase.nombre }}</td>
                                    <td class="justify-center layout px-0">
                                        <v-icon
                                            v-show="props.item.notas > ''"
                                            small
                                            class="mr-2"
                                            @click="props.expanded = !props.expanded"
                                        >
                                            visibility
                                        </v-icon>
                                        &nbsp;
                                        <v-icon
                                            small
                                            v-if="whatsApp == 1 && hasWhatsApp && props.item.cliente.tfmovil > ''"
                                            color="green"
                                            @click="sendWhatsApp(props.item)"
                                        >
                                            mdi-whatsapp
                                        </v-icon>
                                        &nbsp;
                                        &nbsp;
                                        <v-icon
                                            small
                                            class="mr-2"
                                            @click="editItem(props.item)"
                                        >
                                            edit
                                        </v-icon>
                                        &nbsp;
                                        <v-icon v-if="hasDelCom"
                                            small
                                            @click="openDialog(props.item)"
                                        >
                                        delete
                                        </v-icon>


                                    </td>
                                </template>
                                <template v-slot:expand="props">
                                    <v-card flat>
                                        <v-card-text class="font-italic">
                                            {{ props.item.notas }}
                                        </v-card-text>
                                    </v-card>
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
import FiltroCom from './FiltroCom'
import {mapGetters} from 'vuex'
import {mapActions} from "vuex";
  export default {
    components: {
        'my-dialog': MyDialog,
        'menu-ope': MenuOpe,
        'loading': Loading,
        'filtro-com': FiltroCom
    },
    data () {
      return {
        expand: false,
        titulo: "Operaciones de Compra",
        compra:{
            id: 0,
            cliente_id: 0
        },
        paginaActual:{},
        pagination:{
            model: "compra",
            descending: true,
            page: 1,
            rowsPerPage: 10,
            sortBy: "eje_alb",
        },
        search:"",
        headers: [
             {
                text: 'NIF/NIE',
                align: 'left',
                value: 'cliente.dni'
            },
            {
                text: 'Nombre y Apellidos',
                align: 'left',
                value: 'cliente.razon',
                width: '30%'
            },
            {
                text: 'Número',
                align: 'left',
                value: 'eje_alb',
                width: '18%'
            },
            {
                text: 'F. Compra',
                align: 'left',
                value: 'fecha_compra',
                width: '20%'
            },
            {
                text: 'Importe',
                align: 'left',
                value: 'importe'
            },
            {
                text: '% Ren',
                align: 'left',
                value: 'interes',
                width: '2%'
            },
            {
                text: 'Ret',
                align: 'left',
                value: 'retraso',
                width: '1%'
            },
            {
                text: 'Fase',
                align: 'left',
                value: 'fase.nombre'
            },
            {
                text: 'Acciones',
                align: 'Center',
                value: ''
            }
        ],
        items:[],
        status: false,
		registros: false,
        dialog: false,
        item_destroy: {},
        show_loading: true,
        url: "/compras/compras",
        ruta: "compra",

        filtro: false,

      }
    },
    mounted()
    {
        this.pagination.model="compra"+this.empresaActiva;
        if (this.getPagination.model == this.pagination.model)
            this.updatePosPagina(this.getPagination);
        else
            this.unsetPagination();

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
            'hasDelCom',
            'getPagination',
            'hasExcel',
            'hasWhatsApp',
            'hasEdtCli',
            'parametros',
            'whatsApp'
        ])
    },
    methods:{
        ...mapActions([
            'setPagination',
            'unsetPagination'
        ]),
        getDni(dni){
            return this.hasEdtCli ? dni : "******"+dni.substr(-4);
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
        formatDate(f){
            if (f == null) return null;
            moment.locale('es');
            return moment(f).format('DD/MM/YYYY');
        },
        create(){
            this.$router.push({ name: this.ruta+'.create'})
        },
        editItem (item) {

            this.setPagination(this.paginaActual);

            var ruta = item.tipo_id == 1 ? 'recompra' : 'compra';

            if (item.fase_id <= 2)
                this.$router.push({ name: this.ruta+'.edit', params: { id: item.id } })
            else
                this.$router.push({ name: ruta+'.close', params: { id: item.id } })

        },
        openDialog (item){
            this.dialog = true;
            this.item_destroy = item;
        },
        destroyReg () {
            this.dialog = false;

            axios.post(this.url+'/'+this.item_destroy.id,{_method: 'delete'})
                .then(res => {

                    const index = this.items.indexOf(this.item_destroy)
                    this.items.splice(index, 1)

                    if (res.data.estado)
                        this.$toast.success('Registro eliminado! '+res.data.msg);
                    else
                        this.$toast.warning(res.data.msg);


                })
            .catch(err => {
                this.status = true;

                var msg = err.response.data.message;
                this.$toast.error(msg);

            });

        },
        sendWhatsApp(item){
             axios.get('/compras/whatsapp/'+item.id)
                .then(res => {

                    //window.open(res.data,'Enviar WhatsApp','toolbar=no,resizable=no, width=400, height=500, scrollbar=no, status=no');
                    window.open(res.data,'_blank');
                     //window.location.href = res.data;
                })
                .catch(err => {
                    console.log(err);
                });
        },
        goExcel(){

            this.show_loading = true;
            axios({
                url: this.url+"/excel",
                method: 'POST',
                responseType: 'blob', // important
                data:{ data: this.items }
                })
            .then(response => {

                let blob = new Blob([response.data])
                let link = document.createElement('a')
                link.href = window.URL.createObjectURL(blob)

                link.download = "Compras."+new Date().getFullYear()+(new Date().getMonth()+1)+(new Date().getDate())+'.xlsx';

                document.body.appendChild(link);
                link.click()
                document.body.removeChild(link);

                this.$toast.success('Download Ok! '+link.download);

                this.show_loading = false;

            })
            .catch(err => {
                this.$toast.error(err.response.data.message);
                this.show_loading = false;
            });
        },
    }
  }
</script>
