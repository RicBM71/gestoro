<template>
    <div>

            <v-layout row wrap>
                <v-flex xs12>
                    <v-data-table
                        v-model="selected"
                        :headers="headers"
                        :items="lineas"
                        select-all
                        item-key="id"
                        rows-per-page-text="Registros por página"
                    >
                        <template slot="items" slot-scope="props">
                             <td>
                                <v-checkbox
                                v-model="props.selected"
                                primary
                                hide-details
                                ></v-checkbox>
                            </td>
                            <td v-if="estaLiquidado(props.item.fecha_liquidado)" class="indigo--text darken-4">{{ props.item.concepto }}</td>
                            <td v-else class="indigo--text darken-4"><span class="red--text">Liquidado {{getFecha(props.item.fecha_liquidado)}}</span> {{ props.item.concepto }}</td>
                            <td v-if="show_grab" class="indigo--text darken-4">{{ props.item.grabaciones }}</td>
                            <td class="indigo--text darken-4">{{ props.item.clase.nombre }} {{props.item.quilates}}</td>
                            <td class="indigo--text darken-4 text-xs-right">{{ props.item.peso_gr | currency('', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false }) }}</td>
                            <td class="indigo--text darken-4 text-xs-right">{{ props.item.importe | currency('', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false }) }}</td>
                            <td class="justify-center layout px-0">
                                <v-icon
                                    v-show="props.item.fecha_liquidado != null"
                                    small
                                    class="mr-2"
                                    @click="openDialog(props.item)"
                                >
                                    save
                                </v-icon>
                            </td>
                        </template>
                        <template slot="pageText" slot-scope="props">
                            Registros {{ props.pageStart }} - {{ props.pageStop }} de {{ props.itemsLength }}
                        </template>
                    </v-data-table>
                </v-flex>
            </v-layout>
            <producto-create
                :compra="compra"
                :itemCreate.sync="itemCreate"
                :dialog_pro.sync="dialog_pro"
                :ir_a_edit="ir_a_edit"
            >
            </producto-create>
    </div>
</template>
<script>
import {mapGetters} from 'vuex';
import ProductoCreate from './LiquidarProductoCreate'
import moment from 'moment'
export default {
    props:{
        compra: Object,
        grabaciones: Number,
        lineas: Array,
        lin_selected: Array,
        productoCreado: Object
    },
     components: {
        'producto-create': ProductoCreate
	},
    data () {
        return {
            show_grab: false,
            dialog: false,
            dialog_pro: false,
            ir_a_edit: false,

            editedIndex: -1,
            editedItem: {},
            itemCreate:{
                nombre:"",
                nombre_interno:"",
                peso_gr:0,
                quilates:"",
                precio_coste:0,
                clase_id: 0
            },

            headers:[],
            selected: [],
            headers_grab: [
                {
                    text: 'Detalle del lote',
                    align: 'left',
                    value: 'concepto',
                    sortable: false,
                    width:'50%'
                },
                {
                    text: 'Grabaciones',
                    align: 'left',
                    value: 'grabaciones',
                    sortable: false,
                    width:'30%'
                },
                 {
                    text: 'Clase',
                    align: 'left',
                    value: 'clase-quil',
                    sortable: false,
                    width:'10%'
                },
                {
                    text: 'Peso',
                    align: 'center',
                    value: 'peso_gr',
                    sortable: false,
                    width:'5%'
                },
                {
                    text: 'Importe',
                    align: 'center',
                    value: 'importe',
                    sortable: false,
                    width:'5%'
                },
                 {
                    text: 'A',
                    align: 'Center',
                    value: '',
                    sortable: false,
                    width:'1%'
                }
             ],
            headers_singrab: [
                {
                    text: 'Concepto',
                    align: 'left',
                    value: 'concepto',
                    sortable: false
                },
                {
                    text: 'Clase',
                    align: 'left',
                    value: 'clase-quil',
                    sortable: false,
                    width:'10%'
                },
                {
                    text: 'Peso Gr.',
                    align: 'center',
                    value: 'peso_gr',
                    sortable: false,
                    width:'5%'
                },
                {
                    text: 'Importe',
                    align: 'center',
                    value: 'importe',
                    sortable: false,
                    width:'5%'
                },
                 {
                    text: 'A',
                    align: 'Center',
                    value: '',
                    sortable: false,
                    width:'1%'
                }
             ],
            }
        },
    mounted(){

        this.show_grab = (this.grabaciones) ? true : false;
        this.headers =  (this.grabaciones) ?  this.headers_grab : this.headers_singrab;
    },
    watch:{
        itemCreate: function () {
            this.$emit('update:productoCreado', this.itemCreate);
        },
        selected: function () {
            this.$emit('update:lin_selected', this.selected);
        },
        lin_selected: function () {
            this.selected = this.lin_selected;
        },
    },
    methods:{
        impUni(peso_gr,importe){

            if (peso_gr == 0) return "-";

            return (importe / peso_gr).toFixed(2);

        },
        getFecha(f){
            moment.locale('es');
            return f ? moment(f).format('DD/MM/YYYY') : '';
        },
        estaLiquidado(f){
            return f==null ? true : false;
        },
        openDialog(item){
            
            this.itemCreate.nombre = item.concepto;
            this.itemCreate.nombre_interno = '';
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
