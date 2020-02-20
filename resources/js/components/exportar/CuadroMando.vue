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
                            v-show="items_comprados.length > 0"
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
                            v-show="items_comprados.length > 0"
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
                        <!-- <v-flex sm2>
                            <v-select
                                v-model="detalle"
                                v-validate="'required'"
                                data-vv-name="detalle"
                                data-vv-as="detalle"
                                :error-messages="errors.collect('detalle')"
                                :items="detalles"
                                label="Detalle"
                                required
                                ></v-select>
                        </v-flex> -->
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
                        <v-flex sm2>
                            <v-switch
                                label="Totales"
                                v-model="totales"
                                color="primary"
                            ></v-switch>
                        </v-flex>

                        <v-flex sm2>
                            <v-btn @click="submit" :loading="show_loading" flat round small block  color="info">
                                Enviar
                            </v-btn>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap v-if="items_comprados.length>0">
                        <v-flex xs12>
                            <div>
                                <table class="v-datatable v-table theme--light">
                                    <thead>
                                        <tr>
                                            <th>Concepto</th>
                                            <th>Clase</th>
                                            <th>Peso</th>
                                            <th>Importe</th>
                                            <th>Imp. Gr.</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(item, index) in items_comprados" :key="index">
                                            <td>{{item.tipo}}</td>
                                            <td v-if="item.quilates > 0">{{ item.clase+" "+item.quilates+" K" }}</td>
                                            <td v-else>{{ item.clase }}</td>
                                            <td class="text-xs-right">{{ item.peso_gr | currency('', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false })}}</td>
                                            <td class="text-xs-right">{{ item.importe | currency('€', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false })}}</td>
                                            <td class="text-xs-right">{{ getPrecioGramo(item.peso_gr,item.importe) | currency('€', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false })}}</td>
                                        </tr>

                                        <tr v-for="(item, index) in items_liquidados" :key="miKey(index,'L')">
                                            <td>LIQUIDADO BRUTO</td>
                                            <td v-if="item.quilates > 0">{{ item.clase+" "+item.quilates+" K" }}</td>
                                            <td v-else>{{ item.clase }}</td>
                                            <td class="text-xs-right">{{ item.peso_gr | currency('', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false })}}</td>
                                            <td class="text-xs-right">{{ item.importe | currency('€', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false })}}</td>
                                            <td class="text-xs-right">{{ getPrecioGramo(item.peso_gr,item.importe) | currency('€', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false })}}</td>
                                        </tr>
                                        <tr v-for="(item, index) in items_inventariados" :key="miKey(index,'I')">
                                            <td>NUEVOS PRODUCTOS</td>
                                            <td v-if="item.quilates > 0">{{ item.clase+" "+item.quilates+" K" }}</td>
                                            <td v-else>{{ item.clase }}</td>
                                            <td class="text-xs-right">{{ item.peso_gr | currency('', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false })}}</td>
                                            <td class="text-xs-right">{{ item.importe | currency('€', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false })}}</td>
                                            <td class="text-xs-right">{{ getPrecioGramo(item.peso_gr,item.importe) | currency('€', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false })}}</td>
                                        </tr>
                                        <tr v-for="(item, index) in items_liquidado_neto" :key="miKey(index,'N')">
                                            <td>LIQUIDADO NETO</td>
                                            <td v-if="item.quilates > 0">{{ item.clase+" "+item.quilates+" K" }}</td>
                                            <td v-else>{{ item.clase }}</td>
                                            <td class="text-xs-right">{{ item.peso_gr | currency('', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false })}}</td>
                                            <td class="text-xs-right">{{ item.importe | currency('€', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false })}}</td>
                                            <td class="text-xs-right">{{ getPrecioGramo(item.peso_gr,item.importe) | currency('€', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false })}}</td>
                                        </tr>
                                        <tr v-for="(item, index) in items_ventas" :key="miKey(index,'V')">
                                            <td>{{detalle_ven(item.tipo_id)}}</td>
                                            <td v-if="item.quilates > 0">{{ item.clase+" "+item.quilates+" K" }}</td>
                                            <td v-else>{{ item.clase }}</td>
                                            <td v-if="item.tipo_id != 3" class="text-xs-right">{{ item.peso_gr | currency('', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false })}}</td>
                                            <td v-else></td>
                                            <td class="text-xs-right">{{ item.importe | currency('€', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false })}}</td>
                                            <td v-if="item.tipo_id != 3" class="text-xs-right">{{ getPrecioGramo(item.peso_gr,item.importe) | currency('€', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false })}}</td>
                                            <td v-else></td>
                                        </tr>
                                        <tr v-for="(item, index) in items_depositos" :key="miKey(index,'D')">
                                            <td>{{detalle_dep(item.tipo_id)}}</td>
                                            <td v-if="item.quilates > 0">{{ item.clase+" "+item.quilates+" K" }}</td>
                                            <td v-else>{{ item.clase }}</td>
                                            <td class="text-xs-right">{{ item.peso_gr | currency('', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false })}}</td>
                                            <td class="text-xs-right">{{ item.importe | currency('€', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false })}}</td>
                                            <td class="text-xs-right">{{ getPrecioGramo(item.peso_gr,item.importe) | currency('€', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false })}}</td>
                                        </tr>
                                        <tr v-for="(item, index) in items_inventario" :key="miKey(index,'X')">
                                            <td>INVENTARIO</td>
                                            <td>{{ item.clase }}</td>
                                            <td class="text-xs-right">{{ item.peso_gr | currency('', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false })}}</td>
                                            <td class="text-xs-right">{{ item.importe | currency('€', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false })}}</td>
                                            <td class="text-xs-right">{{ getPrecioGramo(item.peso_gr,item.importe) | currency('€', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false })}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
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
            detalles:[
                {value: '1', text:"Compras"},
                {value: '2', text:"Liquidado Bruto"},
                {value: '3', text:"Nuevos Productos"},
                {value: '4', text:"Liquidado Neto"},
                {value: '5', text:"Ventas"},
                {value: '6', text:"Depósitos"},
                {value: '7', text:"Inventario"},
                {value: 'T', text:"Todo"},
            ],
            detalle: 'T',
            operaciones:[
                    {value: 'F', text:"Facturadas"},
                    {value: 'T', text:"Todas"},
                ],
            totales: false,
            facturado: 'T',
            show_loading: false,
            ejercicio:new Date().toISOString().substr(0, 4),
            show_filtro: true,
            items_comprados: [],
            items_inventariados: [],
            items_liquidado_neto:[],
            items_depositos:[],
            fecha_d: new Date().toISOString().substr(0, 7)+"-01",
            //fecha_d: "2019-01-01",
            fecha_h: new Date().toISOString().substr(0, 10),
            menu_d: false,
            menu_h: false,
            titulo: 'Cuadro de Mando',
            key_id: 0,
      }
    },
    computed: {
        ...mapGetters([
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
        miKey(index,key){
            return key+index;
        },
        detalle_ven(t){
            if (t==3)
                return "VENTAS REBU + RESERVAS";
            else if(t==4)
                return "VENTAS RG";
            else if(t==5)
                return "VENTAS TALLER";
            else return "";
        },
         detalle_dep(t){
            if (t==1)
                return "DEPÓSITO RECOMPRAS (AC)";
            else if(t==2)
                return "DEPÓSITO COMPRAS (AC)"; // SON valores acumulados hasta fecha.
            else return "";
        },
        getPrecioGramo(gr, imp){
            return (gr != 0) ? (parseFloat(imp) / parseFloat(gr)).toFixed(2) : "";
        },
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
                            url: '/exportar/mando',
                            method: 'POST',
                            data:{
                                fecha_d: this.fecha_d,
                                fecha_h: this.fecha_h,
                                totales:  this.totales,
                                facturado: this.facturado
                            }
                            })
                        .then(res => {
                            this.items_comprados = res.data.comprados;
                            this.items_liquidados = res.data.liquidados;
                            this.items_inventariados = res.data.inventariados;
                            this.items_liquidado_neto = res.data.liquidado_neto;
                            this.items_ventas = res.data.ventas;
                            this.items_depositos = res.data.depositos;
                            this.items_inventario = res.data.inventario;

                            if (this.items_comprados.length > 0)
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
                url: "/exportar/mando/excel",
                method: 'POST',
                responseType: 'blob', // important
                data:{
                        data_com: this.items_comprados,
                        data_inv: this.items_inventariados,
                        data_liq: this.items_liquidados,
                        data_ven: this.items_ventas,
                        data_dep: this.items_depositos,
                    }
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
