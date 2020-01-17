<template>
	<div>
        <v-card>
            <v-card-title color="indigo">
                <h2 color="indigo">{{titulo}}</h2>
                <v-spacer></v-spacer>
                <menu-ope :id="user.id"></menu-ope>
            </v-card-title>
        </v-card>
        <v-card>
            <v-form>
                <v-container>
                    <v-layout row wrap>
                        <v-flex sm3></v-flex>
                        <v-flex sm3>
                            <v-text-field
                                v-model="user.name"
                                v-validate="'required'"
                                :error-messages="errors.collect('name')"
                                label="Nombre"
                                data-vv-name="name"
                                data-vv-as="nombre"
                                required
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                            <v-text-field
                                v-model="user.username"
                                v-validate="'required|min:6'"
                                :counter="20"
                                :error-messages="errors.collect('username')"
                                label="Usuario"
                                data-vv-name="username"
                                data-vv-as="usuario"
                                required
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                            <v-text-field
                                v-model="user.email"
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
                                v-model="user.lastname"
                                :error-messages="errors.collect('lastname')"
                                label="Apellidos"
                                data-vv-name="lastname"
                                data-vv-as="apellidos"
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                    </v-layout>
                     <v-layout row wrap>
                        <v-flex sm4>
                        </v-flex>
                        <v-flex sm6>
                            <div class="text-xs-center">
                                <v-btn @click="submit"  round  :loading="loading" block  color="primary">
                                    Crear Usuario
                                </v-btn>
                            </div>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-form>
        </v-card>
	</div>
</template>
<script>
    import moment from 'moment'
    import {mapGetters} from 'vuex';
    import MenuOpe from './MenuOpe'

	export default {
		$_veeValidate: {
      		validator: 'new'
    	},
        components: {
            'menu-ope': MenuOpe

		},
    	data () {
      		return {
                user: {
                    id:       0,
                    name:     "",
                    lastname: "",
                    username: "",
                    email:    "",
                    blocked_at:"",
                    blocked:  "",
                    updated_at:"",
                    created_at:"",
                },
                password: "",
                password_confirmation:"",
                titulo:   "Usuarios",
                role_user: [],
                permisos:[],
                permisos_selected:[],

        		status: false,
        		msg : "",
                color: "error",
                icon: "warning",
                loading: false,

                show: false,
                menu2: false,

                showPer: false,
      		}
        },
        mounted(){
            // if (!this.isRoot)
            //     this.items.splice(3,1)

            axios.get('/admin/users/create')
                .then(res => {
                    this.showMainDiv = true;
                })
                .catch(err => {
                     if (err.response.status != 401){
                        this.$toast.error(err.response.data.message);
                        this.$router.push({ name: 'users.index'});
                        }
                })
        },
        computed: {
            ...mapGetters([
	    		'isRoot'
    		]),
            computedDateFormat() {
                moment.locale('es');
                return this.user.blocked_at ? moment(this.user.blocked_at).format('L') : '';
            },
            computedFModFormat() {
                moment.locale('es');
                return this.user.updated_at ? moment(this.user.updated_at).format('D/MM/YYYY H:mm') : '';
            },
            computedFCreFormat() {
                moment.locale('es');
                return this.user.created_at ? moment(this.user.created_at).format('D/MM/YYYY H:mm') : '';
            },

        },
    	methods:{
            submit() {
                if (this.loading === false){
                    this.loading = true;

                    var url = "/admin/users";

                    this.$validator.validateAll().then((result) => {
                        if (result){
                            axios.post(url, this.user)
                                .then(response => {

                                    this.$router.push({ name: 'users.edit', params: { id: response.data.user.id } })
                                    this.loading = false;
                                })
                                .catch(err => {
                                        const msg_valid = err.response.data.errors;
                                        for (const prop in msg_valid) {
                                            this.errors.add({
                                                field: prop,
                                                msg: `${msg_valid[prop]}`
                                            })
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
            clearDate(){
                this.user.blocked_at = null;
            }

    }
  }
</script>
