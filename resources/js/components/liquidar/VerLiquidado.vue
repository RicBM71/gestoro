<template>
    <v-container>
        <v-layout row wrap>
            <v-flex xs2>
                <v-text-field
                    :value="computedTotalGr"
                    class="inputPrice"
                    label="Total Gr."
                    readonly
                ></v-text-field>
            </v-flex>
            <v-flex xs4></v-flex>
            <v-flex xs6>
                <v-text-field
                    v-model="search"
                    append-icon="search"
                    label="Buscar"
                    single-line
                    hide-details
                ></v-text-field>
            </v-flex>
        </v-layout>
        <v-layout row wrap>
            <v-flex xs12>
                <v-data-table
                    :headers="headers"
                    :items="lineas"
                    :search="search"
                    :expand="expand"
                    :pagination.sync="pagination"
                    item-key="id"
                    rows-per-page-text="Registros por página"
                >
                    <template slot="items" slot-scope="props">
                        <tr>
                            <td class="indigo--text darken-4">{{getFecha(props.item.compra.fecha_compra)}}</td>
                            <td class="indigo--text darken-4">{{props.item.compra.alb_ser}}</td>
                            <td class="indigo--text darken-4">{{ props.item.concepto }}</td>
                            <td class="indigo--text darken-4">{{ props.item.clase.nombre}}<span v-show="props.item.quilates != null"> {{props.item.quilates}} K</span></td>
                            <td class="indigo--text darken-4 text-xs-right">{{ props.item.peso_gr | currency('', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false }) }}</td>
                            <td class="indigo--text darken-4 text-xs-right">{{ props.item.importe | currency('', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false }) }}</td>
                            <td class="indigo--text darken-4 text-xs-right">{{ precioGr(props.item) | currency('', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false }) }}</td>
                            <td class="justify-center layout px-0">
                                <v-icon
                                    small
                                    class="mr-2"
                                    @click="openDialog(props.item)"
                                >
                                    save
                                </v-icon>
                                <v-icon
                                    v-show="props.item.compra.productos.length > 0"
                                    small
                                    color="orange darken-4"
                                    class="mr-2"
                                    @click="props.expanded = !props.expanded"
                                >
                                    visibility
                                </v-icon>
                            </td>
                        </tr>
                    </template>
                    <template v-slot:expand="props">
                        <v-card flat>
                        <v-card-text>
                            <p :key="producto.id" v-for="producto in props.item.compra.productos">
                                <v-icon
                                    small
                                    class="mr-2"
                                    @click="editPro(producto.id)"
                                >
                                    style
                                </v-icon>
                                {{producto.referencia + " " + producto.nombre}}
                            </p>
                        </v-card-text>
                        </v-card>
                    </template>
                    <template slot="pageText" slot-scope="props">
                        Registros {{ props.pageStart }} - {{ props.pageStop }} de {{ props.itemsLength }}
                    </template>
                </v-data-table>
            </v-flex>
        </v-layout>
        <producto-create
            :compra="compra"
            :itemCreate="itemCreate"
            :dialog_pro.sync="dialog_pro"
            :ir_a_edit.sync="ir_a_edit"
        >
        </producto-create>
    </v-container>
</template>
<script>
import {mapGetters} from 'vuex';
import {mapActions} from "vuex";
import moment from 'moment'
import ProductoCreate from '@/components/compras/LiquidarProductoCreate'
export default {
    props:{
        lineas: Array,
        reload_lineas: Boolean,
        total_gr: Number
    },
    components: {
        'producto-create': ProductoCreate,
	},
    data () {
        return {
            show_data: false,
            paginaActual:{},
            pagination:{
                model: "liquidar",
                descending: true,
                page: 1,
                rowsPerPage: 10,
                sortBy: "fecha_compra",
            },
            itemCreate:{
                nombre:"",
                peso_gr:0,
                quilates:"",
                precio_coste:0,
                clase_id: 0
            },
            expand:false,
            dialog_pro: false,
            compra:{
                id:0,
                grupo_id:0,
            },

            loading_f: false,
            search:"",
            selected: [],
            reg_sel:null,
            ref_lin:null,
            ir_a_edit: false,

            headers: [
                {
                    text: 'Fecha',
                    align: 'left',
                    value: 'fecha_compra',
                    width:'10%'
                },
                 {
                    text: 'Compra',
                    align: 'left',
                    value: 'alb_ser',
                    width:'12%'
                },
                {
                    text: 'Concepto',
                    align: 'left',
                    value: 'concepto',
                },
                {
                    text: 'Clase',
                    align: 'left',
                    value: 'nombre',
                    width:'12%'
                },
                {
                    text: 'Peso',
                    align: 'center',
                    value: 'peso_gr',
                    width:'5%'
                },
                {
                    text: 'Importe',
                    align: 'center',
                    value: 'importe',
                    width:'5%'
                },
                {
                    text: 'Precio Gr.',
                    align: 'center',
                    value: 'importe',
                    width:'5%'
                },
                {
                    text: 'A',
                    align: 'Center',
                    value: '',
                    sortable: false,
                    width:'1%'
                }
             ]
            }

        },
    mounted(){

        if (this.getPagination.model == this.pagination.model)
            this.updatePosPagina(this.getPagination);
        else
            this.unsetPagination();

    },
    updated(){


    },
    watch:{
        ir_a_edit: function () {
            this.$emit('update:reload_lineas', this.ir_a_edit)
            this.ir_a_edit = false;
        }
    },
    computed: {
        ...mapGetters([
            'getPagination',
            'getLineasIndex'
        ]),
        computedTotalGr(){
            return parseFloat(this.total_gr).toLocaleString('de-DE',{ style: 'decimal'});
        },
    },
    methods:{
        ...mapActions([
            'setPagination',
            'unsetPagination',
        ]),
        precioGr(item){

            return (item.peso_gr != 0) ? (parseFloat(item.importe) / parseFloat(item.peso_gr)).toFixed(2) : "";

        },
        editPro(producto_id){
             this.setPagination(this.paginaActual);
             this.$router.push({ name: 'producto.edit', params: { id: producto_id } })
        },
        updateEventPagina(obj){

            this.paginaActual = obj;

        },
        updatePosPagina(pag){

            this.pagination.page = pag.page;
            this.pagination.descending = pag.descending;
            this.pagination.rowsPerPage= pag.rowsPerPage;
            this.pagination.sortBy = pag.sortBy;

        },
        getFecha(f){

            moment.locale('es');
            return f ? moment(f).format('DD/MM/YYYY') : '';
        },
        openDialog(item){
            this.compra = item.compra;

            this.itemCreate.nombre = item.concepto;
            this.itemCreate.precio_coste = item.importe;
            this.itemCreate.peso_gr = item.peso_gr;
            this.itemCreate.clase_id = item.clase_id;
            this.itemCreate.quilates = item.quilates;

            this.itemCreate.precio_venta = Math.round(item.importe * (1 +.30));

            this.dialog_pro = true;
        },

    }

}
</script>

<style scoped>

table.v-table tbody td, table.v-table tbody th {
    height: 38px;
}
</style>
