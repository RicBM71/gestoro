<template>
	<div v-show="show">
        <loading :show_loading="show_loading"></loading>
        <v-card>
            <v-card-title color="indigo">
                <h2 color="indigo">{{titulo}}</h2>
                <v-spacer></v-spacer>
            </v-card-title>
        </v-card>
        <v-card>
            <v-form>
                 <v-container>
                     <v-layout row wrap>
                        <v-flex sm4></v-flex>
                        <v-flex sm4>
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
                     </v-layout>
                    <v-layout row wrap>
                        <v-flex sm4></v-flex>
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
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex sm4></v-flex>
                        <v-flex sm2>
                            <v-select
                                v-model="accion"
                                :items="acciones"
                                label="Acción"
                                ></v-select>
                        </v-flex>
                        <v-flex sm2 v-if="accion!='D'">
                            <v-select
                                v-model="cobro"
                                :items="cobros"
                                label="Cobros"
                                ></v-select>
                        </v-flex>
                        <v-flex sm1></v-flex>
                        <v-flex sm1>
                            <div class="text-xs-center">
                                <v-btn @click="submit" round small :loading="loading"  block  color="primary">
                                    Enviar
                                </v-btn>
                            </div>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-form>
        </v-card>
	</div>
</template>
<script>
import {mapGetters} from 'vuex';
import moment from 'moment'
//import MenuOpe from './MenuOpe'
import Loading from '@/components/shared/Loading'

	export default {
		$_veeValidate: {
      		validator: 'new'
        },
        components: {
//            'menu-ope': MenuOpe,
            'loading': Loading
		},
    	data () {
      		return {

                menu1: false,
                url: "/ventas/facturacion/compras",
                ruta: "compra",

                cobros:[
                    {value: 'T', text:"Todos"},
                    {value: 'E', text:"En efectivo"},
                    {value: 'B', text:"Por Banco"},
                ],
                cobro: "T",


                acciones:[
                    {value: 'F', text:"Facturar"},
                    {value: 'D', text:"Desfacturar"},
                ],
                accion:"F",
                menu_h: false,
                menu_d: false,
                fecha_d: new Date().toISOString().substr(0, 7)+"-01",
                fecha_h: new Date().toISOString().substr(0, 10),

                grupo_id:"",
                grupos:[],

        		status: false,
                loading: false,

                show: false,
                show_loading: true,
                titulo:'Facturar Recuperaciones'
      		}
        },
        mounted(){
            axios.get('/ventas/facturacion')
                .then(res => {

                    this.grupos = res.data.grupos;
                    this.grupo_id = res.data.grupos[0].value;

                    this.show = true;
                    this.show_loading = false;
                })
                .catch(err => {
                    console.log(err);
                    this.$toast.error(err.response.data.message);
                    this.$router.push({ name:'dash'})
                })
            //this.$nextTick(() => this.$refs.dni.focus())
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
            },
        },
    	methods:{
            submit() {
                if (this.loading === false){

                    this.loading = true;
                    this.show_loading = true;

                    this.$validator.validateAll().then((result) => {
                        if (result){
                            axios.put(this.url, {
                                accion: this.accion,
                                grupo_id: this.grupo_id,
                                fecha_d: this.fecha_d,
                                fecha_h: this.fecha_h,
                                cobro: this.cobro,
                            })
                                .then(res => {

                                    this.$toast.success(res.data);
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
