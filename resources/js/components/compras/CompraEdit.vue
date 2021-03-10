<template>
	<div v-show="show">
        <loading :show_loading="show_loading"></loading>
        <v-card>
            <v-card-title color="indigo">
                <h2 color="indigo">{{titulo}}: <span v-if="show" :class="fase.color">{{fase.nombre}}</span></h2>
                <v-spacer></v-spacer>
                <menu-ope :compra="compra" :docu_ok="docu_ok"></menu-ope>
            </v-card-title>
        </v-card>
        <v-card>
            <v-form v-if="show">
                <v-container>
                    <v-layout row wrap>
                        <v-flex sm2>
                            <v-text-field
                                v-model="compra.cliente.dni"
                                label="Nº Documento"
                                readonly
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm4>
                            <v-text-field
                                v-model="compra.cliente.razon"
                                label="Cliente"
                                readonly
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm2>
                            <v-text-field
                                v-model="compra.grupo.nombre"
                                label="Grupo"
                                readonly
                            >
                            </v-text-field>
                        </v-flex>
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
                                :disabled="!hasReaCom"
                            >
                                <v-text-field
                                    slot="activator"
                                    :value="computedFechaCompra"
                                    label="Fecha Compra"
                                    append-icon="event"
                                    :readonly="!hasReaCom"
                                    v-on:keyup.enter="submit"
                                    ></v-text-field>
                                <v-date-picker
                                    v-model="compra.fecha_compra"
                                    no-title
                                    locale="es"
                                    first-day-of-week=1
                                    @input="menu1 = false"
                                    :readonly="!hasReaCom"

                                ></v-date-picker>
                            </v-menu>
                        </v-flex>
                        <v-flex sm2 v-if="!hasReaCom">
                            <v-text-field
                                class="centered-input"
                                v-model="compra.alb_ser"
                                label="Nº Registro"
                                readonly
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm2  v-else>
                            <v-text-field
                                class="centered-input"
                                v-model="compra.albaran"
                                v-validate="'numeric|min:1'"
                                data-vv-name="albaran"
                                data-vv-as="núero de registro"
                                :error-messages="errors.collect('albaran')"
                                label="Nº Registro"
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex sm2>
                            <v-text-field
                                v-model="computedFModFormat"
                                :label="computedTxtMod"
                                readonly                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm1 v-if="parametros.doble_interes">
                            <v-text-field
                                v-model="compra.papeleta"
                                v-validate="'numeric'"
                                data-vv-name="papeleta"
                                :error-messages="errors.collect('papeleta')"
                                label="Papeleta"
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm2 v-else>
                            <v-text-field
                                v-model="compra.papeleta"
                                v-validate="'numeric'"
                                data-vv-name="papeleta"
                                :error-messages="errors.collect('papeleta')"
                                label="Papeleta"
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm1>
                            <v-text-field
                                v-show="compra.tipo_id==1"
                                class="centered-input"
                                v-model="compra.interes"
                                v-validate="'decimal|max_value:40'"
                                data-vv-name="interes"
                                data-vv-as="interés"
                                :error-messages="errors.collect('interes')"
                                label="% Renovación"
                                :readonly="!computedHayDepositos"
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm1 v-if="parametros.doble_interes">
                            <v-text-field
                                v-show="compra.tipo_id==1"
                                class="centered-input"
                                v-model="compra.interes_recuperacion"
                                v-validate="'decimal|max_value:40'"
                                data-vv-name="interes_recuperacion"
                                data-vv-as="interés"
                                :error-messages="errors.collect('interes_recuperacion')"
                                label="% Recuperación"
                                :readonly="!computedHayDepositos"
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm1>
                            <v-text-field
                                v-show="compra.tipo_id==1"
                                class="centered-input"
                                v-model="compra.dias_custodia"
                                v-validate="'numeric'"
                                data-vv-name="dias_custodia"
                                data-vv-as="días"
                                :error-messages="errors.collect('dias_custodia')"
                                label="Días Custodia"
                                :readonly="computedCustodia"
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm2>
                            <v-text-field
                                v-model="computedFechaBloqueo"
                                label="Fin Bloqueo"
                                readonly
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm2>
                            <v-text-field
                                class="inputPrice"
                                v-model="computedValorCompras"
                                label="Compras Acumuladas día"
                                readonly
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm2>
                            <v-text-field
                                class="centered-input"
                                v-model="computedImporte"
                                label="Importe"
                                readonly
                            >
                            </v-text-field>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex sm9>
                             <v-text-field
                                v-model="compra.notas"
                                counter="100"
                                label="Observaciones"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm2>
                            <div class="text-xs-center">
                                <v-btn
                                    :disabled="compra.importe==0"
                                    small
                                    @click="submit"  round  :loading="enviando" block  color="warning">
                                    Cerrar Lote
                                </v-btn>
                            </div>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-form>
            <v-container v-if="compra.id>0">
                <com-lines
                    :compra.sync="compra"
                    :compra_id="compra.id"
                    :grabaciones="grabaciones"
                    :auth_liquidar="auth_liquidar"
                ></com-lines>
                <deposito-create
                    v-if="compra.id>0"
                    :compra.sync="compra"
                    :valor_compras="valor_compras"
                    :dialog_depo.sync="dialog_depo"
                >
                </deposito-create>
            </v-container>
        </v-card>
	</div>
</template>
<script>
import moment from 'moment'
import Loading from '@/components/shared/Loading'
import MenuOpe from './MenuOpe'
import Comlines from './Comlines'
import DepositoCreate from './DepositoCreate'
import {mapGetters} from 'vuex';
	export default {
		$_veeValidate: {
      		validator: 'new'
        },
        components: {
            'menu-ope': MenuOpe,
            'com-lines': Comlines,
            'deposito-create': DepositoCreate,
            'loading': Loading
		},
    	data () {
      		return {
                titulo:"Operación: ",
                compra: {},
                url: "/compras/compras",
                ruta: "compra",
                grupos: [],
                totales_concepto: [],

                deposito:{
                    compra_id:"",
                    concepto_id:"",
                    importe:0,
                },
                valor_compras: "0",
        		status: false,
                enviando: false,

                dialog_depo: false,

                show: false,
                show_loading: true,
                menu1: false,
                fase: {
                    color:"",
                    nombre:""
                },
                docu_ok: true,
                auth_liquidar: false,
                grabaciones: false,
                cambio_interes: 0

      		}
        },
        mounted(){

            var id = this.$route.params.id;

            if (id > 0)
                axios.get(this.url+'/'+id+'/edit')
                    .then(res => {

                        this.grabaciones = res.data.grabaciones;
                        this.compra = res.data.compra;

                        this.titulo = this.compra.tipo.nombre;

                        this.valor_compras = res.data.valor_compras;

                        this.fase.nombre = this.compra.fase.nombre;
                        this.fase.color = this.compra.fase.color;

                        this.fpago = this.compra.cliente.fpago_id;

                        this.cambio_interes = res.data.cambio_interes;

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
                'hasReaCom',
                'hasEdtInt',
                'parametros'
            ]),
            computedHayDepositos(){

                if (this.hasReaCom) return true;

                if (this.hasEdtInt && this.cambio_interes == 0 )
                    return true;

                return false;

                // TODO: Revisar con perfil compras

                // if (this.hasReaCom) return false;

                // return this.totales_concepto[0] != 0;
            },
            computedCustodia(){

                return this.totales_concepto[0] != 0;

            },
            computedTxtMod(){
                return "Mod. "+this.compra.username;
            },
            computedValorCompras(){
                return this.getMoneyFormat(parseFloat(this.compra.importe)+parseFloat(this.valor_compras));
            },
            computedImporte(){
                return parseFloat(this.compra.importe).toLocaleString('es-ES',{ style: 'currency', currency: 'EUR' });
            },
            computedFechaCompra() {
                moment.locale('es');
                return this.compra.fecha_compra ? moment(this.compra.fecha_compra).format('DD/MM/YYYY') : '';
            },
            computedFechaBloqueo() {
                moment.locale('es');
                return this.compra.fecha_bloqueo ? moment(this.compra.fecha_bloqueo).format('L') : '';
            },
            computedFModFormat() {
                moment.locale('es');
                return this.compra.updated_at ? moment(this.compra.updated_at).format('D/MM/YYYY H:mm') : '';
            },
            computedFCreFormat() {
                moment.locale('es');
                return this.compra.created_at ? moment(this.compra.created_at).format('D/MM/YYYY H:mm') : '';
            }

        },
        watch: {
            compra: function () {

                axios.post("/utilidades/helpdepo",{compra_id: this.compra.id,
                                                   cliente_id: this.compra.cliente_id})
                    .then(res => {
                        this.totales_concepto = res.data.totales_concepto;

                    })
            }
        },
    	methods:{
            getMoneyFormat(value){
                return new Intl.NumberFormat("de-DE",{style: "currency", currency: "EUR"}).format(parseFloat(value))
            },
            submit() {

                if (this.totales_concepto[0] == 0)
                    this.dialog_depo=true;
                else{
                    this.enviando = true;
                    var com = this.compra;
                    if ((this.totales_concepto[0]+this.totales_concepto[5]) != this.compra.importe){
                        this.$toast.error('Importe en compra difiere de importe en depósito');
                    }
                    //     this.enviando = false;
                    // }else{
                        this.$validator.validateAll().then((result) => {
                            if (result){
                                this.show_loading = true;
                                this.compra.fase_id = 3;

                                axios.put(this.url+"/"+this.compra.id, this.compra)
                                    .then(res => {
                                        //
                                        // this.$toast.success(res.data.message);
                                        // this.compra = res.data.compra;
                                        if (this.compra.tipo_id == 1)
                                            this.$router.push({ name: 'recompra.close', params: { id: this.compra.id } })
                                        else
                                            this.$router.push({ name: 'compra.close', params: { id: this.compra.id } })

                                        this.enviando = false;
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
                                        this.enviando = false;
                                        this.show_loading = false;
                                    });
                                }
                            else{
                                this.enviando = false;
                            }
                        });
                    //}
                }
            },
        }
  }
</script>
<style scoped>

.centered-input >>> input {
  text-align: center;
  -moz-appearance:textfield;
}

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
