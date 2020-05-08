<template>
    <v-layout row justify-center>
        <v-dialog v-model="dialog" lazy persistent max-width="600px">
            <v-card>
                <v-card-title>
                <span class="headline">RECUPERAR </span>
                </v-card-title>
                <v-card-text>
                    <v-form>
                        <v-container>
                            <v-layout row wrap v-if="!multiple">
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
                                            v-validate="'required'"
                                            data-vv-name="fecha"
                                            append-icon="event"
                                            readonly
                                            data-vv-as="Fecha"
                                            :error-messages="errors.collect('fecha')"
                                            ></v-text-field>
                                        <v-date-picker
                                            v-model="deposito_recu.fecha"
                                            :min="fecha_min"
                                            :max="hoy"
                                            no-title
                                            locale="es"
                                            first-day-of-week=1
                                            @input="menu1 = false"
                                            :readonly="!isSupervisor"
                                        ></v-date-picker>
                                    </v-menu>
                                </v-flex>
                                <v-flex sm6 d-flex>
                                    <v-select
                                        v-model="deposito_recu.concepto_id"
                                        :items="conceptos_rec"
                                        label="Concepto"
                                        v-validate="'required'"
                                        :error-messages="errors.collect('concepto_id')"
                                        data-vv-name="concepto_id"
                                        data-vv-as="concepto"

                                    ></v-select>
                                </v-flex>
                               <v-flex sm3>
                                   <v-text-field
                                        v-model="deposito_recu.importe"
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
                            <v-layout row wrap v-else>
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
                                            v-validate="'required'"
                                            data-vv-name="fecha"
                                            append-icon="event"
                                            readonly
                                            data-vv-as="Fecha"
                                            :error-messages="errors.collect('fecha')"
                                            ></v-text-field>
                                        <v-date-picker
                                            v-model="deposito_recu.fecha"
                                            :min="fecha_min"
                                            :max="hoy"
                                            no-title
                                            locale="es"
                                            first-day-of-week=1
                                            @input="menu1 = false"
                                            :readonly="!isSupervisor"
                                        ></v-date-picker>
                                    </v-menu>
                                </v-flex>
                                <v-flex sm6 d-flex>
                                    <v-select
                                        v-model="deposito_recu.concepto_id"
                                        :items="conceptos_rec1"
                                        label="Concepto"
                                        v-validate="'required'"
                                        :error-messages="errors.collect('concepto_id')"
                                        data-vv-name="concepto_id"
                                        data-vv-as="concepto"

                                    ></v-select>
                                </v-flex>
                               <v-flex sm3>
                                   <v-text-field
                                        v-model="importe1"
                                        v-validate="'required|decimal:2|min:1'"
                                        :error-messages="errors.collect('importe1')"
                                        append-icon="swap_vert"
                                        @click:append="toggleImp()"
                                        label="Importe"
                                        data-vv-name="importe1"
                                        data-vv-as="importe"
                                        class="inputPrice"
                                        type="number"

                                        v-on:keyup.enter="submit"
                                    >
                                   </v-text-field>
                                </v-flex>
                            </v-layout>
                            <v-layout row wrap v-if="multiple">
                                <v-flex sm3>
                                </v-flex>
                                <v-flex sm6 d-flex>
                                    <v-select
                                        v-model="concepto_id2"
                                        :items="conceptos_rec2"
                                        label="Concepto"
                                        v-validate="'required'"
                                        :error-messages="errors.collect('concepto_id2')"
                                        data-vv-name="concepto_id2"
                                        data-vv-as="concepto"

                                    ></v-select>
                                </v-flex>
                               <v-flex sm3>
                                   <v-text-field
                                        v-model="importe2"
                                        v-validate="'required|decimal:2|min:0'"
                                        :error-messages="errors.collect('importe2')"
                                        append-icon="clear"
                                        @click:append="clear()"
                                        label="Importe"
                                        data-vv-name="importe2"
                                        data-vv-as="importe"
                                        class="inputPrice"
                                        type="number"
                                        readonly
                                        v-on:keyup.enter="submit"
                                    >
                                   </v-text-field>
                                </v-flex>
                            </v-layout>
                            <v-layout row wrap>
                                <v-flex sm11>
                                    <v-text-field
                                        v-model="deposito_recu.notas"
                                        label="Notas"
                                        v-on:keyup.enter="submit"
                                    >
                                    </v-text-field>
                                </v-flex>
                            </v-layout>
                            <v-layout row wrap>
                                <v-flex sm2>
                                    <v-switch
                                        v-model="multiple"
                                        label="Múltiple"
                                    ></v-switch>
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
        deposito_recu: Object,
        lineas: Array
    },
    data () {
      return {
        menu1: false,

        concepto_id2: 12,
        importe1: 0,
        importe2: 0,
        multiple: false,

        disabled: false,

        conceptos_rec: [],
        conceptos_rec1: [],
        conceptos_rec2: [],
        url: "/compras/recuperar",
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

        if (!this.validarBloqueo()){
            this.$toast.error('No se puede recupear el lote');
            this.$router.go(-1)
        }

        // this.importe1 = parseFloat(this.deposito_recu.importe);
        // this.importe2 = parseFloat(0);

        this.importe1 = this.deposito_recu.importe;

        axios.get(this.url)
            .then(res => {
                this.conceptos_rec = res.data.conceptos;
                this.conceptos_rec1 = res.data.conceptos1;
                this.conceptos_rec2 = res.data.conceptos2;

                this.deposito_recu.concepto_id = this.conceptos_rec[0].value;

            })
            .catch(err => {
                this.$toast.error(err.response.data.message);
                this.$router.push({ name:'compra.index'})
            })

        //this.$nextTick(() => this.$refs.dni.focus())
    },
    computed: {
        ...mapGetters([
            'isAdmin',
            'isSupervisor',
            'hasLimEfe',
            'parametros'
        ]),
        computedFecha() {
            moment.locale('es');
            return this.deposito_recu.fecha ? moment(this.deposito_recu.fecha).format('L') : '';
        },
    },
    watch: {

        importe1: function () {

            if (this.importe1 != this.deposito_recu.importe)
                this.importe2 = this.deposito_recu.importe - this.importe1;
        },

    },
    methods:{
        toggleImp(){
            var n = this.importe2;

            this.importe2 = this.importe1;
            this.importe1 = n;
        },
        clear(){
            this.importe2 = 0;
        },
        validarBloqueo(){
            if (this.isSupervisor) return true;

            if (new Date() < new Date(this.compra.fecha_bloqueo))
                return false; // está bloqueado por fecha
            else{
                return true;
                //return (this.compra.retraso > 0) ? false : true;
            }


        },
        closeDialog (){
            this.$emit('update:dialog', false)
        },
        calImp(){

            this.deposito_recu.importe = Math.round(this.compra.importe_renovacion * (this.deposito_recu.dias / this.compra.dias_custodia),0);

        },
        submit(){

            if (this.multiple){

                this.deposito_recu.importe = parseFloat(this.importe1) + this.importe2;
                this.deposito_recu.importe1 = parseFloat(this.importe1);
                this.deposito_recu.importe2 = this.importe2;
                this.deposito_recu.concepto_id2 = this.concepto_id2;

            }else{
                this.deposito_recu.importe1 = parseFloat(this.deposito_recu.importe);
                this.deposito_recu.importe2 = 0;
            }

            if (this.loading === false){
                this.loading = true;
                this.$validator.validateAll().then((result) => {
                    if (result){
                        axios.post(this.url, this.deposito_recu)
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
