<template>
	<div>
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
                        <v-alert
                            :value="true"
                            type="warning"
                            >
                            Este proceso es irreversible, no podrán recuperarse de nuevo los registros borrados
                        </v-alert>
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
                                    :max="max_fecha"
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
                                    :max="max_fecha"
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
                        <v-flex sm4>
                            <v-radio-group v-model="radioGroup">
                                <v-radio
                                    v-for="n in 3"
                                    :key="n"
                                    :label="radio[n-1]"
                                    :value="n"
                                    color="red darken-4"
                                ></v-radio>
                            </v-radio-group>
                        </v-flex>
                        <v-flex sm1></v-flex>
                        <v-flex sm1>
                            <div class="text-xs-center">
                                <v-btn @click="submit" round small :loading="loading"  block  color="red darken-4">
                                    Borrar
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

                radioGroup: 1,
                menu1: false,
                url: "/utilidades/borrar",

                max_fecha: "",
                menu_h: false,
                menu_d: false,
                fecha_d: new Date().toISOString().substr(0, 7)+"-01",
                fecha_h: new Date().toISOString().substr(0, 10),

        		status: false,
                loading: false,
                caja: true,
                ampliaciones: true,
                compras: false,

                radio:[
                    'BORRAR Apuntes de caja, SOLO empresa ACTIVA',
                    'PURGAR Histórico de compras y caja (TODAS las empresas)',
                    'n+1 BORRAR Ampliaciones recuperados (TODAS las empresas)'
                ],

                show: false,
                show_loading: false,
                titulo:'Borrar registros antiguos'
      		}
        },
        mounted(){

                axios.get(this.url)
                    .then(res => {
                        this.max_fecha = res.data.fecha_h;
                        this.fecha_d = res.data.fecha_d;
                        this.fecha_h = res.data.fecha_h;

                    })
                    .catch(err => {

                        this.$toast.error(err.response.data.message);
                        this.$router.push({ name:'dash'})
                    })
                    .finally(()=> {
                        this.loading = this.show_loading = false;
                    });

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
                console.log(this.radioGroup);
                this.$validator.validateAll().then((result) => {
                    if (result){
                        this.loading = this.show_loading = true;
                        if (this.radioGroup == 1){
                            this.borrarCaja();
                        }else if (this.radioGroup == 2){
                            this.purgarHistorico()
                        }else if(this.radioGroup == 3){
                            this.borrarAmpliaciones()
                        }
                    }
                    else{
                        this.loading = this.show_loading = false;
                    }
                });


            },
            borrarCaja() {
                axios.post(this.url+'/caja', {
                    fecha_d: this.fecha_d,
                    fecha_h: this.fecha_h,
                })
                    .then(res => {
                        if (res.data.registros > 0)
                            this.$toast.success("Se han borrado "+res.data.registros+" registros.");
                        else
                            this.$toast.warning("No hay registros.");
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
                    })
                    .finally(()=> {
                        this.loading = this.show_loading = false;
                    });

            },
            borrarAmpliaciones() {

                axios.post(this.url+'/ampliaciones', {
                    fecha_d: this.fecha_d,
                    fecha_h: this.fecha_h,
                })
                    .then(res => {
                        console.log(res);
                        if (res.data.registros > 0)
                            this.$toast.success("Se han borrado "+res.data.registros+" registros.");
                        else
                            this.$toast.warning("No hay registros.");
                    })
                    .catch(err => {

                        console.log(err);

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
                    })
                    .finally(()=> {
                        this.loading = this.show_loading = false;
                    });

            },
            purgarHistorico() {
                axios.post(this.url+'/purgar', {
                })
                    .then(res => {
                        this.$toast.success("Se han purgado las tablas de histórico.");
                    })
                    .catch(err => {

                        this.$toast.error(err.response.data.message);
                    })
                    .finally(()=> {
                        this.loading = this.show_loading = false;
                    });

            },

        }
  }
</script>
