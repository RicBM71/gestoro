<template>
	<div>
        <loading :show_loading="show_loading"></loading>
        <v-card  v-if="show">
            <v-card-title color="indigo">
                <h2 color="indigo">{{titulo}}: <span v-if="show" :class="compra.fase.color">{{compra.fase.nombre}}</span></h2>
                <v-spacer></v-spacer>
                <v-tooltip bottom>
                    <template v-slot:activator="{ on }">
                        <v-btn
                            v-show="compra.fase_id == 4 && hasAddCom"
                            v-on="on"
                            color="white"
                            icon
                            :disabled="!computedAmpliar"
                            @click="goAmpliar()"
                        >
                            <v-icon color="purple darken-2">timer</v-icon>
                        </v-btn>
                    </template>
                    <span>Ampliar periodo</span>
                </v-tooltip>
                <v-tooltip bottom>
                    <template v-slot:activator="{ on }">
                        <v-btn
                            v-show="compra.fase_id == 4  && hasAddCom"
                            v-on="on"
                            color="white"
                            icon
                            :disabled="!computedAmpliarCapital"
                            @click="goCapital()"
                        >
                            <v-icon color="purple darken-2">post_add</v-icon>
                        </v-btn>
                    </template>
                    <span>Ampliar Capital</span>
                </v-tooltip>
                <v-tooltip bottom>
                    <template v-slot:activator="{ on }">
                        <v-btn
                            v-show="compra.fase_id == 4  && hasAddCom"
                            v-on="on"
                            color="white"
                            icon
                            :disabled="computedDisabledAcuenta"
                            @click="goAcuenta()"
                        >
                            <v-icon color="purple darken-2">trending_down</v-icon>
                        </v-btn>
                    </template>
                    <span>A Cuenta</span>
                </v-tooltip>
                <v-tooltip bottom>
                    <template v-slot:activator="{ on }">
                        <v-btn
                            v-show="compra.fase_id ==4 && hasAddCom"
                            v-on="on"
                            color="white"
                            icon
                            :disabled="computedDisabledRecuperar"
                            @click="goRecuperar()"
                        >
                            <v-icon color="purple darken-2">check_circle_outline</v-icon>
                        </v-btn>
                    </template>
                    <span>Recuperar</span>
                </v-tooltip>
                <v-tooltip bottom>
                    <template v-slot:activator="{ on }">
                        <v-btn
                            v-if="cambio_recompra"
                            v-show="compra.fase_id <=4  && hasAddCom"
                            v-on="on"
                            color="white"
                            icon
                            @click="goComprar()"
                        >
                            <v-icon color="teal darken-1">shopping_cart</v-icon>
                        </v-btn>
                        <v-btn
                            v-else
                            v-show="compra.fase_id <=4  && hasAddCom"
                            v-on="on"
                            color="white"
                            icon
                            @click="cambiarTipo()"
                        >
                            <v-icon color="teal darken-1">shopping_cart</v-icon>
                        </v-btn>
                    </template>
                    <span>Comprar</span>
                </v-tooltip>
                <v-tooltip bottom>
                    <template v-slot:activator="{ on }">
                        <v-btn
                            v-on="on"
                            color="white"
                            icon
                            @click="show_lindepo = !show_lindepo"
                        >
                            <v-icon color="black">aspect_ratio</v-icon>
                        </v-btn>
                    </template>
                    <span>Alternar conceptos/importes</span>
                </v-tooltip>
                 <v-tooltip bottom>
                    <template v-slot:activator="{ on }">
                        <v-btn
                            v-show="hasLiquidar"
                            :disabled="!computedAuthLiquidar"
                            v-on="on"
                            color="white"
                            icon
                            @click="goLiquidar()"
                        >
                            <v-icon color="orange darken-4">fireplace</v-icon>
                        </v-btn>
                    </template>
                    <span>Liquidar Lote</span>
                </v-tooltip>
                <menu-ope :compra.sync="compra" :refresh.sync="refresh"  :docu_ok="docu_ok"></menu-ope>
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
                            <v-text-field
                                v-model="computedFechaCompra"
                                label="Fecha Compra"
                                readonly
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm2>
                            <v-text-field
                                class="centered-input font-weight-bold"
                                v-model="compra.alb_ser"
                                label="Nº Registro"
                                readonly
                            >
                            </v-text-field>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex sm2>
                            <v-text-field
                                v-if="compra.factura > 0"
                                v-model="compra.fac_ser"
                                :label="computedFechaFactura"
                                readonly
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm2 v-if="compra.fase_id <=4">
                             <v-dialog
                                ref="dialog"
                                v-model="modal"
                                :return-value.sync="compra.fecha_recogida"
                                persistent
                                offset-y
                                lazy
                                full-width
                                width="290px"
                            >
                                <template v-slot:activator="{ on }">
                                <v-text-field
                                   :value="computedFechaRecogida"
                                    label="Fecha Recogida"
                                    prepend-icon="event"
                                    readonly
                                    clearable
                                    @click:clear="clearDate"
                                    v-on="on"
                                ></v-text-field>
                                </template>
                                <v-date-picker
                                    v-model="compra.fecha_recogida"
                                    locale="es"
                                    first-day-of-week=1
                                    scrollable
                                >
                                <v-spacer></v-spacer>
                                <v-btn flat color="primary" @click="modal = false">Cancelar</v-btn>
                                <v-btn flat color="primary" @click="updateRecogida(computedFechaRecogida)">OK</v-btn>
                                </v-date-picker>
                            </v-dialog>
                        </v-flex>
                        <v-flex sm2 v-else></v-flex>
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
                                v-model="computedTotalAmpliaciones"
                                label="Intereses"
                                readonly
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm2 v-if = "compra.fase_id != 5 || compra.fecha_recogida == null">
                            <v-text-field
                                v-model="computedFechaRenovacion"
                                label="Fecha Renovación"
                                readonly
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm2 v-else>
                            <v-text-field
                                v-model="computedFechaRecogida"
                                label="Fecha Recuperación"
                                readonly
                            >
                            </v-text-field>
                        </v-flex>

                        <v-flex sm2>
                            <v-text-field
                                class="inputPrice"
                                v-model="computedImportePres"
                                label="Importe Préstamo"
                                readonly
                            >
                            </v-text-field>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex sm2>
                            <v-text-field
                                v-model="computedFModFormat"
                                :label="computedTxtMod"
                                readonly
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm2>
                            <v-text-field
                                v-model="compra.papeleta"
                                label="Papeleta"
                                readonly
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm1>
                            <v-text-field
                                class="centered-input"
                                v-model="computedInteres"
                                label="Interes %"
                                readonly
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm1 v-if="compra.retraso <= 0">
                            <v-text-field
                                v-show="compra.fase_id <=4"
                                class="centered-input"
                                v-model="compra.resto_custodia"
                                label="Resto Custodia"
                                readonly
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm1 v-else>
                            <v-text-field
                                v-show="compra.fase_id <=4"
                                class="centered-input"
                                v-model="compra.retraso"
                                label="Días Retraso"
                                error
                                readonly
                            >
                            </v-text-field>
                        </v-flex>

                        <v-flex sm2>
                            <v-text-field
                                class="inputPrice"
                                v-model="computedACuenta"
                                label="Importe a Cuenta"
                                readonly
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm2>
                            <v-text-field
                                class="inputPrice"
                                v-model="computedImpRenova"
                                label="Importe Renovación"
                                readonly
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm2>
                            <v-text-field
                                class="inputPrice"
                                v-model="computedImpRecu"
                                label="Importe Recuperación"
                                readonly
                            >
                            </v-text-field>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex sm9>
                            <v-text-field
                                v-model="compra.notas"
                                append-icon="save"
                                label="Observaciones Compra"
                                @click:append="updateNota"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm1></v-flex>
                        <v-flex sm2 v-show="compra.fase_id <=4 && hasAddCom">
                            <div class="text-xs-center">
                                <v-btn
                                    small
                                    :disabled="!computedReabrir"
                                    @click="goFase"  round  :loading="loading" block  color="primary">
                                    Reabrir Lote
                                </v-btn>
                            </div>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap v-if="compra.cliente.notas!=null">
                        <v-flex sm12>
                            <v-text-field
                                v-model="compra.cliente.notas"
                                append-icon="save"
                                label="Observaciones Cliente"
                                @click:append="updateNotaCli"
                            >
                            </v-text-field>
                        </v-flex>
                    </v-layout>
                        <div v-if="compra.id>0">
                            <com-lines v-show="!show_lindepo"
                                :compra.sync="compra"
                                :compra_id="compra.id"
                                :grabaciones="grabaciones"
                                :refresh_lin.sync="refresh_lin"
                            >
                            </com-lines>
                            <depo-lines v-show="show_lindepo"
                                :compra.sync="compra"
                                :conceptos="conceptos"
                                :lineas.sync="lineas_deposito"
                                :refresh_lin.sync="refresh_lin">
                            </depo-lines>
                            <comprar-create
                                v-if="dialog_com"
                                :compra.sync="compra"
                                :deposito_com="deposito"
                                :dialog.sync="dialog_com"
                                :lineas.sync="lineas_deposito">
                            </comprar-create>
                            <ampliar-create
                                v-if="dialog_amp"
                                :compra.sync="compra"
                                :ampliacion="deposito"
                                :dialog_amp.sync="dialog_amp"
                                :lineas.sync="lineas_deposito">
                            </ampliar-create>
                            <acuenta-create
                                v-if="dialog_acuenta"
                                :compra.sync="compra"
                                :deposito_acuenta="deposito"
                                :dialog.sync="dialog_acuenta"
                                :lineas.sync="lineas_deposito">
                            </acuenta-create>
                            <capital-create
                                v-if="dialog_capital"
                                :compra.sync="compra"
                                :deposito_capital="deposito"
                                :dialog.sync="dialog_capital"
                                :lineas.sync="lineas_deposito"
                                :refresh_lin.sync="refresh_lin">
                            </capital-create>
                            <recuperar-create
                                v-if="dialog_recu"
                                :compra.sync="compra"
                                :deposito_recu="deposito"
                                :dialog.sync="dialog_recu"
                                :lineas.sync="lineas_deposito"
                                >
                            </recuperar-create>
                        </div>
                </v-container>
            </v-form>
        </v-card>
	</div>
</template>
<script>
import moment from 'moment'
import Loading from '@/components/shared/Loading'
import MenuOpe from './MenuOpe'
import Comlines from './Comlines'
import DepoLines from './DepoLines'
import Comprar from './Comprar'
import Ampliar from './Ampliar'
import Acuenta from './Acuenta'
import Capital from './Capital'
import Recuperar from './Recuperar'
import {mapGetters} from 'vuex';
	export default {
		$_veeValidate: {
      		validator: 'new'
        },
        components: {
            'menu-ope': MenuOpe,
            'com-lines': Comlines,
            'depo-lines': DepoLines,
            'comprar-create': Comprar,
            'ampliar-create': Ampliar,
            'acuenta-create': Acuenta,
            'capital-create': Capital,
            'recuperar-create': Recuperar,
            'loading': Loading,
		},
    	data () {
      		return {
                titulo:"Operación: ",
                compra: {},
                url: "/compras/compras",
                ruta: "compra",
                grupos: [],
                conceptos: [],
                totales_concepto: [0,0,0,0,0],
                lineas_deposito: [],

                valor_compras: 0,

        		status: false,
                loading: false,

                show: false,
                show_loading: true,
                show_lindepo: true,
                menu1: false,
                fase: {
                    color:"",
                    nombre:""
                },
                dialog_amp: false,
                dialog_com: false,
                dialog_acuenta: false,
                dialog_recu: false,
                dialog_capital: false,

                deposito: {
                    compra_id:0,
                    cliente_id:0,
                    fecha: new Date().toISOString().substr(0, 10),
                    importe:0,
                    concepto_id:0,
                    dias:0,
                    iban:"",
                    bic:""
                },
                ampliacion: [],
                modal: false,

                docu_ok: false,
                refresh: 0,
                refresh_lin: 0,
                grabaciones: false,
                dias_cortesia: 0,
                cambio_recompra: false,

      		}
        },
        beforeMount(){

            var id = this.$route.params.id;

            if (id > 0)
                //axios.get(this.url+'/'+id+'/edit')
                axios.get(this.url+'/'+id)
                    .then(res => {

                        this.compra = res.data.compra;
                        this.grabaciones = res.data.grabaciones;
                        this.dias_cortesia = res.data.dias_cortesia;
                        this.cambio_recompra = res.data.cambio_recompra;

                        this.docu_ok = res.data.documentos.status > 0 ? true : false;

                        this.valor_compras = res.data.valor_compras;

                        this.lineas_deposito = res.data.lineas_deposito;

                        if (this.compra.tipo_id == 2){
                            this.$router.push({ name: 'compra.close', params: { id: this.compra.id } })
                        }

                        if (this.compra.fase_id <= 2){
                            this.$router.push({ name: 'compra.edit', params: { id: this.compra.id } })
                        }

                        this.titulo = this.compra.tipo.nombre;

                        this.fase.nombre = this.compra.fase.nombre;
                        this.fase.color = this.compra.fase.color;

                        this.fpago = this.compra.cliente.fpago_id;

                        this.show_lindepo = (this.compra.fase_id == 6) ? false : true;

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
                'isSupervisor',
                'isAdmin',
                'hasLiquidar',
                'parametros',
                'hasReaCompras',
                'userName',
                'hasAddCom'
            ]),
            computedAuthLiquidar(){

                if (this.compra.fase_id !=5 || this.compra.resto_custodia < 0){ // no está recuperado
                    const hoy = new Date().toISOString().substr(0, 10);
                    return (hoy > this.compra.fecha_bloqueo);
                }else{
                    return false;
                }

            },
            computedAmpliar(){

                return true;

                // if (this.isSupervisor) return false;

                // return (new Date() < new Date(this.compra.fecha_bloqueo)) ? true : false;

            },
            computedAmpliarCapital(){

                if (this.compra.fase_id != 4) return false;

                if (this.isAdmin)
                    return true;
                else

                return (this.compra.retraso <= 0)
                // lo dejamos para cualquier User JL.
                //return !this.isSupervisor;

            },
            computedDisabledAcuenta(){

                // con esto un supervisor, tiene que hacer una ampliación con importe a cero, así queda constancia
                 // lo dejo para poder hacer ampliaciones en negativo, es decir, aumenta préstamo.

                if (new Date() < new Date(this.compra.fecha_bloqueo))
                    return true; // está bloqueado por fecha
                else{

                    if (this.isSupervisor) return false;

                    if (this.totales_concepto[1] == 0) // si no hay retraso y no hay ninguna ampliación adicional.
                        return (this.compra.retraso > this.dias_cortesia) ? true : false;
                    else    // damos cuartelillo en la primera ampliación, en el resto hay que pagar intereses salvo un administrador.
                        return (this.compra.retraso > 0) ? true : false;
                    }
            },
            computedDisabledRecuperar(){

                // con esto un supervisor, tiene que hacer una ampliación con importe a cero, así queda constancia
                if (this.isAdmin) return false; // lo dejo para poder recupear aún bloqueado.

                if (new Date() < new Date(this.compra.fecha_bloqueo))
                    return true; // está bloqueado por fecha
                else{
                    if (this.totales_concepto[1] == 0)
                        return (this.compra.retraso > this.dias_cortesia) ? true : false;
                    else
                        return (this.compra.retraso > 0) ? true : false;
                    }
            },
            computedReabrir(){

                const hoy = new Date().toISOString().substr(0, 10);

                if (this.compra.created_at.substr(0, 10) == hoy){
                    if (this.compra.username == this.userName || this.isSupervisor)
                        return true;
                    else
                        return this.isSupervisor;
                }else{
                    return this.hasReaCompras;
                }

            },
            computedInteres(){
                return this.getDecimalFormat(this.compra.interes);
            },
            computedValorCompras(){
                return this.getMoneyFormat(parseFloat(this.compra.importe)+parseFloat(this.valor_compras));
            },
            computedImpRenova(){
              //  return this.getMoneyFormat(this.compra.imp_renova);
                return this.getMoneyFormat(this.compra.importe_renovacion);
            },
            computedACuenta(){
                return this.getMoneyFormat(this.compra.importe_acuenta);
            },
            computedImpRecu(){
                return this.getMoneyFormat(this.compra.imp_recu);
            },
            computedImportePres(){
                return this.getMoneyFormat(this.compra.imp_pres);
            },
            computedTotalAmpliaciones(){
                return this.getMoneyFormat(this.totales_concepto[1]);
            },
            computedTotalACuenta(){
                return this.getMoneyFormat(this.totales_concepto[2]);
            },
            computedFechaRenovacion() {
                moment.locale('es');
                return this.compra.fecha_renovacion ? moment(this.compra.fecha_renovacion).format('L') : '';
            },
            computedFechaCompra() {
                moment.locale('es');
                return this.compra.fecha_compra ? moment(this.compra.fecha_compra).format('L') : '';
            },
             computedFechaFactura() {
                moment.locale('es');
                return this.compra.fecha_factura ? "Factura "+moment(this.compra.fecha_factura).format('L') : '';
            },
            computedFechaRecogida() {
                moment.locale('es');
                return this.compra.fecha_recogida ? moment(this.compra.fecha_recogida).format('L') : '';
            },
            computedFechaFactura() {
                moment.locale('es');
                return this.compra.fecha_factura ? "Factura "+moment(this.compra.fecha_factura).format('L') : '';
            },
            computedFechaBloqueo() {
                moment.locale('es');
                return this.compra.fecha_bloqueo ? moment(this.compra.fecha_bloqueo).format('L') : '';
            },
            computedTxtMod(){
                return "Mod. "+this.compra.username;
            },
            computedUsername() {
                moment.locale('es');
                return this.compra.updated_at ? "Observaciones - "+this.compra.username+" "+moment(this.compra.updated_at).format('DD/MM/YYYY H:mm:ss') : '';
            },
            computedFModFormat() {
                moment.locale('es');
                return this.compra.updated_at ? moment(this.compra.updated_at).format('DD/MM/YYYY H:mm:ss') : '';
            }
        },
        watch: {
            refresh () {
                 axios.get(this.url+'/'+this.compra.id)
                    .then(res => {
                         this.compra = res.data.compra;
                         this.titulo = this.compra.tipo.nombre;
                         })
                    .catch(err => {
                        this.$toast.error(err.response.data.message);
                        this.$router.push({ name: this.ruta+'.index'})
                    })
            },
            compra: function () {

                axios.post("/utilidades/helpdepo",{compra_id: this.compra.id})
                    .then(res => {
                        this.totales_concepto = res.data.totales_concepto;
                    })

                this.deposito.dias =  (this.compra.retraso > 0) ? this.compra.retraso : this.compra.dias_custodia;

            },
            // computedFechaRecogida: function (){

            //     axios.put(this.url+"/"+this.compra.id+"/recogida", {fecha_recogida:this.compra.fecha_recogida})
            //         .then(res => {
            //             this.$toast.success(res.data.message);
            //         })

            // }
        },
    	methods:{
            getDecimalFormat(value){
                return new Intl.NumberFormat("de-DE",{style: "decimal",minimumFractionDigits:2}).format(parseFloat(value))
            },
            getMoneyFormat(value){
                //return value;
                return new Intl.NumberFormat("de-DE",{style: "currency", currency: "EUR"}).format(parseFloat(value))
            },
            goComprar(){

                this.deposito.cliente_id = this.compra.cliente_id;
                this.deposito.compra_id = this.compra.id;
                this.deposito.importe = 0;
                this.dialog_com = true;

            },
            cambiarTipo(){
                if (this.compra.fase_id <= 4){
                    this.loading = true;

                    axios.put(this.url+"/"+this.compra.id+"/tipo",{tipo_id: 2})
                        .then(res => {
                            this.$router.push({ name: 'compra.close', params: { id: this.compra.id } })
                            this.loading = false;
                        })
                        .catch(err => {
                            this.$toast.error(err.response.data.message);
                            this.loading = false;
                        });
                }
            },
            goAmpliar(){
                this.show_loading = true;
                this.show_lindepo = true;
                axios.get("/compras/ampliaciones/"+this.compra.id)
                    .then(res => {

                        this.deposito.cliente_id = this.compra.cliente_id;
                        this.deposito.compra_id = this.compra.id;

                        this.deposito.importe = res.data.ampliacion['importe'];
                        this.deposito.dias = res.data.ampliacion['dias'];

                        this.dialog_amp = true;
                        this.show_loading = false;
                    })
                    .catch(err => {
                        this.$toast.error(err.response.data.message);
                        this.$router.push({ name:   'compra.index'})
                    })
            },
            goAcuenta(){
                this.show_loading = true;
                this.show_lindepo = true;
                axios.get("/compras/acuenta/"+this.compra.id)
                    .then(res => {

                        this.deposito.cliente_id = this.compra.cliente_id;
                        this.deposito.compra_id = this.compra.id;
                        this.deposito.importe = "";

                        this.dialog_acuenta = true;
                        this.show_loading = false;
                    })
                    .catch(err => {
                        this.$toast.error(err.response.data.message);
                        this.$router.push({ name:   'compra.index'})
                    })
            },
            goCapital(){
                this.show_loading = true;
                this.show_lindepo = true;
                axios.get("/compras/capital/"+this.compra.id)
                    .then(res => {

                        this.deposito.cliente_id = this.compra.cliente_id;
                        this.deposito.compra_id = this.compra.id;
                        this.deposito.importe = "";

                        this.dialog_capital = true;

                        this.show_loading = false;
                    })
                    .catch(err => {
                        this.$toast.error(err.response.data.message);
                        this.$router.push({ name: 'compra.index'})
                    })
            },
            goRecuperar(){
                this.show_loading = true;
                this.show_lindepo = true;
                axios.get("/compras/recuperar/"+this.compra.id)
                    .then(res => {

                        this.compra = res.data.compra;
                        this.deposito.cliente_id = this.compra.cliente_id;
                        this.deposito.compra_id = this.compra.id;
                        this.deposito.importe = this.compra.imp_recu;

                        this.dialog_recu = true;
                        this.show_loading = false;
                    })
                    .catch(err => {
                        this.$toast.error(err.response.data.message);
                        this.$router.push({ name:   'compra.index'})
                    })
            },
            updateNota(){
                if (this.compra.fase_id > 0 &&  this.hasAddCom){
                    // this.loading = true;
                    axios.put(this.url+"/"+this.compra.id+"/obs", this.compra)
                        .then(res => {
                            this.$toast.success(res.data.message);
                            this.compra = res.data.compra;
                            // this.loading = false;
                        })
                        .catch(err => {
                            this.$toast.error(err.response.data.message);
                            // this.loading = false;
                        });
                }
            },
            updateNotaCli(){

                axios.put("/mto/clientes/"+this.compra.cliente_id+"/obs", {notas: this.compra.cliente.notas})
                    .then(res => {
                        this.$toast.success(res.data.message);
                        // this.loading = false;
                    })
                    .catch(err => {
                        this.$toast.error(err.response.data.message);
                        // this.loading = false;
                    });
            },
            goFase() {

                if (this.loading === false){
                    this.loading = true;

                    axios.put(this.url+"/"+this.compra.id+"/fase",{fase_id: 2} )
                        .then(res => {
                            this.$router.push({ name: this.ruta+'.edit', params: { id: this.compra.id } })
                            this.loading = false;
                        })
                        .catch(err => {
                            this.$toast.error(err.response.data.message);
                            this.loading = false;
                        });
                }
            },
            updateRecogida(f){
                this.$refs.dialog.save(this.compra.fecha_recogida);
                axios.put(this.url+"/"+this.compra.id+"/recogida", {fecha_recogida:this.compra.fecha_recogida})
                    .then(res => {
                        this.compra = res.data.compra;
                        this.$toast.success(res.data.message);
                    })

            },
            clearDate(){
                //this.$emit('update:compra.fecha_recogida', null);
                //this.compra.fecha_recogida = null;

                axios.put(this.url+"/"+this.compra.id+"/recogida", {fecha_recogida:null})
                    .then(res => {
                        this.$toast.success(res.data.message);
                    })
            },
            goLiquidar(){
                 this.$router.push({ name: 'compra.liquidar', params: {compra_id: this.compra.id } })
            }

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
