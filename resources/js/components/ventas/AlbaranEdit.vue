<template>
	<div>
        <loading :show_loading="show_loading"></loading>
        <abono :dialog_abono.sync="dialog_abono" :albaran="albaran" :tipo_abono="tipo_abono"></abono>
        <v-card v-if="show">
            <v-card-title color="indigo">
                <h2 color="indigo">
                        {{titulo}}: <span v-if="show" :class="albaran.fase.color">{{albaran.fase.nombre}}</span>
                </h2>
                <h3 class="red--text darken-4">{{computedMotivo}}</h3>
                <v-spacer></v-spacer>
                <v-tooltip bottom>
                    <template v-slot:activator="{ on }">
                        <v-btn
                            v-show="computedReubicar"
                            v-on="on"
                            color="white"
                            icon
                            @click="goReubicar()"
                        >
                            <v-icon color="purple darken-4">shuffle</v-icon>
                        </v-btn>
                    </template>
                    <span>Reubicar Albarán</span>
                </v-tooltip>
                <v-tooltip bottom>
                    <template v-slot:activator="{ on }">
                        <v-btn
                            v-show="albaran.albaran_abonado_id > 0"
                            v-on="on"
                            color="white"
                            icon
                            @click="goAlbaran()"
                        >
                            <v-icon color="purple darken-4">replay</v-icon>
                        </v-btn>
                    </template>
                    <span>Ir a albarán abono/abonado</span>
                </v-tooltip>
                <v-tooltip bottom>
                    <template v-slot:activator="{ on }">
                        <v-btn
                            v-show="computedShowGoCobros"
                            v-on="on"
                            color="white"
                            icon
                            @click="goCobros()"
                        >
                            <v-icon color="green">euro</v-icon>
                        </v-btn>
                    </template>
                    <span>Cobrar Albarán</span>
                </v-tooltip>
                <v-tooltip bottom>
                    <template v-slot:activator="{ on }">
                        <v-btn
                            v-show="albaran.id>0  && hasAddVen"
                            :disabled="!computedAbonar"
                            v-on="on"
                            color="white"
                            icon
                            @click="goAbonar('A')"
                        >
                            <v-icon color="red accent-4">spellcheck</v-icon>
                        </v-btn>
                    </template>
                    <span>Abonar Factura</span>
                </v-tooltip>
                <v-tooltip bottom>
                    <template v-slot:activator="{ on }">
                        <v-btn
                            v-show="albaran.id>0 && hasAddVen"
                            :disabled="!computedCancelar"
                            v-on="on"
                            color="white"
                            icon
                            @click="goAbonar('C')"
                        >
                            <v-icon color="red accent-4">clear</v-icon>
                        </v-btn>
                    </template>
                    <span>Cancelar Albarán</span>
                </v-tooltip>
                <v-tooltip bottom>
                    <template v-slot:activator="{ on }">
                        <v-btn
                            v-show="computedFacturar"
                            v-on="on"
                            color="white"
                            icon
                            :disabled="!albaran.facturar"
                            @click="facturar()"
                        >
                            <v-icon color="teal accent-4">lock_open</v-icon>
                        </v-btn>
                    </template>
                    <span>Facturar</span>
                </v-tooltip>
                <v-tooltip bottom>
                    <template v-slot:activator="{ on }">
                        <v-btn
                            v-show="albaran.id > 0 && hasAddVen"
                            :disabled="!(albaran.factura==null && isAdmin)"
                            v-on="on"
                            color="white"
                            icon
                            @click="facturar_sino()"
                        >
                            <v-icon v-if="albaran.facturar" color="teal accent-4">toggle_on</v-icon>
                            <v-icon v-else color="orange accent-4">toggle_off</v-icon>
                        </v-btn>
                    </template>
                    <span>Permite facturar un albarán</span>
                </v-tooltip>
                <v-tooltip bottom>
                    <template v-slot:activator="{ on }">
                        <v-btn
                            v-show="computedDesFacturar"
                            v-on="on"
                            color="white"
                            icon
                            @click="goDesfacturar()"
                        >
                            <v-icon color="teal accent-4">lock</v-icon>
                        </v-btn>
                    </template>
                    <span>Desfacturar</span>
                </v-tooltip>
                <v-tooltip bottom>
                    <template v-slot:activator="{ on }">
                        <v-btn
                            v-on="on"
                            color="white"
                            icon
                            @click="show_lincob = !show_lincob"
                        >
                            <v-icon color="black">aspect_ratio</v-icon>
                        </v-btn>
                    </template>
                    <span>Alternar productos/cobros</span>
                </v-tooltip>
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
                                @click:append="facturarHoy()"
                                append-icon="priority_high"
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
                        <v-flex sm2>
                            <div class="text-xs-center">
                                <v-btn
                                    v-show="albaran.factura == null && hasAddVen"
                                    small
                                    @click="submit"  round  :loading="enviando" block  color="primary">
                                    Guardar
                                </v-btn>
                            </div>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap v-else>
                        <v-flex sm5>
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
                        <v-flex sm5>
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
                        <v-flex sm2>
                            <div class="text-xs-center">
                                <v-btn
                                    v-show="albaran.factura == null && hasAddVen"
                                    small
                                    @click="submit"  round  :loading="enviando" block  color="primary">
                                    Guardar
                                </v-btn>
                            </div>
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
                    v-show="!show_lincob"
                    :totales.sync="totales"
                    :acuenta="acuenta"
                    :albaran.sync="albaran"
                ></alba-lin>
                <cobro-lin
                    v-show="show_lincob"
                    :acuenta.sync="acuenta"
                    :totales="totales"
                    :cobros_lin.sync="cobros_lin"
                    :albaran.sync="albaran"
                ></cobro-lin>

                <cobro-create
                    :resto="computedResto"
                    :albaran.sync="albaran"
                    :dialog_cobro.sync="dialog_cobro"
                >
                </cobro-create>
                <facturar
                    :albaran.sync="albaran"
                    :dialog_fac.sync="dialog_fac"
                >
                </facturar>
            </v-container>
        </v-card>
	</div>
</template>
<script>
import moment from 'moment'
import Loading from '@/components/shared/Loading'
import MenuOpe from './MenuOpe'
import Albalin from './Albalin'
import Cobrolin from './Cobroslines'
import CobroCreate from './CobroCreate'
import Facturar from './Facturar'
import Abono from './Abono'
import {mapGetters} from 'vuex';
import {mapActions} from "vuex";
import {mapState} from 'vuex'
	export default {
		$_veeValidate: {
      		validator: 'new'
        },
        components: {
            'menu-ope': MenuOpe,
            'alba-lin': Albalin,
            'cobro-lin': Cobrolin,
            'cobro-create': CobroCreate,
            'facturar': Facturar,
            'loading': Loading,
            'abono': Abono
		},
    	data () {
      		return {
                titulo:"Albaranes",
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
                cobros_lin:[],
                fpagos:[],
                cuentas:[],
                talleres:[],
                empresas:[],
                tipo_abono:"",
                notas_cliente:"",

      		}
        },
        beforeMount(){

            var id = this.$route.params.id;

            if (id > 0)
                axios.get(this.url+'/'+id+'/edit')
                    .then(res => {

                        this.albaran = res.data.albaran;

                        if (res.data.parametros != false)
                            this.setAuthUser(res.data.parametros.user);

                        this.notas_cliente = this.albaran.cliente.notas;

                        this.titulo = this.albaran.tipo.nombre;

                        this.fase.nombre = this.albaran.fase.nombre;
                        this.fase.color = this.albaran.fase.color;

                        this.fpagos = res.data.fpagos;
                        this.cuentas = res.data.cuentas;
                        this.talleres = res.data.talleres;
                        this.empresas = res.data.empresas;

                        this.empresas.push({value: null, text: '-'});


                        this.reLoadCobros();

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
                'isSupervisor',
                'isAdmin',
                'isRoot',
                'hasFactura',
                'hasAddVen'
            ]),
            computedTaller(){
                return (this.factura > 0);
            },
            computedProcedenciaRebu(){
                if (this.albaran.tipo_id <=3 && this.albaran.procedencia_empresa_id !=null) return true;
            },
            computedReubicar(){

                if (!this.isRoot) return false;

                if (this.albaran.factura == null && this.albaran.fase_id == 11)
                    return true;

                return false;
            },
            computedShowGoCobros(){

                if (!this.hasAddVen) return false;

                if (this.albaran.fase_id == 10 && this.computedResto != 0)
                    return true;
                return false;
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

                return !this.isSupervisor;
            },
            computedAbonar(){
                if (this.totales.total == 0)
                    return false;

                    // si es factura manual/auto o no está facturado, no permito abonar abonos...
                if (this.albaran.tipo_factura <= 2 && this.albaran.fase_id < 12){
                    return this.isSupervisor;
                }

                return false;
            },
            computedCancelar(){
                if (this.totales.total == 0)
                    return false;

                    // si es factura manual o no está facturado ni vendido
                if (this.albaran.tipo_factura <= 1 && this.albaran.fase_id == 10){
                    return this.isSupervisor;
                }

                return false;
            },
            computedFacturar(){

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
            computedDesFacturar(){

                if (!this.hasFactura || !this.hasAddVen) return false;

                //if (this.albaran.factura > 0 && this.albaran.fase_id ==11)
                if (this.albaran.factura > 0)
                    return true;

                return false;
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
            dialog_cobro: function () {

                if (this.dialog_cobro == false && this.show){
                    this.reLoadCobros();
                }

            }
        },
    	methods:{
            ...mapActions([
                'setAuthUser',
                'unsetParametros'
			]),
            getMoneyFormat(value){
                return new Intl.NumberFormat("de-DE",{style: "currency", currency: "EUR"}).format(parseFloat(value))
            },
            reLoadCobros(){
                axios.get('/ventas/cobros/'+this.albaran.id)
                    .then(res => {
                        this.cobros_lin = res.data.lineas;
                        this.acuenta = parseFloat(res.data.acuenta);
                        this.albaran = res.data.albaran;
                    })
                    .catch(err => {
                        if (err.response.status == 404)
                            this.$toast.error("Albarán No encontrado!");
                        else
                            this.$toast.error(err.response.data.message);
                        this.$router.push({ name: 'albaran.index'})
                    })

            },
            goCobros(){
                this.show_lincob = true;
                this.dialog_cobro = true;
            },
            goReubicar(){
                if (confirm("Reubicar Albarán?")){
                    this.enviando = true;
                    this.show_loading = true;

                    axios.get("/ventas/reubicar/"+this.albaran.id+"/albaran")
                        .then(res => {

                            this.$toast.success("Albarán reubicado!");
                            this.$router.push({ name: 'albaran.index'});
                        })
                        .catch(err => {
                            this.$toast.error(err.response.data.message);
                        })
                        .finally( () =>{
                            this.enviando = false;
                            this.show_loading = false;
                        });
                }

            },
            goAbonar(tipo_abono){
                //this.$emit('update:dialog_abono', op);
                this.tipo_abono = tipo_abono;
                this.dialog_abono = true;
            },
            goAlbaran(){
                this.$router.push({ name: 'albaran.edit', params: { id: this.albaran.albaran_abonado_id } })
            },
            submit() {
                this.$validator.validateAll().then((result) => {
                    if (result){
                        if (this.albaran.fpago_id != 2)
                            this.albaran.cuenta_id = null;

                        this.show_loading = true;
                        this.enviando = true;
                        axios.put(this.url+"/"+this.albaran.id, this.albaran)
                            .then(res => {
                                this.albaran = res.data.albaran;
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
                            .finally( () =>{
                                // always executed
                                this.enviando = false;
                                this.show_loading = false;
                            });
                        }
                });
            },
            facturar(){
                this.dialog_fac = true;
            },
            facturarHoy(){
                this.show_loading = true;
                axios.put(this.url+"/"+this.albaran.id+"/facturar")
                    .then(res => {
                        this.albaran = res.data.albaran;
                    })
                    .catch(err => {
                        this.$toast.error(err.response.data.message);
                    })
                    .finally( () =>{
                        this.show_loading = false;
                    });

            },
            goDesfacturar(){
                if (confirm('¿Quieres continuar para DESFACTURAR?')){
                    this.show_loading = true;
                    axios.put(this.url+"/"+this.albaran.id+"/desfacturar")
                        .then(res => {
                            this.albaran = res.data.albaran;
                            if (res.data.contador.estado)
                                this.$toast.success('Desfacturado! '+res.data.contador.msg);
                            else
                                this.$toast.warning(res.data.contador.msg);
                        })
                        .catch(err => {
                            this.$toast.error(err.response.data.message);
                        })
                        .finally( () =>{
                            this.show_loading = false;
                        });
                }
            },
            facturar_sino(){
                this.show_loading = true;
                axios.put(this.url+"/"+this.albaran.id+"/actfac")
                    .then(res => {
                        this.albaran = res.data.albaran;
                    })
                    .catch(err => {
                        this.$toast.error(err.response.data.message);
                    })
                    .finally( () =>{
                        this.show_loading = false;
                    });
            },
            updateNotaCli(){

                axios.put("/mto/clientes/"+this.albaran.cliente_id+"/obs", {notas: this.notas_cliente})
                    .then(res => {
                        this.$toast.success(res.data.message);
                        // this.loading = false;
                    })
                    .catch(err => {
                        this.$toast.error(err.response.data.message);
                        // this.loading = false;
                    });
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
