<template>
	<div>
        <v-card>
            <v-card-title color="indigo">
                <h2 color="indigo">{{titulo}}</h2>
                <v-spacer></v-spacer>
                <menu-ope :id="cliente.id"></menu-ope>
            </v-card-title>
        </v-card>
        <v-card>
            <v-tabs fixed-tabs>
                <v-tab>
                        Datos generales
                </v-tab>
                <v-tab>
                        Albaranes
                </v-tab>
                <v-tab>
                        Documentos
                </v-tab>
                <v-tab-item>
                    <v-form>
                        <v-container>
                            <v-layout row wrap>
                                <v-flex sm1 d-flex>
                                    <v-select
                                    v-model="cliente.tipodoc"
                                    :items="tiposdoc"
                                    label="Documento"
                                    ></v-select>
                                </v-flex>

                                <v-flex sm2>
                                    <v-text-field
                                        v-model="cliente.dni"
                                        v-validate="'required|min:4'"
                                        :error-messages="errors.collect('dni')"
                                        label="Nº Documento"
                                        required
                                        data-vv-name="dni"
                                        data-vv-as="Documento"
                                        v-on:keyup.enter="submit"
                                    >
                                    </v-text-field>
                                </v-flex>
                                <v-flex sm3>
                                    <v-text-field
                                        v-model="cliente.nombre"
                                        v-validate="'required'"
                                        :error-messages="errors.collect('nombre')"
                                        label="Nombre"
                                        data-vv-name="nombre"
                                        data-vv-as="nombre"
                                        required
                                        v-on:keyup.enter="submit"
                                    >
                                    </v-text-field>
                                </v-flex>
                                <v-flex sm4>
                                    <v-text-field
                                        v-model="cliente.apellidos"
                                        v-validate="'required'"
                                        :error-messages="errors.collect('apellidos')"
                                        label="Apellidos"
                                        data-vv-name="apellidos"
                                        data-vv-as="apellidos"
                                        required
                                        v-on:keyup.enter="submit"
                                    >
                                    </v-text-field>
                                </v-flex>
                                <v-flex sm2>
                                     <!-- <v-menu
                                            v-model="menu"
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
                                                :value="computedFechaDoc"
                                                clearable
                                                label="Fecha Doc"
                                                prepend-icon="event"
                                                :error-messages="errors.collect('fecha_dni')"
                                                readonly
                                                data-vv-as="F. dod"
                                                @click:clear="clearDateFD"
                                                ></v-text-field>
                                            <v-date-picker
                                                v-model="cliente.fecha_dni"
                                                no-title
                                                locale="es"
                                                first-day-of-week=1
                                                @input="calfbaja = false"

                                            ></v-date-picker>
                                    </v-menu> -->
                                    <v-menu
                                        ref="menu"
                                        v-model="menu"
                                        :close-on-content-click="false"
                                        :nudge-right="40"
                                        lazy
                                        transition="scale-transition"
                                        offset-y
                                        full-width
                                        min-width="290px"
                                    >
                                        <template v-slot:activator="{ on }">
                                        <v-text-field
                                            v-model="computedFechaDoc"
                                            v-validate="'required'"
                                            data-vv-name="fecha_dni"
                                            :error-messages="errors.collect('fecha_dni')"
                                            label="Fecha Validez"
                                            prepend-icon="event"
                                            clearable
                                            @click:clear="clearDateFD"
                                            readonly
                                            v-on="on"
                                        ></v-text-field>
                                        </template>
                                        <v-date-picker
                                            ref="picker"
                                            v-model="cliente.fecha_dni"
                                            locale="es"
                                            first-day-of-week=1
                                            @change="setFechaDoc"
                                        ></v-date-picker>
                                    </v-menu>
                                </v-flex>
                                <!-- <v-flex sm2>
                                    <v-text-field
                                        v-model="computedFDNI"
                                        mask="##/##/####"
                                        :error-messages="errors.collect('fecha_dni')"
                                        label="F. Validez"
                                        data-vv-name="fecha_dni"
                                        v-on:keyup.enter="submit"                                    >
                                    </v-text-field>
                                </v-flex> -->
                            </v-layout>
                            <v-layout row wrap>
                                <v-flex sm4>
                                    <v-text-field
                                        v-model="cliente.razon"
                                        v-validate="'required'"
                                        :error-messages="errors.collect('razon')"
                                        label="Razon"
                                        data-vv-name="razon"
                                        required
                                        v-on:keyup.enter="submit"
                                    >
                                    </v-text-field>
                                </v-flex>

                                <v-flex sm2>
                                    <v-text-field
                                        v-model="computedFN"
                                        mask="##/##/####"
                                        :error-messages="errors.collect('fecha_nacimiento')"
                                        label="F. Nacimiento"
                                        data-vv-name="fecha_nacimiento"

                                        v-on:keyup.enter="submit"
                                    >
                                    </v-text-field>
                                </v-flex>

                                <v-flex sm2>
                                    <v-text-field
                                        v-model="cliente.tfmovil"
                                        :error-messages="errors.collect('tfmovil')"
                                        label="Tf. Móvil"
                                        data-vv-name="tfmovil"
                                        mask="### ### ###"
                                        v-on:keyup.enter="submit"
                                    >
                                    </v-text-field>
                                </v-flex>
                                <v-flex sm2>
                                    <v-text-field
                                        v-model="cliente.telefono1"
                                        :error-messages="errors.collect('telefono1')"
                                        label="Teléfono"
                                        data-vv-name="telefono1"
                                        data-vv-as="Teléfono"
                                        mask="### ### ###"
                                        v-on:keyup.enter="submit"
                                    >
                                    </v-text-field>
                                </v-flex>
                                <v-flex sm2>
                                    <v-text-field
                                        v-model="cliente.telefono2"
                                        :error-messages="errors.collect('telefono2')"
                                        label="Teléfono"
                                        data-vv-name="telefono2"
                                        data-vv-as="Teléfono"
                                        mask="### ### ###"
                                        v-on:keyup.enter="submit"
                                    >
                                    </v-text-field>
                                </v-flex>
                            </v-layout>
                            <v-layout row wrap>
                                <v-flex sm5>
                                    <v-text-field
                                        v-model="cliente.direccion"
                                        :error-messages="errors.collect('direccion')"
                                        label="Dirección"
                                        data-vv-name="direccion"
                                        v-on:keyup.enter="submit"
                                    >
                                    </v-text-field>
                                </v-flex>
                                <v-flex sm1>
                                    <v-text-field
                                        v-model="cliente.cpostal"
                                        :error-messages="errors.collect('cpostal')"
                                        label="CP"
                                        data-vv-name="CP"
                                        v-on:keyup.enter="submit"
                                    >
                                    </v-text-field>
                                </v-flex>
                                <v-flex sm3>
                                    <v-text-field
                                        v-model="cliente.poblacion"
                                        :error-messages="errors.collect('poblacion')"
                                        label="Población"
                                        data-vv-name="poblacion"
                                        v-on:keyup.enter="submit"
                                    >
                                    </v-text-field>
                                </v-flex>
                                <v-flex sm3>
                                    <v-text-field
                                        v-model="cliente.provincia"
                                        :error-messages="errors.collect('provincia')"
                                        label="Provincia"
                                        data-vv-name="provincia"
                                        v-on:keyup.enter="submit"
                                    >
                                    </v-text-field>
                                </v-flex>
                            </v-layout>
                            <v-layout row wrap>
                                <v-flex sm4>
                                    <v-text-field
                                        v-model="cliente.nacpais"
                                        :error-messages="errors.collect('nacpais')"
                                        label="Localidad Nac."
                                        data-vv-name="nacpais"
                                        v-on:keyup.enter="submit"
                                    >
                                    </v-text-field>
                                </v-flex>
                                <v-flex sm4>
                                    <v-text-field
                                        v-model="cliente.nacpro"
                                        :error-messages="errors.collect('nacpro')"
                                        label="Provincia Nac."
                                        data-vv-name="nacpro"
                                        v-on:keyup.enter="submit"
                                    >
                                    </v-text-field>
                                </v-flex>
                                <v-flex sm4>
                                    <v-text-field
                                        v-model="cliente.email"
                                        v-validate="'email'"
                                        :error-messages="errors.collect('email')"
                                        label="email"
                                        data-vv-name="email"
                                        v-on:keyup.enter="submit"
                                    >
                                    </v-text-field>
                                </v-flex>
                            </v-layout>
                            <v-layout row wrap>
                                <v-flex sm4>
                                    <v-text-field
                                        v-model="cliente.iban"
                                        :error-messages="errors.collect('iban')"
                                        label="IBAN"
                                        mask="AA## #### #### #### #### ####"
                                        counter=24
                                        data-vv-name="iban"
                                        v-on:keyup.enter="submit"
                                    >
                                    </v-text-field>
                                </v-flex>
                                <v-flex sm3>
                                    <v-text-field
                                        v-model="cliente.bic"
                                        v-validate="rules"
                                        :error-messages="errors.collect('bic')"
                                        label="BIC"
                                        counter=11
                                        data-vv-name="bic"
                                        v-on:keyup.enter="submit"
                                    >
                                    </v-text-field>
                                </v-flex>
                                <v-flex sm3 d-flex>
                                    <v-select
                                    v-model="cliente.fpago_id"
                                    :items="fpagos"
                                    label="Forma de Pago"
                                    ></v-select>
                                </v-flex>
                                <v-flex sm2>
                                    <v-menu
                                            v-model="calfbaja"
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
                                                :value="computedFechaBaja"
                                                clearable
                                                label="Fecha Baja"
                                                prepend-icon="event"
                                                readonly
                                                data-vv-as="F. Baja"
                                                @click:clear="clearDate"
                                                ></v-text-field>
                                            <v-date-picker
                                                v-model="cliente.fecha_baja"
                                                no-title
                                                locale="es"
                                                first-day-of-week=1
                                                @input="calfbaja = false"

                                            ></v-date-picker>
                                    </v-menu>
                                </v-flex>
                            </v-layout>
                            <v-layout row wrap>
                                <v-flex sm12>
                                    <v-text-field
                                        v-model="cliente.notas"
                                        :error-messages="errors.collect('notas1')"
                                        label="Notas"
                                        data-vv-name="notas1"
                                        v-on:keyup.enter="submit"
                                    >
                                    </v-text-field>
                                </v-flex>
                            </v-layout>

                            <v-layout row wrap>
                                <v-flex sm2>
                                     <v-select
                                    v-model="cliente.bloqueado"
                                    :items="bloqcli"
                                    label="Bloqueos"
                                    ></v-select>
                                </v-flex>
                                 <v-flex sm2>
                                    <v-switch
                                        label="Prv. Asociado"
                                        v-model="cliente.asociado"
                                        color="primary">
                                    ></v-switch>
                                </v-flex>
                                <v-flex sm2>
                                    <v-switch
                                        label="Exento IVA"
                                        v-model="cliente.exento_iva"
                                        color="primary">
                                    ></v-switch>
                                </v-flex>
                                <v-flex sm2>
                                    <v-switch
                                        label="Facturar"
                                        v-model="cliente.facturar"
                                        color="primary">
                                    ></v-switch>
                                </v-flex>
                                <v-flex sm2>
                                    <v-switch
                                        label="VIP"
                                        v-model="cliente.vip"
                                        color="primary">
                                    ></v-switch>
                                </v-flex>
                                <v-flex sm2>
                                    <v-switch
                                        label="Incluir 347"
                                        v-model="cliente.listar_347"
                                        color="primary">
                                    ></v-switch>
                                </v-flex>
                            </v-layout>
                            <v-layout row wrap>

                                <v-flex sm2>
                                    <v-text-field
                                        v-model="cliente.username"
                                        :error-messages="errors.collect('username')"
                                        label="Usuario"
                                        data-vv-name="username"
                                        readonly
                                        v-on:keyup.enter="submit"
                                    >
                                    </v-text-field>
                                </v-flex>
                                <v-flex sm2>
                                    <v-text-field
                                        v-model="computedFModFormat"
                                        label="Modificado"
                                        readonly
                                    >
                                    </v-text-field>
                                </v-flex>
                                <v-flex sm2>
                                    <v-text-field
                                        v-model="computedFCreFormat"
                                        label="Creado"
                                        readonly
                                    >
                                    </v-text-field>
                                </v-flex>
                                <v-flex sm3></v-flex>
                                <v-flex sm2>
                                    <div class="text-xs-center">
                                                <v-btn @click="submit"  round  :loading="enviando" block  color="primary">
                                        Guardar
                                        </v-btn>
                                    </div>
                                </v-flex>
                            </v-layout>
                        </v-container>
                    </v-form>
                </v-tab-item>
                <v-tab-item>

                </v-tab-item>
                <v-tab-item>

                </v-tab-item>
            </v-tabs>
        </v-card>
	</div>
</template>
<script>
import moment from 'moment'
import MenuOpe from './MenuOpe'

	export default {
		$_veeValidate: {
      		validator: 'new'
        },
        components: {
            'menu-ope': MenuOpe,
		},
    	data () {
      		return {
                titulo:"Cliente/Proveedor",
                cliente: {
                    nombre            :"",
                    apellidos         :"",
                    razon             :"",
                    direccion         :"",
                    cpostal           :"",
                    poblacion         :"",
                    provincia         :"",
                    telefono1         :"",
                    telefono2         :"",
                    tfmovil           :"",
                    email             :"",
                    tipodoc           :"",
                    dni               :"",
                    fecha_nacimiento  :"",
                    fecha_baja        :"",
                    nacpro            :"",
                    nacpais            :"",
                    fecha_dni         :"",
                    notas             :"",
                    bloqueado         :false,
                    exento_iva        :false,
                    facturar          :true,
                    vip               :false,
                    asociado          :false,
                    listar_347        :true,
                    iban              :"",
                    bic               :"",
                    fpago_id          :"",
                    username          :"",
                },

                bloqcli:[
                    {value: 'N', text:"No"},
                    {value: 'A', text:"BlackList"},
                    {value: 'B', text:"Bloqueado"},
                ],

                tiposdoc:[
                    {value: 'N', text:"DNI/NIE"},
                    {value: 'C', text:"CIF"},
                    {value: 'O', text:"Otros"},
                ],

                sino:[
                    {value: 1, text:"Si"}, {value: 0, text:"No"},
                ],
                clientes:[],
                carpetas:[],
                documentos:[],

                fpagos:[],

                cliente_id: "",

        		status: false,
                enviando: false,

                calfbaja:false,
                show: false,
                menu: false,


      		}
        },
        mounted(){
            var id = this.$route.params.id;
            if (id > 0)
                axios.get('/mto/clientes/'+id+'/edit')
                    .then(res => {

                        this.cliente = res.data.cliente;
                        this.fpagos = res.data.fpagos;

                        this.show=true;
                    })
                    .catch(err => {
                        if (err.response.status == 404)
                            this.$toast.error("Cliente No encontrado!");
                        else
                            this.$toast.error(err.response.data.message);
                        this.$router.push({ name: 'cliente.index'})
                    })
        },
        computed: {
            computedFModFormat() {
                moment.locale('es');
                return this.cliente.updated_at ? moment(this.cliente.updated_at).format('D/MM/YYYY H:mm') : '';
            },
            computedFCreFormat() {
                moment.locale('es');
                return this.cliente.created_at ? moment(this.cliente.created_at).format('D/MM/YYYY H:mm') : '';
            },
            computedFechaBaja() {
                moment.locale('es');
                return this.cliente.fecha_baja ? moment(this.cliente.fecha_baja).format('D/MM/YYYY') : '';
            },
            computedFechaDoc() {
                moment.locale('es');
                return this.cliente.fecha_dni ? moment(this.cliente.fecha_dni).format('DD/MM/YYYY') : '';
            },
            computedFN: {
                // getter
                get: function () {
                  moment.locale('es');
                    return this.cliente.fecha_nacimiento ? moment(this.cliente.fecha_nacimiento).format('DD/MM/YYYY') : '';
                },
                // setter
                set: function (newValue) {

                    if (newValue.length == 8){
                            var f = newValue.substring(4,8)+"-"+
                                    newValue.substring(2,4)+"-"+
                                    newValue.substring(0,2);

                        this.cliente.fecha_nacimiento = f;
                    }
                }
            },
            computedFDNI: {
                // getter
                get: function () {
                  moment.locale('es');
                    return this.cliente.fecha_dni ? moment(this.cliente.fecha_dni).format('DD/MM/YYYY') : '';
                },
                // setter
                set: function (newValue) {

                    // if (newValue.length == 8){
                    //         var f = newValue.substring(4,8)+"-"+
                    //                 newValue.substring(2,4)+"-"+
                    //                 newValue.substring(0,2);

                        this.cliente.fecha_dni = null;
                   // }
                }
            },

            rules() {
                return this.cliente.iban > '' ? 'required' : '';
            }
        },
        watch: {
              menu (val) {
                val && setTimeout(() => (this.$refs.picker.activePicker = 'YEAR'))
            }
        },
    	methods:{
            setFechaDoc (date) {

                this.$refs.menu.save(date)
            },
            submit() {

                this.enviando = true;

                var url = "/mto/clientes/"+this.cliente.id;


                // var kk = this.cliente.fecha_nacimiento.substring(4,8)+"-"+
                //          this.cliente.fecha_nacimiento.substring(2,4)+"-"+
                //          this.cliente.fecha_nacimiento.substring(0,2);

                this.$validator.validateAll().then((result) => {
                    if (result){

                        axios.put(url, this.cliente)
                            .then(res => {

                                this.$toast.success(res.data.message);
                                this.cliente = res.data.cliente;
                                this.enviando = false;
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
                                this.enviando = false;
                            });
                        }
                    else{
                        this.enviando = false;
                    }
                });

            },
            clearDate(){
                this.cliente.fecha_baja = null;
            },

            clearDateFD(){
               // this.cliente.fecha_dni = null;

                this.$refs.menu.save(null);
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

.v-form>.container>.layout>.flex {
    padding: 1px 8px 1px 8px;
}
.v-text-field {
    padding-top: 6px;
    margin-top: 2px;
}
</style>
