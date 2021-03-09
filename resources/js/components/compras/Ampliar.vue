<template>
    <v-layout row justify-center>
        <v-dialog v-model="dialog_amp" lazy persistent max-width="600px">
            <v-card>
                <v-card-title>
                <span class="headline">AMPLIAR</span>
                </v-card-title>
                <v-card-text>
                    <v-form>
                        <v-container>
                            <v-layout row wrap>
                                <v-flex sm4>
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
                                            v-validate="'required'"
                                            data-vv-name="fecha"
                                            :error-messages="errors.collect('fecha')"
                                            readonly
                                            data-vv-as="Fecha"
                                            ></v-text-field>
                                        <v-date-picker
                                            v-model="ampliacion.fecha"
                                            :min="fecha_min"
                                            :max="hoy"
                                            no-title
                                            locale="es"
                                            first-day-of-week=1
                                            @input="menu1 = false"
                                            :readonly="!hasEdtFec"
                                        ></v-date-picker>
                                    </v-menu>
                                </v-flex>
                                <v-flex sm6 d-flex>
                                    <v-select
                                        v-model="ampliacion.concepto_id"
                                        :items="conceptos"
                                        label="Concepto"
                                        v-validate="'required'"
                                        :error-messages="errors.collect('concepto_id')"
                                        data-vv-name="concepto_id"
                                        data-vv-as="concepto"
                                    ></v-select>
                                </v-flex>
                            </v-layout>
                            <v-layout row wrap>
                                <v-flex sm2>
                                    <v-text-field
                                        v-model="ampliacion.dias"
                                        v-validate="'numeric|min_value:1|required'"
                                        :error-messages="errors.collect('dias')"
                                        label="Días"
                                        data-vv-as="dias"
                                        data-vv-name="Días"
                                        class="inputPrice"
                                        @change="calImp()"
                                        v-on:keyup.enter="submit"
                                    >
                                    </v-text-field>
                                </v-flex>
                               <v-flex sm3>
                                   <v-text-field
                                        v-model="ampliacion.importe"
                                        v-validate="'required|min_value:0|decimal:2'"
                                        :error-messages="errors.collect('importe')"
                                        label="Importe"
                                        data-vv-name="importe"
                                        data-vv-as="importe"
                                        :readonly="!hasEdtFec"
                                        class="inputPrice"
                                        type="number"
                                        v-on:keyup.enter="submit"
                                    >
                                   </v-text-field>
                                </v-flex>
                                <v-flex sm7>
                                    <v-text-field
                                        v-model="ampliacion.notas"
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
        dialog_amp: Boolean,
        ampliacion: Object,
        lineas: Array
    },
    data () {
      return {
        // ampliacion: {
        //     compra_id:0,
        //     fecha: "",
        //     importe:0,
        //     concepto_id:0,
        //     iban:"",
        //     bic:""
        // },

        menu1: false,

        disabled: false,
        conceptos: [],
        ruta: "amplia",
        url: "/compras/ampliaciones",
        loading: false,
        result: false,
        excede_limite : false,
        fecha_min: "",
        hoy: new Date().toISOString().substr(0, 10),
      }
    },
    beforeMount(){
        this.fecha_min = this.lineas[0].fecha;
        axios.get(this.url)
            .then(res => {
                this.conceptos = res.data.conceptos;
                this.ampliacion.concepto_id = this.conceptos[0].value;
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
            return this.ampliacion.fecha ? moment(this.ampliacion.fecha).format('L') : '';
        },
    },
    methods:{
        closeDialog (){
            this.$emit('update:dialog_amp', false)
        },
        calImp(){

            this.ampliacion.importe = Math.round(this.compra.importe_renovacion * (this.ampliacion.dias / this.compra.dias_custodia),0);


        },
        submit(){
            if (this.loading === false){
                this.loading = true;
                this.$validator.validateAll().then((result) => {
                    if (result){
                        axios.post(this.url, this.ampliacion)
                            .then(res => {

                                this.$emit('update:compra', res.data.compra)
                                this.$emit('update:lineas', res.data.lineas);
                                this.$emit('update:dialog_amp', false)
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
