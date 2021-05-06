
<template>
    <div>
        <loading :show_loading="show_loading"></loading>
        <div v-if="registros">
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
                                @click="goProcesar()"
                            >
                                <v-icon color="primary">mdi-earth</v-icon>
                            </v-btn>
                        </template>
                        <span>Comprobar pedidos ahora</span>
                    </v-tooltip>
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
                    <menu-ope :albaran="albaran"></menu-ope>
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
                            :items="items"
                            :search="search"
                            :expand="expand"
                            @update:pagination="updateEventPagina"
                            :pagination.sync="pagination"
                            rows-per-page-text="Registros por página"
                            >
                                <template slot="items" slot-scope="props">
                                    <td>{{ props.item.empresa.titulo }}</td>
                                    <td>{{ props.item.alb_ser }}</td>
                                    <td>{{ formatDate(props.item.fecha_albaran) }}</td>
                                    <td>{{ props.item.pedido }}</td>
                                    <td>{{ getDni(props.item.cliente.dni) }}</td>
                                    <td>{{ getNombre(props.item) }}</td>
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
import Loading from '@/components/shared/Loading'
import MenuOpe from './MenuOpe'
import {mapGetters} from 'vuex'
import {mapActions} from "vuex";
  export default {
    components: {
        'menu-ope': MenuOpe,
        'loading': Loading,
    },
    data () {
      return {
        titulo: "Albaranes procedentes eCommerce",
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
                text: 'Empresa',
                align: 'left',
                value: 'empresa_id',
                width: '8%'
            },
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
        url: "/ecommerce/pedidos",
        ruta: "ecommerce",

        filtro: false,

      }
    },
    beforeMount()
    {
        this.pagination.model="ecommerce"+this.empresaActiva;
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
        goProcesar(){
            this.show_loading = true;
            axios.get('/ecommerce/manual')
                .then(res => {
                    this.items = res.data.pendientes;
                    if (res.data.procesados > 0)
                        this.$toast.success('Procesados '+res.data.procesados+' nuevos!');
                    else
                        this.$toast.warning('No hay nuevos pedidos!');
                    this.registros = true;
                    this.show_loading = false;
                })
                .catch(err =>{

                    this.$toast.error(err.response.data.message);
                    this.$router.push({ name: 'dash' })
                })

        },
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
        editItem (item) {

            this.setPagination(this.paginaActual);

            this.$router.push({ name: 'albaran.edit', params: { id: item.id } })


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
