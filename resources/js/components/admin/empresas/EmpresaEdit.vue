<template>
	<div>
        <v-card>
            <v-card-title color="indigo">
                <h2 color="indigo">{{titulo}}</h2>
                <v-spacer></v-spacer>
                <menu-ope :id="empresa.id"></menu-ope>
            </v-card-title>
        </v-card>
        <v-card>
            <v-tabs fixed-tabs>
                <v-tab>
                        Datos generales
                </v-tab>
                <v-tab>
                        Logos
                </v-tab>
                <v-tab>
                        Permisos
                </v-tab>
                <v-tab-item>
                        <v-form>
                            <v-container>
                                <v-layout row wrap>
                                    <v-flex sm3>
                                        <v-text-field
                                            v-model="empresa.nombre"
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
                                    <v-flex sm3>
                                        <v-text-field
                                            v-model="empresa.razon"
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
                                            v-model="empresa.cif"
                                            :error-messages="errors.collect('cif')"
                                            label="CIF"
                                            data-vv-name="cif"
                                            v-on:keyup.enter="submit"
                                        >
                                        </v-text-field>
                                    </v-flex>
                                    <v-flex sm2>
                                        <v-text-field
                                            v-model="empresa.telefono1"
                                            :error-messages="errors.collect('telefono1')"
                                            label="Telefono"
                                            data-vv-name="telefono1"
                                            data-vv-as="Teléfono"
                                            v-on:keyup.enter="submit"
                                        >
                                        </v-text-field>
                                    </v-flex>
                                    <v-flex sm2>
                                        <v-text-field
                                            v-model="empresa.telefono2"
                                            :error-messages="errors.collect('telefono2')"
                                            label="Telefono"
                                            data-vv-name="telefono2"
                                            data-vv-as="Teléfono"
                                            v-on:keyup.enter="submit"
                                        >
                                        </v-text-field>
                                    </v-flex>
                                </v-layout>
                                <v-layout row wrap>
                                    <v-flex sm3>
                                        <v-text-field
                                            v-model="empresa.direccion"
                                            :error-messages="errors.collect('direccion')"
                                            label="Dirección"
                                            data-vv-name="direccion"
                                            v-on:keyup.enter="submit"
                                        >
                                        </v-text-field>
                                    </v-flex>
                                    <v-flex sm1>
                                        <v-text-field
                                            v-model="empresa.cpostal"
                                            :error-messages="errors.collect('cpostal')"
                                            label="CP"
                                            data-vv-name="CP"
                                            v-on:keyup.enter="submit"
                                        >
                                        </v-text-field>
                                    </v-flex>
                                    <v-flex sm3>
                                        <v-text-field
                                            v-model="empresa.poblacion"
                                            :error-messages="errors.collect('poblacion')"
                                            label="Población"
                                            data-vv-name="poblacion"
                                            v-on:keyup.enter="submit"
                                        >
                                        </v-text-field>
                                    </v-flex>
                                    <v-flex sm3>
                                        <v-text-field
                                            v-model="empresa.provincia"
                                            :error-messages="errors.collect('provincia')"
                                            label="Provincia"
                                            data-vv-name="provincia"
                                            v-on:keyup.enter="submit"
                                        >
                                        </v-text-field>
                                    </v-flex>
                                    <v-flex sm2>
                                        <v-text-field
                                            v-model="empresa.sigla"
                                            :error-messages="errors.collect('sigla')"
                                            label="Sigla"
                                            data-vv-name="sigla"
                                            v-on:keyup.enter="submit"
                                        >
                                        </v-text-field>
                                    </v-flex>
                                </v-layout>
                                <v-layout row wrap>
                                    <v-flex sm3>
                                        <v-text-field
                                            v-model="empresa.titulo"
                                            v-validate="'required'"
                                            :error-messages="errors.collect('titulo')"
                                            label="Tíitulo"
                                            data-vv-name="titulo"
                                            v-on:keyup.enter="submit"
                                        >
                                        </v-text-field>
                                    </v-flex>
                                    <v-flex sm3>
                                        <v-text-field
                                            v-model="empresa.contacto"
                                            :error-messages="errors.collect('contacto')"
                                            label="Contacto"
                                            data-vv-name="contacto"
                                            v-on:keyup.enter="submit"
                                        >
                                        </v-text-field>
                                    </v-flex>
                                    <v-flex sm3>
                                        <v-text-field
                                            v-model="empresa.email"
                                            v-validate="'email'"
                                            :error-messages="errors.collect('email')"
                                            label="email"
                                            data-vv-name="email"
                                            v-on:keyup.enter="submit"
                                        >
                                        </v-text-field>
                                    </v-flex>
                                    <v-flex sm3>
                                        <v-text-field
                                            v-model="empresa.web"
                                            :error-messages="errors.collect('web')"
                                            label="Web"
                                            data-vv-name="web"
                                            v-on:keyup.enter="submit"
                                        >
                                        </v-text-field>
                                    </v-flex>
                                </v-layout>
                                <v-layout row wrap v-if="isRoot">
                                    <v-flex sm2>
                                        <v-menu
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
                                                :value="computedDateFormat"
                                                clearable
                                                label="Fecha Scaneo"
                                                prepend-icon="event"
                                                readonly
                                                data-vv-as="F. Scaneo"
                                                @click:clear="clearDate"
                                                ></v-text-field>
                                            <v-date-picker
                                                v-model="empresa.scan_doc"
                                                no-title
                                                locale="es"
                                                first-day-of-week=1
                                                @input="menu = false"
                                            ></v-date-picker>
                                        </v-menu>
                                    </v-flex>
                                    <v-flex sm3 d-flex>
                                        <v-select
                                            :readonly="!isRoot"
                                            v-validate="'required'"
                                            v-model="empresa.deposito_empresa_id"
                                            :error-messages="errors.collect('deposito_empresa_id')"
                                            data-vv-name="deposito_empresa_id"
                                            data-vv-as="empresa"
                                            :items="empresas"
                                            label="Empresa Depósitos"
                                            :disabled="!isRoot"
                                        ></v-select>
                                    </v-flex>
                                    <v-flex sm3 d-flex>
                                        <v-select
                                            :readonly="!isRoot"
                                            v-model="empresa.comun_empresa_id"
                                            v-validate="'required'"
                                            :error-messages="errors.collect('comun_empresa_id')"
                                            data-vv-name="comun_empresa_id"
                                            data-vv-as="empresa"
                                            :items="empresas"
                                            label="Empresa Común"
                                            :disabled="!isRoot"
                                        ></v-select>
                                    </v-flex>
                                </v-layout>
                                <v-layout row wrap>
                                    <v-flex sm12>
                                        <v-text-field
                                            v-model="empresa.txtpie1"
                                            :error-messages="errors.collect('txtpie1')"
                                            label="Pie"
                                            data-vv-name="txtpie1"
                                            v-on:keyup.enter="submit"
                                        >
                                        </v-text-field>
                                    </v-flex>
                                </v-layout>
                                <v-layout row wrap>
                                    <v-flex sm12>
                                        <v-text-field
                                            v-model="empresa.txtpie2"
                                            :error-messages="errors.collect('txtpie2')"
                                            label="Pie"
                                            data-vv-name="txtpie2"
                                            v-on:keyup.enter="submit"
                                        >
                                        </v-text-field>
                                    </v-flex>

                                </v-layout>
                                <v-layout row wrap>
                                    <v-flex sm3 d-flex>
                                        <v-select
                                        v-model="empresa.almacen_id"
                                        v-validate="'numeric'"
                                        :error-messages="errors.collect('almacen_id')"
                                        data-vv-name="almacen_id"
                                        data-vv-as="almacén"
                                        :items="almacenes"
                                        label="Ubicación"
                                        required
                                        ></v-select>
                                    </v-flex>
                                    <v-flex sm2>
                                        <v-text-field
                                            v-model="empresa.username"
                                            label="Usuario"
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
                                    <v-flex sm5>
                                    </v-flex>
                                    <v-flex sm2>
                                        <div class="text-xs-center">
                                                    <v-btn @click="submit"  round  :loading="loading" block  color="primary">
                                            Guardar
                                            </v-btn>
                                        </div>
                                    </v-flex>
                                </v-layout>
                            </v-container>
                        </v-form>
                    </v-tab-item>
                    <v-tab-item>
                        <v-container>
                            <v-layout row wrap>
                                <v-flex sm12 class="font-weight-black text-xs-center"><p>{{empresa.nombre}}</p></v-flex>
                            </v-layout>
                            <v-layout row wrap>
                                <v-flex sm2></v-flex>
                                <v-flex sm3 v-if="empresa.img_logo==null">
                                    <vue-dropzone
                                            ref="myVueDropzone"
                                            id="dropzone"
                                            :options="dropzoneOptions"
                                            v-on:vdropzone-success="uploadLogo"
                                    ></vue-dropzone>
                                </v-flex>
                                <v-flex sm3 v-else>
                                    <v-img class="img-fluid" :src="empresa.img_logo"></v-img>
                                    <v-btn @click="borraLogo" flat round><v-icon color="red darken-4">delete</v-icon> Eliminar Logo</v-btn>
                                </v-flex>
                                <v-flex sm1></v-flex>
                                <v-flex sm3 v-if="empresa.img_fondo==null">
                                    <vue-dropzone
                                            ref="myVueDropzone2"
                                            id="dropzone2"
                                            :options="dropzoneOptions2"
                                            v-on:vdropzone-success="uploadFondo"
                                    ></vue-dropzone>
                                </v-flex>
                                <v-flex sm3 v-else>
                                    <v-img class="img-fluid" :src="empresa.img_fondo"></v-img>
                                    <v-btn @click="borraFondo" flat round><v-icon color="red darken-4">delete</v-icon> Eliminar Fondo</v-btn>
                                </v-flex>
                            </v-layout>
                        </v-container>
                    </v-tab-item>
                    <v-tab-item>
                        <v-container>
                            <v-layout row wrap>
                                <v-flex sm12 class="font-weight-black text-xs-center"><p>{{empresa.nombre}}</p></v-flex>
                            </v-layout>
                            <v-layout row wrap>
                                <v-flex sm3>
                                    <v-switch
                                        label="Activa"
                                        v-model="sw[0]"
                                        color="primary">
                                        @change="estado"
                                    ></v-switch>
                                </v-flex>
                                <v-flex sm3>
                                    <v-switch
                                        label="Compras"
                                        v-model="sw[1]"
                                        color="primary">
                                    ></v-switch>
                                </v-flex>
                                <v-flex sm3>
                                    <v-switch
                                        label="Ventas"
                                        v-model="sw[2]"
                                        color="primary">
                                    ></v-switch>
                                </v-flex>
                                <v-flex sm3>
                                    <v-switch
                                        label="Nuevas Compras"
                                        v-model="sw[3]"
                                        color="primary">
                                    ></v-switch>
                                </v-flex>
                                <v-flex sm3>
                                    <v-switch
                                        label="Nuevas Ventas"
                                        v-model="sw[4]"
                                        color="primary">
                                    ></v-switch>
                                </v-flex>
                                <v-flex sm3>
                                    <v-switch
                                        label="Proveedora Efectivo"
                                        v-model="sw[5]"
                                        color="primary">
                                    ></v-switch>
                                </v-flex>
                                <v-flex sm3>
                                    <v-switch
                                        label="Bloquear Facturación"
                                        v-model="sw[6]"
                                        color="primary">
                                    ></v-switch>
                                </v-flex>
                            </v-layout>
                            <v-layout row wrap>
                                   <v-flex sm5></v-flex>
                                   <v-flex sm2>
                                        <div class="text-xs-center">
                                                    <v-btn @click="submit"  round  :loading="loading" block  color="primary">
                                            Guardar
                                            </v-btn>
                                        </div>
                                    </v-flex>
                            </v-layout>
                        </v-container>
                    </v-tab-item>
                </v-tabs>

            </v-card>
	</div>
</template>
<script>
import moment from 'moment'
import MenuOpe from './MenuOpe'
import vue2Dropzone from 'vue2-dropzone'
import {mapGetters} from 'vuex';
import 'vue2-dropzone/dist/vue2Dropzone.min.css'
	export default {
		$_veeValidate: {
      		validator: 'new'
        },
        components: {
            'menu-ope': MenuOpe,
            'vueDropzone': vue2Dropzone,
		},
    	data () {
      		return {
                titulo:"Empresas",
                empresa: {
                    id: 0,
                    nombre: "",
                    razon: "",
                    cif: "",
                    poblacion: "",
                    direccion: "",
                    cpostal: "",
                    provincia: "",
                    telefono1: "",
                    telefono2: "",
                    contacto: "",
                    email: "",
                    web: "",
                    txtpie1: "",
                    txtpie2: "",
                    flags: "",
                    sigla: "",
                    titulo: "",
                    img_logo:"",
                    img_fondo:"",
                    certificado:"",
                    passwd_cer:"",
                    almacen_id:"",
                    scan_doc:"",
                    comun_empresa_id:"",
                    username: "",
                    updated_at:"",
                    created_at:"",
                },
                sw:[] ,
                almacenes:[],
                empresas:[],
                empresa_id: "",

        		status: false,
                loading: false,

                show: false,
                menu: false,
                dropzoneOptions: {
                    url: '/admin/empresas/'+this.$route.params.id+'/logo',
                    paramName: 'logo',
                    acceptedFiles: '.jpg,jpeg,.png',
                    thumbnailWidth: 150,
                    maxFiles: 1,
                    maxFilesize: 2,
                    headers: {
		    		    'X-CSRF-TOKEN':  window.axios.defaults.headers.common['X-CSRF-TOKEN']
                    },
                    dictDefaultMessage: 'Arrastra la imagen LOGOTIPO aquí'
                },
                dropzoneOptions2: {
                    url: '/admin/empresas/'+this.$route.params.id+'/fondo',
                    paramName: 'logo',
                    acceptedFiles: 'image/*',
                    thumbnailWidth: 150,
                    maxFiles: 1,
                    maxFilesize: 2,
                    headers: {
		    		    'X-CSRF-TOKEN':  window.axios.defaults.headers.common['X-CSRF-TOKEN']
                    },
                    dictDefaultMessage: 'Arrastra la imagen FONDO PANTALLA aquí'
                }

      		}
        },
        mounted(){
            var id = this.$route.params.id;

            if (id > 0)
                axios.get('/admin/empresas/'+id+'/edit')
                    .then(res => {

                        this.empresa = res.data.empresa;

                        this.almacenes = res.data.almacenes;
                        this.empresas = res.data.empresas;

                        // var a  = Array.from(this.empresa.flags);

                        // this.sw = a.map(function (x) {
                        //     return parseInt(x, 10);
                        // });
                        this.getFlags(this.empresa.flags);




                    })
                    .catch(err => {
                        this.$toast.error(err.response.data.message);
                        this.$router.push({ name: 'empresa.index'})
                    })
        },
        computed: {
            ...mapGetters([
                'isRoot'
            ]),
            computedDateFormat() {
                moment.locale('es');
                return this.empresa.scan_doc ? moment(this.empresa.scan_doc).format('L') : '';
            },
            computedFModFormat() {
                moment.locale('es');
                return this.empresa.updated_at ? moment(this.empresa.updated_at).format('D/MM/YYYY H:mm') : '';
            },
            computedFCreFormat() {
                moment.locale('es');
                return this.empresa.created_at ? moment(this.empresa.created_at).format('D/MM/YYYY H:mm') : '';
            }

        },
    	methods:{
            getFlags(flags){

                    var a  = Array.from(flags);
                    this.sw = a.map(function (x) {
                        return parseInt(x);
                    });

            },
            setFlags(){

                var a = this.sw.toString();

                a = a.replace(/false/g,'0');
                a = a.replace(/true/g,'1');
                a = a.replace(/,/g,'');

                this.empresa.flags = a;
            },
            clearDate(){
                this.empresa.scan_doc = null;
            },
            uploadLogo(file, response){
                this.empresa = response.empresa;
            },
            uploadFondo(file, response){
                this.empresa = response.empresa;
            },
            borraLogo(){
                axios.put('/admin/empresas/'+this.empresa.id+'/logo/delete')
                    .then(response => {
                        this.empresa = response.data.empresa;
                        this.loading = false;
                    })
            },
            borraFondo(){
                axios.put('/admin/empresas/'+this.empresa.id+'/fondo/delete')
                    .then(response => {
                        this.empresa = response.data.empresa;
                        this.loading = false;
                    })
            },
            submit() {
                if (this.loading === false){

                    this.loading = true;
                    this.setFlags();

                    var url = "/admin/empresas/"+this.empresa.id;

                    this.$validator.validateAll().then((result) => {
                        if (result){
                            axios.put(url, this.empresa)
                                .then(response => {
                                    this.$toast.success(response.data.message);
                                    this.empresa = response.data.empresa;
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
                        else{
                            this.loading = false;
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
