<template>
	<div v-show="!show_loading">
        <loading :show_loading="show_loading"></loading>
        <v-card>
            <v-card-title color="indigo">
                <h2 color="indigo">{{titulo}}</h2>
                <v-spacer></v-spacer>
                <v-tooltip bottom v-if="this.producto.compra_id">
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
                <menu-ope :id="producto.id" :cliente_id="producto.cliente_id"></menu-ope>

            </v-card-title>
        </v-card>
        <v-card>
            <v-tabs fixed-tabs>
                <v-tab>
                        Datos generales
                </v-tab>
                <v-tab>
                        Albaranes
                </v-tab>
                <v-tab-item>
                    <v-form>
                        <v-container>
                            <v-layout row wrap>
                                <v-flex sm2>
                                    <v-text-field
                                        v-model="producto.referencia"
                                        v-validate="'required|alpha_num'"
                                        :error-messages="errors.collect('referencia')"
                                        label="Referencia"
                                        data-vv-name="referencia"
                                        data-vv-as="referencia"
                                        :disabled="!computedEditPro"
                                        v-on:keyup.enter="submit"
                                    >
                                    </v-text-field>
                                </v-flex>
                                <v-flex sm2>
                                    <v-text-field
                                        v-model="producto.ref_pol"
                                        v-validate="'required_if:iva_id,2'"
                                        :error-messages="errors.collect('ref_pol')"
                                        label="Ref. Pol."
                                        data-vv-name="ref_pol"
                                        data-vv-as="Ref. Pol"
                                        required
                                        :disabled="!computedEditPro"
                                        v-on:keyup.enter="submit"
                                    >
                                    </v-text-field>
                                </v-flex>
                                <v-flex sm2 d-flex>
                                    <v-select
                                    v-model="producto.clase_id"
                                    v-validate="'required'"
                                    data-vv-name="clase_id"
                                    data-vv-as="clase"
                                    :error-messages="errors.collect('clase_id')"
                                    :items="clases"
                                    :disabled="!computedEditPro"
                                    @change="clase"
                                    label="Clase"
                                    ></v-select>
                                </v-flex>
                                <v-flex sm1>
                                    <v-select
                                        v-if="show_quilates"
                                        v-model="producto.quilates"
                                        :items="quilates"
                                        label="Quilates"
                                        v-validate="'numeric'"
                                        :error-messages="errors.collect('quilates')"
                                        data-vv-name="quilates"
                                        data-vv-as="quilates"
                                        :disabled="!computedEditPro"
                                    ></v-select>
                                </v-flex>
                                <v-flex sm2 d-flex>
                                    <v-select
                                        v-model="producto.univen"
                                        v-validate="'required'"
                                        data-vv-name="univen"
                                        data-vv-as="iva"
                                        :error-messages="errors.collect('univen')"
                                        :items="unidades"
                                        :disabled="!computedEditPro"
                                        label="Ud. Venta"
                                    ></v-select>
                                </v-flex>
                                <v-flex sm2 d-flex>
                                    <v-select
                                        v-model="producto.estado_id"
                                        v-validate="'required'"
                                        data-vv-name="estado_id"
                                        data-vv-as="estado"
                                        :error-messages="errors.collect('estado_id')"
                                        :items="estados"
                                        :disabled="!computedEditPro"
                                        label="Estado"
                                    ></v-select>
                                </v-flex>
                                <v-flex sm1>
                                    <v-switch
                                        label="Online"
                                        v-model="producto.online"
                                        :disabled="!computedEditPro"
                                        color="primary">
                                    ></v-switch>
                                </v-flex>
                            </v-layout>
                            <v-layout row wrap>
                                <v-flex sm6>
                                    <v-textarea
                                        v-model="producto.nombre"
                                        v-validate="'required'"
                                        :error-messages="errors.collect('nombre')"
                                        label="Nombre"
                                        data-vv-name="nombre"
                                        data-vv-as="nombre"
                                        required
                                        :disabled="!computedEditPro"
                                    >
                                    </v-textarea>
                                </v-flex>
                                <v-flex sm6>
                                    <v-text-field
                                        v-model="producto.nombre_interno"
                                        v-validate="'max:190'"
                                        :error-messages="errors.collect('nombre_interno')"
                                        label="Obs. Interna"
                                        data-vv-name="nombre_interno"
                                        data-vv-as="nombre interno"
                                        required
                                        v-on:keyup.enter="submit"
                                    >
                                    </v-text-field>
                                    <v-text-field
                                        v-model="producto.caracteristicas"
                                        v-validate="'max:190'"
                                        :error-messages="errors.collect('caracteristicas')"
                                        label="Características"
                                        data-vv-name="caracteristicas"
                                        data-vv-as="características"
                                        v-on:keyup.enter="submit"
                                    >
                                    </v-text-field>
                                </v-flex>
                            </v-layout>
                            <v-layout row wrap>
                                <v-flex sm2 d-flex>
                                    <v-select
                                        :disabled="!computedEditPro"
                                        v-model="producto.almacen_id"
                                        v-validate="'required'"
                                        data-vv-name="almacen_id"
                                        data-vv-as="ubicación"
                                        :error-messages="errors.collect('almacen_id')"
                                        :items="almacenes"
                                        label="Ubicación"
                                        ></v-select>
                                </v-flex>
                                <v-flex sm4 d-flex>
                                    <v-select
                                        :disabled="!computedEditPro"
                                        v-model="producto.cliente_id"
                                        v-validate="'numeric'"
                                        data-vv-name="cliente_id"
                                        data-vv-as="proveedor"
                                        :error-messages="errors.collect('cliente_id')"
                                        :items="asociados"
                                        label="Proveedor"
                                        ></v-select>
                                </v-flex>
                                <v-flex sm3 d-flex>
                                    <v-select
                                        :disabled="!computedEditPro"
                                        v-model="producto.destino_empresa_id"
                                        v-validate="'required'"
                                        data-vv-name="destino_empresa_id"
                                        data-vv-as="empresa"
                                        :error-messages="errors.collect('destino_empresa_id')"
                                        :items="empresas"
                                        label="Destino Venta"
                                        ></v-select>
                                </v-flex>
                                <v-flex sm2 d-flex>
                                    <v-select
                                        v-model="producto.etiqueta_id"
                                        v-validate="'required'"
                                        data-vv-name="etiqueta_id"
                                        data-vv-as="estado"
                                        :error-messages="errors.collect('etiqueta_id')"
                                        :items="etiquetas"
                                        :disabled="!computedEditPro"
                                        label="Etiqueta"
                                    ></v-select>
                                </v-flex>

                            </v-layout>
                            <v-layout row wrap>
                                <v-flex sm2 d-flex>
                                    <v-select
                                    v-model="producto.iva_id"
                                    v-validate="'required'"
                                    data-vv-name="iva_id"
                                    data-vv-as="iva"
                                    :error-messages="errors.collect('iva_id')"
                                    :items="ivas"
                                    :disabled="!computedEditPro"
                                    label="IVA"
                                    ></v-select>
                                </v-flex>
                                <v-flex sm2>
                                    <v-text-field
                                        v-model="producto.peso_gr"
                                        v-validate="'required|decimal:2'"
                                        :error-messages="errors.collect('peso_gr')"
                                        label="Ud./Peso Gr."
                                        data-vv-name="peso_gr"
                                        data-vv-as="peso/unidades"
                                        class="inputPrice"
                                        type="number"
                                        :disabled="!computedEditPro"
                                        v-on:keyup.enter="submit"
                                    >
                                    </v-text-field>
                                </v-flex>
                                <v-flex sm2>
                                    <v-text-field
                                        v-model="producto.precio_coste"
                                        v-validate="'required|decimal:2'"
                                        :error-messages="errors.collect('precio_coste')"
                                        label="Precio Coste"
                                        data-vv-name="precio_coste"
                                        data-vv-as="coste"
                                        class="inputPrice"
                                        type="number"
                                        :disabled="!computedEditPro"
                                        v-on:keyup.enter="submit"
                                    >
                                    </v-text-field>
                                </v-flex>
                                <v-flex sm2>
                                    <v-text-field
                                        v-model="producto.precio_venta"
                                        v-validate="'required|decimal:2|min:1'"
                                        :error-messages="errors.collect('precio_venta')"
                                        label="PVP"
                                        data-vv-name="precio_venta"
                                        data-vv-as="PVP"
                                        class="inputPrice"
                                        type="number"
                                        :disabled="!computedEditPro"
                                        v-on:keyup.enter="submit"
                                    >
                                    </v-text-field>
                                </v-flex>
                                <v-flex sm2>
                                    <v-text-field
                                        :value="computedMargen"
                                        label="Márgen"
                                        class="inputPrice"
                                        readonly
                                    >
                                    </v-text-field>
                                </v-flex>
                                <v-flex sm2 v-if="stockComple > 0">
                                     <v-text-field
                                        v-model="producto.stock"
                                        v-validate="'required|decimal:2|min:1'"
                                        :error-messages="errors.collect('stock')"
                                        label="Stock"
                                        data-vv-name="stock"
                                        data-vv-as="stock"
                                        class="inputPrice"
                                        type="number"
                                        :disabled="!computedEditPro"
                                        v-on:keyup.enter="submit"
                                    >
                                    </v-text-field>
                                </v-flex>
                            </v-layout>
                            <v-layout row wrap>
                                <v-flex sm2 d-flex>
                                    <v-select
                                        v-model="producto.garantia_id"
                                        v-validate="'numeric'"
                                        data-vv-name="garantia_id"
                                        data-vv-as="garantia"
                                        :error-messages="errors.collect('garantia_id')"
                                        :items="garantias"
                                        label="Garantía"
                                    ></v-select>
                                </v-flex>
                                <v-flex sm1 d-flex>
                                    <v-text-field
                                        v-model="producto.meses_garantia"
                                        v-validate="'numeric|max_value:24'"
                                        :error-messages="errors.collect('meses_garantia')"
                                        label="Meses"
                                        data-vv-name="meses_garantia"
                                        data-vv-as="meses"
                                        v-on:keyup.enter="submit"
                                    >
                                    </v-text-field>
                                </v-flex>
                                <v-flex sm2 d-flex>
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
                                            :value="computedFechaRevision"
                                            label="Fecha Revisión"
                                            append-icon="event"
                                            data-vv-as="Última Revisión"

                                            :error-messages="errors.collect('fecha_ultima_revision')"
                                            readonly
                                            ></v-text-field>
                                        <v-date-picker
                                            v-model="producto.fecha_ultima_revision"
                                            no-title
                                            locale="es"
                                            first-day-of-week=1
                                            @input="menu1 = false"
                                        ></v-date-picker>
                                    </v-menu>
                                </v-flex>
                                <v-flex sm2>
                                    <v-text-field
                                        v-model="producto.username"
                                        :error-messages="errors.collect('username')"
                                        label="Usuario"
                                        data-vv-name="username"
                                        readonly
                                        v-on:keyup.enter="submit"
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
                                <v-flex sm10>
                                    <v-textarea
                                        v-model="producto.notas"
                                        v-validate="'max:300'"
                                        :error-messages="errors.collect('notas')"
                                        label="Observaciones"
                                        data-vv-name="notas"
                                        data-vv-as="notas"
                                        v-on:keyup.enter="submit"
                                    >
                                    </v-textarea>
                                </v-flex>
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
                </v-tab-item>
                <v-tab-item>
                    <producto-alb v-if="producto.id>0" :producto_id="producto.id"></producto-alb>
                </v-tab-item>
            </v-tabs>
        </v-card>
	</div>
</template>
<script>
import moment from 'moment'
import Loading from '@/components/shared/Loading'
import MenuOpe from './MenuOpe'
import ProductoAlb from './ProductoAlb'
import {mapGetters} from 'vuex';
	export default {
		$_veeValidate: {
      		validator: 'new'
        },
        components: {
            'menu-ope': MenuOpe,
            'loading': Loading,
            'producto-alb': ProductoAlb,
		},
    	data () {
      		return {
                titulo:"Productos",
                producto: {},
                url: "/mto/productos",
                ruta: "producto",
                clases: [],
                estados: [],
                etiquetas: [],
                almacenes: [],
                asociados: [],
                garantias:[],
                quilates:[],
                ivas:[],
                empresas:[],

                unidades: [
                    {'value': 'U', 'text': 'Unidades'},
                    {'value': 'G', 'text': 'Gramos'}
                ],

                saldo: 0,
                loading: false,
                menu1: false,
                show_quilates: false,

                show: false,
                show_loading: true,
      		}
        },
        mounted(){

            var id = this.$route.params.id;
            if (id > 0)
                axios.get(this.url+'/'+id+'/edit')
                    .then(res => {

                        this.producto = res.data.producto;

                        // if (this.producto.estado_id == 3 || this.producto.estado_id == 4)
                        //     if (!this.isAdmin){
                        //         this.$router.push({ name: this.ruta+'.show',  params: { id: this.producto.id }})
                        //     }

                        this.empresas = res.data.empresas;
                    //    this.empresas.push({value:0,text:"Todas"});

                        this.clases = res.data.clases;
                        this.estados = res.data.estados;
                        this.ivas = res.data.ivas;
                        this.etiquetas = res.data.etiquetas;
                        this.almacenes = res.data.almacenes;
                        this.asociados = res.data.asociados;
                        this.garantias = res.data.garantias;
                        this.quilates  = res.data.quilates;

                        this.asociados.push({value: null, text: '-'});

                        this.show_quilates = this.producto.clase.quilates;

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
                    'hasEditPro',
                    'stockComple'
                ]),
            computedEditPro(){
                if (this.producto.estado_id == 3 || this.producto.estado_id == 4)
                    return this.hasEditPro;

                return true;
            },
            computedMargen(){
                return this.getMoneyFormat(this.producto.margen);
            },
            computedFecha() {
                moment.locale('es');
                return this.producto.fecha ? moment(this.producto.fecha).format('L') : '';
            },
            computedFechaRevision() {
                    moment.locale('es');
                    return this.producto.fecha_ultima_revision ? moment(this.producto.fecha_ultima_revision).format('L') : '';
            },
            computedFModFormat() {
                moment.locale('es');
                return this.producto.updated_at ? moment(this.producto.updated_at).format('DD/MM/YYYY H:mm:ss') : '';
            },
            computedFCreFormat() {
                moment.locale('es');
                return this.producto.created_at ? moment(this.producto.created_at).format('DD/MM/YYYY H:mm') : '';
            }

        },
    	methods:{
            clase(){
                var idx = this.clases.map(x => x.value).indexOf(this.producto.clase_id);
                this.show_quilates = this.clases[idx].quilates;
            },
            goCompra() {
                this.$router.push({ name: 'compra.close', params: { id: this.producto.compra_id } })
            },
            getMoneyFormat(value){
                return new Intl.NumberFormat("de-DE",{style: "currency", currency: "EUR"}).format(parseFloat(value))
            },
            submit() {

                if (this.loading === false){
                    this.loading = true;

                    if (!this.show_quilates)
                        this.producto.quilates = 0;

                    this.$validator.validateAll().then((result) => {
                        if (result){
                                this.$validator.reset();
                             axios.put(this.url+"/"+this.producto.id, this.producto)
                                .then(res => {

                                    this.$toast.success(res.data.message);
                                    this.producto = res.data.producto;

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
