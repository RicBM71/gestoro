<template>
	<div>
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
                            @click="goeCommerce"
                            disabled
                        >
                            <v-icon color="primary">add_link</v-icon>
                        </v-btn>
                    </template>
                        <span>Subir producto a eCommerce</span>
                </v-tooltip>
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
                <menu-ope :id="producto.id" :cliente_id="producto.cliente_id" :estado_id="producto.estado_id"></menu-ope>

            </v-card-title>
        </v-card>
        <v-card v-if="!show_loading">
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
                                        :label="computedLabelRef"
                                        data-vv-name="referencia"
                                        data-vv-as="referencia"
                                        :disabled="!computedEditPro"
                                        v-on:keyup.enter="submit"
                                    >
                                    </v-text-field>
                                </v-flex>
                                <v-flex sm1>
                                    <v-text-field
                                        v-show="producto.estado_id <= 4"
                                        v-model="producto.ref_pol"
                                        v-validate="'required_if:iva_id,2'"
                                        :error-messages="errors.collect('ref_pol')"
                                        label="Ref. Pol."
                                        data-vv-name="ref_pol"
                                        data-vv-as="Ref. Pol"
                                        required
                                        :disabled="!computedEditPro || producto.compra_id > 0"
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
                                        :disabled="computedEditEstado"
                                        label="Estado"
                                    ></v-select>
                                </v-flex>
                                <v-flex sm1 v-if="producto.online">
                                    <v-text-field
                                        v-model="producto.ecommerce_id"
                                        v-validate="'numeric'"
                                        :error-messages="errors.collect('ecommerce_id')"
                                        label="eCommerce ID"
                                        data-vv-name="ecommerce_id"
                                        data-vv-as="eCommerce"
                                        :disabled="!computedEditPro"
                                        v-on:keyup.enter="submit"
                                    >
                                    </v-text-field>
                                </v-flex>
                            </v-layout>
                            <v-layout row wrap>
                                <v-flex sm6>
                                    <v-text-field
                                        v-model="producto.nombre"
                                        v-validate="'required'"
                                        :error-messages="errors.collect('nombre')"
                                        label="Nombre"
                                        data-vv-name="nombre"
                                        data-vv-as="nombre"
                                        required
                                        :disabled="!computedEditPro"
                                    >
                                    </v-text-field>
                                    <v-text-field
                                        v-show="producto.estado_id != 5"
                                        v-model="producto.nombre_interno"
                                        v-validate="'max:190'"
                                        :error-messages="errors.collect('nombre_interno')"
                                        label="Nombre Interno"
                                        data-vv-name="nombre_interno"
                                        data-vv-as="nombre interno"
                                        required
                                        v-on:keyup.enter="submit"
                                    >
                                    </v-text-field>
                                </v-flex>
                                <v-flex sm6>
                                    <v-textarea
                                        v-show="producto.estado_id != 5"
                                        v-model="producto.caracteristicas"
                                        v-validate="'max:190'"
                                        :error-messages="errors.collect('caracteristicas')"
                                        label="Descripción: Brillantes: Talla, color, pureza, quilates - Relojes: S/N"
                                        data-vv-name="caracteristicas"
                                        data-vv-as="características"
                                        v-on:keyup.enter="submit"
                                    >
                                    </v-textarea>
                                </v-flex>
                            </v-layout>
                            <v-layout row wrap v-show="producto.estado_id != 5">
                                <v-flex sm2 d-flex>
                                    <v-select
                                        :disabled="!computedEditPro"
                                        v-model="producto.almacen_id"
                                        v-validate="'numeric'"
                                        data-vv-name="almacen_id"
                                        data-vv-as="ubicación"
                                        :error-messages="errors.collect('almacen_id')"
                                        :items="almacenes"
                                        label="Ubicación"
                                        ></v-select>
                                </v-flex>
                                <v-flex sm4 d-flex>
                                    <v-select
                                        :disabled="computedEditEstado || producto.compra_id > 0"
                                        v-model="producto.cliente_id"
                                        v-validate="'numeric'"
                                        data-vv-name="cliente_id"
                                        data-vv-as="proveedor"
                                        :error-messages="errors.collect('cliente_id')"
                                        :items="asociados"
                                        label="Proveedor"
                                        ></v-select>
                                </v-flex>

                                <v-flex sm4 d-flex>
                                    <v-select
                                        :disabled="computedEditEstado"
                                        v-model="producto.destino_empresa_id"
                                        v-validate="'required'"
                                        data-vv-name="destino_empresa_id"
                                        data-vv-as="empresa"
                                        :error-messages="errors.collect('destino_empresa_id')"
                                        :items="empresas"
                                        :label="computedLabelDestino"
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
                                        :disabled="computedEditEstado || producto.compra_id > 0"
                                        label="IVA"
                                        ></v-select>
                                </v-flex>
                                <v-flex sm1></v-flex>
                                <v-flex sm1>
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
                                <v-flex sm1>
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
                                <v-flex sm1>
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
                                <v-flex sm1>
                                    <v-text-field
                                        :value="computedMargen"
                                        label="Márgen"
                                        class="inputPrice"
                                        disabled
                                    >
                                    </v-text-field>
                                </v-flex>
                                <v-flex sm1></v-flex>
                                <v-flex sm1 v-if="computedStock">
                                    <v-text-field
                                        v-model="producto.stock"
                                        v-validate="'required|min:1'"
                                        :error-messages="errors.collect('stock')"
                                        label="Stock Inicial"
                                        data-vv-name="stock"
                                        data-vv-as="Stock"
                                        type="number"
                                        :disabled="!computedEditStock"
                                        v-on:keyup.enter="submit"
                                    >
                                    </v-text-field>
                                </v-flex>
                                <v-flex sm1 v-if="computedStock">
                                    <v-text-field
                                        v-model="this.stock_real"
                                        label="Stock Real"
                                        readonly
                                    >
                                    </v-text-field>
                                </v-flex>
                                <v-flex sm1></v-flex>
                                <v-flex sm1>
                                    <v-switch
                                        v-show="producto.estado_id != 5"
                                        label="eCommerce"
                                        v-model="producto.online"
                                        :disabled="!computedEditPro"
                                        color="primary">
                                    ></v-switch>
                                </v-flex>
                            </v-layout>
                            <v-layout row wrap>
                                <v-flex sm2 d-flex>
                                    <v-select
                                        v-show="producto.estado_id != 5"
                                        v-model="producto.garantia_id"
                                        v-validate="'numeric'"
                                        data-vv-name="garantia_id"
                                        data-vv-as="garantia"
                                        :error-messages="errors.collect('garantia_id')"
                                        :items="garantias"
                                        :disabled="computedEditGarantia"
                                        label="Garantía"
                                    ></v-select>
                                </v-flex>
                                <v-flex sm1 d-flex>
                                    <v-text-field
                                        v-show="producto.estado_id != 5"
                                        v-model="producto.meses_garantia"
                                        v-validate="'numeric|max_value:24'"
                                        :error-messages="errors.collect('meses_garantia')"
                                        label="Meses"
                                        data-vv-name="meses_garantia"
                                        data-vv-as="meses"
                                        :disabled="computedEditGarantia"
                                        v-on:keyup.enter="submit"
                                    >
                                    </v-text-field>
                                </v-flex>
                                <v-flex sm2 d-flex>
                                    <v-menu
                                        v-show="producto.estado_id != 5"
                                        v-model="menu1"
                                        :close-on-content-click="false"
                                        :nudge-right="40"
                                        lazy
                                        transition="scale-transition"
                                        offset-y
                                        full-width
                                        min-width="290px"
                                        :disabled="computedEditGarantia"
                                    >
                                        <v-text-field
                                            slot="activator"
                                            :value="computedFechaRevision"
                                            label="Fecha Revisión"
                                            append-icon="event"
                                            data-vv-as="Última Revisión"
                                            :error-messages="errors.collect('fecha_ultima_revision')"
                                            readonly
                                            :disabled="computedEditGarantia"
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
                                <v-flex sm2 d-flex>
                                    <v-select
                                        v-model="producto.categoria_id"
                                        v-validate="'numeric'"
                                        data-vv-name="categoria_id"
                                        data-vv-as="categoria"
                                        :error-messages="errors.collect('categoria_id')"
                                        :items="categorias"
                                        label="Categoría"
                                    ></v-select>
                                </v-flex>
                                <v-flex sm2 d-flex>
                                    <v-select
                                        v-model="producto.marca_id"
                                        v-validate="'numeric'"
                                        data-vv-name="marca_id"
                                        data-vv-as="marca"
                                        :error-messages="errors.collect('marca_id')"
                                        :items="marcas"
                                        label="Marca"
                                    ></v-select>
                                </v-flex>
                                <v-flex sm1>
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
                                <v-flex sm1>
                                    <v-text-field
                                        v-model="computedFModFormat"
                                        label="Modificado"
                                        readonly
                                    >
                                    </v-text-field>
                                </v-flex>
                                <v-flex sm1>
                                    <v-text-field
                                        v-model="computedFCreFormat"
                                        label="Creado"
                                        readonly
                                    >
                                    </v-text-field>
                                </v-flex>
                            </v-layout>
                            <v-layout row wrap>
                                <v-flex sm6>
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
                                <v-flex sm5 d-flex>
                                    <v-combobox
                                        v-if="tags.length > 0"
                                        v-model="producto.tags"
                                        item-text='nombre'
                                        item-value='id'
                                        :items="tags"
                                        label="Distintivo"
                                        chips
                                        clearable
                                        multiple
                                        solo
                                        flat
                                    >
                                        <template v-slot:selection="data">
                                        <v-chip
                                            text-color="blue"
                                            outline
                                            :selected="data.selected"
                                            close
                                            @input="delTag(data.item)"
                                        >
                                            {{ data.item.nombre }}&nbsp;
                                        </v-chip>
                                        </template>
                                    </v-combobox>
                                </v-flex>
                                <v-flex sm1>
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
import {mapActions} from "vuex";
import {mapState} from 'vuex'
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
                producto: {
                    id:0,
                    estado_id: 0
                },
                url: "/mto/productos",
                ruta: "producto",
                clases: [],
                estados: [],
                etiquetas: [],
                almacenes: [],
                asociados: [],
                garantias:[],
                quilates:[],
                marcas:[],
                categorias:[],
                ivas:[],
                empresas:[],
                tags:[],
                chips:[ {value:1},{value:2}],

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
                stock_real: 0,
                show_stock: false,
      		}
        },
        mounted(){

            var id = this.$route.params.id;
            if (id > 0)
                axios.get(this.url+'/'+id+'/edit')
                    .then(res => {

                        this.tags = res.data.tags;

                        this.chips = res.data.producto.tags;

                        this.producto = res.data.producto;

                        if (res.data.parametros != false)
                            this.setAuthUser(res.data.parametros.user);

                        // if (this.producto.estado_id == 3 || this.producto.estado_id == 4)
                        //     if (!this.hasEdtPro){
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
                        this.marcas = res.data.marcas;
                        this.categorias = res.data.categorias;

                        this.asociados.push({value: null, text: '-'});
                        this.garantias.push({value: null, text: '-'});
                        this.almacenes.push({value: null, text: '-'});
                        this.marcas.push({value: null, text: '-'});
                        this.categorias.push({value: null, text: '-'});

                        this.show_quilates = this.producto.clase.quilates;

                        this.stock_real = res.data.stock_real;
                        this.clase();

                    })
                    .catch(err => {
                        this.$toast.error(err.response.data.message);
                        this.$router.push({ name: this.ruta+'.index'})
                    })
                    .finally(()=> {
                        this.show = true;
                        this.show_loading = false;
                    });
        },
        computed: {
            ...mapGetters([
                    'hasEdtPro',
                    'isRoot',
                    'hasEdtPro',
                    'userName'
                ]),
            computedEditEstado(){

               if (this.isRoot) return false;

                return (this.producto.estado_id == 3 || this.producto.estado_id == 4);
            },
            computedLabelRef(){
                return "Referencia/Id "+this.producto.id;
            },
            computedEditGarantia(){

                if (this.hasEdtPro == true) return false;

                return this.producto.estado_id > 3;

            },
            computedStock(){

                return this.show_stock;

                //return (this.producto.estado_id <= 3 && this.producto.stock > 1);

            },
            computedEditStock(){
                if (this.show_stock && this.hasEdtPro)
                    return true;
                return false;
            },
            computedEditPro(){

                if (this.producto.estado_id == 5)
                    return true;

                if (this.producto.estado_id == 3 || this.producto.estado_id == 4)
                    return false;

                //     return this.hasEdtPro;

                const hoy = new Date().toISOString().substr(0, 10);
                if (this.producto.username == this.userName && this.producto.created_at.substr(0, 10) == hoy)
                    return true;
                else
                    return this.hasEdtPro;
            },
            computedLabelDestino(){

                if (this.producto.id > 0)
                    return "Destino venta - Procede de "+ this.producto.empresa.nombre.toUpperCase();

                return "";

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
            ...mapActions([
                'setAuthUser',
                'unsetParametros'
			]),
            delTag(item) {
                this.producto.tags.splice(this.producto.tags.indexOf(item), 1)
                this.producto.tags = [...this.producto.tags]
            },
            clase(){
                var idx = this.clases.map(x => x.value).indexOf(this.producto.clase_id);

                this.show_quilates = this.clases[idx].quilates;
                this.show_stock = this.clases[idx].stockable;
            },
            goCompra() {
                this.$router.push({ name: 'compra.close', params: { id: this.producto.compra_id } })
            },
            getMoneyFormat(value){
                return new Intl.NumberFormat("de-DE",{style: "currency", currency: "EUR"}).format(parseFloat(value))
            },
            goeCommerce(){

                this.loading = true;
                axios.post("/woocommerce/store/"+this.producto.id)
                    .then(res => {

                        this.$toast.success('Ok');
                        this.producto = res.data.producto;
                        this.loading = false;

                    })
                    .catch(err => {
                        this.$toast.error(err.response.data.message);
                        this.loading = false;
                    });

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

                                    this.stock_real = res.data.stock_real;

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
