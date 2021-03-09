<template>
	<div>
        <loading :show_loading="show_loading"></loading>
        <v-card>
            <v-card-title color="indigo">
                <h2 color="indigo">{{titulo}}</h2>
                <v-spacer></v-spacer>
                <v-tooltip bottom>
                    <template v-slot:activator="{ on }">
                        <v-btn
                            v-on="on"
                            color="white"
                            icon
                            @click="goCompra"
                        >
                            <v-icon color="primary">shopping_cart</v-icon>
                        </v-btn>
                    </template>
                        <span>Ir a compra</span>
                </v-tooltip>
                <menu-ope :id="producto.id"></menu-ope>

            </v-card-title>
        </v-card>
        <v-card>
            <v-form>
                 <v-container  v-if="!show_loading">
                     <v-layout row wrap  v-if="producto.deleted_at != null">
                       <v-flex sm2>
                            <h3 class="red--text darken-4">PRODUCTO BORRADO!</h3>
                        </v-flex>
                     </v-layout>
                     <v-layout row wrap>
                         <v-flex sm2>
                            <v-text-field
                                v-model="producto.referencia"
                                label="Referencia"
                                disabled
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm8>
                            <v-text-field
                                v-model="producto.nombre"
                                :error-messages="errors.collect('nombre')"
                                label="Nombre"
                                disabled
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm2>
                            <v-text-field
                                v-model="producto. ref_pol"
                                label="Ref. Pol."
                                disabled
                            >
                            </v-text-field>
                        </v-flex>
                     </v-layout>
                     <v-layout row wrap>
                        <v-flex sm6>
                            <v-text-field
                                v-model="producto.nombre_interno"
                                label="Nombre Interno"
                                disabled
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm3 d-flex>
                            <v-text-field
                                :value="producto.clase.nombre"
                                label="Clase"
                                disabled
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm1>
                            <v-text-field
                                v-model="producto.quilates"
                                label="Kt"
                                disabled
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm2 d-flex>
                             <v-text-field
                                :value="producto.estado.nombre"
                                label="Estado"
                                disabled
                            >
                            </v-text-field>
                        </v-flex>
                     </v-layout>
                    <v-layout row wrap>
                        <v-flex sm2 d-flex>
                            <v-text-field
                                :value="producto.iva.nombre"
                                label="IVA"
                                disabled
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm2>
                            <v-text-field
                                :value="getDecimalFormat(producto.peso_gr)"
                                label="Peso gr."
                                disabled
                            >
                            </v-text-field>
                        </v-flex>
                         <v-flex sm2>
                            <v-text-field
                                :value="getMoneyFormat(producto.precio_coste)"
                                label="Precio Coste"
                                class="inputPrice"
                                disabled
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm2>
                            <v-text-field
                                :value="getMoneyFormat(producto.precio_venta)"
                                label="PVP"
                                disabled
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm2>
                            <v-text-field
                                :value="computedMargen"
                                label="Márgen"
                                class="inputPrice"
                                disabled
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm2>
                            <v-switch
                                label="Online"
                                v-model="producto.online"
                                color="primary"
                                disabled
                            >
                            ></v-switch>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex sm12>
                            <v-text-field
                                v-model="producto.notas"
                                label="Observaciones"
                                disabled
                            >
                            </v-text-field>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex sm2 d-flex>
                            <v-text-field
                                :value="producto.destino.nombre"
                                label="Destino Venta"
                                disabled
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm2>
                            <v-text-field
                                v-model="producto.username"
                                :error-messages="errors.collect('username')"
                                label="Usuario"
                                data-vv-name="username"
                                disabled
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm2>
                            <v-text-field
                                v-model="computedFModFormat"
                                label="Modificado"
                                disabled
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm2>
                            <v-text-field
                                v-model="computedFCreFormat"
                                label="Creado"
                                disabled
                            >
                            </v-text-field>
                        </v-flex>
                    </v-layout>
                    <!-- <v-layout row wrap v-if="producto.id>0">
                        <v-flex sm3 d-flex>
                            <v-text-field
                                :value="empresas[producto.destino_empresa_id].text"
                                label="Empresa Origen"
                                disabled
                            >
                            </v-text-field>
                        </v-flex>
                    </v-layout> -->
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
                titulo:"Productos",
                producto: {},
                url: "/mto/productos",
                ruta: "producto",
                clases: [],
                estados: [],
                ivas:[],
                empresas:[],

                saldo: 0,
                enviando: false,
                menu1: false,

                show: false,
                show_loading: true,
      		}
        },
        mounted(){

            var id = this.$route.params.id;
            if (id > 0)
                axios.get(this.url+'/'+id)
                    .then(res => {

                        this.empresas = res.data.empresas;

                        this.producto = res.data.producto;
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
        computedMargen(){
            return this.getMoneyFormat(this.producto.margen);
        },
        computedFecha() {
            moment.locale('es');
            return this.producto.fecha ? moment(this.producto.fecha).format('L') : '';
        },
        computedFModFormat() {
            moment.locale('es');
            return this.producto.updated_at ? moment(this.producto.updated_at).format('DD/MM/YYYY H:mm') : '';
        },
        computedFCreFormat() {
            moment.locale('es');
            return this.producto.created_at ? moment(this.producto.created_at).format('DD/MM/YYYY H:mm') : '';
        }

        },
    	methods:{
            goCompra() {
                this.$router.push({ name: 'compra.close', params: { id: this.producto.compra_id } })
            },
            getMoneyFormat(value){
                return new Intl.NumberFormat("de-DE",{style: "currency", currency: "EUR"}).format(parseFloat(value))
            },
            getDecimalFormat(value){
                return new Intl.NumberFormat("de-DE",{style: "decimal",minimumFractionDigits:2}).format(parseFloat(value))
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


.theme--light.v-input--is-disabled,  .theme--light.v-input--is-disabled input, .theme--light.v-input--is-disabled textarea {
    color: #263238;
}

.v-form>.container>.layout>.flex{
    padding: 0px 8px;
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
</style>
