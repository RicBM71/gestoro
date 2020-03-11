<template>
    <div>
        <v-card>
            <loading :show_loading="show_loading"></loading>
            <v-card-title color="indigo">
                <h2 color="indigo">Inventario</h2>
                <v-spacer></v-spacer>
                <v-tooltip bottom>
                    <template v-slot:activator="{ on }">
                        <v-btn
                            v-show="items.length > 0"
                            v-on="on"
                            color="white"
                            icon
                            @click="show_filtro = !show_filtro"
                        >
                            <v-icon color="primary">filter_list</v-icon>
                        </v-btn>
                    </template>
                    <span>Selección</span>
                </v-tooltip>
                <v-tooltip bottom>
                    <template v-slot:activator="{ on }">
                        <v-btn
                            v-show="items.length > 0"
                            v-on="on"
                            color="white"
                            icon
                            @click="goExcel()"
                        >
                            <v-avatar size="32px">
                                <img class="img-fluid" src="/assets/excel.png">
                            </v-avatar>
                        </v-btn>
                    </template>
                    <span>Exportar a Excel</span>
                </v-tooltip>
            </v-card-title>
        </v-card>
        <v-card>
            <v-form>
                <v-container>
                    <v-layout row wrap v-show="show_filtro">
                        <v-spacer></v-spacer>
                         <v-flex sm2>
                            <v-select
                                v-model="grupo_id"
                                v-validate="'required'"
                                data-vv-name="grupo_id"
                                data-vv-as="grupo"
                                :error-messages="errors.collect('grupo_id')"
                                :items="grupos"
                                label="Grupo"
                                required
                                ></v-select>
                        </v-flex>
                        <v-flex sm3>
                            <v-select
                                v-model="clase_id"
                                v-validate="'numeric'"
                                data-vv-name="clase_id"
                                data-vv-as="clase"
                                :error-messages="errors.collect('clase_id')"
                                :items="clases"
                                label="Clase"
                                required
                                ></v-select>
                        </v-flex>
                        <v-flex sm4>
                            <v-select
                                v-model="cliente_id"
                                v-validate="'numeric'"
                                data-vv-name="cliente_id"
                                data-vv-as="cliente"
                                :error-messages="errors.collect('cliente_id')"
                                :items="asociados"
                                label="Proveedor"
                                ></v-select>
                        </v-flex>
                        <v-flex sm2>
                            <v-select
                                v-model="estado_id"
                                v-validate="'numeric'"
                                data-vv-name="essado"
                                data-vv-as="estado"
                                :error-messages="errors.collect('cliente_id')"
                                :items="estados"
                                label="Inventario"
                                ></v-select>
                        </v-flex>
                        <v-flex sm1>
                            <v-btn @click="submit" :loading="show_loading" flat round small block  color="info">
                                Enviar
                            </v-btn>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap v-show="show_filtro">
                        <v-flex sm3>
                            <v-select
                                v-model="tipoinv_id"
                                v-validate="'required'"
                                data-vv-name="tipoinv_id"
                                data-vv-as="tipo"
                                :error-messages="errors.collect('tipoinv_id')"
                                :items="tiposinv"
                                label="Tipo"
                                ></v-select>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap v-if="items.length>0">
                        <v-flex xs3>
                            <v-text-field
                                :value="computedValorInventario"
                                class="inputPrice"
                                label="Valor Inventario"
                                readonly
                            ></v-text-field>
                        </v-flex>
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
                    </v-layout>
                    <v-layout row wrap v-if="items.length>0">
                        <v-flex xs12>
                            <v-data-table

                                :headers="headers"
                                :items="items"
                                :search="search"
                                :pagination.sync="pagination"
                                rows-per-page-text="Registros por página"
                            >
                               <template slot="items" slot-scope="props">
                                    <td>{{ props.item.referencia }}</td>
                                    <td>{{ props.item.nombre }}</td>
                                    <td>{{ props.item.clase.nombre }} <span v-if="props.item.quilates > 0">{{props.item.quilates}}</span></td>
                                    <td class="text-xs-right">{{ props.item.peso_gr | currency('', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false })}}</td>
                                    <td class="text-xs-right">{{ props.item.precio_coste | currency('€', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false })}}</td>
                                    <td class="text-xs-right">{{ props.item.precio_venta | currency('€', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false })}}</td>
                                    <td>{{ props.item.estado.nombre }}</td>
                                    <td>{{ props.item.ref_pol }}</td>
                                </template>
                                <template slot="pageText" slot-scope="props">
                                    Registros {{ props.pageStart }} - {{ props.pageStop }} de {{ props.itemsLength }}
                                </template>
                            </v-data-table>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-form>
        </v-card>
    </div>
</template>
<script>
import moment from 'moment'
import {mapGetters} from 'vuex';
import Loading from '@/components/shared/Loading'
export default {
    $_veeValidate: {
        validator: 'new'
    },
    components: {
        'loading': Loading
    },
    data () {
      return {
            search:"",
            valor_inventario:0,
            headers: [
            {
                text: 'Referencia',
                align: 'left',
                value: 'referencia',
                width: '10%'
            },
            {
                text: 'Nombre',
                align: 'left',
                value: 'nombre',
            },
            {
                text: 'Clase',
                align: 'left',
                value: 'clase.nombre',
                width: '10%'
            },
            {
                text: 'Peso',
                align: 'right',
                value: 'peso_ger'
            },
            {
                text: 'P. Coste',
                align: 'right',
                value: 'precio_coste'
            },
            {
                text: 'P. Venta',
                align: 'right',
                value: 'precio_venta'
            },
            {
                text: 'Estado',
                align: 'left',
                value: 'estado.nombre',
                width: '10%'
            },
            {
                text: 'Ref. Pol',
                align: 'left',
                value: 'ref_pol',
                width: '10%'
            },],
            estados:[
                {value: 2, text: 'En Venta'},
                {value: 3, text: 'Reservas'},
                {value: null, text: 'Todo'},
            ],
            pagination:{
                rowsPerPage: 10,
            },
            clases: [],
            asociados: [],
            grupos:[],
            tipoinv_id: 'C',
            tiposinv:[
                {value: 'C', text: 'Contable - origen pieza'},
                {value: 'F', text: 'Ubicación de venta'},
            ],
            grupo_id: null,
            clase_id: null,
            cliente_id: null,
            estado_id: null,
            show_loading: false,
            ejercicio:new Date().toISOString().substr(0, 4),
            show_filtro: true,
            items: [],
            fecha_d: new Date().toISOString().substr(0, 7)+"-01",
            fecha_h: new Date().toISOString().substr(0, 10),
            menu_d: false,
            menu_h: false,
      }
    },
    mounted(){
        axios.get('/utilidades/helppro/filtro')
            .then(res => {
                this.grupos = res.data.grupos;
                this.clases = res.data.clases;
                this.asociados = res.data.asociados;
                this.grupo_id = this.grupos[0].value;

                this.clases.push({value:null,text:"---"});
                this.asociados.push({value:null,text:"---"});
            })
            .catch(err => {
                this.$toast.error('Error al montar <inventario>');
            })
    },
    computed: {
        ...mapGetters([
        ]),
        computedFechaD() {
            moment.locale('es');
            return this.fecha_d ? moment(this.fecha_d).format('L') : '';
        },
        computedFechaH() {
            moment.locale('es');
            return this.fecha_h ? moment(this.fecha_h).format('L') : '';
        },
        computedValorInventario(){
            return parseFloat(this.valor_inventario).toLocaleString('es-ES',{ style: 'decimal'});
        },
    },
    methods:{
        submit(){


            if (this.show_loading === false){
                this.$validator.validateAll().then((result) => {
                    if (result){

                        this.show_loading = true;
                        axios({
                            url: '/exportar/inventario',
                            method: 'POST',
                            data:{
                                cliente_id: this.cliente_id,
                                clase_id: this.clase_id,
                                grupo_id:  this.grupo_id,
                                estado_id: this.estado_id,
                                tipoinv_id: this.tipoinv_id
                            }
                            })
                        .then(res => {

                            this.items = res.data.inventario;
                            this.valor_inventario = res.data.valor_inventario;

                            if (this.items.length > 0)
                                this.show_filtro = false;
                            else
                                this.$toast.warning('No hay registros!');

                        })
                        .catch(err => {
                            if (err.request.status == 422){ // fallo de validated.
                                const msg_valid = err.response.data.errors;
                                for (const prop in msg_valid) {
                                    // this.$toast.error(`${msg_valid[prop]}`);

                                    this.errors.add({
                                        field: prop,
                                        msg: `${msg_valid[prop]}`
                                    })
                                }
                            }else{
                                this.$toast.error(err.response.data.message);
                            }
                        })
                        .finally(()=> {
                             this.show_loading = false;
                        });
                    }

                });

            }
        },
        goExcel(){

            this.show_loading = true;
            axios({
                url: "/exportar/inventario/excel",
                method: 'POST',
                responseType: 'blob', // important
                data:{ data: this.items }
                })
            .then(response => {

                let blob = new Blob([response.data])
                let link = document.createElement('a')
                link.href = window.URL.createObjectURL(blob)

                link.download = "Inventario."+new Date().getFullYear()+(new Date().getMonth()+1)+(new Date().getDate())+'.xlsx';

                document.body.appendChild(link);
                link.click()
                document.body.removeChild(link);

                this.$toast.success('Download Ok! '+link.download);

                this.show_loading = false;

            })
            .catch(err => {
                this.$toast.error(err.response.data.message);
                this.show_loading = false;
            });
        },


    }
}
</script>
<style scoped>
.inputPrice >>> input {
  text-align: center;
  -moz-appearance:textfield;
}
</style>
