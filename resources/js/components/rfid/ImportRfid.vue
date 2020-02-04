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
                    <v-card>
                        <v-form>
                            <v-container grid-list-md text-xs-center>
                                <v-layout row wrap>
                                    <v-flex sm1></v-flex>
                                    <v-flex sm4 d-flex>
                                        <v-select
                                            v-model="operacion"
                                            v-validate="'numeric|required'"
                                            :error-messages="errors.collect('operacion')"
                                            data-vv-name="operacion"
                                            data-vv-as="operación"
                                            :items="operaciones"
                                            label="Operación"
                                        ></v-select>
                                    </v-flex>
                                </v-layout>
                                <v-layout row wrap>
                                    <v-flex sm3></v-flex>
                                    <v-flex sm6 d-flex>
                                        <vue-dropzone
                                                ref="myVueDropzone"
                                                id="dropzone"
                                                :options="dropzoneOptions"
                                                v-on:vdropzone-success="upload"
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
                titulo:"Importar ficheros de etiquetas",

                dropzoneOptions: {
                    url: '/rfid/upload',
                    paramName: 'file',
                    acceptedFiles: '.txt,.csv',
                    thumbnailWidth: 150,
                    maxFiles: 1,
                    maxFilesize: 2,
                    headers: {
		    		    'X-CSRF-TOKEN':  window.axios.defaults.headers.common['X-CSRF-TOKEN']
                    },
                    dictDefaultMessage: 'Arrastra el fichero a subir aquí'
                },

                operaciones:[
                    {value: 'R', text: 'Importar Recuento'},
                    {value: 'L', text: 'Importar Localizadas'},
                ],

                operacion: 'R',

                status: false,
                loading: false,
                show_loading: true,
      		}
        },
        mounted(){
            if (!this.isAdmin){
                this.$toast.error('Permiso Administrador requerido');
                this.$router.push({ name: 'dash'})
            }
        },
        computed: {
            ...mapGetters([
                'isAdmin',
            ]),
        },
    	methods:{
            upload(file, response){
                console.log(response);
            },
            submit() {
                if (this.loading === false){
                    this.loading = true;
                    this.$validator.validateAll().then((result) => {
                        if (result){

                            axios.post("/utilidades/intercambio",
                                    {   albaran: this.albaran,
                                        serie: this.serie,
                                        albaran_des: this.albaran_d,
                                        serie_des: this.serie_d,
                                        ejercicio: this.ejercicio
                                    }
                                )
                                .then(res => {
                                    this.$toast.success('Intercambio Ok!');
                                    this.loading = false;
                                })
                                .catch(err => {

                                    this.loading = false;

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
