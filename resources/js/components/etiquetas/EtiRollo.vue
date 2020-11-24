<template>
    <div>
        <v-card>
            <loading :show_loading="show_loading"></loading>
            <v-card-title color="indigo">
                <h2 color="indigo">Etiquetas Excel</h2>
                <v-spacer></v-spacer>
            </v-card-title>
        </v-card>
        <v-card>
            <v-form>
                <v-container>
                    <v-layout row wrap>
                        <v-flex sm1></v-flex>
                        <v-flex sm2>
                            <v-select
                                v-model="form.clase_id"
                                v-validate="'numeric'"
                                data-vv-name="clase_id"
                                data-vv-as="clase"
                                :error-messages="errors.collect('clase_id')"
                                :items="clases"
                                label="Clase"
                                required
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
                                    label="Desde"
                                    append-icon="event"
                                    v-validate="'date_format:dd/MM/yyyy'"
                                    data-vv-name="fecha_d"
                                    :error-messages="errors.collect('fecha_d')"
                                    data-vv-as="Desde"
                                    readonly
                                    ></v-text-field>
                                <v-date-picker
                                    v-model="form.fecha_d"
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
                                    label="Hasta"
                                    append-icon="event"
                                    v-validate="'date_format:dd/MM/yyyy'"
                                    data-vv-name="fecha_h"
                                    :error-messages="errors.collect('fecha_h')"
                                    data-vv-as="Hasta"
                                    readonly
                                    ></v-text-field>
                                <v-date-picker
                                    v-model="form.fecha_h"
                                    no-title
                                    locale="es"
                                    first-day-of-week=1
                                    @input="menu_h = false"
                                    ></v-date-picker>
                            </v-menu>
                        </v-flex>
                        <v-flex sm1>
                            <v-select
                                v-model="form.tipo_fecha"
                                v-validate="'required'"
                                data-vv-name="tipo_fecha"
                                data-vv-as="fecha"
                                :error-messages="errors.collect('tipo_fecha')"
                                :items="fechas"
                                label="Tipo Fecha"
                                required
                                ></v-select>
                        </v-flex>
                        <v-flex sm1 d-flex></v-flex>
                        <v-flex sm1>
                            <v-btn @click="submit" :loading="show_loading" flat round small block  color="info">
                                Enviar
                            </v-btn>
                        </v-flex>
                     </v-layout>

                </v-container>
            </v-form>
        </v-card>
    </div>
</template>
<script>
import moment from 'moment'
import {mapGetters} from 'vuex';
import Loading from '@/components/shared/Loading'
export default {
    $_veeValidate: {
        validator: 'new'
    },
    components: {
        'loading': Loading
    },
    data () {
      return {
        form:{
            etiqueta_id: "",
            clase_id: null,
            limite: 0,
            fecha_d: "", //new Date().toISOString().substr(0, 10),
            fecha_h: "",
            tipo_fecha: 'C',
        },
        etiquetas: [],
        clases:[],
        show_loading: true,
        menu_d: false,
        menu_h: false,
        fechas:[
                {value: 'C', text: 'Creación'},
                {value: 'M', text: 'Modificación'}
            ],
      }
    },
    mounted(){
        axios.get('/etiquetas/index')
            .then(res => {
                this.etiquetas = res.data.etiquetas;
                this.clases = res.data.clases;
                this.form.etiqueta_id = this.etiquetas[0].value;
                this.clases.push({value:null,text:"Todas las Clases"});
            })
            .catch(err => {
                this.$toast.error(err.response.data.message);
                //this.$toast.error('Error al montar <apli-pdf>');
            })
            .finally(()=> {
                this.show_loading = false;
            });
    },
    computed: {
        ...mapGetters([
        ]),
        computedFechaD() {
            moment.locale('es');
            return this.form.fecha_d ? moment(this.form.fecha_d).format('L') : '';
        },
        computedFechaH() {
            moment.locale('es');
            return this.form.fecha_h ? moment(this.form.fecha_h).format('L') : '';
        }
    },
    methods:{
        submit(){


            if (this.show_loading === false){
                this.$validator.validateAll().then((result) => {
                    if (result){

                        this.show_loading = true;
                        axios({
                            url: '/etiquetas/rollo',
                            method: 'POST',
                            responseType: 'blob',
                            data: this.form,
                            })
                        .then(res => {


                            let blob = new Blob([res.data], { type: 'data:xlsx'})
                            let link = document.createElement('a')
                            link.href = window.URL.createObjectURL(blob)

                            link.download = 'Etiquetas.xlsx';

                            document.body.appendChild(link);
                            link.click()
                            document.body.removeChild(link);

                            this.$toast.success('Etiquetas generadas correctamente '+link.download);

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
                            }else if(err.request.status == 404){
                                this.$toast.error('No hay etiquetas');
                            }
                            else{
                                this.$toast.error(err.response.data.message);
                            }
                        })
                        .finally(()=> {
                             this.show_loading = false;
                        });
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
</style>
