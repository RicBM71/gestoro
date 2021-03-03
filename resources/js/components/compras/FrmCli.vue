<template>
    <div>
        <v-form>
            <v-container>
                <v-layout row wrap>
                    <v-flex sm1 d-flex>
                        <v-select
                        v-model="cliente.tipodoc"
                         v-validate="'required'"
                        :error-messages="errors.collect('tipodoc')"
                        data-vv-name="tipodoc"
                        data-vv-as="documento"
                        :items="tiposdoc"
                        label="Documento"
                        readonly
                        ></v-select>
                    </v-flex>

                    <v-flex sm2>
                        <v-text-field
                            v-model="cliente.dni"
                            v-validate="'required|min:5'"
                            :error-messages="errors.collect('dni')"
                            label="Nº Documento"
                            readonly
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
                        <v-text-field
                            v-model="computedFDNI"
                            mask="##/##/####"
                            :error-messages="errors.collect('fecha_dni')"
                            v-validate="'required|date_format:dd/MM/yyyy'"
                            label="F. Documento"
                            data-vv-name="fecha_dni"
                            data-vv-as="Fecha"
                            hint="Fecha validez del documento"
                            v-on:keyup.enter="submit"                                    >
                        </v-text-field>
                    </v-flex>
                </v-layout>
                <v-layout row wrap>
                    <v-flex sm2>
                        <v-text-field
                            v-model="computedFN"
                            v-validate="'required|date_format:dd/MM/yyyy'"
                            mask="##/##/####"
                            :error-messages="errors.collect('fecha_nacimiento')"
                            label="F. Nacimiento"
                            data-vv-name="fecha_nacimiento"
                            data-vv-as="F. Nac."
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
                    <v-flex sm5>
                        <v-text-field
                            v-model="cliente.direccion"
                            v-validate="'required'"
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
                            v-validate="'max:10'"
                            :error-messages="errors.collect('cpostal')"
                            label="CP"
                            data-vv-name="cpostal"
                            data-vv-as="C.P."
                            v-on:keyup.enter="submit"
                        >
                        </v-text-field>
                    </v-flex>
                    <v-flex sm3>
                        <v-text-field
                            v-model="cliente.poblacion"
                            :error-messages="errors.collect('poblacion')"
                            v-validate="'required'"
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
                            v-validate="'required'"
                            label="Provincia"
                            data-vv-name="provincia"
                            v-on:keyup.enter="submit"
                        >
                        </v-text-field>
                    </v-flex>
                </v-layout>
                <v-layout row wrap>
                    <v-flex sm3>
                        <v-text-field
                            v-model="cliente.nacpais"
                            :error-messages="errors.collect('nacpais')"
                            v-validate="'required'"
                            label="Nacido en"
                            hint="Pais de nacimiento"
                            data-vv-name="nacpais"
                            data-vv-as="País"
                            v-on:keyup.enter="submit"
                        >
                        </v-text-field>
                    </v-flex>
                    <v-flex sm3>
                        <v-text-field
                            v-model="cliente.nacpro"
                            :error-messages="errors.collect('nacpro')"
                            v-validate="'required'"
                            label="Provincia Nac."
                            hint="Provincia, estado"
                            data-vv-name="nacpro"
                            data-vv-as="Provincia"
                            v-on:keyup.enter="submit"
                        >
                        </v-text-field>
                    </v-flex>
                    <v-flex sm6>
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
                <v-layout row wrap v-if="hasEdtCli">
                    <v-flex sm1>
                        <v-text-field
                            class="inputPrice"
                            v-model="cliente.interes"
                            v-validate="'required'"
                            :error-messages="errors.collect('interes')"
                            data-vv-name="interes"
                            data-vv-as="interés"
                            label="Interés Renovación"
                            :disabled="!hasEdtCli"
                            v-on:keyup.enter="submit"
                        >
                        </v-text-field>
                    </v-flex>
                    <v-flex sm1>
                        <v-text-field
                            v-if="parametros.doble_interes"
                            class="inputPrice"
                            v-model="cliente.interes_recuperacion"
                            v-validate="'required'"
                            data-vv-name="interes_recuperacion"
                            data-vv-as="interés"
                            :error-messages="errors.collect('doble_interes')"
                            label="Interés Recuperación"
                            :disabled="!hasEdtCli"
                            v-on:keyup.enter="submit"
                        >
                        </v-text-field>
                    </v-flex>
                </v-layout>
                <v-layout row wrap>
                    <v-flex sm8></v-flex>
                    <v-flex sm2>
                        <div class="text-xs-center">
                            <v-btn
                                @click="atras()"  round  flat  color="primary">
                                Atrás
                            </v-btn>
                        </div>
                    </v-flex>

                    <v-flex sm2>
                        <div class="text-xs-center">
                            <v-btn @click="submit"  round  :loading="loading" block  color="primary">
                                Siguiente
                            </v-btn>
                        </div>
                    </v-flex>
                </v-layout>
            </v-container>
        </v-form>
    </div>
</template>
<script>
import {mapGetters} from 'vuex';
import moment from 'moment'
export default {
    $_veeValidate: {
        validator: 'new'
    },
    props:{
        miCliente: Object,
        fase_valid: Number,
    },
    components: {

    },
    data () {
      return {
        dialog: false,
        ruta: "compra",
        cliente: {
                id                :0,
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
                bloqueado         :"N",
                iva_no_residente  : false,
                interes: 0,
                interes_recuperacion:0
            },
            tiposdoc:[
                    {value: 'N', text:"DNI/NIE"},
                    {value: 'O', text:"Otros"},
                ],
            loading: false,
            result: false,
            validBlackList: false
      }
    },
    mounted(){

        if (this.miCliente.id > 0){
            this.cliente = this.miCliente;
            if (this.cliente.bloqueado == "B"){
                this.$emit('update:fase_valid', 1);
                this.$toast.error("El cliente está bloqueado, contactar con un administrador.");
            }
            if (this.cliente.fecha_baja != null){
                this.$emit('update:fase_valid', 1);
                this.$toast.error("El cliente está dado de baja, contactar con un administrador.");
            }
        }
        else{
            this.cliente.dni = this.miCliente.dni;
            this.cliente.tipodoc = this.miCliente.tipodoc;
        }
    },
    watch: {
        cliente: function () {
            if (!this.validBlackList && this.cliente.nombre != "" && this.cliente.apellidos != ""){
                this.validBlackList = true;
                this.blacklist();
            }

        },
    },
    computed: {
        ...mapGetters([
            'hasEdtCli',
            'parametros'
        ]),
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
        }
    },
    methods:{
        atras(){
            this.$emit('update:fase_valid', 1);
        },
        submit(){

            this.blacklist();

            if (this.cliente.id > 0)
                this.goEditar();
            else
                this.goCrear();
        },
        blacklist(){
            axios.post('/utilidades/helpcli/blacklist', this.cliente)
                .then(res => {
                    this.$toast.error("Cliente en lista de nombres conflictivos, verificar autenticidad.");

                })
                .catch(err => {
                    console.log(err.response.data);
                });
        },
        goCrear(){

                if (this.loading === false){
                    this.loading = true;
                    this.cliente.razon = this.cliente.nombre + " " + this.cliente.apellidos;

                    var url = "/mto/clientes";

                    this.$validator.validateAll().then((result) => {
                        if (result){
                            axios.post(url, this.cliente)
                                .then(response => {

                                    this.$emit('update:miCliente', response.data.cliente);
                                    this.$emit('update:fase_valid', 3);
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
        goEditar(){
            if (this.loading === false){
                this.loading = true;

                var url = "/mto/clientes/"+this.cliente.id;
                this.$validator.validateAll().then((result) => {
                    if (result){

                        axios.put(url, this.cliente)
                            .then(res => {

                                this.cliente = res.data.cliente;
                              //  this.miCliente  = res.data.cliente;

                                this.$emit('update:miCliente', this.cliente);
                                this.$emit('update:fase_valid', 3);

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
