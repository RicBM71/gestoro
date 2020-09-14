<template>
    <div>
            <v-layout row wrap>
                <v-flex xs12>
                    <v-data-table
                    :headers="headers"
                    :items="hcomlines"
                    rows-per-page-text="Registros por página"
                    >
                        <template slot="items" slot-scope="props">
                            <td class="indigo--text darken-4">{{ props.item.concepto }}</td>
                            <td v-if="show_grab" class="indigo--text darken-4">{{ props.item.grabaciones }}</td>
                            <td v-if="show_grab" class="indigo--text darken-4">{{ props.item.colores }}</td>
                            <td class="indigo--text darken-4">{{ props.item.clase.nombre }} {{textQuilates(props.item.quilates)}}</td>
                            <td class="indigo--text darken-4 text-xs-right">{{ props.item.peso_gr | currency('', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false }) }}</td>
                            <td class="indigo--text darken-4 text-xs-right">{{ props.item.importe | currency('', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false }) }}</td>
                            <td class="indigo--text darken-4 text-xs-right">{{ props.item.importe_gr | currency('', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false }) }}</td>
                        </template>
                        <template slot="footer">
                        <td v-if="show_grab"></td>
                        <td v-if="show_grab"></td>
                        <td class="indigo--text darken-4 text-xs-right font-weight-bold" colspan="2">TOTAL</td>
                        <td class="indigo--text darken-4 text-xs-right font-weight-bold">{{ totales.peso_gr| currency(' ', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false }) }}</td>
                        <td class="indigo--text darken-4 text-xs-right font-weight-bold">{{ totales.importe| currency('', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false }) }}</td>
                        <td class=""></td>
                        <td class=""></td>
                        </template>
                        <template slot="pageText" slot-scope="props">
                            Registros {{ props.pageStart }} - {{ props.pageStop }} de {{ props.itemsLength }}
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
    props:['hcomlines'],
    data () {
        return {
           show_grab: false,


           clases: [],
           totales:"",
           comlines: {},
           comlines_id:0,
           dialog_lin: false,
           quilates:[],


            headers: [
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
                    text: 'Imp. Uni',
                    align: 'center',
                    value: 'impuni',
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

    },

    methods:{
        textQuilates(k){
            return k > 0 ? k+"K" : null;
        },
        impUni(peso_gr,importe){

            if (peso_gr == 0) return "-";

            return (importe / peso_gr).toFixed(2);

        },
        getFecha(f){
            moment.locale('es');
            return f ? moment(f).format('DD/MM/YYYY') : '';
        },
    }

}
</script>

<style scoped>

table.v-table tbody td, table.v-table tbody th {
    height: 38px;
}
</style>
