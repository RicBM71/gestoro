<template>
    <div>
        <loading :show_loading="show_loading"></loading>
        <v-card>
            <v-card-title color="indigo">
                <h2 color="indigo">{{titulo}}</h2>
                <v-spacer></v-spacer>
                <menu-ope></menu-ope>
            </v-card-title>
        </v-card>
        <v-card>
            <v-form>
                <v-container>
                    <v-layout row wrap>
                        <v-flex sm1 d-flex>
                            <v-select
                                v-model="parametros.tipo_id"
                                :items="tipos"
                                label="Operación"
                            ></v-select>
                        </v-flex>
                        <v-flex sm2>
                            <v-select
                                v-model="parametros.clase_id"
                                v-validate="'required'"
                                data-vv-name="clase_id"
                                data-vv-as="clase"
                                :error-messages="errors.collect('clase_id')"
                                :items="clases"
                                label="Clase"
                                ></v-select>
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
                                    label="Hasta F. Compra"
                                    append-icon="event"
                                    v-validate="'date_format:dd/MM/yyyy'"
                                    data-vv-name="fecha_h"
                                    :error-messages="errors.collect('fecha_h')"
                                    data-vv-as="fecha"
                                    readonly
                                    ></v-text-field>
                                <v-date-picker
                                    v-model="parametros.fecha_h"
                                    no-title
                                    locale="es"
                                    first-day-of-week=1
                                    @input="menu_h = false"
                                    ></v-date-picker>
                            </v-menu>
                        </v-flex>
                        <v-flex sm2 v-show="computedShowFechaLiq">
                            <v-menu
                                v-model="menu_liq"
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
                                    :value="computedFechaLiq"
                                    label="F. Liquidado"
                                    append-icon="event"
                                    v-validate="'date_format:dd/MM/yyyy'"
                                    data-vv-name="fecha_liq"
                                    :error-messages="errors.collect('fecha_liq')"
                                    data-vv-as="fecha"
                                    readonly
                                    ></v-text-field>
                                <v-date-picker
                                    v-model="parametros.fecha_liq"
                                    no-title
                                    locale="es"
                                    first-day-of-week=1
                                    @input="menu_liq = false"
                                    ></v-date-picker>
                            </v-menu>
                        </v-flex>
                        <v-flex sm2 v-show="!computedShowFechaLiq"></v-flex>
                        <v-flex sm2 d-flex>
                            <v-select
                                v-model="parametros.accion"
                                :items="acciones"
                                label="Acción"
                                @change="selectAccion"
                            ></v-select>
                        </v-flex>
                        <v-flex sm2 v-show="computedShowBlq">
                            <v-switch
                                label="Saltar Bloqueo"
                                v-model="parametros.saltar_bloqueo"
                                color="primary"
                                >
                            ></v-switch>
                        </v-flex>
                        <v-flex sm1>
                            <v-btn @click="submit" :loading="loading" round small block flat color="info">
                                Enviar
                            </v-btn>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-form>
            <pre-liquidado
                v-show="parametros.accion=='P'"
                :lineas.sync="lineas"
                :parametros="parametros"
                :loading.sync="loading"
                :total_gr="total_gr"
            >
            </pre-liquidado>
            <ver-liquidado
                v-if="parametros.accion=='M'"
                :lineas.sync="lineas"
                :loading.sync="loading"
                :reload_lineas.sync="reload_lineas"
                :total_gr="total_gr"
            >
            </ver-liquidado>
        </v-card>
    </div>
</template>
<script>
import {mapGetters} from 'vuex';
import {mapActions} from "vuex";
import MenuOpe from './MenuOpe'
import PreLiquidado from './PreLiquidado'
import VerLiquidado from './VerLiquidado'
import Loading from '@/components/shared/Loading'
import moment from 'moment'
export default {
    $_veeValidate: {
        validator: 'new'
    },
    components: {
        'menu-ope': MenuOpe,
        'pre-liquidado': PreLiquidado,
        'ver-liquidado': VerLiquidado,
        'loading': Loading
    },
    data () {
      return {
            titulo: "Liquidar Lotes",
            reload_lineas: false,
            loading: false,
            show_loading: true,
            result: false,
            acciones:[
                {value: 'P', text:"Preselección"},
                {value: 'F', text:"Liquidar directo"},
                {value: 'D', text:"Deshacer Liquidado"},
                {value: 'M', text:"Mostrar Liquidado"},
                ],
            tipos:[],
            clases: [],
            parametros:{
                accion: "M",
                tipo_id:"",
                clase_id:"",
                fecha_d: new Date().toISOString().substr(0, 7)+"-01",
                fecha_h: new Date().toISOString().substr(0, 10),
                fecha_liq: new Date().toISOString().substr(0, 10),
                saltar_bloqueo: false
            },
            menu_h: false,
            menu_d: false,
            menu_liq:false,
            preliquidar:false,
            lineas:[],
            total_gr:0
      }
    },
    beforeMount(){

        axios.get('/compras/liquidar')
            .then(res => {

                this.clases = res.data.clases;
                this.parametros.clase_id = this.clases[0].value;

                this.tipos = res.data.tipos;
                this.parametros.tipo_id = this.tipos[0].value;


            })
            .catch(err => {

                this.$toast.error(err.response.data.message);
                this.$router.push({ name: 'dash' })
            })
    },
    mounted(){

        // if (this.getParamModelo == 'liquidarseleccion'){
        //     this.parametros = this.getParamSeleccion;
        //     this.submit();
        // }
            this.show_loading=false;
    },
    watch:{
        reload_lineas: function () {
            if (this.reload_lineas){
                this.submit();
                this.reload_lineas = false;
            }
        }
    },
    computed: {
        ...mapGetters([
            'getParamSeleccion',
        ]),
        computedShowFechaLiq(){
            return true;
            return this.parametros.accion != 'P';
        },
        computedShowBlq(){
            return this.parametros.accion == 'P' || this.parametros.accion == "F";
        },
        computedFechaLiq() {
            moment.locale('es');
            return this.parametros.fecha_liq ? moment(this.parametros.fecha_liq).format('L') : '';
        },
        computedFechaD() {
            moment.locale('es');
            return this.parametros.fecha_d ? moment(this.parametros.fecha_d).format('L') : '';
        },
        computedFechaH() {
            moment.locale('es');
            return this.parametros.fecha_h ? moment(this.parametros.fecha_h).format('L') : '';
        }
    },
    methods:{
        ...mapActions([
            'setParametros',
		]),
        selectAccion(){
            this.lineas = [];
        //    this.submit();
        },
        submit(){

            if (this.parametros.accion == "P"){

                if (this.loading === false){
                    this.loading = true;

                    axios.post('/compras/liquidar/preliquidado', this.parametros)
                        .then(res => {

                            this.lineas = res.data.compras;
                            this.total_gr = res.data.total_gr;

                            if (this.lineas.length == 0)
                                this.$toast.warning('No hay registros!');

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
                            this.loading = false;
                        });

                }
            }else if(this.parametros.accion == "M"){
                this.loading = true;
             //   this.setParametros(this.parametros);
                axios.post('/compras/liquidar/mostrar', this.parametros)
                    .then(res => {

                        this.lineas = res.data.lineas;
                        this.total_gr = res.data.total_gr;

                        if (this.lineas.length == 0)
                            this.$toast.warning('No hay registros!');

                        this.show_loading=false;
                        this.loading = false;
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
                        this.loading = false;
                    });

            }else if(this.parametros.accion == "D"){
                this.loading = this.show_loading = true;
                axios.put('/compras/liquidar/deshacer', this.parametros)
                    .then(res => {

                        this.loading = this.show_loading=false;

                        this.parametros.accion = "P";
                        this.submit();
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
                        this.loading = this.show_loading=false;
                    });
            }else if(this.parametros.accion == "F"){
                this.loading = this.show_loading = true;
                axios.put('/compras/liquidar/direct', this.parametros)
                    .then(res => {

                        this.loading = this.show_loading=false;

                        this.parametros.accion = "M";
                        this.submit();
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
                        this.loading = this.show_loading=false;
                    });

            }

        },
    }
}
</script>
