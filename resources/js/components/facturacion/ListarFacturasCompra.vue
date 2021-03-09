<template>
	<div v-show="show">
        <loading :show_loading="show_loading"></loading>
        <v-card>
            <v-card-title color="indigo">
                <h2 color="indigo">{{titulo}}</h2>
                <v-spacer></v-spacer>
                <v-tooltip bottom>
                    <template v-slot:activator="{ on }">
                        <v-btn
                            v-if="lineas.length > 0"
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
            </v-card-title>
        </v-card>
        <v-card>
            <v-form>
                 <v-container>
                     <v-layout row wrap>
                        <v-flex sm5></v-flex>
                        <v-flex sm2>
                            <v-select
                            v-model="grupo_id"
                            v-validate="'required'"
                            data-vv-name="grupo_id"
                            data-vv-as="grupo"
                            :error-messages="errors.collect('grupo_id')"
                            :items="grupos"
                            label="Grupo"
                            ></v-select>
                        </v-flex>
                        <v-flex sm2>
                            <v-menu
                                v-model="menu_d"
                                :close-on-content-click="false"
                                :nudge-right="40"
                                lazy
                                transition="scale-transition"
                                offset-y
                                full-width
                                min-width="290px"
                            >
                                <v-text-field
                                    slot="activator"
                                    :value="computedFechaD"
                                    label="Desde"
                                    append-icon="event"
                                    v-validate="'date_format:dd/MM/yyyy'"
                                    data-vv-name="fecha_d"
                                    :error-messages="errors.collect('fecha_d')"
                                    data-vv-as="Desde"
                                    readonly
                                    ></v-text-field>
                                <v-date-picker
                                    v-model="fecha_d"
                                    no-title
                                    locale="es"
                                    first-day-of-week=1
                                    @input="menu_d = false"
                                    ></v-date-picker>
                            </v-menu>
                        </v-flex>
                        <v-flex sm2>
                            <v-menu
                                v-model="menu_h"
                                :close-on-content-click="false"
                                :nudge-right="40"
                                lazy
                                transition="scale-transition"
                                offset-y
                                full-width
                                min-width="290px"
                            >
                                <v-text-field
                                    slot="activator"
                                    :value="computedFechaH"
                                    label="Hasta"
                                    append-icon="event"
                                    v-validate="'date_format:dd/MM/yyyy'"
                                    data-vv-name="fecha_h"
                                    :error-messages="errors.collect('fecha_h')"
                                    data-vv-as="Hasta"
                                    readonly
                                    ></v-text-field>
                                <v-date-picker
                                    v-model="fecha_h"
                                    no-title
                                    locale="es"
                                    first-day-of-week=1
                                    @input="menu_h = false"
                                    ></v-date-picker>
                            </v-menu>
                        </v-flex>
                        <v-flex sm1>
                            <div class="text-xs-center">
                                <v-btn @click="submit" flat round small :loading="loading"  block  color="primary">
                                    Enviar
                                </v-btn>
                            </div>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-form>
            <v-container>
                <v-layout row wrap>
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
                    <v-flex xs12>
                        <v-data-table
                            :headers="headers"
                            :items="lineas"
                            :search="search"
                            @update:pagination="updateEventPagina"
                            :pagination.sync="pagination"
                            rows-per-page-text="Registros por página"
                        >
                            <template slot="items" slot-scope="props">
                                <tr>
                                    <td class="indigo--text darken-4">{{props.item.facser}}</td>
                                    <td class="indigo--text darken-4">{{getFecha(props.item.fecha_factura)}}</td>
                                    <td class="indigo--text darken-4">{{getFecha(props.item.fecha_compra)}}</td>
                                    <td class="indigo--text darken-4">{{ props.item.dni }}</td>
                                    <td class="indigo--text darken-4">{{ props.item.razon}}</td>
                                    <td class="indigo--text darken-4 text-xs-right">{{ props.item.pvp | currency('', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false }) }}</td>
                                    <td class="indigo--text darken-4 text-xs-right">{{ props.item.coste | currency('', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false }) }}</td>
                                    <td class="indigo--text darken-4 text-xs-right">{{ props.item.bene | currency('', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false }) }}</td>
                                    <td class="indigo--text darken-4 text-xs-right">{{ props.item.base | currency('', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false }) }}</td>
                                    <td class="indigo--text darken-4 text-xs-right">{{ props.item.iva | currency('', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false }) }}</td>
                                    <td class="justify-center layout px-0">
                                        <v-icon
                                            small
                                            color="gray"
                                            class="mr-2"
                                             @click="goCompra(props.item)"
                                        >
                                            edit
                                        </v-icon>
                                    </td>
                                </tr>
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
</template>
<script>
import moment from 'moment'
import Loading from '@/components/shared/Loading'
import {mapGetters} from 'vuex';
import {mapActions} from "vuex";

	export default {
		$_veeValidate: {
      		validator: 'new'
        },
        components: {
            'loading': Loading
		},
    	data () {
      		return {
                menu1: false,
                url: "/ventas/facturacion/compras/listar",
                ruta: "compra",

                menu_h: false,
                menu_d: false,
                fecha_d: new Date().toISOString().substr(0, 7)+"-01",
                fecha_h: new Date().toISOString().substr(0, 10),

                grupo_id:"",
                grupos:[],
                lineas:[],

        		status: false,
                loading: false,

                show: false,
                show_loading: true,
                titulo:'Relación de facturas de recuperaciones',

                parametros:{

                },

                search:"",
                paginaActual:{},
                pagination:{
                    model: "lisfaccom",
                    descending: true,
                    page: 1,
                    rowsPerPage: 10,
                    sortBy: "factura",
                },
                headers: [
                    {
                        text: 'Factura',
                        align: 'left',
                        value: 'factura',
                        width:'1%'
                    },
                    {
                        text: 'F.Factura',
                        align: 'left',
                        value: 'fecha_factura',
                        width:'1%'
                    },
                    {
                        text: 'F.Compra',
                        align: 'left',
                        value: 'fecha_compra',
                        width:'1%'
                    },
                    {
                        text: 'DNI',
                        align: 'left',
                        value: 'dni',
                        width:'1%'
                    },
                    {
                        text: 'Cliente',
                        align: 'left',
                        value: 'razon',
                        width:'10%'
                    },
                    {
                        text: 'PVP',
                        align: 'center',
                        value: 'pvp',
                        width:'2%'
                    },
                    {
                        text: 'Coste',
                        align: 'center',
                        value: 'coste',
                        width:'2%'
                    },
                     {
                        text: 'Beneficio',
                        align: 'center',
                        value: 'bene',
                        width:'2%'
                    },
                    {
                        text: 'Base',
                        align: 'center',
                        value: 'base',
                        width:'2%'
                    },
                    {
                        text: 'IVA',
                        align: 'center',
                        value: 'iva',
                        width:'1%'
                    },
                    {
                        text: 'A',
                        align: 'Center',
                        value: '',
                        sortable: false,
                        width:'1%'
                    }
                ]
      		}
        },
        beforeMount(){
            if (this.getPagination.model == this.pagination.model)
                if (this.getLineasIndex.length > 0){
                    this.lineas = this.getLineasIndex;
                }
        },
        mounted(){

            axios.get('/ventas/facturacion/listar')
                .then(res => {

                    this.grupos = res.data.grupos;
                    this.grupo_id = res.data.grupos[0].value;

                    if (this.getPagination.model == this.pagination.model)
                        this.updatePosPagina(this.getPagination);
                    else
                        this.unsetPagination();

                    this.show = true;
                    this.show_loading = false;
                })
                .catch(err => {
                    this.$toast.error(err.response.data.message);
                    this.$router.push({ name:'dash'})
                })

        },
        computed: {
            ...mapGetters([
                    'getPagination',
                    'getLineasIndex',

                ]),
            computedFechaD() {
                moment.locale('es');
                return this.fecha_d ? moment(this.fecha_d).format('L') : '';
            },
            computedFechaH() {
                moment.locale('es');
                return this.fecha_h ? moment(this.fecha_h).format('L') : '';
            },
        },
    	methods:{
            ...mapActions([
                'setPagination',
                'unsetPagination',
                'setResult'
            ]),
            getFecha(f){
                moment.locale('es');
                return f ? moment(f).format('DD/MM/YYYY') : '';
            },
            updateEventPagina(objPagina){

                this.paginaActual = objPagina;

            },
            updatePosPagina(miPagina){

                this.pagination.page = miPagina.page;
                this.pagination.descending = miPagina.descending;
                this.pagination.rowsPerPage= miPagina.rowsPerPage;
                this.pagination.sortBy = miPagina.sortBy;


            },
            goCompra(item) {

                this.setResult(this.lineas);

                 this.setPagination(this.paginaActual);

                var ruta = item.tipo_id == 1 ? 'recompra' : 'compra';

                this.$router.push({ name: ruta+'.close', params: { id: item.id } })

            },
            goExcel(){
                this.show_loading = true;
                axios({
                            url: this.url+"/excel",
                            method: 'POST',
                            responseType: 'blob', // important
                            data:{ data: this.lineas }
                            })
                        .then(response => {

                            let blob = new Blob([response.data])
                            let link = document.createElement('a')
                            link.href = window.URL.createObjectURL(blob)

                            link.download = "Facturas."+new Date().getFullYear()+(new Date().getMonth()+1)+(new Date().getDate())+'.xlsx';

                            document.body.appendChild(link);
                            link.click()
                            document.body.removeChild(link);

                            this.$toast.success('Facturas generado correctamente'+link.download);

                            this.show_loading = false;

                        })
                        .catch(err => {
                            this.$toast.error(err.response.data.message);
                            this.show_loading = false;
                        });
            },
            submit() {
                if (this.loading === false){

                    this.loading = true;
                    this.show_loading = true;

                    this.$validator.validateAll().then((result) => {
                        if (result){
                            axios.post(this.url, {
                                grupo_id: this.grupo_id,
                                fecha_d: this.fecha_d,
                                fecha_h: this.fecha_h,
                            })
                                .then(res => {

                                    this.lineas = res.data;

                                    //this.$toast.success(res.data);
                                    this.show_loading = false;
                                    this.loading = false;

                                })
                                .catch(err => {

                                    if (err.request.status == 422){ // fallo de validated.
                                        const msg_valid = err.response.data.errors;
                                        for (const prop in msg_valid) {
                                            this.errors.add({
                                                field: prop,
                                                msg: `${msg_valid[prop]}`
                                            })
                                        }
                                    }else{
                                        this.$toast.error(err.response.data.message);
                                    }
                                    this.loading = this.show_loading = false;
                                });
                            }
                        else{
                            this.loading = this.show_loading = false;
                        }
                    });
                }

            },

        }
  }
</script>

<style scoped>

table.v-table tbody td, table.v-table tbody th {
    height: 38px;
}
table.v-table tbody td:first-child, table.v-table tbody td:not(:first-child), table.v-table tbody th:first-child, table.v-table tbody th:not(:first-child), table.v-table thead td:first-child, table.v-table thead td:not(:first-child), table.v-table thead th:first-child, table.v-table thead th:not(:first-child){
    padding: 0 2px;
}
</style>
