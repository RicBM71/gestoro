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
            <v-flex xs1></v-flex>
            <v-spacer></v-spacer>
            <v-flex xs6>
                <v-text-field
                    v-model="search"
                    append-icon="search"
                    label="Buscar"
                    single-line
                    hide-details
                ></v-text-field>
            </v-flex>
            <v-flex sm2>
                <v-btn @click="liquidar" :disabled="selected.length==0" :loading="loading" round small block flat color="orange">
                    Liquidar {{reg_sel}}
                </v-btn>
            </v-flex>
        </v-layout>
        <v-layout row wrap>
            <v-flex xs12>
                <v-data-table
                    v-model="selected"
                    :headers="headers"
                    :items="lineas"
                    :search="search"
                    :pagination.sync="pagination"
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
                        <td>{{getFecha(props.item.fecha_compra)}}</td>
                        <td>{{props.item.serie_com+''+props.item.albaran}}</td>
                        <td class="gray--text darken-4">{{ props.item.concepto }}</td>
                        <td class="gray--text darken-4">{{ props.item.nombre}}</td>
                        <td class="gray--text darken-4 text-xs-right">{{ props.item.peso_gr | currency('', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false }) }}</td>
                        <td class="gray--text darken-4 text-xs-right">{{ props.item.importe | currency('', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false }) }}</td>
                        <td class="gray--text darken-4 text-xs-right">{{ precioGr(props.item) | currency('', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false }) }}</td>
                    </template>
                    <template slot="pageText" slot-scope="props">
                        Registros {{ props.pageStart }} - {{ props.pageStop }} de {{ props.itemsLength }}
                    </template>
                </v-data-table>
            </v-flex>
        </v-layout>
    </v-container>
</template>
<script>
import {mapGetters} from 'vuex';
import moment from 'moment'
export default {
    props:{
        parametros: Object,
        lineas: Array,
        total_gr: Number
    },
    data () {
        return {
            paginaActual:{},
            pagination:{
                model: "liquidar",
                descending: true,
                page: 1,
                rowsPerPage: 10,
                sortBy: "fecha_compra",
            },
            loading: false,
            search:"",
            selected: [],
            reg_sel:null,

            headers: [
                {
                    text: 'Fecha',
                    align: 'left',
                    value: 'fecha_compra',
                    width:'8%'
                },
                 {
                    text: 'Compra',
                    align: 'left',
                    value: 'albaran',
                    width:'8%'
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
             ],
            }
        },
    mounted(){
        // if (this.getPagination.model == this.pagination.model)
        //     this.updatePosPagina(this.getPagination);
        // else
        //     this.unsetPagination();

    },
    computed: {
        computedTotalGr(){

            return parseFloat(this.total_gr).toLocaleString('de-DE',{ style: 'decimal'});
        },
    },
    watch:{
        selected: function () {
            this.reg_sel = (this.selected.length > 0) ? "("+this.selected.length+")" : null;
        }
    },
    methods:{
        precioGr(item){

            return (item.peso_gr != 0) ? (parseFloat(item.importe) / parseFloat(item.peso_gr)).toFixed(2) : "";

        },
        getFecha(f){
            moment.locale('es');
            return f ? moment(f).format('DD/MM/YYYY') : '';
        },
        liquidar(){
            this.loading = false;
            if (this.loading === false){
                this.loading = true;
                axios.put('/compras/liquidar/masivo', {
                    lineas: this.selected,
                    fecha_liq: this.parametros.fecha_liq
                })
                    .then(res => {
                        this.$emit('update:lineas', [])
                        this.loading = false;
                    })
                    .catch(err => {
                        this.$toast.error(err);
                        this.loading = false;
                    });

            }

        },
        editItem (item) {

            var ruta = item.tipo_id == 1 ? 'recompra' : 'compra';

            if (item.fase_id <= 2)
                this.$router.push({ name: 'compra.edit', params: { id: item.compra_id } })
            else
                this.$router.push({ name: ruta+'.close', params: { id: item.compra_id } })

        },
    }

}
</script>

<style scoped>

table.v-table tbody td, table.v-table tbody th {
    height: 38px;
}

.inputPrice >>> input {
  text-align: center;
  -moz-appearance:textfield;
}
</style>
