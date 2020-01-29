<template>
	<div v-show="!show_loading">
        <loading :show_loading="show_loading"></loading>
        <v-card>
            <v-card-title color="indigo">
                <h2 color="indigo">{{titulo}}</h2>
                <v-spacer></v-spacer>
                <menu-ope :id="caja.id"></menu-ope>
            </v-card-title>
        </v-card>
        <v-card>
            <v-form>
                 <v-container>
                     <v-layout row wrap>
                        <v-flex sm1></v-flex>
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
                                :disabled="computedEditFecha"
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
                                    v-model="caja.fecha"
                                    no-title
                                    locale="es"
                                    first-day-of-week=1
                                    @input="menu1 = false"
                                    :disabled="computedEditFecha"
                                ></v-date-picker>
                            </v-menu>
                        </v-flex>
                        <v-flex sm2 d-flex>
                            <v-select
                            v-model="caja.dh"
                            v-validate="'required'"
                            data-vv-name="dh"
                            data-vv-as="dh"
                            :error-messages="errors.collect('dh')"
                            :items="dhs"
                            label="D/H"
                            required
                            ></v-select>
                        </v-flex>
                        <v-flex sm2 d-flex>
                            <v-select
                                v-model="caja.manual"
                                v-validate="'required'"
                                data-vv-name="manual"
                                data-vv-as="origen"
                                :error-messages="errors.collect('manual')"
                                :items="origenes"
                                label="Origen"
                                :readonly="!isAdmin"
                                ></v-select>
                        </v-flex>
                         <v-flex sm3 d-flex>
                            <v-select
                            v-model="caja.apunte_id"
                            :error-messages="errors.collect('apunte_id')"
                            data-vv-name="apunte_id"
                            data-vv-as="apunte_id"
                            :items="apuntes"
                            label="Apunte"
                            ></v-select>
                        </v-flex>
                     </v-layout>
                    <v-layout row wrap>
                        <v-flex sm1></v-flex>
                        <v-flex sm8>
                            <v-text-field
                                v-model="caja.nombre"
                                v-validate="'required'"
                                :error-messages="errors.collect('nombre')"
                                label="Nombre"
                                data-vv-name="nombre"
                                data-vv-as="nombre"
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                         <v-flex sm2>
                            <v-text-field
                                v-model="caja.importe"
                                v-validate="'required|decimal:2|min:1'"
                                :error-messages="errors.collect('importe')"
                                label="Importe"
                                data-vv-name="importe"
                                data-vv-as="importe"
                                class="inputPrice"
                                type="number"
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex sm8></v-flex>
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
import Loading from '@/components/shared/Loading'
import MenuOpe from './MenuOpe'
import {mapGetters} from 'vuex';
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
                titulo:"Apuntes de caja",
                caja: {
                    fecha: new Date().toISOString().substr(0, 10),
                    importe: null,
                    dh: 'D',
                    nombre: '',
                    manual: 'S',
                    deposito_id:null,
                    cobro_id: null,
                    apunte_id: null
                },
                url: "/mto/cajas",
                ruta: "caja",
                origenes:[
                    {value: 'S', text:"Manual"},
                    {value: 'R', text:"Regularización"},
                ],
                dhs:[
                    {value: 'D', text:"Debe"},
                    {value: 'H', text:"Haber"},
                ],

                loading: false,
                menu1: false,

                show: false,
                show_loading: true,
                apuntes:[]
      		}
        },
        mounted(){
             this.show_loading = false;
            axios.get(this.url+'/create')
                .then(res => {

                    this.apuntes = res.data.apuntes;
                    this.apuntes.push({value: null, text: '-'});
                    this.show = true;
                    this.show_loading = false;
                })
                .catch(err => {
                    this.$toast.error(err.response.data.message);
                    this.$router.push({ name: this.ruta+'.index'})
                })
        },
        computed: {
            ...mapGetters([
                'isAdmin',
                'isSupervisor',
            ]),
            computedEditFecha(){
                
                return !this.isSupervisor;
            },
            computedFecha() {
                moment.locale('es');
                return this.caja.fecha ? moment(this.caja.fecha).format('L') : '';
            },
        },
    	methods:{
            submit() {

                if (this.loading === false){
                    this.loading = true;

                    this.$validator.validateAll().then((result) => {
                        if (result){
                            axios.post(this.url, this.caja)
                                .then(response => {

                                    this.$toast.success(response.data.message);

                                    this.loading = false;
                                    this.$router.push({ name: this.ruta+'.edit', params: { id: response.data.caja.id } })
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

<style scoped>

.inputPrice >>> input {
  text-align: center;
  -moz-appearance:textfield;
}

input[type=number]::-webkit-inner-spin-button,
input[type=number]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    margin: 0;
}
</style>
