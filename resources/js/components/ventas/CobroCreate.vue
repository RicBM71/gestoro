<template>
    <v-layout row justify-center>
        <v-dialog v-model="dialog_cobro" persistent max-width="600px">
            <v-card>
                <v-card-title>
                <span class="headline">Cobros</span>
                </v-card-title>
                <v-card-text>
                    <v-form>
                        <v-container>
                            <p v-if="excede_limite" class="font-weight-medium red--text dark-4">Se ha superado el límite legal de efectivo {{ computedValorCompras }}</p>
                            <v-layout row wrap>
                                 <v-flex sm3>
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
                                            append-icon="event"
                                            readonly
                                            data-vv-as="Fecha"
                                            ></v-text-field>
                                        <v-date-picker
                                            v-model="cobro.fecha"
                                            no-title
                                            :min="fecha_min"
                                            :max="hoy"
                                            locale="es"
                                            first-day-of-week=1
                                            @input="menu1 = false"
                                            :readonly="!isSupervisor"
                                        ></v-date-picker>
                                    </v-menu>
                                </v-flex>
                                <v-flex sm5 d-flex>
                                    <v-select
                                        v-model="cobro.fpago_id"
                                        :items="fpagos"
                                        :readonly="!computedLimImporte"
                                        label="Concepto"
                                    ></v-select>
                                </v-flex>
                               <v-flex sm3>
                                   <v-text-field
                                        v-model="cobro.importe"
                                        v-validate="'required|decimal:2'"
                                        :error-messages="errors.collect('importe')"
                                        label="Importe"
                                        data-vv-name="importe"
                                        data-vv-as="importe"
                                        required
                                        class="inputPrice"
                                        type="number"
                                        v-on:keyup.enter="submit"
                                    >
                                   </v-text-field>
                                </v-flex>
                            </v-layout>
                            <v-layout row wrap>
                                <v-flex sm11>
                                   <v-text-field
                                        v-model="cobro.notas"
                                        v-validate="'max:50'"
                                        :error-messages="errors.collect('notas')"
                                        label="Notas"
                                        data-vv-name="notas"
                                        data-vv-as="notas"
                                        required
                                        v-on:keyup.enter="submit"
                                    >
                                   </v-text-field>
                                </v-flex>
                            </v-layout>
                        </v-container>
                    </v-form>
                </v-card-text>
                <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue darken-1" flat round @click="closeDialog">Cancelar</v-btn>
                <v-btn color="blue darken-1" flat round :disabled="loading || cobro.importe == 0" :loading="loading" @click="submit">Guardar</v-btn>
                </v-card-actions>
            </v-card>
    </v-dialog>
    </v-layout>
</template>
<script>
import {mapGetters} from 'vuex';
import moment from 'moment'
export default {
    $_veeValidate: {
        validator: 'new'
    },
    props: ['albaran','dialog_cobro','resto'],
    // props:{
    //     albaran: Object,
    //     dialog_cobro: Boolean,
    //     resto: String
    // },
    data () {
      return {
        cobro: {
            fecha: new Date().toISOString().substr(0, 10),
            albaran_id:0,
            importe:0,
            fpago_id:1,
            cliente_id: 0,
            notas: ""
        },

        menu1: false,
        disabled: false,
        fpagos: [],
        dialog: false,
        ruta: "cobro",
        url: "/ventas/cobros",
        loading: false,
        result: false,
        valor_total_albaranes:0,
        excede_limite : false,
        bancos: [],
        fecha_min: "",
        hoy: new Date().toISOString().substr(0, 10),
      }
    },
    beforeMount(){

        axios.get(this.url+'/create')
            .then(res => {

                this.fpagos = res.data.fpagos;

                this.cobro.cliente_id = this.albaran.cliente_id;


            })
            .catch(err => {
                this.$toast.error(err.response.data.message);
                this.$router.push({ name: 'albaran.index'})
            })
    },
    mounted(){

    },
    watch: {
        albaran: function () {
            this.fecha_min = this.albaran.fecha_albaran;
        },
        resto: function () {

            this.cobro.importe = this.resto;

            if (!this.albaran.iva_no_residente){
                if (this.cobro.importe >= parseFloat(this.parametros.lim_efe))
                    this.cobro.fpago_id++;
            }else{
                if (this.cobro.importe >= parseFloat(this.parametros.lim_efe_nores))
                    this.cobro.fpago_id++;
                else
                    this.cobro.fpago_id = this.fpagos[0].value;
            }
        },
    },
    computed: {
        ...mapGetters([
            'isSupervisor',
            'hasLimEfe',
            'parametros'
        ]),
        computedLimImporte(){
            return true;
            if (this.compra.importe >= 2500){
                this.cobro.fpago_id = 2;
            }
        },
        computedFecha() {
            moment.locale('es');
            return this.cobro.fecha ? moment(this.cobro.fecha).format('L') : '';
        },

    },
    methods:{
        getMoneyFormat(value){
            return new Intl.NumberFormat("de-DE",{style: "currency", currency: "EUR"}).format(parseFloat(value))
        },
        closeDialog (){
            this.$emit('update:dialog_cobro', false)
        },
        submit(){

            if (this.loading === false){
                this.loading = true;
                this.cobro.albaran_id = this.albaran.id;

                this.cobro.resto = this.resto;

                this.$validator.validateAll().then((result) => {
                    if (result){
                        axios.post(this.url, this.cobro)
                            .then(response => {

                                this.$emit('update:dialog_cobro', false)
                                this.loading = false;


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
