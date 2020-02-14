<template>
    <div>
        <v-card>
            <loading :show_loading="show_loading"></loading>
            <v-card-title color="indigo">
                <h2 color="indigo">Recogidas</h2>
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
                <!-- <v-tooltip bottom>
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
                </v-tooltip> -->
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
                                    label="Recogida Desde"
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
                            <v-btn @click="submit" :loading="show_loading" flat round small block  color="info">
                                Enviar
                            </v-btn>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap  v-if="items.length>0">
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
                    <v-layout row wrap v-if="items.length>0">
                        <v-flex xs12>
                            <v-data-table
                                :headers="headers"
                                :search="search"
                                :items="items"
                                :pagination.sync="pagination"
                                rows-per-page-text="Registros por página"
                            >
                                <template slot="items" slot-scope="props">
                                    <tr>
                                        <td>{{props.item.empresa}}</td>
                                        <td>{{ albser(props.item) }}</td>
                                        <td>{{ formatDate(props.item.fecha_compra) }} </td>
                                        <td>{{ formatDate(props.item.fecha_recogida) }}</td>
                                    </tr>
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
            search:"",
            headers: [
            {
                text: 'Empresa',
                align: 'left',
                value: 'empresa',
            },
            {
                text: 'Compra',
                align: 'left',
                value: 'com_ser',
            },
            {
                text: 'F. Compra',
                align: 'left',
                value: 'fecha_compra'
            },
            {
                text: 'F. Recogida',
                align: 'left',
                value: 'fecha_recogida'
            }],


            show_loading: false,
            ejercicio:new Date().toISOString().substr(0, 4),
            show_filtro: true,
            items: [],
            //fecha_d: new Date().toISOString().substr(0, 7)+"-01",
            fecha_d: new Date().toISOString().substr(0, 10),
            fecha_h: new Date().toISOString().substr(0, 10),
            menu_d: false,
            menu_h: false,
            total: 0,
            pagination:{
                rowsPerPage: 10,
                sortBy: "fecha_recogida",
                descending: true,

            },
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
        albser(item){
            return item.serie_com+item.albaran+"-"+moment(item.fecha_compra).format('YY');
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
                            url: '/exportar/recogidas',
                            method: 'POST',
                            data:{
                                fecha_d: this.fecha_d,
                                fecha_h: this.fecha_h,
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
    }
}
</script>
