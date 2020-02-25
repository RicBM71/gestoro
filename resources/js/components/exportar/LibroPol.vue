<template>
    <div>
        <v-card>
            <v-card-title color="indigo">
                <h2 color="indigo">Generar Libro Registro Policía</h2>
                <v-spacer></v-spacer>
            </v-card-title>
        </v-card>
        <v-card>
            <v-form>
                <v-container>
                    <v-layout row wrap>
                        <v-flex sm3></v-flex>
                        <v-flex sm3>
                            <v-select
                                v-model="libro_id"
                                v-validate="'required'"
                                data-vv-name="libro_id"
                                data-vv-as="libro"
                                :error-messages="errors.collect('libro_id')"
                                :items="libros"
                                label="Libro"
                                required
                                ></v-select>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap v-show="accion!='B'">
                        <v-flex sm3></v-flex>
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
                                    label="Hasta"
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
                    </v-layout>
                    <v-layout row wrap v-show="accion=='B' || accion=='E'">
                        <v-flex sm3 d-flex></v-flex>
                        <v-flex sm2 d-flex>
                            <v-text-field
                                v-model="primera_pagina"
                                label="Primera Página"
                                v-validate="'required|numeric|between:1,100'"
                                data-vv-name="primera_pagina"
                                data-vv-as="página"
                                :error-messages="errors.collect('primera_pagina')"
                                v-on:keyup.enter="submit"
                            ></v-text-field>
                        </v-flex>
                        <v-flex sm2 d-flex>
                            <v-text-field
                                v-show="accion=='B'"
                                v-model="paginas"
                                label="Páginas"
                                v-validate="'required|numeric|between:1,100'"
                                data-vv-name="paginas"
                                data-vv-as="página"
                                :error-messages="errors.collect('paginas')"
                                v-on:keyup.enter="submit"
                            ></v-text-field>
                        </v-flex>
                    </v-layout>
                     <v-layout row wrap  v-show="accion=='E' || accion=='S'">
                        <v-flex sm3 d-flex></v-flex>
                        <v-flex sm2 d-flex>
                            <v-text-field
                                v-model="primer_registro"
                                label="Primer Registro"
                                v-validate="'required|numeric'"
                                data-vv-name="primer_registro"
                                data-vv-as="primer registro"
                                :error-messages="errors.collect('primer_registro')"
                                v-on:keyup.enter="submit"
                            ></v-text-field>
                        </v-flex>
                        <v-flex sm3>
                            <v-switch
                                label="Eliminar Última Página"
                                v-model="ultima"
                                color="primary"
                            ></v-switch>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex sm3 d-flex></v-flex>
                        <v-flex sm3 d-flex>
                            <v-select
                                v-model="accion"
                                :items="acciones"
                                label="Acción"
                            ></v-select>
                        </v-flex>
                        <v-flex sm2>
                            <v-btn @click="submit" flat :loading="loading" round small block  color="info">
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
            acciones:[
                    {value: 'X', text:"Generar Excel"},
                    {value: 'P', text:"Portada PDF"},
                    {value: 'B', text:"PDF en blanco"},
                    {value: 'E', text:"PDF con encabezados"},
                    {value: 'S', text:"PDF sin encabezados"},
                ],
            accion: 'E',
            loading: false,
            result: false,

            url:"",
            filename:"",
            tipo_file:"",
            primera_pagina: 1,
            primer_registro: 1,
            paginas: 100,

            fases: [],
            libros: [],
            libro_id: "",
            fase_id:"",
            menu_h: false,
            menu_d: false,
            label:"",
            fecha_d: new Date().toISOString().substr(0, 7)+"-01",
            fecha_h: new Date().toISOString().substr(0, 10),
            ultima: true
      }
    },
    beforeMount(){

        axios.get('/exportar/libro/index')
            .then(res => {
                this.libros = res.data.libros;
                this.libro_id = this.libros[0].value;
            })
            .catch(err => {
                this.$toast.error(err.response.data.message);
                this.$router.push({ name:'dash'})
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
        submit(){


            if (this.loading === false){
                this.loading = true;
                this.$validator.validateAll().then((result) => {
                    if (result){

                        switch (this.accion){
                            case "X":
                                this.url = '/exportar/libro/excel';
                                this.tipo_file ='text/csv';
                                this.filename = 'csv';
                                break;
                            case "P":
                                this.url = '/exportar/libro/portada';
                                this.tipo_file ='pdf';
                                this.filename = 'portada.pdf';
                                break;
                            case "B":
                                this.url = '/exportar/libro/blanco';
                                this.tipo_file ='pdf';
                                this.filename = 'libro.pdf';
                                break;
                            case "E":
                                this.url = '/exportar/libro/completo';
                                this.tipo_file ='pdf';
                                this.filename = 'libro.pdf';
                                break;
                            case "S":
                                this.url = '/exportar/libro/detalle';
                                this.tipo_file ='pdf';
                                this.filename = 'libro.pdf';
                                break;
                        }

                    this.sendpost();

                    }
                });

            }
        },
        sendpost(){

            axios({
                url: this.url, //your url
                method: 'POST',
                responseType: 'blob', // important
                data:{
                    accion: this.accion,
                    fecha_d: this.fecha_d,
                    fecha_h: this.fecha_h,
                    libro_id: this.libro_id,
                    primera_pagina: this.primera_pagina,
                    paginas: this.paginas,
                    primer_registro: this.primer_registro,
                    ultima: this.ultima

                }
                })
            .then(response => {

                    var idx = this.libros.map(x => x.value).indexOf(this.libro_id);

                    let blob = new Blob([response.data], { type: 'data:'+this.tipo_file})
                    let link = document.createElement('a')
                    link.href = window.URL.createObjectURL(blob)

                    if (this.filename == 'csv')
                        link.download = this.libros[idx].codigo_pol+"."+this.libros[idx].nombre_csv+"."+new Date().getFullYear()+(new Date().getMonth()+1)+(new Date().getDate())+'.csv';
                    else
                        link.download = this.filename;

                    document.body.appendChild(link);
                    link.click()
                    document.body.removeChild(link);

                    this.$toast.success('Libro generado correctamente '+link.download);

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
    }
}
</script>
