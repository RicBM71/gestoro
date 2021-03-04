<template>
	<div>
        <loading :show_loading="show_loading"></loading>
        <v-card>
            <v-card-title color="indigo">
                <h2 color="indigo">{{titulo}}</h2>
                <v-spacer></v-spacer>
                <menu-ope :id="taller.id"></menu-ope>
            </v-card-title>
        </v-card>
        <v-card>
            <v-tabs fixed-tabs>
                <v-tab>
                        Datos generales
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
                                    v-model="taller.tipodoc"
                                    :items="tiposdoc"
                                    label="Documento"
                                    ></v-select>
                                </v-flex>

                                <v-flex sm2>
                                    <v-text-field
                                        v-model="taller.dni"
                                        v-validate="'required|alpha_num|min:4'"
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
                                        v-model="taller.nombre"
                                        v-validate="'max:30'"
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
                                        v-model="taller.apellidos"
                                        v-validate="'max:45'"
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
                                        v-model="taller.telefono1"
                                        :error-messages="errors.collect('telefono1')"
                                        label="Teléfono"
                                        data-vv-name="telefono1"
                                        data-vv-as="Teléfono 1"
                                        mask="### ### ###"
                                        v-on:keyup.enter="submit"
                                    >
                                    </v-text-field>
                                </v-flex>
                            </v-layout>
                            <v-layout row wrap>
                                <v-flex sm4>
                                    <v-text-field
                                        v-model="taller.razon"
                                        v-validate="'required'"
                                        :error-messages="errors.collect('razon')"
                                        label="Razon"
                                        data-vv-name="razon"
                                        required
                                        v-on:keyup.enter="submit"
                                    >
                                    </v-text-field>
                                </v-flex>
                                 <v-flex sm4>
                                    <v-text-field
                                        v-model="taller.email"
                                        v-validate="'email'"
                                        :error-messages="errors.collect('email')"
                                        label="email"
                                        data-vv-name="email"
                                        v-on:keyup.enter="submit"
                                    >
                                    </v-text-field>
                                </v-flex>
                                <v-flex sm2>
                                    <v-text-field
                                        v-model="taller.tfmovil"
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
                                        v-model="taller.telefono2"
                                        :error-messages="errors.collect('telefono2')"
                                        label="Teléfono 2"
                                        data-vv-name="telefono2"
                                        data-vv-as="Teléfono 2"
                                        mask="### ### ###"
                                        v-on:keyup.enter="submit"
                                    >
                                    </v-text-field>
                                </v-flex>
                            </v-layout>
                            <v-layout row wrap>
                                <v-flex sm5>
                                    <v-text-field
                                        v-model="taller.direccion"
                                        :error-messages="errors.collect('direccion')"
                                        label="Dirección"
                                        data-vv-name="direccion"
                                        v-on:keyup.enter="submit"
                                    >
                                    </v-text-field>
                                </v-flex>
                                <v-flex sm1>
                                    <v-text-field
                                        v-model="taller.cpostal"
                                        :error-messages="errors.collect('cpostal')"
                                        label="CP"
                                        data-vv-name="CP"
                                        v-on:keyup.enter="submit"
                                    >
                                    </v-text-field>
                                </v-flex>
                                <v-flex sm3>
                                    <v-text-field
                                        v-model="taller.poblacion"
                                        :error-messages="errors.collect('poblacion')"
                                        label="Población"
                                        data-vv-name="poblacion"
                                        v-on:keyup.enter="submit"
                                    >
                                    </v-text-field>
                                </v-flex>
                                <v-flex sm3>
                                    <v-text-field
                                        v-model="taller.provincia"
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
                                        v-model="taller.iban"
                                        :error-messages="errors.collect('iban')"
                                        label="IBAN"
                                        mask="AA## #### #### #### #### ####"
                                        counter=24
                                        data-vv-name="iban"
                                        @change="buscarBic"
                                        v-on:keyup.enter="submit"
                                    >
                                    </v-text-field>
                                </v-flex>
                                <v-flex sm3>
                                    <v-text-field
                                        v-model="taller.bic"
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
                                    v-model="taller.fpago_id"
                                    :items="fpagos"
                                    label="Forma de Pago"
                                    ></v-select>
                                </v-flex>
                                <v-flex sm2>
                                    <v-switch
                                        label="Facturar"
                                        v-model="taller.facturar"
                                        color="primary">
                                    ></v-switch>
                                </v-flex>
                            </v-layout>
                            <v-layout row wrap>
                                <v-flex sm12>
                                    <v-text-field
                                        v-model="taller.notas"
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
                                    <v-text-field
                                        v-model="taller.username"
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
                    <ventas-taller v-if="taller.id>0" :taller="taller"></ventas-taller>
                </v-tab-item>
            </v-tabs>
        </v-card>
	</div>
</template>
<script>
import Loading from '@/components/shared/Loading'
import moment from 'moment'
import MenuOpe from './MenuOpe'
import VentasTaller from './VentasTaller'
import {mapGetters} from 'vuex';
	export default {
		$_veeValidate: {
      		validator: 'new'
        },
        components: {
            'menu-ope': MenuOpe,
            'loading': Loading,
            'ventas-taller': VentasTaller
        },
    	data () {
      		return {
                titulo:"Taller",
                taller: {
                    id: 0
                },

                tiposdoc:[
                    {value: 'N', text:"DNI/NIE"},
                    {value: 'C', text:"CIF"},
                    {value: 'O', text:"Otros"},
                ],

                fpagos:[],
                bancos:[],

                taller_id: "",

        		status: false,
                loading: false,

                show_loading: false,
                show_docu: false,
      		}
        },
        mounted(){

            this.show_loading = true;
            var id = this.$route.params.id;

            if (id > 0)
                axios.get('/mto/talleres/'+id+'/edit')
                    .then(res => {

                        this.taller = res.data.taller;
                        this.fpagos = res.data.fpagos;
                        this.bancos = res.data.bancos;

                        this.show_loading = false;

                    })
                    .catch(err => {
                        if (err.response.status == 404)
                            this.$toast.error("taller No encontrado!");
                        else
                            this.$toast.error(err.response.data.message);
                        this.$router.push({ name: 'taller.index'})

                    })
                    .finally(()=> {
                             this.show_loading = false;
                    });
        },
        computed: {
            ...mapGetters([
            ]),
            computedFModFormat() {
                moment.locale('es');
                return this.taller.updated_at ? moment(this.taller.updated_at).format('D/MM/YYYY H:mm') : '';
            },
            computedFCreFormat() {
                moment.locale('es');
                return this.taller.created_at ? moment(this.taller.created_at).format('D/MM/YYYY H:mm') : '';
            },
            rules() {
                return this.taller.iban > '' ? 'required' : '';
            }
        },
    	methods:{
            buscarBic(){
                if (this.taller.iban.length >= 8 && this.taller.bic == null){
                    var entidad = this.taller.iban.substring(4,8);
                    var idx = this.bancos.map(x => x.entidad).indexOf(entidad);
                    if (idx >= 0)
                        this.taller.bic = this.bancos[idx].bic;
                }
            },
            submit() {

                if (this.loading === false){
                    this.loading = this.show_loading = true;

                    var url = "/mto/talleres/"+this.taller.id;

                    this.$validator.validateAll().then((result) => {
                        if (result){

                            axios.put(url, this.taller)
                                .then(res => {

                                    this.$toast.success(res.data.message);
                                    this.taller = res.data.taller;
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
                                })
                                 .finally(()=> {
                                      this.show_loading = this.loading = false;
                                });
                            }
                        else{
                            this.loading = this.show_loading = false;
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
