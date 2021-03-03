<template>
    <v-layout row justify-center>
        <v-dialog v-model="dialog_fac" persistent max-width="600px">
            <v-card>
                <v-card-title>
                <span class="headline">Facturar</span>
                </v-card-title>
                <v-card-text>
                    <v-form>
                        <v-container>
                            <v-layout wrap>
                                <v-flex sm6>
                                    <v-select
                                        v-model="tipo_factura"
                                        :items="tipos_factura"
                                    label="Tipo Factura"
                                ></v-select>
                                </v-flex>
                            </v-layout>
                            <v-layout wrap>
                                <v-flex sm5>
                                    <v-menu
                                        v-model="menu1"
                                        :close-on-content-click="false"
                                        :nudge-right="40"
                                        lazy
                                        transition="scale-transition"
                                        offset-y
                                        full-width
                                        min-width="290px"
                                    >
                                        <v-text-field
                                            slot="activator"
                                            :value="computedFechaFactura"
                                            label="Fecha Factura"
                                            append-icon="event"
                                            readonly
                                            ></v-text-field>
                                        <v-date-picker
                                            v-model="fecha_factura"
                                            :min="fecha_min"
                                            no-title
                                            locale="es"
                                            first-day-of-week=1
                                            @input="menu1 = false"
                                        ></v-date-picker>
                                    </v-menu>
                                </v-flex>

                                <v-flex sm4>
                                    <v-text-field
                                        v-model="factura"
                                        label="Factura"
                                        v-validate="'required|numeric'"
                                        :error-messages="errors.collect('factura')"
                                        data-vv-name="factura"
                                        data-vv-as="factura"
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
                <v-btn color="blue darken-1" flat round :disabled="loading" :loading="loading" @click="submit">Facturar</v-btn>
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
        albaran: Object,
        dialog_fac: Boolean,
    },
    data () {
      return {
        menu1: false,
        tipos_factura:[
            {value: 1, text:"Factura Manual"},
            {value: 2, text:"Factura Automática"},
            {value: 3, text:"Factura de Abono"},
        ],
        url: "/ventas/albaranes/",
        loading: false,
        fecha_min: "",
        fecha_factura: new Date().toISOString().substr(0, 10),
        tipo_factura: 1,
        factura: 0
      }
    },
    mounted(){

        this.fecha_min = this.albaran.fecha_albaran;
    },
    computed: {
        ...mapGetters([
            
        ]),
        computedFechaFactura() {
            moment.locale('es');
            return this.fecha_factura ? moment(this.fecha_factura).format('L') : '';
        }
    },
    methods:{
        closeDialog (){
            this.$emit('update:dialog_fac', false)
        },
        submit(){

            if (this.loading === false){
                this.loading = true;

                this.$validator.validateAll().then((result) => {
                    if (result){
                        axios.put(this.url+this.albaran.id+"/facauto", {
                            fecha_factura: this.fecha_factura,
                            tipo_factura: this.tipo_factura,
                            factura: this.factura
                        })
                            .then(res => {

                                this.$emit('update:albaran', res.data.albaran);

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
                            })
                            .finally(() => {
                                this.$emit('update:dialog_fac', false)
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
