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
                        <td>{{ formatDate(props.item.fecha)}}</td>
                        <td :class="props.item.concepto.color">{{ props.item.concepto.nombre+formatDias(props.item.dias)+" "+ formatNotas(props.item.notas)+" "+datosIBAN(props.item.iban,props.item.bic) }}</td>
                        <td class="text-xs-right">{{ props.item.importe | currency('', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false }) }}</td>
                        <td v-if="sepaEmpresa">
                              <v-icon
                                small
                                :color="colorRemesada(props.item.remesada)"
                                >
                                {{ remesada(props.item.remesada) }}
                            </v-icon>
                            <span v-if="props.item.remesada">Remesa OK</span>
                            <span v-else class="red--text">Pendiente</span>
                        </td>
                        <td class="text-xs-center">{{ modificado(props.item) }}</td>
                        <td class="justify-center layout px-0">
                            <v-icon
                                v-show="borrarLinea(props.item)"
                                small
                                color="red darken-4"
                                @click="openDialog(props.item)"
                                >
                                delete
                            </v-icon>
                        </td>
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
        conceptos: Array,
        compra: Object,
        lineas: Array,
        refresh_lin: Number
    },
    components: {
        'my-dialog': MyDialog,
	},
    data () {
        return {
            dialog: false,
            totales:"",

            hoy: new Date().toISOString().substr(0, 10),

            dialog_edt: false,
            clase:"blue--text",

            editedIndex: -1,
            editedItem: {},

            headers1: [
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
                    text: 'Remesada',
                    align: 'left',
                    value: 'remesada',
                    sortable: false,

                },
                {
                    text: 'Usuario',
                    align: 'center',
                    value: 'username',
                    sortable: false,
                    width:'24%'
                },
                {
                    text: 'A',
                    align: 'center',
                    sortable: false,
                    width:'1%'
                }
             ],
             headers2: [
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
                {
                    text: 'A',
                    align: 'center',
                    sortable: false,
                    width:'1%'
                }
             ],
             headers:[],
            }
        },
    mounted(){

            this.headers = (this.sepaEmpresa) ? this.headers1 : this.headers2;



    //     if (this.compra.id > 0){
    //         axios.get('/compras/depositos/'+this.compra.id)
    //         .then(res => {

    //             this.lineas = res.data.lineas;

    //         })
    //         .catch(err => {
    //             if (err.response.status == 404)
    //                 this.$toast.error("Compra No encontrada!");
    //             else
    //                 this.$toast.error(err.response.data.message);
    //             this.$router.push({ name: 'compra.index'})
    //         })
    //     }
    },
    computed: {
        ...mapGetters([
            'hasDelDep',
            'userName',
            'hasAddCom',
            'sepaEmpresa'
        ]),
    },
    methods:{
        formatDias(d){
            return d > 0 ? " "+d+" días" : "";
        },
        formatNotas(n){
            return  n > "" ? "("+n+")" : "";
        },
        formatDate(f){
            if (f == null) return null;
            moment.locale('es');
            return moment(f).format('DD/MM/YYYY');
        },
        datosIBAN(iban,bic){

            if (iban == null) return '';

            var iban_f= iban.substring(0,4)+" "+ iban.substring(4,8) + " " + iban.substring(8,12) + " "+ iban.substring(12,16) +" "+ iban.substring(16,20)+" "+ iban.substring(20,24);

            return (this.hasDelDep) ? "IBAN: "+iban_f+" - BIC: "+bic :  iban.substring(0,4)+" "+ iban.substring(4,8) + " **** **** **** *" + iban.substring(21,24);
        },
        modificado(item){
            moment.locale('es');
            //return item.username;
            return item.username+" "+moment(item.updated_at).format('D/MM/YYYY H:mm');
        },
        remesada(remesada){
            return remesada ? 'done_all' : 'report_problem';
        },
        colorRemesada(remesada){
            return remesada ? 'blue' : 'red';
        },
        borrarLinea(item){

            if (!this.hasAddCom) return false;

            if (this.compra.fase_id >= 6 || this.compra.factura > 0) return false;

            if (this.lineas.indexOf(item) == 0){
                //if (item.concepto_id <= 3 && item.created_at.substr(0, 10) == this.hoy) // esto es menos restrictivo
                if (item.username == this.userName && item.created_at.substr(0, 10) == this.hoy)
                    return true;
                else
                    return (this.hasDelDep);
            }
            else
                return false;


        },
        reLoadLineas(lineas, compra){

            this.$emit('update:lineas', lineas);
            this.$emit('update:compra', compra);

        },
        openDialog (item){
            this.dialog = true;
            this.editedItem = item;
        },
        destroyReg () {
            this.dialog = false;

            if (this.editedItem.concepto_id <= 3)
                this.borraDepo();
            else if(this.editedItem.concepto_id >= 4 && this.editedItem.concepto_id <= 6)
                this.borraAmpli();
            else if(this.editedItem.concepto_id >= 7 && this.editedItem.concepto_id <= 9)
                this.borraAcuenta();
            else if(this.editedItem.concepto_id >= 10 && this.editedItem.concepto_id <= 12)
                this.borraRecuperar();
            else if(this.editedItem.concepto_id >= 13 && this.editedItem.concepto_id <= 15)
                this.borraComprar();
            else if(this.editedItem.concepto_id >= 16 && this.editedItem.concepto_id <= 18)
                this.borraCapital();
        },
        borraDepo(){
            axios.post('/compras/depositos/'+this.editedItem.id,{_method: 'delete'})
                .then(res => {
                if (res.data.compra.fase_id <= 2)
                    this.$router.push({ name: 'compra.edit', params: { id: res.data.compra.id } })
                else
                    this.reLoadLineas(res.data.lineas, res.data.compra);

            })
            .catch(err => {
                this.status = true;
                var msg = err.response.data;
                this.$toast.error(msg);

            });

        },
        borraAcuenta(){
            axios.post('/compras/acuenta/'+this.editedItem.id,{_method: 'delete'})
                .then(res => {
                    this.reLoadLineas(res.data.lineas, res.data.compra);

            })
            .catch(err => {
                this.status = true;
                var msg = err.response.data;
                this.$toast.error(msg);
            });

        },
        borraCapital(){
            axios.post('/compras/capital/'+this.editedItem.id,{_method: 'delete'})
                .then(res => {
                    this.reLoadLineas(res.data.lineas, res.data.compra);
                    this.$emit('update:refresh_lin', this.refresh_lin + 1)
            })
            .catch(err => {
                this.status = true;
                var msg = err.response.data;
                this.$toast.error(msg);
            });

        },
        borraAmpli(){
            axios.post('/compras/ampliaciones/'+this.editedItem.id,{_method: 'delete'})
                .then(res => {
                    this.reLoadLineas(res.data.lineas, res.data.compra);
            })
            .catch(err => {
                this.status = true;
                var msg = err.response.data;
                this.$toast.error(msg);

            });
        },
        borraRecuperar(){
            axios.post('/compras/recuperar/'+this.editedItem.id,{_method: 'delete'})
                .then(res => {
                    this.reLoadLineas(res.data.lineas, res.data.compra);

            })
            .catch(err => {
                this.status = true;
                var msg = err.response.data;
                this.$toast.error(msg);
            });

        },
        borraComprar(){
            axios.post('/compras/comprar/'+this.editedItem.id,{_method: 'delete'})
                .then(res => {
                    //this.reLoadLineas(res.data.lineas, res.data.compra);
                    this.$router.push({ name: 'recompra.close', params: { id: this.compra.id } })
            })
            .catch(err => {
                this.status = true;
                var msg = err.response.data;
                this.$toast.error(msg);

            });
        }

    }

}
</script>

