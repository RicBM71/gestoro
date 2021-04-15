
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
                    <menu-ope :albaran="albaran"></menu-ope>
                </v-card-title>
            </v-card>
            <v-card v-show="filtro">
                 <filtro-alb :filtro.sync="filtro" :reg.sync="items"></filtro-alb>
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
                                    <td>{{ props.item.alb_ser }}</td>
                                    <td>{{ formatDate(props.item.fecha_albaran) }}</td>
                                    <td>{{ props.item.pedido }}</td>
                                    <td>{{ getDni(props.item.cliente.dni) }}</td>
                                    <td>{{ getNombre(props.item) }}</td>
                                    <td class="text-xs-right">{{ totalImpLinea(props.item.albalins) | currency('€', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false })}}</td>
                                    <td>{{ props.item.fac_ser }}</td>
                                    <td>{{ formatDate(props.item.fecha_factura) }}</td>
                                    <td :class="props.item.fase.color">{{ props.item.fase.nombre }}</td>
                                    <td class="justify-center layout px-0">
                                        <v-icon
                                            v-show="showNotas(props.item)"
                                            small
                                            class="mr-2"
                                            @click="props.expanded = !props.expanded"
                                        >
                                            visibility
                                        </v-icon>
                                        <v-icon
                                            small
                                            class="mr-2"
                                            @click="editItem(props.item)"
                                        >
                                            edit
                                        </v-icon>


                                        <v-icon v-if="puedeBorrar(props.item)"
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
                                            {{ misNotas(props.item) }}
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
import FiltroAlb from './FiltroAlb'
import {mapGetters} from 'vuex'
import {mapActions} from "vuex";
  export default {
    components: {
        'my-dialog': MyDialog,
        'menu-ope': MenuOpe,
        'loading': Loading,
        'filtro-alb': FiltroAlb
    },
    data () {
      return {
        titulo: "Albaranes",
        albaran:{
            id: 0,
            cliente_id: 0
        },
        expand: false,
        paginaActual:{},
        pagination:{
            model: "venta",
            descending: true,
            page: 1,
            rowsPerPage: 10,
            sortBy: "alb_ser",
        },
        search:"",
        headers: [
            {
                text: 'Albarán',
                align: 'left',
                value: 'alb_ser',
                width: '8%'
            },
            {
                text: 'F. Albarán',
                align: 'left',
                value: 'fecha_albaran',
                width: '8%'
            },
            {
                text: 'Pedido',
                align: 'left',
                value: 'pedido',
                width: '6%'
            },
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
                text: 'Importe',
                align: 'left',
                value: 'importe',
                width: '10%'
            },
            {
                text: 'Factura',
                align: 'left',
                value: 'fac_ser',
                width: '12%'
            },
            {
                text: 'F. Factura',
                align: 'left',
                value: 'fecha_factura',
                width: '8%'
            },
            {
                text: 'Fase',
                align: 'left',
                value: 'fase.nombre',
                width: '15%'
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
        url: "/ventas/albaranes",
        ruta: "albaran",

        filtro: false,

      }
    },
    beforeMount()
    {
        this.pagination.model="venta"+this.empresaActiva;
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
            'hasDelAlb',
            'getPagination',
            'hasEdtCli',
            'hasExcel',
            'userName'
        ])
    },
    methods:{
        ...mapActions([
            'setPagination',
            'unsetPagination'
        ]),
        puedeBorrar(item){

            if (item.factura > 0) return false;

            return (this.esPropietario(item) || this.hasDelAlb);


        },
        esPropietario(item){

             const hoy = new Date().toISOString().substr(0, 10);
             return (item.created_at.substr(0, 10) == hoy && item.username == this.userName);

        },
        getNombre(item){
            return (item.clitxt == null || item.clitxt == '') ? item.cliente.razon : item.clitxt;
        },
        showNotas(item){

            return (item.notas_int > '')
        },
        misNotas(item){
            //var n1 = item.clitxt != null ? item.clitxt : "";
            var n2 = item.notas_int != null ? item.notas_int : "";

            return n2;
        },
        totalImpLinea(lineas){

            var total = 0;
            lineas.map((lin) =>
            {
                var imp = parseFloat(lin.importe_venta);
                total += imp;

            })
            return total.toFixed(2);
        },
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

            this.$router.push({ name: 'albaran.edit', params: { id: item.id } })


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

                link.download = "Albaranes."+new Date().getFullYear()+(new Date().getMonth()+1)+(new Date().getDate())+'.xlsx';

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
