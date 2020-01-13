<template>
	<div>
        <my-dialog :dialog.sync="dialog" registro="registro" @destroyReg="destroyReg"></my-dialog>
		<v-form @submit.prevent="updatePassword">
            <v-container>
                <v-layout row wrap>
                    <v-flex sm4>
                        <v-text-field
                            v-model="form.new_password"
                            ref="new_password"
                            :append-icon="show ? 'visibility_off' : 'visibility'"
                            :counter="8"
                            :type="show ? 'text' : 'password'"
                            :error-messages="errors.collect('new_password')"
                            v-validate="'required|min:8'"
                            label="password"
                            hint="Indicar password "
                            data-vv-name="new_password"
                            data-vv-as="password"
                            :disabled="loading"
                            @click:append="show = !show"
                            >
                        </v-text-field>
                        <v-text-field
                            v-model="form.password_confirmation"
                            v-validate="'required|min:8|confirmed:new_password'"
                            :append-icon="show ? 'visibility_off' : 'visibility'"
                            :type="show ? 'text' : 'password'"
                            :error-messages="errors.collect('password_confirmation')"
                            label="confirmar password"
                            hint="Confirmar password"
                            data-vv-name="password_confirmation"
                            data-vv-as="password"
                            :disabled="loading"
                            @click:append="show = !show"
                            >
                        </v-text-field>

                        <v-btn @click="submit"  round  :loading="loading" block  color="primary">
                            <span v-show="loading">Actualizando Password</span>
                            <span v-show="!loading">Actualizar Password</span>
                        </v-btn>
                    </v-flex>
                    <v-flex sm1></v-flex>
                    <v-flex sm2 v-if="avatar !='#'">
                        <v-avatar>
                               <img class="img-fluid" :src="avatar">
                        </v-avatar>
                        <v-btn @click="openDialog" flat round><v-icon color="red darken-4">delete</v-icon> Eliminar Avatar</v-btn>
                    </v-flex>
                    <v-flex sm2 v-else>
                        <vue-dropzone
                            ref="myVueDropzone"
                            id="dropzone"
                            :options="dropzoneOptions"
                            v-on:vdropzone-success="upload"
                        ></vue-dropzone>
                    </v-flex>
                </v-layout>
            </v-container>
		</v-form>
	</div>
</template>

<script>
 import vue2Dropzone from 'vue2-dropzone'
 import 'vue2-dropzone/dist/vue2Dropzone.min.css'
 import MyDialog from '@/components/shared/MyDialog'
 import {mapActions} from "vuex";
 import {mapGetters} from 'vuex';
	export default {
        $_veeValidate: {
      		validator: 'new'
        },
         components: {
            'vueDropzone': vue2Dropzone,
            'my-dialog': MyDialog
		},
		data() {
			return {
                avatar: "",
                dialog: false,
                show: false,
				loading: false,
				form: {
					new_password: '',
					password_confirmation: ''
                },
                showDrop: true,
                dropzoneOptions: {
                    url: '/profile/avatar',
                    paramName: 'avatar',
                    acceptedFiles: 'image/*',
                    thumbnailWidth: 150,
                    maxFiles: 1,
		    	    resizeWidth: 80,
		    	    resizeHeight: 80,
                    maxFilesize: 0.5,
                    headers: {
		    		    'X-CSRF-TOKEN':  window.axios.defaults.headers.common['X-CSRF-TOKEN']
                    },
                    dictDefaultMessage: 'Arrastra la foto aquí para subir avatar'
                }
			}
        },
        mounted(){
            this.avatar = this.userAvatar;
        },
        computed: {
            ...mapGetters([
                'userAvatar'
            ]),
        },
		methods: {
            ...mapActions([
				'setAuthUser'
            ]),
            openDialog (){
                this.dialog = true;
            },
            destroyReg () {
                this.dialog = false;

                axios.put('/profile/destroy',{_method: 'delete'})
                    .then(response => {
                        this.avatar="#";
                        this.$toast.success('Avatar eliminado!');
                })
                .catch(err => {
                    this.status = true;
                    var msg = err.response.data.message;
                    this.$toast.error(msg);

                });
            },
            upload(file, response){
                this.avatar = response.url;
            },
			submit() {

                this.$validator.validateAll().then((result) => {
                    if (result){
                        this.loading = true;
                        axios.post('/admin/users/password/update', this.form)
                            .then((res) => {

                                this.loading = false;
                                this.$toast.success(res.data);
                                this.$emit('updateSuccess');
                            })
                            .catch(err => {
                                //(err.response.data.error) &&  this.$toast.error(err.response.data.error);

                                const msg_valid = err.response.data.errors;
                                    //console.log(`obj.${prop} = ${msg_valid[prop]}`);
                                for (const prop in msg_valid) {
                                    this.$toast.error(`${msg_valid[prop]}`);
                                }
                                this.loading = false;
                            });
                    }
                })
			}
		}
	}
</script>
