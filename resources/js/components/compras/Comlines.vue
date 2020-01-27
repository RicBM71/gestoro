<template>
    <div>
        <my-dialog :dialog.sync="dialog" registro="registro" @destroyReg="destroyReg"></my-dialog>
            <v-layout row wrap>
                <v-flex xs12>
                    <v-data-table
                    :headers="headers"
                    :items="lineas"
                    rows-per-page-text="Registros por página"
                    >
                        <template slot="items" slot-scope="props">
                            <td v-if="estaLiquidado(props.item.fecha_liquidado)" class="indigo--text darken-4">{{ props.item.concepto }}</td>
                            <td v-else class="indigo--text darken-4"><span class="red--text">Liquidado {{getFecha(props.item.fecha_liquidado)}}</span> {{ props.item.concepto }}</td>
                            <td v-if="show_grab" class="indigo--text darken-4">{{ props.item.grabaciones }}</td>
                            <td v-if="show_grab" class="indigo--text darken-4">{{ props.item.colores }}</td>
                            <td class="indigo--text darken-4">{{ props.item.clase.nombre }} {{textQuilates(props.item.quilates)}}</td>
                            <td class="indigo--text darken-4 text-xs-right">{{ props.item.peso_gr | currency('', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false }) }}</td>
                            <td class="indigo--text darken-4 text-xs-right">{{ props.item.importe | currency('', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false }) }}</td>
                            <td class="indigo--text darken-4 text-xs-right">{{ props.item.importe_gr | currency('', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false }) }}</td>
                            <td class="justify-center layout px-0">
                                <v-icon
                                    v-show="compra.fase_id<=2"
                                    small
                                    class="mr-2"
                                    @click="editItem(props.item)"
                                >
                                    edit
                                </v-icon>
                                <v-icon
                                    v-show="compra.fase_id==1"
                                    small
                                    @click="openDialog(props.item.id)"
                                    >
                                    delete
                                </v-icon>
                            </td>
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
            <v-layout row wrap v-if="compra.fase_id==1">
                <v-flex xs10></v-flex>
                <v-flex xs2>
                    <v-btn round flat color="primary" v-on:click="create" small >
                        <v-icon small>add</v-icon> Crear Línea
                    </v-btn>
                </v-flex>
            </v-layout>
        <lines-create
            v-if="clases.length > 0"
            :compra_id="compra_id"
            :clases="clases"
            :show_grab="show_grab"
            :dialog_lin.sync="dialog_lin"
            :quilates="quilates"
             @reLoadLineas="reLoadLineas">
        </lines-create>
        <lines-edit
            v-if="clases.length > 0 && editedItem.id > 0"
            :editedItem="editedItem"
            :dialog_edt.sync="dialog_edt"
            :show_grab="show_grab"
            :clases="clases"
            :compra="compra"
            :quilates="quilates"
            @reLoadLineas="reLoadLineas">
        </lines-edit>
    </div>
</template>
<script>
import {mapGetters} from 'vuex';
import moment from 'moment'
import MyDialog from '@/components/shared/MyDialog'
import linesCreate from './LinCreate'
import linesEdit from './LinEdit'
export default {
    props:['compra_id','grabaciones','compra','refresh_lin'],
    // props:{
    //     compra_id: Number,
    //     grabaciones: Boolean,
    //     compra: Object,
    // },
    components: {
        'my-dialog': MyDialog,
        'lines-create': linesCreate,
        'lines-edit': linesEdit
	},
    data () {
        return {
           show_grab: false,
           dialog: false,
           lineas: [],
           clases: [],
           totales:"",
           comlines: {},
           comlines_id:0,
           dialog_lin: false,
           quilates:[],


           dialog_edt: false,

            editedIndex: -1,
            editedItem: {id:0},

            headers:[],

            headers_grab: [
                {
                    text: 'Detalle del lote',
                    align: 'left',
                    value: 'concepto',
                    sortable: false,
                },
                {
                    text: 'Grabaciones',
                    align: 'left',
                    value: 'grabaciones',
                    sortable: false,
                    width:'20%'
                },
                {
                    text: 'Colores',
                    align: 'left',
                    value: 'colores',
                    sortable: false,
                    width:'10%'
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
        if (this.compra_id > 0){
            axios.post('/compras/comlines/load',{
                compra_id: this.compra.id,
                grupo_id: this.compra.grupo_id
            })
            .then(res => {

                this.show_grab = (this.grabaciones) ? true : false;

                this.headers =  (this.grabaciones) ?  this.headers_grab : this.headers_singrab;

                this.clases = res.data.clases;

                this.lineas = res.data.lineas;
                this.totales = res.data.totales;
                this.quilates = res.data.quilates;

                if (this.lineas.length == 0){
                    this.create();
                }

            })
            .catch(err => {
                if (err.response.status == 404)
                    this.$toast.error("Compra No encontrada!");
                else
                    this.$toast.error(err.response.data.message);
                this.$router.push({ name: 'compra.index'})
            })
        }
    },
    computed:{
        computedTotAlb(){

            return parseFloat(this.totales.importe) - parseFloat(this.totales.impirpf)  + parseFloat(this.totales.impiva);
        }
    },
    watch: {
        refresh_lin: function () {
            axios.post('/compras/comlines/load',{
                compra_id: this.compra.id,
                grupo_id: this.compra.grupo_id
            })
            .then(res => {
                this.lineas = res.data.lineas;
                this.totales = res.data.totales;
            })
            .catch(err => {
                if (err.response.status == 404)
                    this.$toast.error("Compra No encontrada!");
                else
                    this.$toast.error(err.response.data.message);
                this.$router.push({ name: 'compra.index'})
            })


        }
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
        estaLiquidado(f){
            return f==null ? true : false;
        },
        reLoadLineas(lineas, totales){
            this.lineas = lineas;
            this.totales = totales;

            axios.get('/compras/compras/'+this.compra_id)
            .then(res => {
                this.$emit('update:compra', res.data.compra);
            })

        },
        create(){
            this.dialog_lin = true;
        },
        editItem(item){

            this.editedIndex = this.lineas.indexOf(item)
           // this.editedItem = Object.assign({}, item)

            this.editedItem = item;
            this.dialog_edt = true

            // axios.get('/compras/comlines/'+id+"/edit")
            //     .then(res => {
            //     this.lineas = res.data.lineas;
            // });

            // this.lineas_id = id;
            // this.dialog_edt = true;
        },
        openDialog (id){
            this.dialog = true;
            this.lineas_id = id;
        },
        destroyReg () {
            this.dialog = false;

            axios.post('/compras/comlines/'+this.lineas_id,{_method: 'delete'})
                .then(res => {
                // this.lineas = res.data.lineas;
                // this.totales = res.data.totales;
                this.reLoadLineas(res.data.lineas, res.data.totales);

            })
            .catch(err => {
                this.status = true;
                var msg = err.response.data;
                this.$toast.error(msg);

            });

        },
    }

}
</script>

<style scoped>

table.v-table tbody td, table.v-table tbody th {
    height: 38px;
}
</style>
