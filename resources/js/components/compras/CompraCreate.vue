<template>
	<div v-show="show">
        <loading :show_loading="show_loading"></loading>
        <v-card>
            <v-card-title color="indigo">
                <h2 color="indigo">{{titulo[fase_valid - 1]}}</h2>
                <v-spacer></v-spacer>
                <menu-ope :compra="compra"></menu-ope>
            </v-card-title>
        </v-card>
        <v-card v-if="fase_valid ==1">
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
                        <v-flex sm2>
                            <v-select
                            v-model="compra.tipo_id"
                            :items="tipos"
                            label="Operación"
                            ></v-select>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex sm3></v-flex>
                        <v-flex sm3>
                            <v-select
                            v-model="tipodoc"
                            v-validate="'required'"
                            :error-messages="errors.collect('tipodoc')"
                            data-vv-name="tipodoc"
                            data-vv-as="documento"
                            :items="tiposdoc"
                            label="Documento"
                            ></v-select>
                        </v-flex>
                        <v-flex sm2>
                            <v-text-field
                                v-model="dni"
                                ref="dni"
                                v-validate="'required|min:5'"
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
                    <v-layout row wrap v-if="hasEdtFec">
                        <v-flex sm3></v-flex>
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
                                :disabled="!hasEdtFec"
                            >
                                <v-text-field
                                    slot="activator"
                                    :value="computedFechaCompra"
                                    label="Fecha Compra"
                                    append-icon="event"
                                    data-vv-as="Fecha Compra"
                                    readonly
                                    ></v-text-field>
                                <v-date-picker
                                    v-model="compra.fecha_compra"
                                    no-title
                                    locale="es"
                                    first-day-of-week=1
                                    @input="menu1 = false"
                                    :disabled="!hasEdtFec"
                                ></v-date-picker>
                            </v-menu>
                        </v-flex>
                        <v-flex sm2>
                            <v-text-field
                                v-model="compra.albaran"
                                v-validate="'numeric'"
                                data-vv-name="albaran"
                                data-vv-as="Nº de registro"
                                :error-messages="errors.collect('albaran')"
                                label="Nº Registro"
                                v-on:keyup.enter="submit"
                                :readonly="!hasEdtFec"
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

                libro_id: 0,
                compra: {
                    empresa_id: 0,
                    grupo_id: 0,
                    dias_custodia: 0,
                    ejercicio: 0,
                    serie_com: "",
                    albaran: null,
                    cliente_id: 0,
                    tipo_id: 0,
                    fecha_compra: new Date().toISOString().substr(0, 10),
                    fecha_renovacion: "",
                    fecha_recogida: "",
                    importe: 0,
                    importe_renovacion:0,
                    importe_acuenta:0,
                    interes: 0,
                    interes_recuperacion:0,
                    fase_id: 1,
                    factura: "",
                    fecha_factura: "",
                    serie_fac: "",
                    papeleta: "",
                    notas:"",
                    username:"",
                    interes:0,
                    interes_recuperacion:0
                },
                cliente: {
                    id: 0,
                    dni: "",
                    tipodoc: ""
                },
                url: "/compras/compras",
                ruta: "compra",
                libros: [],

                tiposdoc:[
                    {value: 'N', text:"DNI/NIE"},
                    {value: 'O', text:"Otros"},
                ],
                tipodoc: 'N',
                dni: "",

        		status: false,
                loading: false,

                tipos: [],

                fase_valid: 1,

                show: false,
                show_loading: true,

                titulo:[
                    'Nueva Operación: 1 de 2 - indicar cliente',
                    'Nueva Operación: 2 de 2 - validar cliente',
                    '...creando la operación',
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
                    this.libros = res.data.libros;
                    if (this.libros.length == 0){
                        this.$toast.error('No hay libro disponible, contactar con un administrador!');
                        this.$router.push({ name: 'dash'})

                    }else{

                        this.libro_id = this.libros[0].value;
                        this.tipos = res.data.tipos;
                        this.compra.tipo_id = this.tipos[0].value;
                        this.show_loading = false;

                        this.$refs.dni.focus();
                    }
                })
                .catch(err => {
                    this.$toast.error(err.response.data.message);
                    this.$router.push({ name: this.ruta+'.index'})
                })

            this.$nextTick(() => this.$refs.dni.focus())
        },
        computed: {
            ...mapGetters([
                    'hasEdtFec'
                ]),
            computedFechaCompra() {
                moment.locale('es');
                return this.compra.fecha_compra ? moment(this.compra.fecha_compra).format('L') : '';
            },
        },
        watch: {
            fase_valid: function () {
                if(this.fase_valid == 3)
                    this.crearCompra();
            },
        },
    	methods:{
            submit() {
                if (this.loading === false){
                    this.loading = true;

                    this.$validator.validateAll().then((result) => {
                        if (result){
                            axios.post(this.url, this.libro)
                                .then(response => {
                                    this.loading = false;
                                    this.$router.push({ name: this.ruta+'.edit', params: { id: response.data.compra.id } })
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
                        else{
                            this.loading = false;
                        }
                    });
                }

            },
            buscar_dni() {

                this.loading = true;

                this.$validator.validateAll().then((result) => {
                    if (result){
                        axios.post('/utilidades/helpcli', {dni:this.dni,tipodoc:this.tipodoc})
                            .then(res => {

                                this.cliente = res.data.cliente;
                                if (res.data.documentos.status == -2){

                                    axios.post('/mto/clidocs/'+res.data.documentos.id+'/renove')
                                        .then(response => {
                                            this.$toast.warning('Documentación caducada eliminada!');
                                        })
                                        .catch(err => {
                                            console.log(err);
                                        });
                                    }
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
            crearCompra(){

                this.compra.cliente_id = this.cliente.id;


                var idx = this.libros.map(x => x.value).indexOf(this.libro_id);
                if (idx >= 0)
                    this.compra.grupo_id = this.libros[idx].grupo_id;

                this.loading = true;
                this.$validator.validateAll().then((result) => {
                    if (result){

                            axios.post(this.url, this.compra)
                            .then(response => {

                              //  this.$toast.success(response.data.message);
                                this.loading = false;
                                this.$router.push({ name: this.ruta+'.edit', params: { id: response.data.compra.id } })
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
