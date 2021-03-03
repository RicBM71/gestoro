<template>
	<div>
        <loading :show_loading="show_loading"></loading>
        <v-card v-if="show">
            <v-card-title color="indigo">
                <h2 color="indigo">
                        {{titulo}}: <span v-if="show" :class="albaran.fase.color">{{albaran.fase.nombre}}</span>
                </h2>
                <h3 class="red--text darken-4">{{computedMotivo}}</h3>
                <v-spacer></v-spacer>
                <menu-ope :albaran.sync="albaran"></menu-ope>
            </v-card-title>
        </v-card>
        <v-card>
            <v-form v-if="show">
                <v-container>
                    <v-layout row wrap>
                        <v-flex sm2>
                            <v-text-field
                                v-model="albaran.cliente.dni"
                                label="Nº Documento"
                                readonly
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm4>
                            <v-text-field
                                v-model="albaran.cliente.razon"
                                label="Cliente"
                                readonly
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm2>
                            <v-text-field
                                class="centered-input"
                                v-model="albaran.alb_ser"
                                :label="computedLabelAlbaran"
                                readonly
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm2 v-if="albaran.factura==null">
                            <v-menu
                                v-model="menu1"
                                :close-on-content-click="false"
                                :nudge-right="40"
                                lazy
                                transition="scale-transition"
                                offset-y
                                full-width
                                min-width="290px"
                                :disabled="disabledFechaAlbaran"
                            >
                                <v-text-field
                                    slot="activator"
                                    :value="computedFechaAlbaran"
                                    label="Fecha Albarán"
                                    append-icon="event"
                                    readonly
                                    ></v-text-field>
                                <v-date-picker
                                    v-model="albaran.fecha_albaran"
                                    no-title
                                    locale="es"
                                    first-day-of-week=1
                                    @input="menu1 = false"
                                ></v-date-picker>
                            </v-menu>
                        </v-flex>
                        <v-flex sm2>
                            <v-text-field
                                v-show="computedFacturar"
                                slot="activator"
                                :value="computedFechaFactura"
                                label="Fecha Factura"
                                readonly
                                :disabled="!albaran.facturar"
                            ></v-text-field>
                            <v-text-field
                                v-if="albaran.factura>0"
                                v-show="!computedFacturar"
                                :value="computedFechaFactura"
                                label="Fecha Factura"
                                readonly
                            ></v-text-field>
                        </v-flex>
                        <v-flex sm2 v-if="albaran.factura>0">
                            <v-text-field
                                class="centered-input"
                                v-model="albaran.fac_ser"
                                label="Factura"
                                readonly
                            >
                            </v-text-field>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex sm2>
                            <v-text-field
                                v-model="computedFModFormat"
                                :label="albaran.username"
                                readonly>
                            </v-text-field>
                        </v-flex>
                        <v-flex sm3>
                            <v-text-field
                                v-model="albaran.clitxt"
                                label="Cliente"
                                :readonly="albaran.factura > 0"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm2 v-if="albaran.tipo_id != 5 && albaran.fecha_notificacion!=null">
                            <v-text-field
                                class="centered-input"
                                :value="computedFechaNotificacion"
                                label="Notificado"
                                readonly
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm3 d-flex v-if="albaran.id > 0 && albaran.tipo_id>=4">
                            <v-select v-if="albaran.factura == null"
                                v-model="albaran.fpago_id"
                                :items="fpagos"
                                label="Forma de Pago"
                            ></v-select>
                            <v-text-field v-else
                                :value="albaran.fpago.nombre"
                                label="Forma de Pago"
                                readonly
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm2 d-flex v-if="albaran.fpago_id==2">
                            <v-select
                                v-if="albaran.factura == null"
                                v-model="albaran.cuenta_id"
                                :items="cuentas"
                                label="IBAN"
                            ></v-select>
                            <v-text-field
                                v-if="albaran.factura > 0 && albaran.cuenta_id > 0"
                                :value="albaran.cuenta.nombre"
                                label="IBAN"
                                readonly
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm1 v-if="albaran.iva_no_residente">
                            <v-text-field
                                :value="computedNoResidente"
                                label="Residente"
                                readonly
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm2 v-if="computedProcedenciaRebu">
                            <v-text-field
                                v-model="albaran.procedencia.nombre"
                                label="Vendido en"
                                readonly
                            >
                            </v-text-field>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap v-if="albaran.tipo_id==5">
                        <v-flex sm5>
                            <v-select
                                v-model="albaran.taller_id"
                                :error-messages="errors.collect('taller_id')"
                                v-validate="'required'"
                                data-vv-name="taller_id"
                                data-vv-as="taller"
                                :items="talleres"
                                label="Taller"
                                :disabled="computedTaller"
                            ></v-select>
                        </v-flex>
                        <v-flex sm4>
                            <v-select
                                v-model="albaran.procedencia_empresa_id"
                                :error-messages="errors.collect('procedencia_empresa_id')"
                                v-validate="'numeric'"
                                data-vv-name="procedencia_empresa_id"
                                data-vv-as="Empresa"
                                :items="empresas"
                                label="Procedencia"
                                :disabled="computedTaller"
                            ></v-select>
                        </v-flex>
                         <v-flex sm3>
                            <v-switch
                                label="Reparación Express"
                                v-model="albaran.express"
                                :disabled="computedTaller"
                                color="primary">
                            ></v-switch>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap v-if="albaran.tipo_id == 5">
                        <v-flex sm10>
                            <v-textarea
                                rows="2"
                                v-model="albaran.notas_ext"
                                :error-messages="errors.collect('notas_ext')"
                                v-validate="'max:191'"
                                data-vv-name="notas_ext"
                                label="Detalle de la pieza"
                                :readonly="albaran.factura > 0"
                            >
                            </v-textarea>
                        </v-flex>
                        <v-flex sm2>
                            <v-menu
                                v-model="menu5"
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
                                    :value="computedFechaNotificacion"
                                    label="Prevista Entrega"
                                    append-icon="event"
                                    readonly
                                    ></v-text-field>
                                <v-date-picker
                                    v-model="albaran.fecha_notificacion"
                                    no-title
                                    locale="es"
                                    first-day-of-week=1
                                    @input="menu5 = false"
                                ></v-date-picker>
                            </v-menu>
                        </v-flex>
                        <v-flex sm10>
                             <v-textarea
                                rows="2"
                                v-model="albaran.notas_int"
                                :error-messages="errors.collect('notas_int')"
                                v-validate="'max:191'"
                                data-vv-name="notas_int"
                                label="Observaciones uso interno"
                                :readonly="albaran.factura > 0"
                            >
                            </v-textarea>
                        </v-flex>

                    </v-layout>
                    <v-layout row wrap v-else>
                        <v-flex sm6>
                             <v-textarea
                                rows="2"
                                v-model="albaran.notas_int"
                                :error-messages="errors.collect('notas_int')"
                                v-validate="'max:191'"
                                data-vv-name="notas_int"
                                label="Observaciones uso interno"
                                :readonly="albaran.factura > 0"
                            >
                            </v-textarea>
                        </v-flex>
                        <v-flex sm6>
                            <v-textarea
                                rows="2"
                                v-model="albaran.notas_ext"
                                :error-messages="errors.collect('notas_ext')"
                                v-validate="'max:191'"
                                data-vv-name="notas_ext"
                                label="Observaciones formulario"
                                :readonly="albaran.factura > 0"
                            >
                            </v-textarea>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap v-if="albaran.cliente.notas > ''">
                        <v-flex sm12>
                            <v-text-field
                                v-model="notas_cliente"
                                append-icon="save"
                                label="Observaciones Cliente"
                                @click:append="updateNotaCli"
                            >
                            </v-text-field>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-form>
            <v-container v-if="albaran.id>0">
                <alba-lin
                    :albaran="albaran"
                    :albalin="alba_lin"
                ></alba-lin>
                <cobro-lin
                    :albaran="albaran"
                    :cobros_lin="cobros_lin"
                ></cobro-lin>
            </v-container>
        </v-card>
	</div>
</template>
<script>
import moment from 'moment'
import Loading from '@/components/shared/Loading'
import MenuOpe from './MenuOpe'
import Albalin from './AlbalinDel'
import Cobrolin from './CobroslinesDel'
import {mapGetters} from 'vuex';
import {mapActions} from "vuex";
	export default {
		$_veeValidate: {
      		validator: 'new'
        },
        components: {
            'menu-ope': MenuOpe,
            'alba-lin': Albalin,
            'cobro-lin': Cobrolin,
            'loading': Loading,
		},
    	data () {
      		return {
                titulo:"Albarán reubicado",
                albaran: {
                    id: 0,
                    facturar: false,


                },
                url: "/ventas/albaranes",
                ruta: "albaran",

                enviando: false,
                acuenta: 0,
                resto: "",
                totales: {},

                dialog_fac: false,
                dialog_abono: false,

                dialog_cobro: false,
                show_lincob: false,

                show: false,
                show_loading: true,
                menu1: false,
                menu5: false,

                fase: {
                    color:"",
                    nombre:""
                },
                alba_lin:[],
                cobros_lin:[],
                fpagos:[],
                cuentas:[],
                talleres:[],
                empresas:[],
                tipo_abono:"",
                notas_cliente:"",
                fixing: 0,
                low_fix: false,
      		}
        },
        beforeMount(){

            var id = this.$route.params.id;

            if (id > 0)
                axios.get(this.url+'/'+id)
                    .then(res => {

                        this.albaran = res.data.albaran;

                        if (res.data.parametros != false)
                            this.setAuthUser(res.data.parametros.user);

                        this.notas_cliente = this.albaran.cliente.notas;

                        if (this.albaran.tipo_id == 3)
                            this.titulo = '***REUBICADO*** '+this.albaran.tipo.nombre;
                        else
                            this.titulo = '***BORRADO*** '+this.albaran.tipo.nombre;

                        this.fase.nombre = this.albaran.fase.nombre;
                        this.fase.color = this.albaran.fase.color;

                        this.fpagos = res.data.fpagos;
                        this.cuentas = res.data.cuentas;
                        this.talleres = res.data.talleres;
                        this.empresas = res.data.empresas;

                        this.empresas.push({value: null, text: '-'});

                        this.alba_lin = res.data.albalin;
                        this.cobros_lin = res.data.cobroslin;

                        //this.reLoadCobros();

                    })
                    .catch(err => {
                        this.$toast.error(err.response.data.message);
                        this.$router.push({ name: this.ruta+'.index'})
                    })
                    .finally(() =>{
                        this.show = true;
                        this.show_loading = false;
                    });
        },
        computed: {
            ...mapGetters([
                'hasEdtFec',
                'isRoot',
                'hasFactura',
                'hasAddVen',
                'parametros'
            ]),
            computedFacturar(){

                /// esto dejarlo temporalmente
                // if (this.isRoot && this.albaran.fase_id >= 12 )
                //     return true;
                //

                if (this.albaran.id == 0  || !this.hasAddVen) return false;

                if (!this.hasFactura) return false;

                if (this.albaran.tipo_id == 3){ //REBU

                    if (this.albaran.factura == null && this.albaran.fase_id==11)
                        return true;

                    return false;
                }else{
                    if (this.albaran.factura == null)
                        return true;

                    return false;
                }
            },
            computedTaller(){
                return (this.factura > 0);
            },
            computedProcedenciaRebu(){
                if (this.albaran.tipo_id <=3 && this.albaran.procedencia_empresa_id !=null) return true;
            },
            computedLabelAlbaran(){
                return (this.albaran.factura == null) ? 'Nº Albarán' : 'Albarán '+ this.computedFechaAlbaran;
            },
            computedMotivo(){
                return (this.albaran.motivo_id > 0) ? (": "+this.albaran.motivo.nombre) :  "";
            },
            computedResto(){
                return (this.totales.total - this.acuenta).toFixed(2);
            },
            disabledFechaAlbaran(){
                if (this.albaran.factura > 0) return true;

                return !this.hasEdtFec;
            },
            computedNoResidente(){
                return this.albaran.iva_no_residente ? 'No residente' : 'Residente';
            },
            computedImporte(){
                return parseFloat(this.albaran.importe).toLocaleString('es-ES',{ style: 'currency', currency: 'EUR' });
            },
            computedFechaAlbaran() {
                moment.locale('es');
                return this.albaran.fecha_albaran ? moment(this.albaran.fecha_albaran).format('DD/MM/YYYY') : '';
            },
            computedFechaFactura() {
                moment.locale('es');
                return this.albaran.fecha_factura ? moment(this.albaran.fecha_factura).format('DD/MM/YYYY') : '';
            },
            computedFechaNotificacion() {
                moment.locale('es');
                return this.albaran.fecha_notificacion ? moment(this.albaran.fecha_notificacion).format('DD/MM/YYYY') : '';
            },
            computedFModFormat() {
                moment.locale('es');
                return this.albaran.updated_at ? moment(this.albaran.updated_at).format('D/MM/YYYY H:mm:ss') : '';
            },
            computedFCreFormat() {
                moment.locale('es');
                return this.albaran.created_at ? moment(this.albaran.created_at).format('D/MM/YYYY H:mm') : '';
            }

        },
        watch: {
        },
    	methods:{
            ...mapActions([
                'setAuthUser',
                'unsetParametros'
			]),
            getMoneyFormat(value){
                return new Intl.NumberFormat("de-DE",{style: "currency", currency: "EUR"}).format(parseFloat(value))
            },
            goAlbaran(){
                this.$router.push({ name: 'albaran.edit', params: { id: this.albaran.albaran_abonado_id } })
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
    padding-top: 1px;
    margin-top: 1px;
}


</style>
