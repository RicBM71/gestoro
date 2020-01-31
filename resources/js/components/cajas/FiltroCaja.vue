<template>
    <v-form>
        <v-container>
            <v-layout row wrap>
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
                    <v-flex sm1>
                        <v-select
                            v-model="dh"
                            data-vv-name="dh"
                            data-vv-as="D-H"
                            :error-messages="errors.collect('dh')"
                            :items="dh_items"
                            label="D/H"
                            required
                            ></v-select>
                    </v-flex>
                    <v-flex sm2>
                        <v-select
                            v-model="manual"
                            data-vv-name="manual"
                            data-vv-as="origen"
                            :error-messages="errors.collect('manual')"
                            :items="ma_items"
                            label="Origen"
                            ></v-select>
                    </v-flex>
                    <v-flex sm3>
                        <v-select
                            v-model="apunte_id"
                            v-validate="'numeric'"
                            data-vv-name="apunte_id"
                            data-vv-as="apunte"
                            :error-messages="errors.collect('apunte_id')"
                            :items="apuntes"
                            label="Apunte"
                            ></v-select>
                    </v-flex>
                    <!-- <v-flex sm2>
                        <v-select
                            v-model="manual"
                            v-validate="'required'"
                            data-vv-name="manual"
                            data-vv-as="Manual"
                            :error-messages="errors.collect('manual')"
                            :items="ma_items"
                            label="Tipo Apunte"
                            required
                            ></v-select>
                    </v-flex> -->
                    <v-spacer></v-spacer>
                    <v-flex sm2>
                        <v-btn small flat @click="submit"  :loading="loading" round  block  color="info">
                            Filtrar
                        </v-btn>
                    </v-flex>
            </v-layout>
        </v-container>
    </v-form>
</template>
<script>
import moment from 'moment'
import {mapGetters} from 'vuex';
export default {
    $_veeValidate: {
        validator: 'new'
    },
    props:{
        filtro: Boolean
    },
    data () {
      return {
            fechas:[
                    {value: 'A', text:"Apunte"},
                    {value: 'M', text:"Modificación"},
                ],
            quefecha: 'A',
            dh: null,
            loading: false,
            result: false,
            dh_items:[
                    {value: 'D', text:"Debe"},
                    {value: 'H', text:"Haber"},
                    {value: null, text:"Todos"},
                ],
            ma_items:[
                    {value: null, text:"Todos"},
                    {value: 'S', text:"Manual"},
                    {value: 'N', text:"Auto"},
                    {value: 'C', text:"Auto-Compras"},
                    {value: 'V', text:"Auto-Ventas"},
                ],
            manual: null,
            menu_h: false,
            menu_d: false,
            apunte_id:  null,
            label:"",
            fecha_d: new Date().toISOString().substr(0, 7)+"-01",
            fecha_h: new Date().toISOString().substr(0, 10),
            apuntes:[],
      }
    },
    beforeMount(){

        axios.get('/utilidades/helpapuntes')
            .then(res => {
                this.apuntes = res.data;
                this.apuntes.push({value: null, text: '-'});
                if (this.isAdmin)
                    this.ma_items.push({value: 'R', text: 'Regularización'});

            })
            .catch(err =>{

                this.$toast.error(err.response.data.message);
                this.$router.push({ name: 'dash' })
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
        submit(){

            if (this.loading === false){
                this.$validator.validateAll().then((result) => {
                    if (result){
                        this.loading = true;
                        axios.post('/mto/cajas/filtrar',
                            {
                                quefecha: this.quefecha,
                                fecha_d: this.fecha_d,
                                fecha_h: this.fecha_h,
                                dh: this.dh,
                                manual: this.manual,
                                apunte_id: this.apunte_id
                            }
                        )
                        .then(res => {

                            this.$emit('update:reg', res.data.caja);
                            this.$emit('update:saldo', res.data.saldo);
                            this.$emit('update:fecha_saldo', res.data.fecha_saldo);

                            this.$emit('update:filtro', false);

                            this.loading = false;

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
