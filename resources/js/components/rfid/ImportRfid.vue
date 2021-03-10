<template>
	<div>
        <v-container grid-list-md text-xs-center>
            <v-layout row wrap>
                 <v-flex sm2></v-flex>
                  <v-flex sm8>
                      <v-card>
                        <v-card-title color="indigo">
                            <h2 color="indigo">{{titulo}}</h2>
                            <v-spacer></v-spacer>
                        </v-card-title>
                    </v-card>
                    <v-card v-show="load">
                        <v-form>
                            <v-container grid-list-md text-xs-center>
                                <v-layout row wrap>
                                    <v-flex sm1></v-flex>
                                    <v-flex sm4 d-flex>
                                        <v-select
                                            v-model="operacion"
                                            v-validate="'max:1|required'"
                                            :error-messages="errors.collect('operacion')"
                                            data-vv-name="operacion"
                                            data-vv-as="operación"
                                            :items="operaciones"
                                            label="Operación"
                                        ></v-select>
                                    </v-flex>
                                    <v-flex sm3 d-flex v-if="operacion=='R'">
                                        <v-select
                                            v-validate="'numeric'"
                                            v-model="agregar_empresa_id"
                                            :error-messages="errors.collect('agregar_empresa_id')"
                                            data-vv-name="agregar_empresa_id"
                                            data-vv-as="empresa"
                                            :items="empresas"
                                            label="Incorporar"
                                        ></v-select>
                                    </v-flex>
                                </v-layout>
                                <v-layout row wrap>
                                    <v-flex sm3></v-flex>
                                    <v-flex sm6 d-flex>
                                        <vue-dropzone
                                                v-show="operacion=='R'"
                                                ref="myVueDropzone"
                                                id="dropzone"
                                                :options="dropzoneOptions"
                                                v-on:vdropzone-sending="sendingEvent"
                                                v-on:vdropzone-error="vderror"
                                                v-on:vdropzone-success="upload"
                                        ></vue-dropzone>
                                        <vue-dropzone
                                                v-show="operacion=='L'"
                                                ref="myVueDropzone2"
                                                id="dropzone2"
                                                :options="dropzoneOptions2"
                                                v-on:vdropzone-error="vderror2"
                                                v-on:vdropzone-success="localizar"
                                        ></vue-dropzone>
                                    </v-flex>
                                </v-layout>
                                <!-- <v-layout row wrap>
                                    <v-flex sm9></v-flex>
                                    <v-flex sm2>
                                        <div class="text-xs-center">
                                            <v-btn @click="submit"  round  :loading="loading" block  color="primary">
                                                Enviar
                                            </v-btn>
                                        </div>
                                    </v-flex>
                                </v-layout> -->
                            </v-container>
                        </v-form>
                    </v-card>
                    <v-card v-show="!load">
                        <v-layout row wrap>
                            <v-flex sm12>
                                <table class="v-datatable v-table theme--light">
                                    <thead>
                                        <tr>
                                            <th>Concepto</th>
                                            <th>Registros</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(item, index) in items" :key="index">
                                            <td class="text-xs-left">{{item.nombre}}</td>
                                            <td class="text-xs-center">{{ item.registros | currency('', 0, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false })}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </v-flex>
                        </v-layout>
                        <v-layout row wrap>
                            <v-flex sm4></v-flex>
                            <v-flex sm3>
                                <!-- <div class="text-xs-center">
                                    <v-btn @click="load = !load"  round flat  block  color="primary">
                                        Upload
                                    </v-btn>
                                </div> -->
                            </v-flex>
                            <v-flex sm1></v-flex>
                            <v-flex sm3>
                                <div class="text-xs-center">
                                    <v-btn @click="detalle"  round flat  :loading="loading" block  color="primary">
                                        Detalle Recuento
                                    </v-btn>
                                </div>
                            </v-flex>
                        </v-layout>
                    </v-card>
                </v-flex>
            </v-layout>
        </v-container>
	</div>
</template>
<script>

import {mapGetters} from 'vuex';
import vue2Dropzone from 'vue2-dropzone'
import 'vue2-dropzone/dist/vue2Dropzone.min.css'
	export default {
		$_veeValidate: {
      		validator: 'new'
        },
        components: {
            'vueDropzone': vue2Dropzone,
		},
    	data () {
      		return {
                titulo:"Importar ficheros de etiquetas RFID",

                load: true,

                dropzoneOptions: {
                    url: '/rfid/recuento',
                    paramName: 'file',
                    acceptedFiles: '.txt,.csv,.rf',
                    thumbnailWidth: 150,
                    maxFiles: 1,
                    maxFilesize: 2,
                    headers: {
		    		    'X-CSRF-TOKEN':  window.axios.defaults.headers.common['X-CSRF-TOKEN']
                    },
                    dictDefaultMessage: 'Arrastra el fichero recuento a subir aquí'
                },

                dropzoneOptions2: {
                    url: '/rfid/localizar',
                    paramName: 'file',
                    acceptedFiles: '.txt,.csv,.rf',
                    thumbnailWidth: 150,
                    maxFiles: 1,
                    maxFilesize: 2,
                    headers: {
		    		    'X-CSRF-TOKEN':  window.axios.defaults.headers.common['X-CSRF-TOKEN']
                    },
                    dictDefaultMessage: 'Arrastra el fichero de localizadas a subir aquí'
                },

                operaciones:[
                    {value: 'R', text: 'Importar Recuento'},
                    {value: 'L', text: 'Importar Localizadas'},
                ],

                items:[],
                empresas: [],

                operacion: 'R',

                status: false,
                loading: false,
                show_loading: true,
                agregar_empresa_id: null
      		}
        },
        mounted(){
            if (!this.hasEdtPro){
                this.$toast.error('Permiso Editpro requerido');
                this.$router.push({ name: 'dash'})
            }
            axios.get('/rfid/recuento')
            .then(res => {
                this.empresas = res.data.empresas;
                this.empresas.push({value:null,text:"---"});
            })
            .catch(err => {
                this.$toast.error('Error al montar export-rfid');
            })
        },
        computed: {
            ...mapGetters([
                'hasEdtPro',
            ]),
        },
    	methods:{
            vderror(file, message, xhr){

                this.$toast.error(message.message);
                this.$refs.myVueDropzone.removeAllFiles()

            },
            vderror2(file, message, xhr){

                this.$toast.error(message.message);
                this.$refs.myVueDropzone2.removeAllFiles()

            },
            sendingEvent (file, xhr, formData) {

                formData.append('agregar_empresa_id', this.agregar_empresa_id);
            },
            upload(file, response){
                this.load = false;
                this.items = response;
            },
            localizar(file, response){

                this.load = false;
                this.items = response;

            },
            detalle() {
                this.$router.push({ name: 'recuento.index' })
            }
        }
  }
</script>
