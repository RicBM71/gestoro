<template>
	<div>
        <loading :show_loading="show_loading"></loading>
        <v-card v-if="show">
            <v-card-title color="indigo">
                <h2 color="indigo">{{titulo}}: <span v-if="show" :class="compra.fase.color">{{compra.fase.nombre}}</span></h2>
                <v-spacer></v-spacer>
                <v-tooltip bottom>
                    <template v-slot:activator="{ on }">
                        <v-btn
                            v-show="computedAuthComprar"
                            v-on="on"
                            color="white"
                            icon
                            @click="cambiarTipo"
                        >
                            <v-icon color="teal darken-1">schedule</v-icon>
                        </v-btn>
                    </template>
                    <span>Cambiar a Recompra</span>
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
                <menu-ope :compra.sync="compra" :refresh.sync="refresh" :docu_ok="docu_ok"></menu-ope>
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
                                    v-model="computedFModFormat"
                                    :label="computedTxtMod"
                                    readonly                            >
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
                            <v-flex sm2>
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
                            </v-flex>
                            <v-flex sm2>
                                <v-text-field
                                    class="inputPrice"

                                    v-model="computedImporte"
                                    label="Importe Compra"
                                    readonly
                                >
                                </v-text-field>
                            </v-flex>
                        </v-layout>
                        <v-layout row wrap>
                            <v-flex sm8>
                                <v-text-field
                                    v-model="compra.notas"
                                    append-icon="save"
                                    label="Observaciones Compra"
                                    @click:append="updateNota"
                                >
                                </v-text-field>
                            </v-flex>
                            <v-flex sm2>
                            </v-flex>
                            <v-flex sm2 v-show="compra.fase_id <=4">
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
                                >
                            </com-lines>
                            <depo-lines v-show="show_lindepo"
                                :compra.sync="compra"
                                :conceptos="conceptos"
                                :lineas.sync="lineas_deposito">
                            </depo-lines>
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
import {mapGetters} from 'vuex';
	export default {
		$_veeValidate: {
      		validator: 'new'
        },
        components: {
            'menu-ope': MenuOpe,
            'com-lines': Comlines,
            'depo-lines': DepoLines,
            'loading': Loading
		},
    	data () {
      		return {
                titulo:"Operación: ",
                compra: {},
                url: "/compras/compras",
                ruta: "compra",
                grupos: [],
                conceptos: [],

                valor_compras: 0,

        		status: false,
                loading: false,

                refresh: 0,
                show: false,
                show_loading: true,
                show_lindepo: true,

                menu1: false,
                fase: {
                    color:"",
                    nombre:""
                },
                docu_ok: false,
                grabaciones: false,
      		}
        },
        mounted(){

            var id = this.$route.params.id;

            if (id > 0)
                //axios.get(this.url+'/'+id+'/edit')
                axios.get(this.url+'/'+id)
                    .then(res => {

                        this.valor_compras = res.data.valor_compras;
                        this.grabaciones = res.data.grabaciones;

                        this.docu_ok = res.data.documentos.status > 0 ? true : false;

                        this.compra = res.data.compra;
                        this.lineas_deposito = res.data.lineas_deposito;

                        if (this.compra.tipo_id == 1){
                            this.$router.push({ name: 'recompra.close', params: { id: this.compra.id } })
                        }

                        if (this.compra.fase_id <= 2){
                            this.$router.push({ name: 'compra.edit', params: { id: this.compra.id } })
                        }
                        this.titulo = this.compra.tipo.nombre;

                        // this.fase.nombre = this.compra.fase.nombre;
                        // this.fase.color = this.compra.fase.color;

                        this.fpago = this.compra.cliente.fpago_id;

                        //this.show_lindepo = (this.compra.fase_id == 6) ? false : true;

                        this.show_lindepo = false;

                        this.show = true;
                        this.show_loading = false;
                    })
                    .catch(err => {
                        this.$toast.error(err.response.data.message);
                        this.$router.push({ name: this.ruta+'.index'})
                    })
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
            }

        },
        computed: {
            ...mapGetters([
                'hasLiquidar',
                'isAdmin',
                'isSupervisor',
                'hasReaCompras',
                'userName'
            ]),
           computedAuthLiquidar(){

                if (this.compra.fase_id !=5){ // no está recuperado
                    const hoy = new Date().toISOString().substr(0, 10);
                    return (hoy > this.compra.fecha_bloqueo);
                }else{
                    return false;
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
            computedAuthComprar(){

                var comprado = false;

                this.lineas_deposito.forEach(function(x) {
                    if (x.concepto_id == 11)
                        comprado = true;
                });



                if (this.compra.fase_id <= 4 && !comprado)
                    return true;
                else
                    return false;


            },
            computedImporte(){
                return this.getMoneyFormat(this.compra.importe);
            },
            computedValorCompras(){
                return this.getMoneyFormat(parseFloat(this.compra.importe)+parseFloat(this.valor_compras));
            },
            computedFechaCompra() {
                moment.locale('es');
                return this.compra.fecha_compra ? moment(this.compra.fecha_compra).format('L') : '';
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
    	methods:{
            getMoneyFormat(value){
                return new Intl.NumberFormat("de-DE",{style: "currency", currency: "EUR"}).format(parseFloat(value))
            },
            updateNota(){
                if (this.compra.fase_id >= 0){
                    this.loading = true;
                    axios.put(this.url+"/"+this.compra.id+"/obs", this.compra)
                        .then(res => {
                            this.$toast.success(res.data.message);
                            this.compra = res.data.compra;
                            this.loading = false;
                        })
                        .catch(err => {
                            this.$toast.error(err.response.data.message);
                            this.loading = false;
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
            cambiarTipo(){
                if (this.compra.fase_id <= 4){
                    this.loading = true;

                    axios.put(this.url+"/"+this.compra.id+"/tipo",{tipo_id: 1})
                        .then(res => {
                            this.$router.push({ name: 'recompra.close', params: { id: this.compra.id } })
                            this.loading = false;
                        })
                        .catch(err => {
                            this.$toast.error(err.response.data.message);
                            this.loading = false;
                        });
                }
            },
            goFase() {

                if (this.loading === false){
                    this.loading = true;
                    axios.put(this.url+"/"+this.compra.id+"/fase",{fase_id: 2} )
                        .then(res => {
                            this.$router.push({ name: this.ruta+'.edit', params: { id: this.compra.id } })
                        })
                        .catch(err => {
                            if (err.request.status == 422){ // fallo de validated.
                                const msg_valid = err.response.data.errors;
                                for (const prop in msg_valid) {
                                        this.$toast.error(`${msg_valid[prop]}`);
                                }
                            }
                            else
                                this.$toast.error(err.response.data.message);
                        })
                        .finally(()=> {
                            this.loading = false;
                        });
                }

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
