<template>
    <v-layout row justify-center>
        <v-dialog v-model="dialog" lazy persistent max-width="600px">
            <v-card>
                <v-card-title>
                <span class="headline">A CUENTA </span>
                </v-card-title>
                <v-card-text>
                    <v-form>
                        <v-container>
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
                                        :readonly="!hasEdtFec"
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
                                            v-model="deposito_acuenta.fecha"
                                            no-title
                                            :min="fecha_min"
                                            :max="hoy"
                                            locale="es"
                                            first-day-of-week=1
                                            @input="menu1 = false"
                                            :readonly="!hasEdtFec"
                                        ></v-date-picker>
                                    </v-menu>
                                </v-flex>
                                <v-flex sm5 d-flex>
                                    <v-select
                                        v-model="deposito_acuenta.concepto_id"
                                        :items="conceptos_com"
                                        label="Concepto"
                                        v-validate="'required'"
                                        :error-messages="errors.collect('concepto_id')"
                                        data-vv-name="concepto_id"
                                        data-vv-as="concepto"

                                    ></v-select>
                                </v-flex>
                               <v-flex sm3>
                                   <v-text-field
                                        v-model="deposito_acuenta.importe"
                                        v-validate="'required|decimal:2|min:1'"
                                        :error-messages="errors.collect('importe')"
                                        label="Importe"
                                        data-vv-name="importe"
                                        data-vv-as="importe"
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
                                        v-model="deposito_acuenta.notas"
                                        label="Notas"
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
                <v-btn color="blue darken-1" flat round :disabled="loading" :loading="loading" @click="submit">Guardar</v-btn>
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
    props:{
        compra: Object,
        dialog: Boolean,
        deposito_acuenta: Object,
        lineas: Array
    },
    data () {
      return {

        menu1: false,

        disabled: false,
        conceptos_com: [],
        ruta: "amplia",
        url: "/compras/acuenta",
        loading: false,
        result: false,
        excede_limite : false,
        fecha_min: "",
        hoy: new Date().toISOString().substr(0, 10),
      }
    },
    beforeMount(){
        this.fecha_min = this.lineas[0].fecha;
    },
    mounted(){
        axios.get(this.url)
            .then(res => {
                this.conceptos_com = res.data.conceptos;
                this.deposito_acuenta.concepto_id = this.conceptos_com[0].value;
            })
            .catch(err => {
                this.$toast.error(err.response.data.message);
                this.$router.push({ name:   'compra.index'})
            })

        //this.$nextTick(() => this.$refs.dni.focus())
    },
    computed: {
        ...mapGetters([
            'hasEdtFec',
            'hasLimEfe',
            'parametros'
        ]),
        computedFecha() {
            moment.locale('es');
            return this.deposito_acuenta.fecha ? moment(this.deposito_acuenta.fecha).format('L') : '';
        },
    },
    methods:{
        closeDialog (){
            this.$emit('update:dialog', false)
        },
        calImp(){

            this.deposito_acuenta.importe = Math.round(this.compra.importe_renovacion * (this.deposito_acuenta.dias / this.compra.dias_custodia),0);


        },
        submit(){
            if (this.loading === false){
                this.loading = true;
                this.$validator.validateAll().then((result) => {
                    if (result){
                        axios.post(this.url, this.deposito_acuenta)
                            .then(res => {

                                this.$emit('update:compra', res.data.compra)
                                this.$emit('update:lineas', res.data.lineas);
                                this.$emit('update:dialog', false)
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
