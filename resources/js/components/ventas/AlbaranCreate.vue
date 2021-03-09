<template>
	<div v-show="show">
        <loading :show_loading="show_loading"></loading>
        <v-card>
            <v-card-title color="indigo">
                <h2 color="indigo">{{titulo[fase_valid - 1]}}</h2>
                <v-spacer></v-spacer>
                <menu-ope :albaran="albaran"></menu-ope>
            </v-card-title>
        </v-card>
        <v-card v-if="fase_valid ==1">
            <v-form>
                 <v-container>
                     <v-layout row wrap>
                        <v-flex sm3></v-flex>
                        <v-flex sm2>
                            <v-select
                            v-model="albaran.tipo_id"
                            :items="tipos"
                            label="Operación"
                            ></v-select>
                        </v-flex>
                        <v-flex sm2>
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
                                    :value="computedFechaAlbaran"
                                    label="Fecha Albarán"
                                    append-icon="event"
                                    data-vv-as="Fecha Albarán"
                                    readonly
                                    ></v-text-field>
                                <v-date-picker
                                    v-model="albaran.fecha_albaran"
                                    no-title
                                    locale="es"
                                    first-day-of-week=1
                                    @input="menu1 = false"
                                ></v-date-picker>
                            </v-menu>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex sm3></v-flex>
                        <v-flex sm2>
                            <v-select
                            v-model="tipodoc"
                            :items="tiposdoc"
                            label="Documento"
                            ></v-select>
                        </v-flex>
                        <v-flex sm2>
                            <v-text-field
                                v-model="dni"
                                ref="dni"
                                v-validate="'required|min:4'"
                                :error-messages="errors.collect('dni')"
                                label="Nº Documento"
                                required
                                data-vv-name="dni"
                                data-vv-as="Documento"
                                v-on:keyup.enter="buscar_dni"
                            >
                            </v-text-field>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex sm8></v-flex>
                        <v-flex sm2>
                            <div class="text-xs-center">
                                <v-btn @click="buscar_dni"  round  :loading="loading" block  color="primary">
                                    Siguiente
                                </v-btn>
                            </div>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-form>
        </v-card>
        <v-card v-if="fase_valid == 2">
            <frm-cli :miCliente.sync="cliente" :fase_valid.sync="fase_valid"></frm-cli>
        </v-card>

	</div>
</template>
<script>
import {mapGetters} from 'vuex';
import moment from 'moment'
import MenuOpe from './MenuOpe'
import FrmCli from './FrmCli'
import Loading from '@/components/shared/Loading'

	export default {
		$_veeValidate: {
      		validator: 'new'
        },
        components: {
            'menu-ope': MenuOpe,
            'frm-cli': FrmCli,
            'loading': Loading
		},
    	data () {
      		return {

                menu1: false,

                albaran: {
                    id: 0,
                    empresa_id: 0,
                    cliente_id: 0,
                    albaran: "",
                    tipo_id: 3,
                    fecha_albaran: new Date().toISOString().substr(0, 10),
                    clitxt: null,
                },
                cliente: {
                    id: 0,
                    dni: "",
                    tipodoc: ""
                },
                url: "/ventas/albaranes",
                ruta: "albaran",
                libros: [],

                tiposdoc:[
                    {value: 'N', text:"DNI/NIE"},
                    {value: 'C', text:"CIF"},
                    {value: 'O', text:"Otros"},
                ],
                tipodoc: 'O',
                dni: "",

        		status: false,
                loading: false,

                tipos: [],

                fase_valid: 1,

                show: false,
                show_loading: true,

                titulo:[
                    'Nuevo Albarán: 1 de 2 - indicar cliente',
                    'Nuevo Albarán: 2 de 2 - validar cliente',
                    '...creando el albarán',
                ]
      		}
        },
        mounted(){

            var cliente_id = this.$route.params.cliente_id;
            if (cliente_id > 0){
                axios.get('/mto/clientes/'+cliente_id)
                    .then(res => {
                        this.dni = res.data.cliente.dni;
                        this.tipodoc = res.data.cliente.tipodoc;
                    });
            }

            axios.get(this.url+'/create')
                .then(res => {
                    // on.dni;
                    // this.$refs.dni.focus();

                    this.show = true;
                    this.tipos = res.data.tipos;
                    if (this.tipos.length == 1)
                        this.albaran.tipo_id = this.tipos[0].value;
                    this.show_loading = false;

                    this.$refs.dni.focus();
                })
                .catch(err => {
                    this.$toast.error(err.response.data.message);
                    this.$router.push({ name: this.ruta+'.index'})
                })

            this.$nextTick(() => this.$refs.dni.focus())
        },
        computed: {
            ...mapGetters([
                ]),
            computedFechaAlbaran() {
                moment.locale('es');
                return this.albaran.fecha_albaran ? moment(this.albaran.fecha_albaran).format('L') : '';
            },
        },
        watch: {
            fase_valid: function () {

                if(this.fase_valid == 3)
                    this.crearAlbaran();
            },
        },
    	methods:{
            // submit() {
            //     if (this.loading === false){
            //         this.loading = true;

            //         this.$validator.validateAll().then((result) => {
            //             if (result){
            //                 axios.post(this.url, this.libro)
            //                     .then(response => {
            //                         this.loading = false;
            //                         this.$router.push({ name: this.ruta+'.edit', params: { id: response.data.albaran.id } })
            //                     })
            //                     .catch(err => {

            //                         if (err.request.status == 422){ // fallo de validated.
            //                             const msg_valid = err.response.data.errors;
            //                             for (const prop in msg_valid) {
            //                                 this.errors.add({
            //                                     field: prop,
            //                                     msg: `${msg_valid[prop]}`
            //                                 })
            //                             }
            //                         }else{
            //                             this.$toast.error(err.response.data.message);
            //                         }
            //                         this.loading = false;
            //                     });
            //                 }
            //             else{
            //                 this.loading = false;
            //             }
            //         });
            //     }

            // },
            buscar_dni() {

                this.loading = true;

                this.$validator.validateAll().then((result) => {
                    if (result){
                        axios.post('/utilidades/helpcli', {dni:this.dni,tipodoc:this.tipodoc})
                            .then(res => {

                                this.cliente = res.data.cliente;
                                this.fase_valid = 2;
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
                                    this.fase_valid = 2;
                                    this.cliente.id = 0;
                                    this.cliente.dni = this.dni;
                                    this.cliente.tipodoc = this.tipodoc;
                                }
                                else{
                                    this.$toast.error(err.response.data.message);
                                }
                                this.loading = false;
                            });
                        }
                    else{
                        this.loading = false;
                    }
                });

            },
            crearAlbaran(){

                this.albaran.cliente_id = this.cliente.id;
                this.albaran.iva_no_residente = this.cliente.iva_no_residente;

                this.loading = true;
                this.$validator.validateAll().then((result) => {
                    if (result){
                            axios.post(this.url, this.albaran)
                            .then(response => {

                              //  this.$toast.success(response.data.message);
                                this.loading = false;
                                this.$router.push({ name: this.ruta+'.edit', params: { id: response.data.albaran.id } })
                            })
                            .catch(err => {
                                this.fase_valid = 1;
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
                    else{
                        this.loading = false;
                    }
                });
            }
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

.v-form>.container>.layout>.flex {
    padding: 1px 8px 1px 8px;
}
.v-text-field {
    padding-top: 6px;
    margin-top: 2px;
}
</style>
