<template>
	<div>
        <mod-menu :showMenuCli="showMenuCli" :x="x" :y="y" :items="items"></mod-menu>

        <h2>Usuarios</h2>
        <v-form>
            <v-container @contextmenu="showMenu">
                <v-btn
                    @click="showMenu"
                    fixed
                    dark
                    fab
                    bottom
                    right
                    color="teal accent-4"
                    >
                    <v-icon>add</v-icon>
                </v-btn>

                <v-layout row wrap>
                    <v-flex sm3>
                        <v-text-field
                            v-model="user.name"
                            v-validate="'required'"
                            :error-messages="errors.collect('name')"
                            label="Nombre"
                            data-vv-name="name"
                            data-vv-as="nombre"
                            required
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
                        >
                        </v-text-field>
                        <v-text-field
                            v-model="user.email"
                            v-validate="'required|email'"
                            :error-messages="errors.collect('email')"
                            label="email"
                            data-vv-name="email"
                            required
                        >
                        </v-text-field>
                        <v-text-field
                            v-model="password"
                            ref="password"
                            :append-icon="show ? 'visibility_off' : 'visibility'"
                            :type="show ? 'text' : 'password'"
                            :error-messages="errors.collect('password')"
                            v-validate="'min:8'"
                            label="password"
                            hint="Indicar password solo si va a modificarla"
                            data-vv-name="password"
                            @click:append="show = !show"
                            >
                        </v-text-field>
                        <v-text-field
                            v-model="password_confirmation"
                            v-validate="'min:8|confirmed:password'"
                            :append-icon="show ? 'visibility_off' : 'visibility'"
                            :type="show ? 'text' : 'password'"
                            :error-messages="errors.collect('password_confirmation')"
                            label="confirmar password"
                            hint="Confirmar password solo si va a modificarla"
                            data-vv-name="password_confirmation"
                            data-vv-as="password"
                            @click:append="show = !show"
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
                        >
                        </v-text-field>
                        <v-switch
                            v-model="user.blocked"
                            :disabled="computedId"
                            color="primary"
                            label="Bloqueado"
                        ></v-switch>
                        <v-menu v-if="user.blocked"
                            v-model="menu2"
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
                                label="Fecha Bloqueo"
                                prepend-icon="event"
                                readonly
                                @click:clear="clearDate"
                                ></v-text-field>
                            <v-date-picker
                                v-model="user.blocked_at"
                                no-title
                                locale="es"
                                @input="menu2 = false"

                            ></v-date-picker>
                        </v-menu>
                        <v-text-field
                            v-model="computedFModFormat"
                            label="Modificado"
                            readonly
                        >
                        </v-text-field>
                        <v-text-field
                            v-model="computedFCreFormat"
                            label="Creado"
                            readonly
                        >
                        </v-text-field>
                    </v-flex>
                    <v-flex sm4>
                        <div v-if="showPer">
                            <!-- <user-role v-bind:user_id="this.user.id" v-bind:role_user="this.role_user"></user-role> -->
                            <user-role :user_id="this.user.id" :role_user="this.role_user"></user-role>
                            <user-permiso :user_id="this.user.id" :permisos="this.permisos" :permisos_selected="permisos_selected"></user-permiso>
                        </div>
                    </v-flex>
                    <v-flex sm6>
                        <div class="text-xs-center">
                            <v-btn @click="submit"  round  :loading="enviando" block  color="primary">
                                Guardar Usuario
                            </v-btn>
                        </div>
                    </v-flex>
                </v-layout>

            </v-container>
        </v-form>

	</div>
</template>
<script>
    import moment from 'moment'
    import UserRole from './UserRole'
    import UserPermiso from './UserPermiso'
    import ModMenu from '@/components/shared/ModMenu'
    import {mapGetters} from 'vuex';

	export default {
		$_veeValidate: {
      		validator: 'new'
    	},
        components: {
            'user-role': UserRole,
            'user-permiso': UserPermiso,
            'mod-menu': ModMenu

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
                enviando: false,

                show: false,
                menu2: false,

                showPer: false,
                showMenuCli: false,
                x: 0,
                y: 0,
                items: [
                    { title: 'Usuarios', name: 'users', icon: 'people' },
                    { title: 'Crear', name: 'users_create', icon: 'person_add' },
                    { title: 'Roles', name: 'roles', icon: 'share' },
                    { title: 'Home', name: 'dash', icon: 'home' },

                ]
      		}
        },
        mounted(){
            if (!this.isRoot)
                this.items.splice(2,1)

            var id = this.$route.params.id;


            if (id > 0){
                var id = this.$route.params.id;
                axios.get('/admin/users/'+id+'/edit')
                    .then(res => {

                        this.showPer=true;
                        this.user = res.data.user;
                        this.role_user = res.data.role_user;
                        this.permisos = res.data.permisos;
                        this.permisos_selected = res.data.permisos_user;

                    })
                    .catch(err => {

                        this.$toast.error(err.response.data.message);

                        this.$router.push({ name: 'users'})
                    })
            }
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
                return this.user.updated_at ? moment(this.user.created_at).format('D/MM/YYYY H:mm') : '';
            },
            computedId(){
                if (this.user.id == 1) return true; else return false;
            }

        },
    	methods:{
            showMenu (e) {

                e.preventDefault()

                this.showMenuCli = false
                this.x = e.clientX
                this.y = e.clientY

                this.$nextTick(() => {
                    this.showMenuCli = true
                })
            },
            submit() {


                this.enviando = true;

                var url = "/admin/users";
                var metodo = "post";

                if (this.user.id > 0){
                    url += '/'+this.user.id;
                    metodo = "put";
                }

                this.$validator.validateAll().then((result) => {
                    if (result){
                        axios({
                            method: metodo,
                            url: url,
                            data:
                                {
                                    name: this.user.name,
                                    lastname: this.user.lastname,
                                    username: this.user.username,
                                    email: this.user.email,
                                    password: this.password,
                                    password_confirmation: this.password_confirmation,
                                    blocked: this.user.blocked,
                                    blocked_at: this.user.blocked_at
                                }
                            })
                            .then(response => {


                                this.$toast.success(response.data.msg);
                                if (response.data.status == "C"){
                                     this.$router.push({ name: 'users_edit', params: { id: response.data.user.id } })
                                }
                                else{
                                    this.user.blocked_at = response.data.user.blocked_at;
                                    this.user.updated_at = response.data.user.updated_at;
                                    this.password = this.password_confirmation = "";
                                }
                                this.enviando = false;
                            })
                            .catch(err => {

                                //if (err.request.status == 422){ // fallo de validated.
                                    const msg_valid = err.response.data.errors;
                                    for (const prop in msg_valid) {
                                        this.$toast.error(`${msg_valid[prop]}`);
                                    }
                                //}
                                this.enviando = false;
                            });
                        }
                    else{
                        this.enviando = false;
                    }
                });

            },
            clearDate(){
                this.user.blocked_at = null;
            }

    }
  }
</script>
