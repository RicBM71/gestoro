<template>
    <div>
        <v-card>
            <loading :show_loading="show_loading"></loading>
            <v-card-title color="indigo">
                <h2 color="indigo">Detalle de Ventas</h2>
                <v-spacer></v-spacer>
                <v-tooltip bottom>
                    <template v-slot:activator="{ on }">
                        <v-btn
                            v-show="items.length > 0"
                            v-on="on"
                            color="white"
                            icon
                            @click="show_filtro = !show_filtro"
                        >
                            <v-icon color="primary">filter_list</v-icon>
                        </v-btn>
                    </template>
                    <span>Selección</span>
                </v-tooltip>
                <v-tooltip bottom>
                    <template v-slot:activator="{ on }">
                        <v-btn
                            v-show="items.length > 0"
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
         <v-card v-show="show_filtro">
            <v-form>
                <v-container>
                    <v-layout row wrap>
                        <v-spacer></v-spacer>
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
                                    :label="desde"
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
                                    :label="hasta"
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
                        <v-flex sm2>
                            <v-select
                                v-model="tipo_id"
                                v-validate="'required'"
                                data-vv-name="tipo_id"
                                data-vv-as="tipo"
                                :error-messages="errors.collect('tipo_id')"
                                :items="tipos"
                                label="Tipo"
                                required
                                ></v-select>
                        </v-flex>
                        <v-flex sm2>
                            <v-select
                                v-model="operacion"
                                v-validate="'required'"
                                data-vv-name="operacion"
                                data-vv-as="operación"
                                :error-messages="errors.collect('operacion')"
                                :items="operaciones"
                                label="Albaranes"
                                required
                                ></v-select>
                        </v-flex>
                        <v-flex sm1>
                            <v-btn @click="submit" :loading="show_loading" flat round small block  color="info">
                                Enviar
                            </v-btn>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-form>
        </v-card>
        <v-card v-if="items.length > 0">
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
                            :search="search"
                            @update:pagination="updateEventPagina"
                            :pagination.sync="pagination"
                            :items="items"
                            rows-per-page-text="Registros por página"
                        >
                            <template slot="items" slot-scope="props">
                                <td>{{alb_ser(props.item)}}</td>
                                <td>{{ formatDate(props.item.fecha) }}</td>
                                <td>{{ props.item.referencia }}</td>
                                <td>{{ props.item.producto }}</td>
                                <td>{{ clase(props.item) }}</td>
                                <td class="text-xs-right">{{ props.item.precio_coste | currency('', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false })}}</td>
                                <td class="text-xs-right">{{ props.item.importe_venta| currency('', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false })}}</td>
                                <td class="text-xs-right">{{ props.item.margen| currency('', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false })}}</td>
                                <td v-if="props.item.efectivo == 0 && props.item.banco == 0"></td>
                                <td v-else class="text-xs-right">{{ props.item.efectivo| currency('', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false })}}</td>
                                <td v-if="props.item.efectivo == 0 && props.item.banco == 0"></td>
                                <td v-else class="text-xs-right">{{ props.item.banco| currency('', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false })}}</td>
                                <td class="justify-center layout px-0">
                                    <v-icon
                                        small
                                        class="mr-2"
                                        @click="editItem(props.item)"
                                    >
                                        edit
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
</template>
<script>
import moment from 'moment'
import {mapGetters} from 'vuex'
import {mapActions} from "vuex";
import Loading from '@/components/shared/Loading'
export default {
    $_veeValidate: {
        validator: 'new'
    },
    components: {
        'loading': Loading
    },
    data () {
      return {
            headers: [
            {
                text: 'Albarán',
                align: 'left',
                value: 'albaran',
                width: '10%'
            },
            {
                text: 'Fecha',
                align: 'left',
                value: 'fecha_albaran',
                width: '10%'
            },
            {
                text: 'Referencia',
                align: 'left',
                value: 'referencia',
                 width: '10%'
            },
            {
                text: 'Producto',
                align: 'left',
                value: 'producto',
                width: '30%'
            },
             {
                text: 'Clase',
                align: 'left',
                value: 'clase',
                width: '8%'
            },
            {
                text: 'P. Coste',
                align: 'right',
                value: 'precio_coste',
                width: '4%'
            },
            {
                text: 'Importe',
                align: 'right',
                value: 'importe_venta',
                width: '4%'
            },
            {
                text: 'Márgen',
                align: 'right',
                value: 'margen',
                width: '4%'
            },
            {
                text: 'Efectivo',
                align: 'right',
                value: 'efectivo',
                width: '4%'
            },
            {
                text: 'Banco',
                align: 'right',
                value: 'banco',
                width: '4%'
            },
            {
                text: 'Acciones',
                align: 'Center',
                value: 'id',
                width: '1%'
            }],
            search:"",
            paginaActual:{},
            pagination:{
                model: "detaven",
                descending: true,
                page: 1,
                rowsPerPage: 10,
                sortBy: "fecha",
            },

            tipos:[],
            clases: [],
            tipo_id: 3,
            clase_id: 1,
            operacion: 'T',
            operaciones:[
                {value: 'T', text:"Todos"},
                {value: 'F', text:"Facturados"},
                {value: 'N', text:"No Facturados"},
            ],

            show_loading: true,
            ejercicio:new Date().toISOString().substr(0, 4),
            show_filtro: true,
            items: [],
            fecha_d: new Date().toISOString().substr(0, 7)+"-01",
            fecha_h: new Date().toISOString().substr(0, 10),
            menu_d: false,
            menu_h: false,
      }
    },
     beforeMount(){
        // if (this.getLineasIndex.length > 0)
        //     this.items = this.getLineasIndex;
    },
    mounted(){

         axios.get('/exportar/detaven')
            .then(res => {
                this.tipos = res.data.tipos;
                this.clases = res.data.clases;

            })
            .catch(err =>{
                this.$toast.error(err.response.data.message);
                this.$router.push({ name: 'dash' })
            })
            .finally(()=> {
                if (this.getPagination.model == this.pagination.model){
                    this.updatePosPagina(this.getPagination);
                    this.show_filtro = false;
                }
                else
                    this.unsetPagination();
                this.show_loading = false;
            });

    },
    computed: {
        ...mapGetters([
            'getPagination',
            'getLineasIndex',
        ]),
        desde(){
            return this.operacion == "F" ? 'Desde F. Factura' : 'Desde F. Albarán';
        },
        hasta(){
            return this.operacion == "F" ? 'Hasta F. Factura' : 'Hasta F. Albarán';
        },
        computedFechaD() {
            moment.locale('es');
            return this.fecha_d ? moment(this.fecha_d).format('L') : '';
        },
        computedFechaH() {
            moment.locale('es');
            return this.fecha_h ? moment(this.fecha_h).format('L') : '';
        }
    },
    methods:{
        ...mapActions([
            'setPagination',
            'unsetPagination',
            'setResult'
        ]),
        formatDate(f){
            if (f == null) return null;

            moment.locale('es');
            return moment(f).format('DD/MM/YYYY');
        },
        alb_ser(item){
            var s = '0'.repeat(5 - item.albaran.toString().length);

            return item.serie_albaran+s+item.albaran;
        },
        clase(item){
            return item.clase;
        },
        editItem (item) {

            this.setPagination(this.paginaActual);


            this.$router.push({ name: 'albaran.edit', params: { id: item.albaran_id } })

        },
        submit(){


            if (this.show_loading === false){
                this.show_loading = true;
                this.$validator.validateAll().then((result) => {
                    if (result){

                        axios({
                            url: '/exportar/detaven',
                            method: 'POST',
                            data:{
                                fecha_d: this.fecha_d,
                                fecha_h: this.fecha_h,
                                tipo_id: this.tipo_id,
                                operacion: this.operacion,
                            }
                            })
                        .then(res => {

                            this.items = res.data;

                            this.setResult(this.items);

                            if (this.items.length > 0)
                                this.show_filtro = false;
                            else
                                this.$toast.warning('No hay registros!');

                        })
                        .catch(err => {
                            if (err.request.status == 422){ // fallo de validated.
                                const msg_valid = err.response.data.errors;
                                for (const prop in msg_valid) {
                                    // this.$toast.error(`${msg_valid[prop]}`);

                                    this.errors.add({
                                        field: prop,
                                        msg: `${msg_valid[prop]}`
                                    })
                                }
                            }else{
                                this.$toast.error(err.response.data.message);
                            }
                        })
                        .finally(()=> {
                             this.show_loading = false;
                        });
                    }

                });

            }
        },
        updatePosPagina(pag){

            this.pagination.page = pag.page;
            this.pagination.descending = pag.descending;
            this.pagination.rowsPerPage= pag.rowsPerPage;
            this.pagination.sortBy = pag.sortBy;

        },
        updateEventPagina(obj){

            this.paginaActual = obj;

        },
        goExcel(){

            this.show_loading = true;
            axios({
                url: "/exportar/detaven/excel",
                method: 'POST',
                responseType: 'blob', // important
                data:{ data: this.items }
                })
            .then(response => {

                let blob = new Blob([response.data])
                let link = document.createElement('a')
                link.href = window.URL.createObjectURL(blob)

                link.download = "Detalle Ventas."+new Date().getFullYear()+(new Date().getMonth()+1)+(new Date().getDate())+'.xlsx';

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
