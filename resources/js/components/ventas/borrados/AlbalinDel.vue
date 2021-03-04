<template>
    <div>

            <v-layout row wrap>
                <v-flex xs12>
                    <v-data-table
                     hide-actions
                    :pagination="pagination"
                    :headers="headers"
                    :items="albalin"
                    >
                        <template slot="items" slot-scope="props">
                            <tr v-if="props.item.producto == null">
                                <td>{{ props.item.producto_id}}</td>
                                <td class="red--text">error al ubicar, contactar admin</td>
                            </tr>
                            <tr v-else>
                                <td><v-icon
                                        v-if="props.item.producto.online"
                                        small
                                        class="blue--text lighten-5 mr-2"
                                    >
                                        star
                                    </v-icon> {{ props.item.producto.referencia }}</td>
                                <td>{{ concepto(props.item) }}</td>
                                <td class="text-xs-right">{{ props.item.unidades | currency('', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false }) }}</td>
                                <td v-if="isAdmin" class="text-xs-right">{{ props.item.precio_coste | currency('', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false }) }}</td>
                                <td v-else>-</td>
                                <td class="text-xs-right">{{ props.item.iva | currency('%', 0, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false }) }}</td>
                                <td v-if="albaran.tipo_id==3" class="text-xs-right">{{ props.item.importe_unidad | currency('', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false }) }}</td>
                                <td v-else class="text-xs-right">{{ props.item.importe_unidad | currency('', 4, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false }) }}</td>
                                <td class="text-xs-right">{{margen_fix(props.item)}}<span v-if="icon_fix(props.item)"><v-icon small color="red darken-4">south</v-icon></span></td>
                                <td class="text-xs-right">{{ props.item.descuento | currency('%', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false }) }}</td>
                                <td class="text-xs-right">{{ props.item.importe_venta | currency('', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false }) }}</td>
                                <td>{{modificado(props.item)}}</td>
                                <td class="justify-center layout px-0">
                                    <v-icon
                                        small
                                        class="mr-2"
                                        @click="goProducto(props.item.producto)"
                                    >
                                        local_offer
                                    </v-icon>
                                </td>
                            </tr>
                        </template>
                    </v-data-table>
                </v-flex>
            </v-layout>
    </div>
</template>
<script>
import {mapGetters} from 'vuex';
import moment from 'moment'
export default {
    props:['albaran','albalin'],
    components: {
	},
    data () {
        return {
           expand: false,

           resto: 0,
           hoy: new Date().toISOString().substr(0, 10),

           dialog_edt: false,

            editedIndex: -1,
            editedItem: {id:0},

            pagination:{},

            headers:[
                {
                    text: 'Referencia',
                    align: 'left',
                    value: 'producto.referencia',
                    width:'10%'
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
                    text: 'Dto.',
                    align: 'center',
                    value: 'descuento',
                    width:'5%'
                },
                {
                    text: 'Importe',
                    align: 'center',
                    value: 'importe_venta',
                    width:'8%'
                },
                 {
                    text: 'Usuario',
                    align: 'center',
                    value: 'username',
                    width:'15%'
                },
                {
                    text: 'Acciones',
                    align: 'Center',
                    value: 'id',
                    width:'2%'
                }
             ],
            }
        },
    mounted(){

    },
    computed:{
         ...mapGetters([
            'hasEdtFac',
            'hasAddVen',
            'userName',
            'isAdmin',
            'parametros'
        ]),
        computedResto(){
            return (this.totales.total - this.acuenta).toFixed(2);
        },
        computedTotAlb(){
            return parseFloat(this.totales.importe) - parseFloat(this.totales.impirpf)  + parseFloat(this.totales.impiva);
        },
    },
    watch: {

    },
    methods:{
        modificado(item){
            moment.locale('es');
            return item.username+" "+moment(item.updated_at).format('D/MM/YYYY H:mm');
        },
        concepto(item) {
            if (item.notas != null)
                return item.producto.nombre + ' ** ' + item.notas;
            else
                return item.producto.nombre;
        },
        icon_fix(item){

            if (this.albaran.tipo_id != 3 || this.parametros.fixing == false || item.producto.clase_id != 1) return false;

            return parseFloat(this.fixing) > parseFloat(this.fix_producto(item));
        },
        margen_fix(item){
            // oro y reservado
            if (this.albaran.tipo_id == 3 && this.parametros.fixing == true && item.producto.clase_id == 1 && item.producto.estado_id == 3){

                var imp_fix = this.fix_producto(item);
                if (parseFloat(this.fixing) > parseFloat(this.fix_producto(item))){
                    if (this.low_fix == false)
                        this.$emit('update:low_fix', true);

                    return this.getDecimalFormat(this.fixing) + " / " + this.getDecimalFormat(imp_fix);
                }
            }

            if (!this.isAdmin) return '-';

            if (item.margen != null)
                return this.getDecimalFormat(item.margen, 2);
            else
                return "";

        },
        fix_producto(item){

            var imp_fix = 0;
            var imp_uni = parseFloat(item.importe_unidad);
            var peso = parseFloat(item.producto.peso_gr);

            return (imp_uni / peso).toFixed(0);

        },

        conCaracteristicas(item){

            const quilates = item.producto.quilates > 0 ? item.producto.quilates+"KT" : "";

            const peso = (item.producto.peso_gr && item.producto.univen == "U") > 0 ? item.producto.peso_gr+" gr." : "";

            const caracteristicas = item.producto.caracteristicas != null ? item.producto.caracteristicas : "";
            const garantia = item.producto.garantia_id != null ?
                        "Garantía: "+item.producto.garantia.nombre + " " + item.producto.meses_garantia + " meses. U. Revisión: " + this.getFecha(item.producto.fecha_ultima_revision) : "";

            const notas = item.producto.notas != null ? '('+item.producto.notas+')' : '';
            const nombre_interno = item.producto.nombre_interno != null ? '('+item.producto.nombre_interno+')' : '';

            if (this.albaran.tipo_id == 3)
                return item.producto.clase.nombre+" "+quilates+" "+caracteristicas+" "+peso + " " + garantia + notas + nombre_interno;
            else
                return item.producto.clase.nombre+": "+ notas + " # " + nombre_interno;

        },
        goProducto(producto){

            if (producto.deleted_at == null)
                this.$router.push({ name: 'producto.edit', params: { id: producto.id } })
            else
                this.$router.push({ name: 'producto.show', params: { id: producto.id } })

        },
        getFecha(newValue) {

            moment.locale('es');
            return newValue ? moment(newValue).format('DD/MM/YYYY') : '';
        },
        getDecimalFormat(value, dec=0){
            return new Intl.NumberFormat("de-DE",{style: "decimal",minimumFractionDigits:dec}).format(parseFloat(value))
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
