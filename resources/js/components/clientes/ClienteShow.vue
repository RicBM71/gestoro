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
                        Compras
                </v-tab>
                <v-tab>
                        Ventas
                </v-tab>
                <v-tab-item>
                    <v-form>
                        <v-container>
                            <v-layout row wrap>
                                <v-flex sm2>
                                    <v-text-field
                                        v-model="cliente.dni"
                                        v-validate="'required|min:4'"
                                        :error-messages="errors.collect('dni')"
                                        label="Nº Documento"
                                        required
                                        data-vv-name="dni"
                                        data-vv-as="Documento"
                                        readonly
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
                                        readonly
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
                                        readonly
                                    >
                                    </v-text-field>
                                </v-flex>
                                <v-flex sm2>
                                    <v-text-field
                                        v-model="computedFDNI"
                                        :error-messages="errors.collect('fecha_dni')"
                                        label="F. Validez"
                                        data-vv-name="fecha_dni"
                                        readonly                                    >
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
                                        readonly
                                    >
                                    </v-text-field>
                                </v-flex>

                                <v-flex sm2>
                                    <v-text-field
                                        v-model="computedFN"
                                        label="F. Nacimiento"
                                        readonly
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
                                        readonly
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
                                        readonly
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
                                        readonly
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
                                        readonly
                                    >
                                    </v-text-field>
                                </v-flex>
                                <v-flex sm1>
                                    <v-text-field
                                        v-model="cliente.cpostal"
                                        :error-messages="errors.collect('cpostal')"
                                        label="CP"
                                        data-vv-name="CP"
                                        readonly
                                    >
                                    </v-text-field>
                                </v-flex>
                                <v-flex sm3>
                                    <v-text-field
                                        v-model="cliente.poblacion"
                                        :error-messages="errors.collect('poblacion')"
                                        label="Población"
                                        data-vv-name="poblacion"
                                        readonly
                                    >
                                    </v-text-field>
                                </v-flex>
                                <v-flex sm3>
                                    <v-text-field
                                        v-model="cliente.provincia"
                                        :error-messages="errors.collect('provincia')"
                                        label="Provincia"
                                        data-vv-name="provincia"
                                        readonly
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
                                        readonly
                                    >
                                    </v-text-field>
                                </v-flex>
                                <v-flex sm4>
                                    <v-text-field
                                        v-model="cliente.nacpro"
                                        :error-messages="errors.collect('nacpro')"
                                        label="Provincia Nac."
                                        data-vv-name="nacpro"
                                        readonly
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
                                        readonly
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
                                        mask="AA## #### **** **** **** *###"
                                        counter=24
                                        data-vv-name="iban"
                                        readonly
                                    >
                                    </v-text-field>
                                </v-flex>
                                <v-flex sm3>
                                    <v-text-field
                                        v-model="cliente.bic"
                                        :error-messages="errors.collect('bic')"
                                        label="BIC"
                                        counter=11
                                        data-vv-name="bic"
                                        readonly
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
                            </v-layout>
                            <v-layout row wrap>
                                <v-flex sm12>
                                    <v-text-field
                                        v-model="cliente.notas"
                                        :error-messages="errors.collect('notas1')"
                                        label="Notas"
                                        data-vv-name="notas1"
                                        readonly
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
                                        disabled
                                    ></v-select>
                                </v-flex>
                                <v-flex sm2>
                                    <v-text-field
                                        v-model="cliente.username"
                                        :error-messages="errors.collect('username')"
                                        label="Usuario"
                                        data-vv-name="username"
                                        readonly
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
                            </v-layout>
                        </v-container>
                    </v-form>
                </v-tab-item>
                <v-tab-item>
                    <compras-cli v-if="cliente.id>0" :cliente_id="cliente.id"></compras-cli>
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
import AlbCliente from './ClienteAlbaran'
import DocuCli from './ClienteDocumento'
import ComprasCli from './ComprasCli'

	export default {
		$_veeValidate: {
      		validator: 'new'
        },
        components: {
            'menu-ope': MenuOpe,
            'alb-cliente': AlbCliente,
            'compras-cli': ComprasCli,
            'docu-cli': DocuCli
		},
    	data () {
      		return {
                titulo:"Cliente",
                cliente: {
                    id:0,
                    empresa_id:"",
                    razon:"",
                    nombre:"",
                    direccion:"",
                    cpostal:"",
                    poblacion:"",
                    provincia:"",
                    telefono1:"",
                    telefono2:"",
                    tfmovil:"",
                    email:"",
                    contacto:"",
                    cif:"",
                    fechabaja:"",
                    web:"",
                    carpeta_id:"",
                    patron:"",
                    notas1:"",
                    efact:"",
                    eusr:"",
                    epass:"",
                    egrupo:"",
                    fpago_id:"",
                    factura:"",
                    iban:"",
                    bic:"",
                    ref19:"",
                    username: "",
                    updated_at:"",
                    created_at:"",
                },

                sino:[
                    {value: 1, text:"Si"}, {value: 0, text:"No"},
                ],
                bloqcli:[
                    {value: 'N', text:"No"},
                    {value: 'L', text:"BlackList"},
                    {value: 'B', text:"Bloqueado"},
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


      		}
        },
        mounted(){
            var id = this.$route.params.id;

            if (id > 0)
                axios.get('/mto/clientes/'+id)
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
            computedFDNI(){
                moment.locale('es');
                return this.cliente.fecha_dni ? moment(this.cliente.fecha_dni).format('D/MM/YYYY') : '';
            },
            computedFN(){
                moment.locale('es');
                return this.cliente.fecha_nacimiento ? moment(this.cliente.fecha_nacimiento).format('D/MM/YYYY') : '';
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
                return this.cliente.fechabaja ? moment(this.cliente.fechabaja).format('D/MM/YYYY') : '';
            },
        },
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
