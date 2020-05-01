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
                         <v-flex sm2>
                            <v-menu
                                v-model="menu_h"
                                :close-on-content-click="true"
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
                                    label="Fecha"
                                    append-icon="event"
                                    v-validate="'date_format:dd/MM/yyyy'"
                                    data-vv-name="fecha_h"
                                    :error-messages="errors.collect('fecha_h')"
                                    data-vv-as="fecha"
                                    readonly
                                    ></v-text-field>
                                <v-date-picker
                                    v-model="fecha_h"
                                    no-title
                                    locale="es"
                                    first-day-of-week=1
                                    @input="menu_liq = false"
                                    ></v-date-picker>
                            </v-menu>
                        </v-flex>
                        <v-flex xs2>
                            <v-text-field
                                v-model="dias"
                                label="Días carencia"
                                single-line
                            ></v-text-field>
                        </v-flex>
                        <v-flex sm1></v-flex>
                        <v-flex sm1>
                            <div class="text-xs-center">
                                <v-btn @click="submit" round small flat :loading="loading"  block  color="primary">
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

                url: "/utilidades/amplimasivo",

                dias: 49,
                fecha_h: new Date().toISOString().substr(0, 10),
                menu_h: false,

        		status: false,
                loading: false,

                show_loading: true,
                titulo:'Ampliación masiva COVID'
      		}
        },
        mounted(){
            if (!this.isRoot){
                this.$toast.error('No dispone de los permisos necesarios');
                this.$router.push({ name:'dash'})
            }
            this.show_loading = false;
        },
        computed: {
            ...mapGetters([
                    'isRoot'
                ]),
            computedFechaH() {
                moment.locale('es');
                return this.fecha_h ? moment(this.fecha_h).format('L') : '';
        }
        },
    	methods:{
            submit() {
                if (this.loading === false){

                    this.loading = true;
                    this.show_loading = true;

                    this.$validator.validateAll().then((result) => {
                        if (result){
                            axios.post(this.url, {
                                dias: this.dias,
                                fecha_h: this.fecha_h,
                            })
                                .then(res => {

                                    this.$toast.success('Se han generado correctamente los apuntes ='+res.data.registros);

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
