<template>
	<div>
        <loading :show_loading="show_loading"></loading>
        <v-card>
            <v-card-title color="indigo">
                <h2 color="indigo">{{titulo}}</h2>
                <v-spacer></v-spacer>
            </v-card-title>
        </v-card>
        <v-card>
            <v-form>
                 <v-container>
                     <v-layout row wrap>
                          <v-flex sm2></v-flex>
                          <v-flex sm2>
                               <v-switch
                                    label="Remesadas"
                                    @change="reload()"
                                    v-model="remesada"
                                    color="primary">
                                ></v-switch>
                          </v-flex>
                          <v-flex sm2>
                            <v-menu
                                v-model="menu_h"
                                :close-on-content-click="true"
                                :nudge-right="40"
                                lazy
                                transition="scale-transition"
                                offset-y
                                full-width
                                min-width="290px"
                            >
                                <v-text-field
                                    slot="activator"
                                    :value="computedFechaH"
                                    label="Fecha"
                                    append-icon="event"
                                    v-validate="'date_format:dd/MM/yyyy'"
                                    data-vv-name="fecha_h"
                                    :error-messages="errors.collect('fecha_h')"
                                    data-vv-as="fecha"
                                    readonly
                                    ></v-text-field>
                                <v-date-picker
                                    v-model="fecha_h"
                                    no-title
                                    locale="es"
                                    first-day-of-week=1
                                    @input="menu_liq = false"
                                    ></v-date-picker>
                            </v-menu>
                        </v-flex>
                        <v-flex sm2>
                            <v-select
                                v-model="cuenta_id"
                                v-validate="'required'"
                                data-vv-name="cuenta_id"
                                data-vv-as="cuenta"
                                :error-messages="errors.collect('cuenta_id')"
                                :items="cuentas"
                                label="Cuentas"
                                ></v-select>
                        </v-flex>
                        <v-flex xs1>
                            <v-text-field
                                v-model="operaciones"
                                label="Operaciones"
                                readonly
                            ></v-text-field>
                        </v-flex>
                        <v-flex xs1>
                            <v-text-field
                                v-model="importe"
                                label="Importe"
                                readonly
                            ></v-text-field>
                        </v-flex>
                        <v-flex sm1>
                            <div class="text-xs-center">
                                <v-btn @click="submit" round small flat :loading="loading"  block  color="primary">
                                    Generar Orden
                                </v-btn>
                            </div>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap v-if="depositos.length > 0">
                        <v-flex xs12>
                            <v-data-table
                                :headers="headers"
                                :items="depositos"
                                :pagination.sync="pagination"
                                rows-per-page-text="Registros por página"
                            >
                                <template slot="items" slot-scope="props">
                                    <td>{{ formatDate(props.item.fecha) }}</td>
                                    <td>{{ props.item.compra.alb_ser }}</td>
                                    <td>{{ props.item.cliente.razon }}</td>
                                    <td>{{datosIBAN(props.item.iban,props.item.bic) }}</td>
                                    <td>{{ props.item.importe }}</td>
                                    <td>Pendiente</td>
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

                url: "/utilidades/sepa",
                pagination:{
                    descending: false,
                    page: 1,
                    rowsPerPage: 10,
                    sortBy: "nombre",
                },
                remesada: false,
                search:"",
                headers: [
                    {
                        text: 'Fecha',
                        align: 'left',
                        value: 'fecha'
                    },
                    {
                        text: 'Compra',
                        align: 'left',
                        value: 'eje_alb'
                    },
                    {
                        text: 'Cliente',
                        align: 'left',
                        value: 'nomape'
                    },
                    {
                        text: 'IBAN',
                        align: 'left',
                        value: 'iban'
                    },
                    {
                        text: 'Importe',
                        align: 'left',
                        value: 'importe'
                    },
                    {
                        text: 'Estado',
                        align: 'left',
                        value: 'id'
                    }
                ],

                fecha_h: new Date().toISOString().substr(0, 10),
                menu_h: false,
        		cuentas: [],
                depositos:[],

                cuenta_id: '',

                loading: false,
                operaciones: 0,
                importe: 0,

                show_loading: true,
                titulo:'Generar Orden SEPA'
      		}
        },
        mounted(){

            axios.get(this.url)
                .then(res => {

                    this.depositos = res.data.depositos;
                    this.cuentas = res.data.cuentas;

                    const idx = this.cuentas.map(x => x.defecto).indexOf(1);

                    this.cuenta_id= res.data.cuentas[idx].value;

                })
                .catch(err => {
                    this.$toast.error(err.response.data.message);
                    this.$router.push({ name:'dash'})
                })
                .finally(()=> {
                    this.show_loading = false;
                });

        },
        computed: {
        ...mapGetters([
               'sepa',
               'hasSepa'
           ]),
           computedFechaH() {
                moment.locale('es');
                return this.fecha_h ? moment(this.fecha_h).format('L') : '';
            },
           computedLabel(){
               return this.reasignar == false ? 'Sin Asignar' : ' Reasignar';
           }
        },
    	methods:{
            datosIBAN(iban,bic){

                if (iban == null) return '';

                var iban_f= iban.substring(0,4)+" "+ iban.substring(4,8) + " " + iban.substring(8,12) + " "+ iban.substring(12,16) +" "+ iban.substring(16,20)+" "+ iban.substring(20,24);

                return "IBAN: "+iban_f+" - BIC: "+bic;
            },
            formatDate(f){
                if (f == null) return null;
                moment.locale('es');
                return moment(f).format('DD/MM/YYYY');
            },
            reload(){

                if (this.loading === false){

                    this.loading = true;
                    this.show_loading = true;

                    this.$validator.validateAll().then((result) => {
                        if (result){
                            axios.post(this.url+'/reload', {
                                cuenta_id: this.cuenta_id,
                                fecha: this.fecha_h,
                                remesada: this.remesada
                            })
                                .then(response => {

                                    this.depositos = response.data.depositos;

                                    this.show_loading = false;

                                })
                                .catch(err => {

                                    if (err.request.status == 422){ // fallo de validated.
                                        const msg_valid = err.response.data.errors;
                                        for (const prop in msg_valid) {
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
                                    this.loading = this.show_loading = false;
                                });
                            }
                        else{
                            this.loading = this.show_loading = false;
                        }
                    });
                }


            },
            submit() {
                if (this.loading === false){

                    this.loading = true;
                    this.show_loading = true;

                    this.$validator.validateAll().then((result) => {
                        if (result){
                            axios.post(this.url+'/transfer', {
                                cuenta_id: this.cuenta_id,
                                fecha: this.fecha_h,
                            })
                                .then(response => {

                                    this.depositos = [];

                                    let blob = new Blob([response.data.xml], { type: 'application/xml' })
                                    let link = document.createElement('a')
                                    link.href = window.URL.createObjectURL(blob)
                                    link.download = 'TRANSF'+new Date().getFullYear()+(new Date().getMonth()+1)+(new Date().getDate())+'.XML';

                                    document.body.appendChild(link);
                                    link.click()
                                    document.body.removeChild(link);


                                    this.importe = response.data.importe;
                                    this.operaciones = response.data.transferencias;

                                    this.$toast.success("Se ha generado correctamente la remesa");

                                    this.show_loading = false;



                                })
                                .catch(err => {

                                    if (err.request.status == 422){ // fallo de validated.
                                        const msg_valid = err.response.data.errors;
                                        for (const prop in msg_valid) {
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
                                    this.loading = this.show_loading = false;
                                });
                            }
                        else{
                            this.loading = this.show_loading = false;
                        }
                    });
                }

            },

        }
  }
</script>
