<template>
	<div v-show="show">
        <v-card>
            <v-card-title color="indigo">
                <h2 color="indigo">{{titulo}}</h2>
                <v-spacer></v-spacer>
                <menu-ope :id="social.id"></menu-ope>
            </v-card-title>
        </v-card>
        <v-card>
            <v-form>
                <v-container>
                    <v-layout row wrap>
                        <v-flex sm1></v-flex>
                        <v-flex sm4>
                            <v-text-field
                                v-model="social.texto"
                                v-validate="'required'"
                                :error-messages="errors.collect('texto')"
                                label="Descripción"
                                data-vv-name="texto"
                                data-vv-as="texto"
                                required
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm1>
                            <div class="text-xs-center">
                                        <v-btn @click="submit"  round  :loading="loading" flat small  color="primary">
                                Guardar
                                </v-btn>
                            </div>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex sm1></v-flex>
                        <v-flex sm2 v-if="social.logo==null">
                            <vue-dropzone
                                    ref="myVueDropzone"
                                    id="dropzone"
                                    :options="dropzoneOptions"
                                    v-on:vdropzone-success="uploadLogo"
                            ></vue-dropzone>
                        </v-flex>
                        <v-flex sm3 v-if="social.logo!=null">
                            <v-img
                                max-height="25"
                                contain
                                class="img-fluid" :src="social.logo">
                            </v-img>
                        </v-flex>
                        <v-flex sm3 v-if="social.logo!=null">
                            <v-btn @click="borraLogo" flat round><v-icon color="red darken-4">clear</v-icon></v-btn>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex sm1></v-flex>
                        <v-flex sm2>
                            <v-text-field
                                v-model="social.username"
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
                    </v-layout>
                </v-container>
            </v-form>
        </v-card>
	</div>
</template>
<script>
import moment from 'moment'
import MenuOpe from './MenuOpe'
import vue2Dropzone from 'vue2-dropzone'
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
            titulo:"Red Social",
            social: {
                id:       0,
            },

            status: false,
            loading: false,

            show: false,

            dropzoneOptions: {
                url: '/admin/social/'+this.$route.params.id+'/logo',
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

        }
    },
    mounted(){
        var id = this.$route.params.id;

        if (id > 0)
            axios.get('/admin/social/'+id+'/edit')
                .then(res => {

                    this.social = res.data.registro;
                    this.show = true;
                })
                .catch(err => {
                    this.$toast.error(err.response.data.message);
                    this.$router.push({ name: 'social.index'})
                })
    },
    computed: {
        computedFModFormat() {
            moment.locale('es');
            return this.social.updated_at ? moment(this.social.updated_at).format('D/MM/YYYY H:mm') : '';
        },
        computedFCreFormat() {
            moment.locale('es');
            return this.social.created_at ? moment(this.social.created_at).format('D/MM/YYYY H:mm') : '';
        }

    },
    methods:{
        uploadLogo(file, response){

            this.social = response.social;
        },
        borraLogo(){
            axios.put('/admin/social/'+this.social.id+'/logo/delete')
                .then(response => {
                    this.social = response.data.social;
                    this.loading = false;
                })
        },
        submit() {

            if (this.loading === false){
                this.loading = true;
                var url = "/admin/social/"+this.social.id;
                this.$validator.validateAll().then((result) => {
                    if (result){
                        axios.put(url,this.social)
                            .then(response => {
                                this.$toast.success(response.data.message);
                                this.social = response.data.registro;
                                this.loading = false;
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
    }
  }
</script>
