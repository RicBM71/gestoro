<template>
    <div>
        <v-layout row wrap>
            <v-flex xs12>
                <v-data-table
                :headers="headers"
                :items="cobros_lin"
                hide-actions
                >
                    <template slot="items" slot-scope="props">
                        <td>{{ formatDate(props.item.fecha)}}</td>
                        <td>{{ props.item.fpago.nombre+" "+ formatNotas(props.item.notas) }}</td>
                        <td class="text-xs-right">{{ props.item.importe | currency('', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false }) }}</td>
                        <td class="text-xs-center">{{ modificado(props.item) }}</td>
                    </template>
                </v-data-table>
            </v-flex>
        </v-layout>
    </div>
</template>
<script>
import moment from 'moment'
import {mapGetters} from 'vuex';
export default {
    props:{
        albaran: Object,
        cobros_lin: Array,
    },
    components: {
	},
    data () {
        return {
            dialog: false,

            hoy: new Date().toISOString().substr(0, 10),

            clase:"blue--text",

            refresh_lineas: 0,
            editedIndex: -1,
            editedItem: {},
           // lineas:[],

            headers: [
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
                    value: 'concepto.nombre',
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
        // if (this.albaran.id > 0){
        //    this.refresh_lineas++;
        // }
    },
    computed: {
        ...mapGetters([
            'hasDelCob',
            'userName',
            'hasEdtFac',
            'hasAddVen'
        ]),
        computedResto(){
            return (this.totales.total - this.acuenta).toFixed(2);
        }
    },
    methods:{
         borrarLinea(item){

            if (!this.hasAddVen) return false;

            if (this.hasEdtFac) return true;

            if (this.albaran.factura > 0 && !this.hasDelCob) return false;

            if (this.cobros_lin.indexOf(item) == 0){ // es el último apunte.
                if (item.username == this.userName && item.updated_at.substr(0, 10) == this.hoy)
                    return true;
                else
                    return (this.hasDelCob);
            }
            else
                return (this.hasDelCob);


        },
        formatNotas(n){
            return  n > "" ? "("+n+")" : "";
        },
        formatDate(f){
            if (f == null) return null;
            moment.locale('es');
            return moment(f).format('DD/MM/YYYY');
        },
        modificado(item){
            moment.locale('es');
            return item.username+" "+moment(item.updated_at).format('D/MM/YYYY H:mm');
        },
        openDialog (item){
            this.dialog = true;
            this.editedItem = item;
        },
        destroyReg () {
            this.dialog = false;

            axios.post('/ventas/cobros/'+this.editedItem.id,{_method: 'delete'})
                .then(res => {

                    this.$emit('update:albaran', res.data.albaran);
                    this.$emit('update:cobros_lin', res.data.lineas);
                    this.$emit('update:acuenta', parseFloat(res.data.acuenta));
                })
                .catch(err => {
                    var msg = err.response.data;
                    this.$toast.error(msg);
                });
        },

    }

}
</script>

