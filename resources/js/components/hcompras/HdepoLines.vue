<template>
    <div>
        <v-layout row wrap>
            <v-flex xs12>
                <v-data-table
                :headers="headers"
                :items="hdepositos"
                rows-per-page-text="Registros por página"
                >
                    <template slot="items" slot-scope="props">
                        <td>{{ props.item.operacion}} {{ props.item.username_his}} {{ props.item.created_his}}</td>
                        <td>{{ formatDate(props.item.fecha)}}</td>
                        <td :class="props.item.concepto.color">{{ props.item.concepto.nombre+formatDias(props.item.dias)+" Notas: "+ props.item.notas+" IBAN: "+props.item.iban }}</td>
                        <td class="text-xs-right">{{ props.item.importe | currency('', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false }) }}</td>
                        <td class="text-xs-center">{{ modificado(props.item) }}</td>
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
import moment from 'moment'
import MyDialog from '@/components/shared/MyDialog'
import {mapGetters} from 'vuex';
export default {
    props:{
        hdepositos: Array,
    },
    components: {

	},
    data () {
        return {
            dialog: false,
            totales:"",

            hoy: new Date().toISOString().substr(0, 10),

            headers: [
                 {
                    text: 'Operacion',
                    align: 'left',
                    value: 'id',
                    width:'10%'
                },
                {
                    text: 'Fecha',
                    align: 'left',
                    value: 'fecha',
                    sortable: false,
                    width:'10%'
                },
                {
                    text: 'Operación',
                    align: 'left',
                    value: 'depositos.nombre',
                    sortable: false
                },
                {
                    text: 'Importe',
                    align: 'center',
                    value: 'importe',
                    sortable: false,
                    width:'5%'
                },
                {
                    text: 'Usuario',
                    align: 'center',
                    value: 'username',
                    sortable: false,
                    width:'24%'
                },
             ],
            }
        },
    mounted(){
    },
    computed: {
        ...mapGetters([
        ]),
    },
    methods:{
        formatDias(d){
            return d > 0 ? " "+d+" días" : "";
        },

        formatDate(f){
            if (f == null) return null;
            moment.locale('es');
            return moment(f).format('DD/MM/YYYY');
        },

        modificado(item){
            moment.locale('es');
            //return item.username;
            return item.username+" "+moment(item.updated_at).format('D/MM/YYYY H:mm:ss');
        },

    }

}
</script>

