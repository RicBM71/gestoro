<template>
	<div>
        <loading :show_loading="show_loading"></loading>
        <v-card>
            <v-card-title color="indigo">
                <h2 color="indigo">{{titulo}}</h2>
                <v-spacer></v-spacer>
                <menu-ope :id="traspaso.id"></menu-ope>
            </v-card-title>
        </v-card>
        <v-card  v-if="show">
            <v-form>
                 <v-container>
                     <v-layout row wrap>
                        <v-flex sm2></v-flex>
                         <v-flex sm3>
                            <v-text-field
                                :value="computedSolicitante"
                                label="Solicitante"
                                readonly
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm2>
                            <v-text-field
                                :value="computedEstado"
                                label="Estado"
                                readonly
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm3 v-if="traspaso.autorizado_user_id > 0">
                            <v-text-field
                                :value="computedAutorizado"
                                label="Autorizado"
                                readonly
                            >
                            </v-text-field>
                        </v-flex>
                     </v-layout>
                     <v-layout row wrap>
                        <v-flex sm2></v-flex>
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
                                :readonly="!hasAuthTras"
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
                                    :readonly="!hasAuthTras"
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
                            :disabled="traspaso.situacion_id > 1 && !hasAuthTras"
                            ></v-select>
                        </v-flex>
                        <v-flex sm2>
                            <v-text-field
                                v-model="traspaso.importe_solicitado"
                                v-validate="'required'"
                                :error-messages="errors.collect('importe_solicitado')"
                                label="Solicitado"
                                data-vv-name="importe_solicitado"
                                data-vv-as="importe"
                                class="inputPrice"
                                :readonly="!computedModifica"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm2>
                            <v-text-field
                                v-model="traspaso.importe_entregado"
                                v-validate="'required'"
                                :error-messages="errors.collect('importe_entregado')"
                                label="Importe Entregado"
                                data-vv-name="importe_entregado"
                                data-vv-as="importe"
                                :disabled="!hasAuthTras"
                                class="inputPrice"
                            >
                            </v-text-field>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex sm4></v-flex>
                        <v-flex sm2>
                            <v-text-field
                                v-if="isAdmin"
                                :value="saldo"
                                label="Saldo"
                                class="inputPrice"
                                readonly
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm1></v-flex>
                        <v-flex sm3 v-if="traspaso.receptor_user_id > 0">
                            <v-text-field
                                :value="computedReceptor"
                                label="Receptor"
                                readonly
                            >
                            </v-text-field>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex sm2></v-flex>
                        <v-flex sm2>
                                <v-text-field
                                    v-model="traspaso.username"
                                    :error-messages="errors.collect('username')"
                                    label="Usuario"
                                    data-vv-name="username"
                                    readonly
                                >
                                </v-text-field>
                            </v-flex>
                            <v-flex sm2>
                                <v-text-field
                                    v-model="computedFModFormat"
                                    label="Modificado"
                                    readonly
                                >
                                </v-text-field>
                            </v-flex>
                            <v-flex sm2>
                                <v-text-field
                                    v-model="computedFCreFormat"
                                    label="Creado"
                                    readonly
                                >
                                </v-text-field>
                            </v-flex>
                        </v-layout>
                        <v-layout row wrap>
                            <v-flex sm8></v-flex>
                            <v-flex sm2>
                            <div class="text-xs-center" v-show="computedGuardar">
                                        <v-btn @click="submit"  round  :loading="loading" block  color="primary">
                                {{computedLabel}}
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
                titulo:"Solicitud de efectivo",
                menu1: false,
                traspaso: {
                },
                situaciones: [
                    'Solicitado',
                    'Autorizado',
                    'Entregado'
                ],
                url: "/mto/traspasos",
                ruta: "traspaso",
                empresas: [],

        		status: false,
                loading: false,
                saldo: 0,


                show: false,
                show_loading: true
      		}
        },
        beforeMount(){
            var id = this.$route.params.id;
            if (id > 0)
                axios.get(this.url+'/'+id+'/edit')
                    .then(res => {
                        this.traspaso = res.data.traspaso;
                        this.saldo = res.data.saldo;
                        this.empresas = res.data.empresas;

                        if (this.traspaso.importe_entregado == 0)
                            this.traspaso.importe_entregado = this.traspaso.importe_solicitado;

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
                'hasAuthTras',
                'userName'
            ]),
        computedLabel(){
            if (this.traspaso.situacion_id == 1)
                return 'Autorizar';
            else if (this.traspaso.situacion_id == 2)
                return 'Confirmar entrega';
            else return 'Guardar';
        },
        computedEstado(){
            return this.situaciones[this.traspaso.situacion_id - 1];
        },
        computedSolicitante(){
            return this.traspaso.solicitante.name + " " + this.traspaso.solicitante.lastname;
        },
        computedAutorizado(){
            return this.traspaso.autorizado.name + " " + this.traspaso.autorizado.lastname;
        },
        computedReceptor(){
            return this.traspaso.receptor.name + " " + this.traspaso.receptor.lastname;
        },
        computedModifica(){

            return this.traspaso.situacion_id == 1 ? true : false;

        },
        computedSaldo(){

        },
        computedGuardar(){

            if (this.traspaso.situacion_id == 3)
                return false;

        /// yo esto no lo pondría.
        //    if (this.isAdmin && this.hasAuthTras) return true;

            // está solicitado, hay que autorizarlo
            if (this.hasAuthTras && this.traspaso.situacion_id == 1 && this.traspaso.username != this.userName)
                return true;

            // está autorizado y falta confirmarlo
            if (this.traspaso.situacion_id == 2 && this.traspaso.username != this.userName)
                return true;

            return false;

        },
        computedFecha() {
            moment.locale('es');
            return this.traspaso.fecha ? moment(this.traspaso.fecha).format('L') : '';
        },
        computedFModFormat() {
            moment.locale('es');
            return this.traspaso.updated_at ? moment(this.traspaso.updated_at).format('DD/MM/YYYY H:mm') : '';
        },
        computedFCreFormat() {
            moment.locale('es');
            return this.traspaso.created_at ? moment(this.traspaso.created_at).format('DD/MM/YYYY H:mm') : '';
        }

        },
    	methods:{
            getMoneyFormat(value){
                return new Intl.NumberFormat("de-DE",{style: "currency", currency: "EUR"}).format(parseFloat(value))
            },
            submit() {

                if (this.loading === false){
                    this.loading = true;

                    this.$validator.validateAll().then((result) => {
                        if (result){
                             axios.put(this.url+"/"+this.traspaso.id, this.traspaso)
                                .then(res => {

                                    this.$toast.success(res.data.message);
                                    this.traspaso = res.data.traspaso;
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

.v-form>.container>.layout>.flex {
    padding: 1px 8px 1px 8px;
}
.v-text-field {
    padding-top: 6px;
    margin-top: 2px;
}
</style>
