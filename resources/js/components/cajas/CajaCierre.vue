<template>
	<div v-show="!show_loading">
        <loading :show_loading="show_loading"></loading>
        <v-card>
            <v-card-title color="indigo">
                <h2 color="indigo">{{titulo}}</h2>
                <v-spacer></v-spacer>
                <menu-ope :id="caja.id"></menu-ope>
                <v-tooltip bottom>
                    <template v-slot:activator="{ on }">
                        <v-btn
                            v-on="on"
                            color="white"
                            icon
                            @click="goBack()"
                        >
                            <v-icon color="primary">arrow_back</v-icon>
                        </v-btn>
                    </template>
                        <span>Volver</span>
                </v-tooltip>
            </v-card-title>
        </v-card>
        <v-card>
            <v-form>
                 <v-container>
                     <v-layout row wrap>
                        <v-flex sm1></v-flex>
                        <v-flex sm2>
                            <v-menu
                                v-model="menu1"
                                :close-on-content-click="false"
                                :nudge-right="40"
                                lazy
                                transition="scale-transition"
                                offset-y
                                full-width
                                min-width="290px"
                                :readonly="!isSupervisor"
                            >
                                <v-text-field
                                    slot="activator"
                                    :value="computedFecha"
                                    label="Fecha"
                                    v-validate="'required'"
                                    data-vv-name="fecha"
                                    append-icon="event"
                                    readonly
                                    data-vv-as="Fecha"
                                    :error-messages="errors.collect('fecha')"
                                    ></v-text-field>
                                <v-date-picker
                                    v-model="caja.fecha"
                                    no-title
                                    locale="es"
                                    first-day-of-week=1
                                    @input="menu1 = false"
                                    :readonly="!isSupervisor"
                                ></v-date-picker>
                            </v-menu>
                        </v-flex>
                        <v-flex sm2>
                            <v-text-field
                                :value="saldo |currency('€', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false })"
                                readonly
                                label="Saldo"
                                class="inputPrice"
                            >
                            </v-text-field>
                        </v-flex>

                        <v-flex sm2>
                            <v-text-field
                                v-model="recuento_manual"
                                v-validate="'decimal'"
                                :error-messages="errors.collect('recuento_manual')"
                                data-vv-name="recuento_manual"
                                data-vv-as="recuento manual"
                                label="Nuevo Saldo"
                                class="inputPrice"
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm2>
                            <v-text-field
                                :value="recuento |currency('€', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false })"
                                readonly
                                label="Recuento"
                                class="inputPrice"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm2>
                            <v-text-field
                                :value="regulariza |currency('€', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false })"
                                readonly
                                label="Regularización"
                                class="inputPrice"
                            >
                            </v-text-field>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex sm8></v-flex>
                        <v-flex sm2>
                            <div class="text-xs-center">
                                        <v-btn @click="submit"  round  :loading="loading" block  color="primary">
                                Regularizar
                                </v-btn>
                            </div>
                        </v-flex>
                     </v-layout>
                    <v-layout row wrap>
                        <v-flex sm1>Billetes</v-flex>
                        <v-flex sm1>
                            <v-text-field
                                v-model="billetes[0]"
                                v-validate="'numeric'"
                                :error-messages="errors.collect('b5')"
                                label="5€"
                                data-vv-name="b5"
                                data-vv-as="número"
                                class="inputPrice"
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm1>
                            <v-text-field
                                :value="importe[0]"
                                disabled
                                label="Valor"
                                class="inputPrice"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm1>
                            <v-text-field
                                v-model="billetes[1]"
                                v-validate="'numeric'"
                                :error-messages="errors.collect('b10')"
                                label="10€"
                                data-vv-name="b10"
                                data-vv-as="número"
                                class="inputPrice"
                                type="number"
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm1>
                            <v-text-field
                                :value="importe[1]"
                                label="Valor"
                                disabled
                                class="inputPrice"
                            >
                            </v-text-field>
                        </v-flex>
                         <v-flex sm1>
                            <v-text-field
                                v-model="billetes[2]"
                                v-validate="'numeric'"
                                :error-messages="errors.collect('b20')"
                                label="20€"
                                data-vv-name="b20"
                                data-vv-as="número"
                                class="inputPrice"
                                type="number"
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm1>
                            <v-text-field
                                :value="importe[2]"
                                label="Valor"
                                disabled
                                class="inputPrice"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm1>
                            <v-text-field
                                v-model="billetes[3]"
                                v-validate="'numeric'"
                                :error-messages="errors.collect('b50')"
                                label="50€"
                                data-vv-name="b05"
                                data-vv-as="número"
                                class="inputPrice"
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm1>
                            <v-text-field
                                :value="importe[3]"
                                disabled
                                label="Valor"
                                class="inputPrice"
                            >
                            </v-text-field>
                        </v-flex>

                    </v-layout>
                     <v-layout row wrap>
                         <v-flex sm1></v-flex>
                        <v-flex sm1>
                            <v-text-field
                                v-model="billetes[4]"
                                v-validate="'numeric'"
                                :error-messages="errors.collect('b100')"
                                label="100€"
                                data-vv-name="b100"
                                data-vv-as="número"
                                class="inputPrice"
                                type="number"
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm1>
                            <v-text-field
                                :value="importe[4]"
                                label="Valor"
                                disabled
                                class="inputPrice"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm1>
                            <v-text-field
                                v-model="billetes[5]"
                                v-validate="'numeric'"
                                :error-messages="errors.collect('b200')"
                                label="200€"
                                data-vv-name="b200"
                                data-vv-as="número"
                                class="inputPrice"
                                type="number"
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm1>
                            <v-text-field
                                :value="importe[5]"
                                label="Valor"
                                disabled
                                class="inputPrice"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm1>
                            <v-text-field
                                v-model="billetes[6]"
                                v-validate="'numeric'"
                                :error-messages="errors.collect('b500')"
                                label="500€"
                                data-vv-name="b500"
                                data-vv-as="número"
                                class="inputPrice"
                                type="number"
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm1>
                            <v-text-field
                                :value="importe[6]"
                                label="Valor"
                                disabled
                                class="inputPrice"
                            >
                            </v-text-field>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex sm1>Monedas</v-flex>
                        <v-flex sm1>
                            <v-text-field
                                v-model="monedas[0]"
                                v-validate="'numeric'"
                                :error-messages="errors.collect('m2')"
                                label="2€"
                                data-vv-name="m2"
                                data-vv-as="número"
                                class="inputPrice"
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm1>
                            <v-text-field
                                :value="importe_m[0]"
                                disabled
                                label="Valor"
                                class="inputPrice"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm1>
                            <v-text-field
                                v-model="monedas[1]"
                                v-validate="'numeric'"
                                :error-messages="errors.collect('m1')"
                                label="1€"
                                data-vv-name="m1"
                                data-vv-as="número"
                                class="inputPrice"
                                type="number"
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm1>
                            <v-text-field
                                :value="importe_m[1]"
                                label="Valor"
                                disabled
                                class="inputPrice"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm1>
                            <v-text-field
                                v-model="monedas[2]"
                                v-validate="'numeric'"
                                :error-messages="errors.collect('m50c')"
                                label="0,50€"
                                data-vv-name="m50c"
                                data-vv-as="número"
                                class="inputPrice"
                                type="number"
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm1>
                            <v-text-field
                                :value="importe_m[2]"
                                label="Valor"
                                disabled
                                class="inputPrice"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm1>
                            <v-text-field
                                v-model="monedas[3]"
                                v-validate="'numeric'"
                                :error-messages="errors.collect('m20c')"
                                label="0,20€"
                                data-vv-name="m20c"
                                data-vv-as="número"
                                class="inputPrice"
                                type="number"
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm1>
                            <v-text-field
                                :value="importe_m[3]"
                                label="Valor"
                                disabled
                                class="inputPrice"
                            >
                            </v-text-field>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex sm1></v-flex>
                        <v-flex sm1>
                            <v-text-field
                                v-model="monedas[4]"
                                v-validate="'numeric'"
                                :error-messages="errors.collect('m10c')"
                                label="0,10€"
                                data-vv-name="m10c"
                                data-vv-as="número"
                                class="inputPrice"
                                type="number"
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm1>
                            <v-text-field
                                :value="importe_m[4]"
                                label="Valor"
                                disabled
                                class="inputPrice"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm1>
                            <v-text-field
                                v-model="monedas[5]"
                                v-validate="'numeric'"
                                :error-messages="errors.collect('m5c')"
                                label="0,05€"
                                data-vv-name="m5c"
                                data-vv-as="número"
                                class="inputPrice"
                                type="number"
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm1>
                            <v-text-field
                                :value="importe_m[5]"
                                label="Valor"
                                disabled
                                class="inputPrice"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm1>
                            <v-text-field
                                v-model="monedas[6]"
                                v-validate="'numeric'"
                                :error-messages="errors.collect('m2c')"
                                label="0,02€"
                                data-vv-name="m2c"
                                data-vv-as="número"
                                class="inputPrice"
                                type="number"
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm1>
                            <v-text-field
                                :value="importe_m[6]"
                                label="Valor"
                                disabled
                                class="inputPrice"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm1>
                            <v-text-field
                                v-model="monedas[7]"
                                v-validate="'numeric'"
                                :error-messages="errors.collect('m1c')"
                                label="0,01€"
                                data-vv-name="m1c"
                                data-vv-as="número"
                                class="inputPrice"
                                type="number"
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm1>
                            <v-text-field
                                :value="importe_m[7]"
                                label="Valor"
                                disabled
                                class="inputPrice"
                            >
                            </v-text-field>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-form>
        </v-card>
	</div>
</template>
<script>
import moment from 'moment'
import Loading from '@/components/shared/Loading'
import MenuOpe from './MenuOpe'
import {mapGetters} from 'vuex';
	export default {
		$_veeValidate: {
      		validator: 'new'
        },
        components: {
            'menu-ope': MenuOpe,
            'loading': Loading
		},
    	data () {
      		return {
                titulo:"Cierre de caja",
                caja: {
                    fecha: new Date().toISOString().substr(0, 10),
                    importe: null,
                    manual: 'C',
                    deposito_id:null,
                    cobro_id: null
                },
                url: "/mto/cajas",
                ruta: "caja",
                dhs:[
                    {value: 'D', text:"Debe"},
                    {value: 'H', text:"Haber"},
                ],

                recuento: 0,
                recuento_manual: 0,
                saldo: 0,
                regulariza: 0,

                val_billete:[5,10,20,50,100,200,500,0],
                monedas:[0,0,0,0,0,0,0,0],
                billetes:[0,0,0,0,0,0,0,0],
                val_monedas:[2,1,.5,.2,.1,.05,0.02,0.01],

                importe:[0,0,0,0,0,0,0,0],
                importe_m:[0,0,0,0,0,0,0,0],

                loading: false,
                menu1: false,

                show: false,
                show_loading: true,
      		}
        },
        mounted(){

            this.show_loading = false;
            axios.post(this.url+'/saldo',{fecha: this.caja.fecha})
                .then(res => {
                    this.saldo = res.data.saldo;
                    this.show = true;
                    this.show_loading = false;
                })
                .catch(err => {
                    this.$toast.error(err.response.data.message);
                    this.$router.push({ name: this.ruta+'.index'})
                })
        },
        watch: {
            billetes: function () {
                this.recuento = 0;
                for (var i=0;i<=6;i++){
                    this.importe[i]=this.val_billete[i] *  this.billetes[i];
                    this.recuento += (this.importe[i] + this.importe_m[i]);
                }

                this.regulariza = this.recuento - this.saldo;

            },
            monedas: function () {
                this.recuento = 0;
                for (var i=0;i<=7;i++){
                    this.importe_m[i]=this.val_monedas[i] *  this.monedas[i];
                    this.recuento += (this.importe[i] + this.importe_m[i]);
                }

                this.regulariza = this.recuento - this.saldo;

            },
            recuento_manual: function () {
                //this.recuento = this.recuento_manual;
                this.regulariza = this.recuento_manual - this.saldo;

            }
        },
        computed: {
         ...mapGetters([
            'isSupervisor',
            ]),
            computedFecha() {
                moment.locale('es');
                return this.caja.fecha ? moment(this.caja.fecha).format('L') : '';
            },
        },
    	methods:{
            goBack(){
                this.$router.go(-1)
            },
            getDecimalFormat(value){
                return new Intl.NumberFormat("de-DE",{style: "decimal",minimumFractionDigits:2}).format(parseFloat(value))
            },
            submit() {

                if (this.loading === false){
                    this.loading = true;

                    if (this.recuento == 0 && this.recuento_manual != 0)
                        this.recuento = this.recuento_manual;

                    this.caja.dh = this.regulariza >= 0 ? "H" : "D";
                    this.caja.importe = Math.abs(this.regulariza).toFixed(2);
                    this.caja.nombre = "CIERRE DE CAJA:"+this.getDecimalFormat(this.saldo.toFixed(2))+" RECUENTO: "+this.getDecimalFormat(this.recuento);


                    this.$validator.validateAll().then((result) => {
                        if (result){
                            axios.post(this.url, this.caja)
                                .then(response => {

                                    this.$toast.success(response.data.message);
                                    this.loading = false;
                                    this.$router.push({ name: this.ruta+'.index'})
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
                                    this.loading = false;
                                });
                            }
                        else{
                            this.loading = false;
                        }
                    });
                }


            },


    }
  }
</script>

<style scoped>

.inputPrice >>> input {
  text-align: center;
  -moz-appearance:textfield;
}

input[type=number]::-webkit-inner-spin-button,
input[type=number]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    margin: 0;
}
</style>
