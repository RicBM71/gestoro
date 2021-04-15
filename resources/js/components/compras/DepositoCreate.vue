<template>
    <v-layout row justify-center>
        <v-dialog v-model="dialog_depo" persistent max-width="650px">
            <v-card>
                <v-card-title>
                <span class="headline">Concepto</span>
                </v-card-title>
                <v-card-text>
                    <v-form>
                        <v-container>
                            <p v-if="excede_limite" class="font-weight-medium red--text dark-4">Se ha superado el límite legal de efectivo {{ computedValorCompras }}</p>
                            <v-layout row wrap>
                                <v-flex sm6 d-flex>
                                    <v-select
                                        v-model="deposito.concepto_id"
                                        :items="conceptos"
                                        :readonly="computedLimImporte"
                                        label="Concepto"
                                    ></v-select>
                                </v-flex>
                               <v-flex sm3>
                                   <v-text-field
                                        v-model="deposito.importe"
                                        v-validate="'required|decimal:2'"
                                        :error-messages="errors.collect('importe')"
                                        label="Importe"
                                        data-vv-name="importe"
                                        data-vv-as="importe"
                                        required
                                        readonly
                                        class="inputPrice"
                                        type="number"
                                        :value="compra.importe"
                                        v-on:keyup.enter="submit"
                                    >
                                   </v-text-field>
                               </v-flex>
                                   <v-flex sm3 v-if="multiple">
                                        <v-text-field
                                                v-model="deposito.importe2"
                                                v-validate="'required|decimal:2|min:0'"
                                                :error-messages="errors.collect('importe2')"
                                                append-icon="clear"
                                                @click:append="clear()"
                                                label="Efectivo"
                                                data-vv-name="importe2"
                                                data-vv-as="importe"
                                                class="inputPrice"
                                                type="number"
                                                v-on:keyup.enter="submit"
                                            >
                                        </v-text-field>
                                </v-flex>

                            </v-layout>
                            <v-layout row wrap v-if="deposito.concepto_id==2">
                                <v-flex sm8>
                                    <v-text-field
                                        v-model="deposito.iban"
                                        v-validate="'required|max:24'"
                                        :error-messages="errors.collect('iban')"
                                        label="IBAN"
                                        mask="AA## #### #### #### #### ####"
                                        counter=24
                                        data-vv-name="iban"
                                        @change="buscarBic"
                                        v-on:keyup.enter="submit"
                                    >
                                    </v-text-field>
                                </v-flex>
                                <v-flex sm4>
                                    <v-text-field
                                        v-model="deposito.bic"
                                        v-validate="'required|max:11'"
                                        :error-messages="errors.collect('bic')"
                                        label="BIC"
                                        counter=11
                                        data-vv-name="bic"
                                        v-on:keyup.enter="submit"
                                    >
                                    </v-text-field>
                                </v-flex>
                            </v-layout>
                            <v-layout row wrap>
                                <v-flex sm11>
                                    <v-text-field
                                        v-model="deposito.notas"
                                        label="Notas"
                                        v-on:keyup.enter="submit"
                                    >
                                    </v-text-field>
                                </v-flex>
                            </v-layout>
                            <v-layout row wrap v-if="isRoot">
                                <v-flex sm4>
                                    <v-switch
                                        v-model="multiple"
                                        label="Múltiple - Root"
                                    ></v-switch>
                                </v-flex>
                            </v-layout>
                        </v-container>
                    </v-form>
                </v-card-text>
                <v-card-actions>
                    <v-layout row wrap>
                        <v-spacer></v-spacer>
                        <v-btn color="blue darken-1" flat round @click="closeDialog">Cancelar</v-btn>
                        <v-btn color="blue darken-1" flat round :disabled="loading" :loading="loading" @click="submit">Guardar</v-btn>
                    </v-layout>
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
        dialog_depo: Boolean,
    },
    data () {
      return {
        deposito: {
            compra_id:0,
            importe:0,
            concepto_id:0,
            cliente_id: 0,
            iban:"",
            bic:"",
            deposito:"",
            importe2: 0,
        },

        multiple: false,

        disabled: false,
        conceptos: [],
        conceptos2: [],
        dialog: false,
        ruta: "deposito",
        url: "/compras/depositos",
        loading: false,
        result: false,
        valor_total_compras:0,
        excede_limite : false,
        bancos: [],
      }
    },
    beforeMount(){

        axios.get(this.url+'/create')
            .then(res => {

                this.conceptos = res.data.conceptos;

                this.bancos = res.data.bancos;

                this.deposito.concepto_id = this.compra.cliente.fpago_id;
                this.deposito.cliente_id = this.compra.cliente_id;

                if (this.deposito.concepto_id == 2){
                    this.deposito.iban = this.compra.cliente.iban;
                    this.deposito.bic = this.compra.cliente.bic;
                }
            })
            .catch(err => {
                this.$toast.error(err.response.data.message);
                this.$router.push({ name: 'compra.index'})
            })

        //this.$nextTick(() => this.$refs.dni.focus())
    },
    beforeUpdate(){
         this.deposito.importe = this.compra.importe;
        //  this.checkLimitesEfectivo();
    },
    computed: {
        ...mapGetters([
            'hasLimEfe',
            'isRoot',
            'parametros'
        ]),
        computedLimImporte(){
            if (this.compra.importe >= 2500){
                this.deposito.concepto_id = 2;
            }
        },
        computedFechaCompra() {
            moment.locale('es');
            return this.compra.fecha_compra ? moment(this.compra.fecha_compra).format('L') : '';
        },

    },
    methods:{
        getMoneyFormat(value){
            return new Intl.NumberFormat("de-DE",{style: "currency", currency: "EUR"}).format(parseFloat(value))
        },
        // checkLimitesEfectivo(){

        //     this.excede_limite = false;

        //     var lim_efe = (this.compra.cliente.iva_no_residente) ? this.parametros.lim_efe_nores : this.parametros.lim_efe;

        //     this.valor_total_compras = ((this.valor_compras+parseFloat(this.compra.importe)));

        //     if (!this.hasLimEfe){
        //         if (this.valor_total_compras >= lim_efe){
        //             this.excede_limite = true;
        //             if (this.conceptos[0].value == 1)
        //                 this.conceptos.splice(0,1);
        //             if (this.deposito.concepto_id <= 1)
        //                 this.deposito.concepto_id = 2;
        //         }
        //     }

        // },
        closeDialog (){
            this.$emit('update:dialog_depo', false)
        },
        buscarBic(){
            if (this.deposito.iban.length >= 8 && this.deposito.bic == ''){
                var entidad = this.deposito.iban.substring(4,8);
                var idx = this.bancos.map(x => x.entidad).indexOf(entidad);
                if (idx >= 0)
                    this.deposito.bic = this.bancos[idx].bic;
            }
        },
        clear(){
            this.deposito.importe2 = 0;
        },
        submit(){

            if (this.loading === false){
                this.loading = true;
                this.deposito.fecha = this.compra.fecha_compra;
                this.deposito.compra_id = this.compra.id;

                this.$validator.validateAll().then((result) => {
                    if (result){
                        axios.post(this.url, this.deposito)
                            .then(response => {

                                this.$emit('update:dialog_depo', false)
                                this.loading = false;

                                var com = this.compra;

                                com.fase_id = 3;
                                axios.put("/compras/compras/"+this.compra.id, com)
                                    .then(res => {
                                        //this.compra = res.data.compra;
                                       // this.$emit('update:compra', res.data.compra)
                                        if (this.compra.tipo_id == 1)
                                            this.$router.push({ name: 'recompra.close', params: { id: this.compra.id } })
                                        else
                                            this.$router.push({ name: 'compra.close', params: { id: this.compra.id } })
                                    });
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
