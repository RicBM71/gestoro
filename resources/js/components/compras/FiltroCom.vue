<template>
    <v-form>
        <v-container>
            <v-layout row wrap>
                <v-flex sm2 d-flex>
                    <v-select
                        v-model="quefecha"
                        :items="fechas"
                        label="Fecha"
                        @change="setValores"
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
                <v-flex sm1>
                    <v-select
                        v-model="grupo_id"
                        v-validate="'required'"
                        data-vv-name="grupo_id"
                        data-vv-as="grupo"
                        :error-messages="errors.collect('grupo_id')"
                        :items="grupos"
                        label="Grupo"
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
                <v-flex sm1>
                    <v-text-field
                        v-model="retraso"
                        v-validate="'numeric'"
                        :error-messages="errors.collect('retraso')"
                        label="Retraso"
                        data-vv-name="retraso"
                        data-vv-as="retraso"
                        v-on:keyup.enter="submit"
                    >
                    </v-text-field>
                </v-flex>
                <v-flex sm1></v-flex>
                <v-flex sm2>
                    <v-select
                        v-model="almacen_id"
                        v-validate="'numeric'"
                        data-vv-name="almacen_id"
                        data-vv-as="Ubicación"
                        :error-messages="errors.collect('almacen_id')"
                        :items="almacenes"
                        label="Ubicación"
                    ></v-select>
                </v-flex>
                <v-flex sm2>
                    <v-switch
                        label="Recuento"
                        v-model="vivos"
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
                    {value: 'C', text:"Compra"},
                    {value: 'R', text:"Recogida"},
                    {value: 'M', text:"Modificación"},
                    {value: 'N', text:"Renovación"},
                ],
            quefecha: 'C',
            loading: false,
            result: false,

            almacen_id: null,
            tipo_id: 0,
            tipos: [],
            fases: [],
            grupos: [],
            almacenes:[],
            grupo_id: "",
            fase_id:"",
            menu_h: false,
            menu_d: false,
            label:"",
            retraso:"",
            fecha_d: new Date().toISOString().substr(0, 7)+"-01",
            fecha_h: new Date().toISOString().substr(0, 10),
            vivos: false,
      }
    },
    beforeMount(){

        axios.get('/utilidades/helpgrupos')
            .then(res => {

                this.grupos = res.data.grupos;

                //this.grupo_id = this.grupos[0].value;
                if (res.data.libro_def != null)
                    this.grupo_id = res.data.libro_def.grupo_id;

                this.fases = res.data.fases;
                this.tipos = res.data.tipos;

                this.almacenes = res.data.almacenes;
                this.almacenes.push({value: null, text: '---'});

                this.fases.push({value:-1,text:"---"});
                this.fase_id = -1;

                this.tipos.push({value:0,text:"---"});
            })
            .catch(err => {
                this.$toast.error('Error al montar <filtro-com>');
            })
    },
    computed: {
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
        setValores(){
            if (this.quefecha == 'N'){
                this.tipo_id = 1;
                this.fase_id = 4;
            }
        },
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
                        axios.post('/compras/compras/filtrar',
                            {
                                quefecha: this.quefecha,
                                fecha_d: this.fecha_d,
                                fecha_h: this.fecha_h,
                                grupo_id: this.grupo_id,
                                fase_id: this.fase_id,
                                tipo_id: this.tipo_id,
                                retraso: this.retraso,
                                vivos: this.vivos,
                                almacen_id: this.almacen_id
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
                                this.$toast.warning('No se han encontrado compras');

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
