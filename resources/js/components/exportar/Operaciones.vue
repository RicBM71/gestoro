<template>
    <div>
        <v-card>
            <loading :show_loading="show_loading"></loading>
            <v-card-title color="indigo">
                <h2 color="indigo">Operaciones por Empresa</h2>
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
                            v-show="items.length > 0 && isGestor"
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
                         <v-flex md2 sm2 xs6>
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
                        <v-flex md2 sm2 xs6>
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
                        <v-flex md2 sm2 xs12>
                            <v-select
                                v-model="operacion"
                                v-validate="'required'"
                                data-vv-name="operacion"
                                data-vv-as="operacion"
                                :error-messages="errors.collect('operacion')"
                                :items="operaciones"
                                label="Operación"
                                required
                                ></v-select>
                        </v-flex>
                        <v-flex md2 sm2 xs12>
                            <v-btn @click="submit" :loading="show_loading" flat round small block  color="info">
                                Enviar
                            </v-btn>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap v-if="items.length>0">
                        <v-flex xl12 lg12 md12 sm12 xs12>
                            <v-data-table
                                :pagination.sync="pagination"
                                :headers="headers"
                                :items="items"
                                rows-per-page-text="Registros por página"
                            >
                                <template slot="items" slot-scope="props">
                                    <tr :class="colorLin(props.item)">
                                        <td v-if="props.item.concepto != 'TOTAL'">{{ props.item.empresa }}</td>
                                        <td v-else></td>
                                        <td>{{ props.item.tipo }}</td>
                                        <td>{{ props.item.fase }}</td>
                                        <td>{{ clase(props.item) }}</td>
                                        <td class="text-xs-right">{{ props.item.operaciones | currency(' ', 0, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false })}}</td>
                                        <td class="text-xs-right">{{ props.item.importe | currency('€', 0, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false })}}</td>
                                        <td class="text-xs-right">{{ props.item.peso | currency(' ', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false })}}</td>
                                        <td class="text-xs-right">{{ peso_gr(props.item) | currency(' ', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false })}}</td>
                                    </tr>
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
                text: 'Empresa',
                align: 'left',
                value: 'empresa',
            },
            {
                text: 'Tipo',
                align: 'left',
                value: 'tipo',
                width: '15%'
            },
            {
                text: 'Fase',
                align: 'left',
                value: 'fase',
                width: '12%'
            },
            {
                text: 'Clase',
                align: 'left',
                value: 'clase',
                width: '10%'
            },
            {
                text: 'Operaciones',
                align: 'right',
                value: 'operaciones',
                width: '10%'
            },
            {
                text: 'Importe',
                align: 'right',
                value: 'importe',
                width: '10%'
            },
            {
                text: 'Peso Gr.',
                align: 'right',
                value: 'peso',
                width: '10%'
            },
            {
                text: 'Imp Gr.',
                align: 'right',
                value: '',
                width: '10%'
            }],
            pagination:{
                rowsPerPage: 10,
            },
            operaciones:[
                    {value: 'C', text:"Compras"},
                    {value: 'V', text:"Ventas"},
                ],
            operacion: 'C',
            show_loading: false,
            ejercicio:new Date().toISOString().substr(0, 4),
            show_filtro: true,
            items: [],
            fecha_d: new Date().toISOString().substr(0, 10),
            fecha_h: new Date().toISOString().substr(0, 10),
            menu_d: false,
            menu_h: false,
      }
    },
    computed: {
        ...mapGetters([
            'isGestor',
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
        colorLin(item){
            return item.tipo == "TOTAL" ? "blue--text darken-4 " : "";
        },
        clase(item){
            return item.quilates > 0 ? item.clase + " " + item.quilates + "K" : item.clase;
        },
        peso_gr(item){
            return item.importe / item.peso;
        },
        submit(){


            if (this.show_loading === false){
                this.show_loading = true;
                this.$validator.validateAll().then((result) => {
                    if (result){

                        axios({
                            url: '/exportar/operaciones',
                            method: 'POST',
                            data:{
                                fecha_d: this.fecha_d,
                                fecha_h: this.fecha_h,
                                operacion: this.operacion,
                            }
                            })
                        .then(res => {

                            this.items = res.data;

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
                url: "/exportar/operaciones/excel",
                method: 'POST',
                responseType: 'blob', // important
                data:{ data: this.items }
                })
            .then(response => {

                let blob = new Blob([response.data])
                let link = document.createElement('a')
                link.href = window.URL.createObjectURL(blob)

                link.download = "Ope."+new Date().getFullYear()+(new Date().getMonth()+1)+(new Date().getDate())+'.xlsx';

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
