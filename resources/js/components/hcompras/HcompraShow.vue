<template>
	<div>
        <loading :show_loading="show_loading"></loading>
        <v-card v-if="show">
            <v-card-title color="indigo">
                <h2 color="indigo">{{titulo}}: <span v-if="show" :class="compra.fase.color">{{compra.fase.nombre}}</span></h2>
                <v-spacer></v-spacer>
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
                                    label="Observaciones Compra"
                                >
                                </v-text-field>
                            </v-flex>
                             <v-flex sm2>
                                <v-text-field
                                    class="inputPrice"
                                    :value="totalOpDia()"
                                    readoly
                                    label="Pagos/Cobros hoy"
                                >
                                </v-text-field>
                            </v-flex>
                            <v-flex sm2>
                                   <b> {{ compra.operacion + " " + compra.username_his + " " + compra.created_his }}</b>
                            </v-flex>
                        </v-layout>
                        <v-layout row wrap v-if="compra.cliente.notas!=null">
                            <v-flex sm10>
                                <v-text-field
                                    v-model="compra.cliente.notas"
                                    label="Observaciones Cliente"
                                >
                                </v-text-field>
                            </v-flex>
                            <v-flex sm2>
                                <div class="text-xs-center">
                                <v-btn
                                    small
                                    @click="restore"  round  :loading="show_loading" block  color="warning">
                                    Restaurar
                                </v-btn>
                            </div>
                            </v-flex>
                        </v-layout>
                        <div v-if="compra.id>0">
                            <com-lines
                                :hcomlines="hcomlines"
                                >
                            </com-lines>
                            <depo-lines
                                :hdepositos="hdepositos">
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
import Comlines from './Hcomlines'
import DepoLines from './HdepoLines'
import {mapGetters} from 'vuex';
import {mapState} from 'vuex'
	export default {
		$_veeValidate: {
      		validator: 'new'
        },
        components: {
            'com-lines': Comlines,
            'depo-lines': DepoLines,
            'loading': Loading
		},
    	data () {
      		return {
                titulo:"Histórico Compra",
                compra: {},
                url: "/compras/hcompras",
                ruta: "compra",
                grupos: [],
                conceptos: [],

                valor_compras: 0,
                valor_pagado : 0,
                valor_cobrado: 0,

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
                hcomlines:[],
                hdepositos:[],
      		}
        },
        mounted(){

            var id = this.$route.params.id;

            if (id > 0)
                //axios.get(this.url+'/'+id+'/edit')
                axios.get(this.url+'/'+id)
                    .then(res => {


                        this.compra = res.data.hcompra;

                        this.hdepositos = res.data.hdepositos;
                        this.hcomlines = res.data.hcomlines;

                        // this.titulo = this.compra.tipo.nombre;

                       // this.fpago = this.compra.cliente.fpago_id;

                        this.show_lindepo = false;

                        this.show = true;
                        this.show_loading = false;
                    })
                    .catch(err => {
                        this.$toast.error(err.response.data.message);
                        this.$router.push({ name: 'hcompras.index'})
                    })
        },
        computed: {
            ...mapGetters([
                'userName',
            ]),

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
            totalOpDia(){
                return this.getMoneyFormat(this.valor_pagado)+" / "+this.getMoneyFormat(this.valor_cobrado);
            },
            restore(){
                this.show_loading = true;
                axios.get(this.url+'/'+this.compra.id+'/restore')
                    .then(res => {

                        this.show_loading = false;
                        var ruta = res.data.compra.tipo_id == 1 ? 'recompra' : 'compra';

                        this.$router.push({ name: ruta+'.close', params: { id: res.data.compra.id } })
                    })
                    .catch(err => {
                        this.$toast.error(err.response.data.message);
                        this.$router.push({ name: 'hcompras.index'})
                    })
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
