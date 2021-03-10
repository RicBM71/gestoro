<template>
    <div>
        <loading :show_loading="show_loading"></loading>
        <v-card v-show="!show_loading">
            <v-card-title color="indigo">
                <h2 color="indigo">{{titulo}}</h2>
                <v-spacer></v-spacer>
                <v-tooltip bottom>
                <template v-slot:activator="{ on }">
                    <v-btn
                        v-show="compra.id > 0"
                        v-on="on"
                        color="white"
                        icon
                        @click="goCliente"
                    >
                        <v-icon color="primary">person</v-icon>
                    </v-btn>
                </template>
                <span>Ir a cliente</span>
            </v-tooltip>
            <v-tooltip bottom>
                <template v-slot:activator="{ on }">
                    <v-btn
                        v-on="on"
                        color="white"
                        icon
                        @click="goBackCompra()"
                    >
                        <v-icon color="primary">shopping_cart</v-icon>
                    </v-btn>
                </template>
                <span>Volver a compra</span>
            </v-tooltip>
            <v-tooltip bottom>
                    <template v-slot:activator="{ on }">
                        <v-btn
                            v-on="on"
                            color="white"
                            icon
                            @click="goBack()"
                        >
                            <v-icon color="primary">arrow_back</v-icon>
                        </v-btn>
                    </template>
                        <span>Volver</span>
                </v-tooltip>
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
                                <v-menu
                                    v-model="menu1"
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
                                        :value="computedFechaLiquidado"
                                        label="Fecha Liquidado"
                                        append-icon="event"
                                        data-vv-as="Fecha Liquidado"
                                        readonly
                                        ></v-text-field>
                                    <v-date-picker
                                        v-model="fecha_liquidado"
                                        :allowed-dates="allowedDates"
                                        no-title
                                        locale="es"
                                        first-day-of-week=1
                                        @input="menu1 = false"
                                    ></v-date-picker>
                                </v-menu>
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
                            <v-flex sm2>
                                 <v-btn
                                    :disabled="lin_selected.length == 0"
                                    small
                                    @click="submit"  flat round  :loading="loading" block  color="warning">
                                    Procesar Líneas
                                    <v-icon right dark>fireplace</v-icon>
                                </v-btn>
                            </v-flex>
                        </v-layout>
                        <v-layout row wrap>
                                <v-flex xs2>
                                    <v-text-field
                                        :value="getDecimalFormat(peso_compra)"
                                        class="inputPrice"
                                        label="Peso Bruto"
                                        readonly
                                    ></v-text-field>
                                </v-flex>
                                <v-flex xs2>
                                    <v-text-field
                                        :value="getDecimalFormat(peso_inventario)"
                                        class="inputPrice"
                                        label="Peso Neto"
                                        readonly
                                    ></v-text-field>
                                </v-flex>
                                <v-flex xs2>
                                    <v-text-field
                                        :value="getDecimalFormat(computedValorRestoCompra)"
                                        class="inputPrice"
                                        label="Valor Resto Compra"
                                        readonly
                                    ></v-text-field>
                                </v-flex>
                            </v-layout>
                        <div v-if="compra.id>0">
                            <lineas-liquidar
                                :lin_selected.sync="lin_selected"
                                :lineas="lineas"
                                :compra="compra"
                                :grabaciones="grabaciones"
                                :auth_liquidar="auth_liquidar"
                                :productoCreado.sync="productoCreado"
                            >
                            </lineas-liquidar>
                            <productos-liquidados
                                :productos="productos">
                            </productos-liquidados>
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
import LiquidarLineas from './LiquidarLineas'
import ProductosLiquidados from './ProductosLiquidados'
import {mapGetters} from 'vuex';
	export default {
		$_veeValidate: {
      		validator: 'new'
        },
        components: {
            'menu-ope': MenuOpe,
            'lineas-liquidar': LiquidarLineas,
            'productos-liquidados': ProductosLiquidados,
            'loading': Loading
		},
    	data () {
      		return {
                titulo:"Liquidar Lote",
                compra: {},
                compra_id: 0,
                url: "/compras/liquidar",
                ruta: "compra",
                grupos: [],
                conceptos: [],
                lin_selected:[],
                lineas:[],
                productos:[],
                grabaciones: false,
                peso_compra:"",
                peso_inventario:"",
                valor_resto_compra:0,

        		status: false,
                loading: false,

                show: false,
                show_loading: true,

                menu1: false,
                fecha_liquidado: new Date().toISOString().substr(0, 10),

                menu1: false,
                fase: {
                    color:"",
                    nombre:""
                },
                docu_ok: false,
                auth_liquidar: true,
                grabaciones: false,
                productoCreado:{}
      		}
        },
        mounted(){
            var compra_id = this.$route.params.compra_id;

            if (compra_id > 0)
                axios.get(this.url+'/'+compra_id+'/edit')
                    .then(res => {

                        this.peso_compra = res.data.peso_compra;
                        this.peso_inventario = res.data.peso_inventario;
                        this.compra = res.data.compra;
                        this.grabaciones = res.data.grabaciones;
                        this.lineas = res.data.lineas;
                        this.productos = res.data.productos;
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
            ]),
            computedValorRestoCompra(){
                var total_inventariado = parseFloat(this.compra.importe);

                this.productos.forEach(function(item){total_inventariado = total_inventariado - item.precio_coste;});

                return total_inventariado;
            },
            computedFechaLiquidado() {
                moment.locale('es');
                return this.fecha_liquidado ? moment(this.fecha_liquidado).format('L') : '';
            },
            computedFModFormat() {
                moment.locale('es');
                return this.compra.updated_at ? moment(this.compra.updated_at).format('DD/MM/YYYY H:mm:ss') : '';
            }
        },
        watch:{
            productoCreado: function () {
                //this.productos.push(this.productoCreado);
                axios.get(this.url+'/'+this.compra.id+'/edit')
                    .then(res => {
                        this.productos = res.data.productos;
                        this.peso_compra = res.data.peso_compra;
                        this.peso_inventario = res.data.peso_inventario;

                    })
                    .catch(err => {
                        this.$toast.error(err.response.data.message);
                        this.$router.push({ name: this.ruta+'.index'})
                    })
            }
        },
    	methods:{
            goBack(){
                this.$router.go(-1);
            },
            goBackCompra(){
                if (this.compra.tipo_id == 1)
                    this.$router.push({ name: 'recompra.close', params: { id: this.compra.id } })
                else
                    this.$router.push({ name: 'compra.close', params: { id: this.compra.id } })
            },
            goCliente(){
                    this.$router.push({ name: 'cliente.edit', params: { id: this.compra.cliente_id } })
            },
            getDecimalFormat(value){
                return new Intl.NumberFormat("de-DE",{style: "decimal",minimumFractionDigits:2}).format(parseFloat(value))
            },
            getMoneyFormat(value){
                return new Intl.NumberFormat("de-DE",{style: "currency", currency: "EUR"}).format(parseFloat(value))
            },
            allowedDates(val){
                return val >= this.compra.fecha_bloqueo;
            },

            submit() {
                if (this.loading === false){
                    this.loading = true;
                    var lineas = this.lin_selected.map((item) =>{
                        return item.id;
                    });

                    axios.put(this.url+"/lote",{
                            compra_id: this.compra.id,
                            lineas: lineas,
                            fecha: this.fecha_liquidado
                            })
                        .then(res => {
                            this.lineas = res.data.lineas;
                            this.lin_selected=[];
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

            },
    }
  }
</script>
<style scoped>

.inputPrice >>> input {
  text-align: center;
  -moz-appearance:textfield;
}
</style>
