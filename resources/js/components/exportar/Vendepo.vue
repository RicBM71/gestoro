<template>
    <div>
        <v-card>
            <loading :show_loading="show_loading"></loading>
            <v-card-title color="indigo">
                <h2 color="indigo">{{titulo}}</h2>
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
        <v-card>
            <v-form>
                <v-container>
                    <v-layout row wrap v-show="show_filtro">
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
                        <v-flex sm2>
                            <v-select
                                v-model="facturado"
                                v-validate="'required'"
                                data-vv-name="facturado"
                                data-vv-as="facturado"
                                :error-messages="errors.collect('facturado')"
                                :items="operaciones"
                                label="Ventas"
                                required
                                ></v-select>
                        </v-flex>
                        <v-flex sm3>
                            <v-select
                                v-model="tipo"
                                v-validate="'required'"
                                data-vv-name="tipo"
                                data-vv-as="tipo"
                                :error-messages="errors.collect('tipo')"
                                :items="tipos"
                                label="Tipo venta"
                                required
                                ></v-select>
                        </v-flex>
                        <v-flex sm1>
                            <v-btn @click="submit" :loading="show_loading" flat round small block  color="info">
                                Enviar
                            </v-btn>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap v-if="items.length>0">
                        <v-flex xs12>
                            <v-data-table
                                :pagination.sync="pagination"
                                :headers="headers"
                                :items="items"
                                rows-per-page-text="Registros por página"
                            >
                                <template slot="items" slot-scope="props">
                                    <td>{{ props.item.empresa }}</td>
                                    <td>{{ props.item.sigla }}/{{ props.item.procede}}</td>
                                    <td>{{ props.item.serie+props.item.numero }}</td>
                                    <td>{{ formatDate(props.item.fecha) }}</td>
                                    <td>{{ props.item.referencia }}</td>
                                    <td>{{ props.item.producto }}</td>
                                    <td class="text-xs-right">{{ props.item.importe_venta | currency('€', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false })}}</td>
                                    <td class="text-xs-right">{{ props.item.precio_coste | currency('€', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false })}}</td>
                                </template>
                                <template slot="pageText" slot-scope="props">
                                    Registros {{ props.pageStart }} - {{ props.pageStop }} de {{ props.itemsLength }}
                                </template>
                            </v-data-table>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-form>
        </v-card>
    </div>
</template>
<script>
import moment from 'moment'
import {mapGetters} from 'vuex';
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
                text: 'Proveedor',
                align: 'left',
                value: 'empresa',
                width: '15%'
            },
            {
                text: 'Emp/Vendido',
                align: 'left',
                value: 'sigla',
                width: '1%'
            },
            {
                text: 'Alb/Fac',
                align: 'left',
                value: 'numero',
                width: '10%'
            },
            {
                text: 'Fecha',
                align: 'left',
                value: 'fecha',
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
            },
            {
                text: 'P. Venta',
                align: 'right',
                value: 'importe_venta',
                width: '10%'
            },
            {
                text: 'P. Coste',
                align: 'right',
                value: 'precio_coste',
                width: '10%'
            }],
            pagination:{
                rowsPerPage: 10,
            },
            operaciones:[
                    {value: 'F', text:"Facturados"},
                    {value: 'V', text:"No Facturados"},
                ],
            tipo: 'D',
            tipos:[
                    {value: 'D', text:"Depósito"},
                    {value: 'P', text:"Proveedor"},
                    // {value: 'S', text:"Sin Reubicar"},
                    // {value: 'R', text:"Reubicados"},
                    // {value: 'T', text:"Todas"},
                ],
            facturado: 'V',
            show_loading: false,
            ejercicio:new Date().toISOString().substr(0, 4),
            show_filtro: true,
            items: [],
            fecha_d: new Date().toISOString().substr(0, 7)+"-01",
            fecha_h: new Date().toISOString().substr(0, 10),
            menu_d: false,
            menu_h: false,
            titulo: 'Ventas en depósito'
      }
    },
    computed: {
        ...mapGetters([
            'isAdmin',
        ]),
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
        formatDate(f){
            if (f == null) return null;
            moment.locale('es');
            return moment(f).format('DD/MM/YYYY');
        },
        submit(){

            if (this.show_loading === false){
                this.show_loading = true;
                this.$validator.validateAll().then((result) => {
                    if (result){

                        axios({
                            url: '/exportar/vendepo',
                            method: 'POST',
                            data:{
                                fecha_d: this.fecha_d,
                                fecha_h: this.fecha_h,
                                facturado: this.facturado,
                                tipo: this.tipo
                            }
                            })
                        .then(res => {

                            this.items = res.data.registros;
                            this.titulo = res.data.titulo;

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
        goExcel(){

            this.show_loading = true;
            axios({
                url: "/exportar/vendepo/excel",
                method: 'POST',
                responseType: 'blob', // important
                data:{ data: this.items }
                })
            .then(response => {

                let blob = new Blob([response.data])
                let link = document.createElement('a')
                link.href = window.URL.createObjectURL(blob)

                link.download = "Ventas."+new Date().getFullYear()+(new Date().getMonth()+1)+(new Date().getDate())+'.xlsx';

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
