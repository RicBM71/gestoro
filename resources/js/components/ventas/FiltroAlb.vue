<template>
    <v-form>
        <v-container>
            <v-layout row wrap>
                <v-flex sm2 d-flex>
                    <v-select
                        v-model="quefecha"
                        :items="fechas"
                        label="Fecha"
                    ></v-select>
                </v-flex>
                <v-flex sm2>
                    <v-menu
                        v-model="menu_d"
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
                            :value="computedFechaD"
                            :label="label"
                            append-icon="event"
                            v-validate="'date_format:dd/MM/yyyy'"
                            data-vv-name="fecha_d"
                            :error-messages="errors.collect('fecha_d')"
                            data-vv-as="Desde"
                            readonly
                            ></v-text-field>
                        <v-date-picker
                            v-model="fecha_d"
                            no-title
                            locale="es"
                            first-day-of-week=1
                            @input="menu_d = false"
                            ></v-date-picker>
                    </v-menu>
                </v-flex>
                <v-flex sm2>
                    <v-menu
                        v-model="menu_h"
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
                            :value="computedFechaH"
                            :label="label"
                            append-icon="event"
                            v-validate="'date_format:dd/MM/yyyy'"
                            data-vv-name="fecha_h"
                            :error-messages="errors.collect('fecha_h')"
                            data-vv-as="Hasta"
                            readonly
                            ></v-text-field>
                        <v-date-picker
                            v-model="fecha_h"
                            no-title
                            locale="es"
                            first-day-of-week=1
                            @input="menu_h = false"
                            ></v-date-picker>
                    </v-menu>
                </v-flex>
                <v-flex sm2 d-flex>
                    <v-select
                        v-model="tipo_id"
                        :items="tipos"
                        label="Operación"
                    ></v-select>
                </v-flex>
                <v-flex sm2>
                    <v-select
                        v-model="fpago_id"
                        v-validate="'required'"
                        data-vv-name="fpago_id"
                        data-vv-as="forma de pago"
                        :error-messages="errors.collect('fpago_id')"
                        :items="fpagos"
                        label="F. Pago"
                        required
                        ></v-select>
                </v-flex>
                <v-flex sm2>
                    <v-select
                        v-model="fase_id"
                        v-validate="'required'"
                        data-vv-name="fase_id"
                        data-vv-as="fase"
                        :error-messages="errors.collect('fase_id')"
                        :items="fases"
                        label="Fase"
                        ></v-select>
                </v-flex>
            </v-layout>
            <v-layout row wrap>
                <v-flex sm2>
                    <v-text-field
                        v-model="clitxt"
                        v-validate="'max:20'"
                        :error-messages="errors.collect('clitxt')"
                        label="Cliente/notas"
                        data-vv-name="clitxt"
                        data-vv-as="clitxt"
                        hint="nombre cliente, :notas"
                        v-on:keyup.enter="submit"
                    >
                    </v-text-field>
                </v-flex>
                <v-flex sm2>
                    <v-select
                        v-model="facturado"
                        :items="facturados"
                        label="Estado"
                        required
                        ></v-select>
                </v-flex>
                <v-flex sm2>
                    <v-switch
                        label="Reservas REBU"
                        v-model="reservas"
                        color="primary">
                    ></v-switch>
                </v-flex>
                <v-flex sm2>
                    <v-switch
                        label="Venta en Depósito"
                        v-model="depositos"
                        color="primary">
                    ></v-switch>
                </v-flex>
                <v-spacer></v-spacer>
                <v-flex sm2>
                    <v-btn @click="submit"  :loading="loading" round small block  color="info">
                        Filtrar
                    </v-btn>
                </v-flex>
            </v-layout>
        </v-container>
    </v-form>
</template>
<script>
import {mapGetters} from 'vuex'
import moment from 'moment'
export default {
    $_veeValidate: {
        validator: 'new'
    },
    props:{
        filtro: Boolean,
        reg: Array
    },
    data () {
      return {
            fechas:[
                    {value: 'V', text:"Venta"},
                    {value: 'F', text:"Factura"},
                    {value: 'M', text:"Modificación"},
                ],
            facturados:[
                    {value: 'T', text:"Todos"},
                    {value: 'F', text:"Facturados"},
                    {value: 'N', text:"No facturados"},
                ],
            facturado: "T",
            quefecha: 'V',
            loading: false,
            result: false,

            tipos: [],
            fases: [],
            fpagos: [],
            tipo_id: 0,
            fpago_id: 0,
            fase_id:  0,
            clitxt: null,

            reservas: false,
            depositos: false,

            menu_h: false,
            menu_d: false,
            label:"",
            fecha_d: new Date().toISOString().substr(0, 7)+"-01",
            fecha_h: new Date().toISOString().substr(0, 10),
      }
    },
    beforeMount(){

        axios.get('/utilidades/helpfiltroalb')
            .then(res => {
                this.fpagos = res.data.fpagos;
                this.fases = res.data.fases;
                this.tipos = res.data.tipos;

                this.fases.push({value:0,text:"---"});
                this.fpagos.push({value:0,text:"---"});
                this.tipos.push({value:0,text:"---"});

                // if (!this.isAdmin){
                //     const index = this.tipos.indexOf(4);
                //     this.tipos.splice(index, 1)
                // }

            })
            .catch(err => {
                this.$toast.error('Error al montar <filtro-alb>');
            })
    },
    computed: {
        ...mapGetters([
            'isAdmin',
        ]),
        computedFechaD() {
            moment.locale('es');
            return this.fecha_d ? moment(this.fecha_d).format('L') : '';
        },
        computedFechaH() {
            moment.locale('es');
            return this.fecha_h ? moment(this.fecha_h).format('L') : '';
        }
    },
    methods:{
        // setFecha(){
        //     var fecha = new Date(this.fecha);

        //     fecha.setDate(fecha.getDate() - this.retroceso);

        //     this.label = "Desde "+fecha.toLocaleDateString();

        // },
        submit(){
            if (this.loading === false){
                this.loading = true;
                this.$validator.validateAll().then((result) => {
                    if (result){
                        axios.post('/ventas/albaranes/filtrar',
                            {
                                quefecha: this.quefecha,
                                fecha_d: this.fecha_d,
                                fecha_h: this.fecha_h,
                                tipo_id: this.tipo_id,
                                fase_id: this.fase_id,
                                fpago_id: this.fpago_id,
                                asociado_id: this.asociado_id,
                                facturado: this.facturado,
                                reservas: this.reservas,
                                depositos: this.depositos,
                                clitxt: this.clitxt
                            }
                        )
                        .then(res => {

                            // if (res.data.length == 0){
                            //     this.$toast.warning("No se han encontrado registros");
                            // }


                            this.$emit('update:reg', res.data);

                            if (res.data.length > 0)
                                this.$emit('update:filtro', false);
                            else
                                this.$toast.warning('No se han encontrado ventas');

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
                });
            }
        },
    }
}
</script>

<style scoped>

.centered-input >>> input {
  text-align: center;
  -moz-appearance:textfield;
}

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

.v-form>.container>.layout>.flex {
    padding: 1px 8px 1px 8px;
}
.v-text-field {
    padding-top: 6px;
    margin-top: 2px;
}
</style>
