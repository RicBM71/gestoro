<template>
	<div v-show="show">
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
                        <v-flex sm3></v-flex>
                        <v-flex sm2>
                            <v-menu
                                v-model="menu1"
                                :close-on-content-click="false"
                                :nudge-right="40"
                                lazy
                                transition="scale-transition"
                                offset-y
                                full-width
                                min-width="290px"
                                :readonly="!hasEdtFec"
                            >
                                <v-text-field
                                    slot="activator"
                                    :value="computedFecha"
                                    label="Fecha"
                                    v-validate="'required'"
                                    data-vv-name="fecha"
                                    append-icon="event"
                                    readonly
                                    data-vv-as="Fecha"
                                    :error-messages="errors.collect('fecha')"
                                    ></v-text-field>
                                <v-date-picker
                                    v-model="traspaso.fecha"
                                    no-title
                                    locale="es"
                                    first-day-of-week=1
                                    @input="menu1 = false"
                                    :readonly="!hasEdtFec"
                                ></v-date-picker>
                            </v-menu>
                        </v-flex>
                        <v-flex sm3 d-flex>
                            <v-select
                            v-model="traspaso.proveedora_empresa_id"
                            v-validate="'required|min_value:1'"
                            data-vv-name="proveedora_empresa_id"
                            data-vv-as="Proveedora"
                            :error-messages="errors.collect('proveedora_empresa_id')"
                            :items="empresas"
                            label="Proveedora"
                            ></v-select>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex sm6></v-flex>
                        <v-flex sm2>
                            <v-text-field
                                v-model="traspaso.importe_solicitado"
                                v-validate="'required'"
                                :error-messages="errors.collect('importe_solicitado')"
                                label="Importe Solicitado"
                                data-vv-name="importe_solicitado"
                                data-vv-as="importe"
                                required
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm1></v-flex>
                        <v-flex sm2>
                            <div class="text-xs-center">
                                        <v-btn @click="submit"  round  :loading="loading" block  color="primary">
                                Guardar
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
import moment from 'moment'
import MenuOpe from './MenuOpe'
import Loading from '@/components/shared/Loading'
import {mapGetters} from 'vuex'
	export default {
		$_veeValidate: {
      		validator: 'new'
        },
        components: {
            'menu-ope': MenuOpe,
            'loading': Loading
		},
    	data () {
      		return {
                titulo:"Solicitud de efectivo",
                menu1: false,
                traspaso: {
                    proveedora_empresa_id: 1,
                    importe_solicitado:  5000,
                    fecha: new Date().toISOString().substr(0, 10),
                },
                url: "/mto/traspasos",
                ruta: "traspaso",
                empresas: [],

        		status: false,
                loading: false,


                show: false,
                show_loading: true
      		}
        },
        mounted(){
            axios.get(this.url+'/create')
                .then(res => {
                    this.show = true;
                    this.empresas = res.data.empresas;
                    this.traspaso.proveedora_empresa_id=this.empresas[0].value;
                    this.show_loading = false;
                })
                .catch(err => {
                    this.$toast.error(err.response.data.message);
                    this.$router.push({ name: this.ruta+'.index'})
                })
        },
        computed: {
         ...mapGetters([
            'hasEdtFec',
        ]),
        computedFecha() {
            moment.locale('es');
            return this.traspaso.fecha ? moment(this.traspaso.fecha).format('L') : '';
            },
        },
    	methods:{
            submit() {
                if (this.loading === false){
                    this.loading = true;

                    this.$validator.validateAll().then((result) => {
                        if (result){
                            axios.post(this.url, this.traspaso)
                                .then(response => {

                                    this.loading = false;
                                    this.$router.push({ name: this.ruta+'.edit', params: { id: response.data.traspaso.id } })
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
                        else{
                            this.loading = false;
                        }
                    });
                }

            },

    }
  }
</script>
