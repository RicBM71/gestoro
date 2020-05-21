<template>
	<div>
        <loading :show_loading="show_loading"></loading>
        <v-card>
            <v-card-title color="indigo">
                <h2 color="indigo">{{titulo}}</h2>
                <v-spacer></v-spacer>
                <menu-ope :id="cliente.id"></menu-ope>
                <v-tooltip bottom v-if="!docu_ok">
                    <template v-slot:activator="{ on }">
                        <v-btn
                            v-on="on"
                            color="white"
                            icon
                            @click="scanDocu()"
                        >
                            <v-icon color="primary">add_a_photo</v-icon>
                        </v-btn>
                    </template>
                    <span>Aportar documetación DNI/NIE/Pasaporte</span>
                </v-tooltip>
            </v-card-title>
        </v-card>
        <v-card>
            <v-tabs fixed-tabs>
                <v-tab>
                        Datos generales
                </v-tab>
                <v-tab>
                        Documentación
                </v-tab>
                <v-tab>
                        Compras
                </v-tab>
                <v-tab>
                        Ventas
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
                                    :disabled="computedDisabled"
                                    ></v-select>
                                </v-flex>

                                <v-flex sm2>
                                    <v-text-field
                                        v-model="cliente.dni"
                                        v-validate="'required|alpha_num|min:4'"
                                        :error-messages="errors.collect('dni')"
                                        label="Nº Documento"
                                        required
                                        data-vv-name="dni"
                                        data-vv-as="Documento"
                                        :disabled="computedDisabled"
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
                                        :disabled="computedDisabled"
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
                                        :disabled="computedDisabled"
                                        v-on:keyup.enter="submit"
                                    >
                                    </v-text-field>
                                </v-flex>
                                <v-flex sm2>
                                    <v-text-field
                                        v-model="computedFDNI"
                                        mask="##/##/####"
                                        clearable
                                        @click:clear="clearDateFD"
                                        :error-messages="errors.collect('fecha_dni')"
                                        label="F. Validez"
                                        data-vv-name="fecha_dni"
                                        :disabled="computedDisabled"
                                        v-on:keyup.enter="submit"                                    >
                                    </v-text-field>
                                </v-flex>
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
                                        :disabled="computedDisabled"
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
                                        :disabled="computedDisabled"
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
                                        :disabled="computedDisabled"
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
                                        :disabled="computedDisabled"
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
                                        :disabled="computedDisabled"
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
                                        :disabled="computedDisabled"
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
                                        :disabled="computedDisabled"
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
                                        :disabled="computedDisabled"
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
                                        :disabled="computedDisabled"
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
                                        :disabled="computedDisabled"
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
                                        :disabled="computedDisabled"
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
                                        :disabled="computedDisabled"
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
                                        :disabled="computedDisabled"
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
                                        :disabled="computedDisabled"
                                        v-on:keyup.enter="submit"
                                    >
                                    </v-text-field>
                                </v-flex>
                                <v-flex sm3 d-flex>
                                    <v-select
                                    v-model="cliente.fpago_id"
                                    :items="fpagos"
                                    label="Forma de Pago"
                                    :disabled="computedDisabled"
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
                                        :disabled="computedDisabled"
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
                                        :disabled="!isSupervisor"
                                    ></v-select>
                                </v-flex>
                                 <v-flex sm2>
                                    <v-switch
                                        label="Prv. Asociado"
                                        v-model="cliente.asociado"
                                        color="primary"
                                        :disabled="!isAdmin">

                                    ></v-switch>
                                </v-flex>
                                <v-flex sm2>
                                    <v-switch
                                        label="Exento IVA"
                                        v-model="cliente.iva_no_residente"
                                        color="primary"
                                        :disabled="!isAdmin">
                                    ></v-switch>
                                </v-flex>
                                <v-flex sm2>
                                    <v-switch
                                        label="Facturar"
                                        v-model="cliente.facturar"
                                        color="primary"
                                        :disabled="!isAdmin">
                                    ></v-switch>
                                </v-flex>
                                <v-flex sm2>
                                    <v-switch
                                        label="VIP"
                                        v-model="cliente.vip"
                                        color="primary"
                                        :disabled="!isAdmin">
                                    ></v-switch>
                                </v-flex>
                                <v-flex sm2>
                                    <v-switch
                                        label="Incluir 347"
                                        v-model="cliente.listar_347"
                                        color="primary"
                                        :disabled="!isAdmin">
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
                                                <v-btn
                                                    :disabled="computedDisabled"
                                                    @click="submit"
                                                    round
                                                    :loading="loading" block  color="primary">
                                        Guardar
                                        </v-btn>
                                    </div>
                                </v-flex>
                            </v-layout>
                        </v-container>
                    </v-form>
                </v-tab-item>
                <v-tab-item>

                    <v-container grid-list-md text-xs-center>
                        <v-layout row wrap v-if="show_docu">
                            <v-flex sm1></v-flex>
                            <v-flex sm5 v-if="img_anverso!=false">
                                <v-img :src="img_anverso"/>
                            </v-flex>
                            <v-flex sm5 v-if="img_reverso!=false">
                                <v-img :src="img_reverso"/>
                            </v-flex>
                        </v-layout>
                        <v-layout row wrap v-if="show_docu">
                            <v-flex sm12>
                                <div class="text-xs-center">
                                    <v-btn
                                        round
                                        @click="goDestroyDocu()"
                                        v-if="puedeBorrar"
                                        color="red darken-4"
                                        dark> <v-icon>delete_forever</v-icon>Eliminar Documentos</v-btn>
                                </div>
                            </v-flex>
                        </v-layout>
                    </v-container>
                </v-tab-item>
                <v-tab-item>
                    <compras-cli v-if="cliente.id>0" :cliente_id="cliente.id"></compras-cli>
                </v-tab-item>
                <v-tab-item>
                    <ventas-cli v-if="cliente.id>0" :cliente_id="cliente.id"></ventas-cli>
                </v-tab-item>
            </v-tabs>
        </v-card>
	</div>
</template>
<script>
import Loading from '@/components/shared/Loading'
import moment from 'moment'
import MenuOpe from './MenuOpe'
import ComprasCli from './ComprasCli'
import VentasCli from './VentasCli'
import {mapGetters} from 'vuex';
	export default {
		$_veeValidate: {
      		validator: 'new'
        },
        components: {
            'menu-ope': MenuOpe,
            'loading': Loading,
            'compras-cli': ComprasCli,
            'ventas-cli': VentasCli
        },
    	data () {
      		return {
                titulo:"Cliente",
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
                    iva_no_residente  :false,
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
                    {value: 'L', text:"BlackList"},
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
                loading: false,

                calfbaja:false,
                show_loading: false,
                show_docu: false,
                show: false,

                clidoc_id: false,
                img_anverso: false,
                img_reverso: false,
                docu_ok: false,
                puedeBorrar: false

      		}
        },
        mounted(){

            this.show_loading = true;
            var id = this.$route.params.id;

            // if (!this.isSupervisor)
            //     this.$router.push({ name: 'cliente.show', params: { id: id } })


            if (id > 0)
                axios.get('/mto/clientes/'+id+'/edit')
                    .then(res => {

                        this.cliente = res.data.cliente;

                        if ( res.data.documentos != false){

                            this.docu_ok = res.data.documentos.status > 0 ? true : false;
                            this.clidoc_id = res.data.documentos.id;
                            this.img_anverso = res.data.documentos.img1;
                            this.img_reverso = res.data.documentos.img2;
                            this.puedeBorrar = res.data.documentos.delete;
                        }

                        this.fpagos = res.data.fpagos;

                        if (this.clidoc_id > 0)
                            this.show_docu = true;

                        this.show_loading = false;
                        this.show = true;


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
            ...mapGetters([
                'isSupervisor',
                'isAdmin'
            ]),
            computedDisabled(){
                if (this.cliente.dni.length <= 4)
                    return !this.isAdmin;
                else
                    return false;
            },
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

                    if (newValue.length == 8){
                            var f = newValue.substring(4,8)+"-"+
                                    newValue.substring(2,4)+"-"+
                                    newValue.substring(0,2);

                        this.cliente.fecha_dni = f;
                    }
                }
            },

            rules() {
                return this.cliente.iban > '' ? 'required' : '';
            }
        },
    	methods:{
            submit() {

                if (this.loading === false){
                    this.loading = this.show_loading = true;

                    var url = "/mto/clientes/"+this.cliente.id;

                    this.$validator.validateAll().then((result) => {
                        if (result){

                            axios.put(url, this.cliente)
                                .then(res => {

                                    this.$toast.success(res.data.message);
                                    this.cliente = res.data.cliente;

                                    this.$validator.errors.clear();

                                    this.loading = this.show_loading = false;
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
                                    this.loading = this.show_loading = false;
                                });
                            }
                        else{
                            this.loading = this.show_loading = false;
                        }
                    });
                }


            },
            scanDocu(){
                this.$router.push({ name: 'clidoc.create', params: { cliente_id: this.cliente.id, compra_id: 0 } })
            },
            goDestroyDocu(){
                if (confirm('Borrar documetación')){

                    axios.post('/mto/clidocs/'+this.clidoc_id,{_method: 'delete'})
                        .then(res => {
                            this.docu_ok = false;
                            this.show_docu = false;
                            this.img_anverso = this.img_reverso = null;
                            this.clidoc_id = false;
                            this.$toast.success('Documetanción eliminada!');

                    })
                    .catch(err => {
                        this.status = true;
                        var msg = err.response.data.message;
                        this.$toast.error(msg);

                    });

                }
            },
            clearDate(){
                this.cliente.fecha_baja = null;
            },
            clearDateFD(){
                this.cliente.fecha_dni = null;

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
