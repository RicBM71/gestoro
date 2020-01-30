<template>
    <div>
        <my-dialog :dialog.sync="dialog" registro="registro" @destroyReg="destroyReg"></my-dialog>
            <v-layout row wrap>
                <v-flex xs12>
                    <v-data-table
                    :pagination.sync="pagination"
                    :headers="headers"
                    :items="lineas"
                    :expand="expand"
                    rows-per-page-text="Registros por página"
                    >
                        <template slot="items" slot-scope="props">
                            <td>{{ props.item.producto.referencia }}</td>
                            <td>{{ props.item.producto.nombre }}</td>
                            <td class="text-xs-right">{{ props.item.unidades | currency('', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false }) }}</td>
                            <td v-if="isSupervisor" class="text-xs-right">{{ props.item.precio_coste | currency('', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false }) }}</td>
                            <td v-else>-</td>
                            <td class="text-xs-right">{{ props.item.iva | currency('%', 0, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false }) }}</td>
                            <td v-if="albaran.tipo_id==3" class="text-xs-right">{{ props.item.importe_unidad | currency('', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false }) }}</td>
                            <td v-else class="text-xs-right">{{ props.item.importe_unidad | currency('', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false }) }}</td>
                            <td v-if="isSupervisor"  class="text-xs-right">{{ props.item.margen | currency('', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false }) }}</td>
                            <td v-else>-</td>
                            <td class="text-xs-right">{{ props.item.importe_venta | currency('', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false }) }}</td>
                            <td class="justify-center layout px-0">
                                <v-icon
                                    small
                                    class="mr-2"
                                    @click="goProducto(props.item.producto_id)"
                                >
                                    local_offer
                                </v-icon>
                                <v-icon
                                    small
                                    class="mr-2"
                                    @click="props.expanded = !props.expanded"
                                >
                                    visibility
                                </v-icon>
                                <v-icon
                                    v-if="computedEdit"
                                    small
                                    class="mr-2"
                                    @click="editItem(props.item)"
                                >
                                    edit
                                </v-icon>
                                <v-icon
                                    v-if="computedEdit"
                                    small
                                    @click="openDialog(props.item.id)"
                                    >
                                    delete
                                </v-icon>
                            </td>
                        </template>
                        <template slot="footer">
                            <td class=""></td>
                            <td class="text-xs-right font-weight-bold">Total Ud./Gr</td>
                            <td class="text-xs-right font-weight-bold">{{ totales.unidades| currency('', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false }) }}</td>
                            <td class="text-xs-right font-weight-bold"></td>
                            <td class="text-xs-right font-weight-bold">IVA</td>
                            <td v-if="albaran.tipo_id==3" class="text-xs-right font-weight-bold">{{ totales.iva_rebu| currency('€', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false }) }}</td>
                            <td v-else class="text-xs-right font-weight-bold">{{ totales.iva| currency('€', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false }) }}</td>
                            <td class="text-xs-right font-weight-bold">TOTAL</td>
                            <td class="text-xs-right font-weight-bold">{{ totales.total| currency('€', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false }) }}</td>
                            <td class="text-xs-right font-weight-bold">RESTO: {{ computedResto | currency('€', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false }) }}</td>
                        </template>
                        <template v-slot:expand="props">
                            <v-card flat>
                                <v-card-text class="font-italic">
                                    {{conCaracteristicas(props.item)}}
                                </v-card-text>
                            </v-card>
                        </template>
                        <template slot="pageText" slot-scope="props">
                            Registros {{ props.pageStart }} - {{ props.pageStop }} de {{ props.itemsLength }}
                        </template>
                    </v-data-table>
                </v-flex>
            </v-layout>
            <v-layout row wrap v-if="albaran.factura == null">
                <v-flex xs10></v-flex>
                <v-flex xs2>
                    <v-btn round flat color="primary" v-on:click="create" small >
                        <v-icon small>add</v-icon> Crear Línea
                    </v-btn>
                </v-flex>
            </v-layout>
        <lines-create
            :albaran="albaran"
            :dialog_lin.sync="dialog_lin"
            :refresh_lineas.sync="refresh_lineas"
        >
        </lines-create>
        <lines-edit
            v-if="editedItem.id > 0"
            :albaran="albaran"
            :editedItem="editedItem"
            :dialog_edt.sync="dialog_edt"
            :refresh_lineas.sync="refresh_lineas"
        >
        </lines-edit>
    </div>
</template>
<script>
import {mapGetters} from 'vuex';
import moment from 'moment'
import MyDialog from '@/components/shared/MyDialog'
import AlbalinCreate from './AlbalinCreate'
import AlbalinEdit from './AlbalinEdit'
export default {
    props:['albaran','totales','acuenta'],
    // props:{
    //     albaran: Object,
    //     totales: Object,
    //     resto: String
    // },
    components: {
        'my-dialog': MyDialog,
        'lines-create': AlbalinCreate,
        'lines-edit': AlbalinEdit
	},
    data () {
        return {
           expand: false,
           refresh_lineas: 0,
           dialog: false,
           lineas: [],
           comlines_id:0,
           dialog_lin: false,
           resto: 0,

           dialog_edt: false,

            editedIndex: -1,
            editedItem: {id:0},

            pagination:{
                descending: true,
                sortBy: "id",
            },

            headers:[
                {
                    text: 'Referencia',
                    align: 'left',
                    value: 'producto.referencia',
                    width:'8%'
                },
                {
                    text: 'Detalle producto',
                    align: 'left',
                    value: 'producto.nombre',
                },
                {
                    text: 'Ud./Peso',
                    align: 'center',
                    value: 'unidades',
                    width:'8%'
                },
                {
                    text: 'Coste',
                    align: 'center',
                    value: 'precio_coste',
                    width:'8%'
                },
                {
                    text: 'IVA',
                    align: 'center',
                    value: 'iva',
                    width:'6%'
                },
                {
                    text: 'Imp. Ud.',
                    align: 'center',
                    value: 'importe_unidad',
                    width:'8%'
                },
                {
                    text: 'Margen',
                    align: 'center',
                    value: 'margen',
                    width:'8%'
                },
                {
                    text: 'Importe',
                    align: 'center',
                    value: 'importe_venta',
                    width:'8%'
                },
                {
                    text: 'Acciones',
                    align: 'Center',
                    value: 'id',
                    width:'15%'
                }
             ],
            }
        },
    mounted(){
        if (this.albaran.id > 0){
           this.refresh_lineas++;
        }
    },
    computed:{
         ...mapGetters([
            'isSupervisor',
            'hasEdtFac'
        ]),
        computedResto(){
            return (this.totales.total - this.acuenta).toFixed(2);
        },
        computedTotAlb(){
            return parseFloat(this.totales.importe) - parseFloat(this.totales.impirpf)  + parseFloat(this.totales.impiva);
        },
        computedEdit(){

            if (this.hasEdtFac) return true;

            if (this.albaran.factura > 0 || this.albaran.fase_id > 10 ) return false;

            return true;

        }
    },
    watch: {
        refresh_lineas: function () {
            axios.post('/ventas/albalins/load',{
                albaran_id: this.albaran.id,
            })
            .then(res => {

                this.lineas = res.data.lineas;
                this.$emit('update:totales', res.data.totales);
                this.$emit('update:albaran', res.data.albaran);

                this.resto = (res.data.totales.total - this.acuenta).toFixed(2);

                if (this.lineas.length == 0){
                    this.create();
                }

            })
            .catch(err => {
                if (err.response.status == 404)
                    this.$toast.error("Albarán No encontrado!");
                else
                    this.$toast.error(err.response.data.message);
                this.$router.push({ name: 'albaran.index'})
            })

        },
    },
    methods:{
        conCaracteristicas(item){

            const quilates = item.producto.quilates > 0 ? item.producto.quilates+"K" : "";

            const peso = (item.producto.peso_gr && item.producto.univen == "G") > 0 ? item.producto.peso_gr+" gr." : "";

            const caracteristicas = item.producto.caracteristicas != null ? item.producto.caracteristicas : "";
            const garantia = item.producto.garantia_id != null ?
                        "Garantía: "+item.producto.garantia.nombre + " " + item.producto.meses_garantia + " meses. U. Revisión: " + this.getFecha(item.producto.fecha_ultima_revision) : "";

            const notas = item.producto.notas != null ? '('+item.producto.notas+')' : '';
            const nombre_interno = item.producto.nombre_interno != null ? '('+item.producto.nombre_interno+')' : '';

            if (this.albaran.tipo_id == 3)
                return item.producto.clase.nombre+" "+quilates+" "+caracteristicas+" "+peso + " " + garantia + notas + nombre_interno;
            else
                return item.producto.clase.nombre+": "+ item.notas + " # " + nombre_interno;

        },
        goProducto(producto_id){
            this.$router.push({ name: 'producto.edit', params: { id: producto_id } })
        },
        create(){
            this.dialog_lin = true;
        },
        editItem(item){

            this.editedIndex = this.lineas.indexOf(item)
            this.editedItem = item;

            this.dialog_edt = true

        },
        openDialog (id){
            this.dialog = true;
            this.lineas_id = id;
        },
        destroyReg () {
            this.dialog = false;

            axios.post('/ventas/albalins/'+this.lineas_id,{_method: 'delete'})
                .then(res => {
                    this.lineas = res.data.lineas;

                    this.$emit('update:totales', res.data.totales);
                    this.refresh_lineas++;

            })
            .catch(err => {
                this.status = true;
                var msg = err.response.data;
                this.$toast.error(msg);

            });

        },
        getFecha(newValue) {

            moment.locale('es');
            return newValue ? moment(newValue).format('DD/MM/YYYY') : '';
        },

    }

}
</script>

<style scoped>

table.v-table tbody td, table.v-table tbody th {
    height: 32px;
}
table.v-table tfoot tr td {
    padding: 0 2px;
}
</style>
