<template>
	<div v-show="!show_loading">
        <loading :show_loading="show_loading"></loading>
        <my-dialog :dialog.sync="dialog" registro="avatar" @destroyReg="destroyReg"></my-dialog>
        <v-card>
            <v-card-title color="indigo">
                <h2 color="indigo">{{titulo}}</h2>
                <v-spacer></v-spacer>
                <v-tooltip bottom>
                    <template v-slot:activator="{ on }">
                        <v-btn
                            v-show="user.id > 0"
                            v-on="on"
                            color="white"
                            icon
                            @click="reset"
                        >
                            <v-icon color="orange">verified_user</v-icon>
                        </v-btn>
                    </template>
                        <span>Reset Password</span>
                </v-tooltip>
                <menu-ope :id="user.id"></menu-ope>
            </v-card-title>
        </v-card>
        <v-card>
            <v-tabs fixed-tabs>
                <v-tab>
                        Datos generales
                </v-tab>
                <v-tab>
                        Empresas
                </v-tab>
                <v-tab>
                        Roles y Permisos
                </v-tab>
                 <v-tab>
                        IP's autorizadas
                </v-tab>
                <v-tab-item>

                    <v-form>
                        <v-container>
                            <v-layout row wrap>
                                <v-flex sm4>
                                    <v-text-field
                                        v-model="user.name"
                                        v-validate="'required'"
                                        :error-messages="errors.collect('name')"
                                        label="Nombre"
                                        data-vv-name="name"
                                        data-vv-as="nombre"
                                        v-on:keyup.enter="submit"
                                        required
                                    >
                                    </v-text-field>
                                    <v-text-field
                                        v-model="user.lastname"
                                        :error-messages="errors.collect('lastname')"
                                        label="Apellidos"
                                        data-vv-name="lastname"
                                        data-vv-as="apellidos"
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
                                        v-on:keyup.enter="submit"
                                        required
                                    >
                                    </v-text-field>
                                </v-flex>
                                <v-flex sm4>
                                    <v-text-field
                                        v-model="user.email"
                                        v-validate="'required_if:id,1,2|email'"
                                        :error-messages="errors.collect('email')"
                                        label="email"
                                        data-vv-name="email"
                                        v-on:keyup.enter="submit"
                                        required
                                    >
                                    </v-text-field>

                                        <v-text-field
                                            v-model="user.expira"
                                            v-validate="'numeric|min_value:0|max_value:180'"
                                            :error-messages="errors.collect('expira')"
                                            label="Días expira password"
                                            hint="Indicar 0 días para password perpetua"
                                            data-vv-name="expira"
                                            v-on:keyup.enter="submit"
                                        >
                                        </v-text-field>

                                         <v-menu
                                            ref="menu_fe"
                                            v-model="menu_fe"
                                            :close-on-content-click="false"
                                            :nudge-right="40"
                                            lazy
                                            transition="scale-transition"
                                            offset-y
                                            full-width
                                            min-width="290px"
                                        >
                                            <template v-slot:activator="{ on }">
                                            <v-text-field
                                                v-model="computedDateExpira"
                                                label="Ult. Password"
                                                prepend-icon="event"
                                                clearable
                                                @click:clear="clearDateExp"
                                                readonly
                                                v-on="on"
                                            ></v-text-field>
                                            </template>
                                            <v-date-picker
                                                ref="picker"
                                                v-model="user.fecha_expira"
                                                :max="new Date().toISOString().substr(0, 10)"
                                                min="2000-01-01"
                                                locale="es"
                                                first-day-of-week=1
                                                @change="save"
                                            ></v-date-picker>
                                        </v-menu>

                                    <v-text-field
                                        v-model="user.password"
                                        ref="password"
                                        :append-icon="show ? 'visibility_off' : 'visibility'"
                                        :type="show ? 'text' : 'password'"
                                        :error-messages="errors.collect('password')"
                                        v-validate="'min:6'"
                                        label="password"
                                        hint="Indicar password solo si va a modificarla"
                                        data-vv-name="password"
                                        v-on:keyup.enter="submit"
                                        @click:append="show = !show"
                                        >
                                    </v-text-field>
                                    <v-text-field
                                        v-model="user.password_confirmation"
                                        v-validate="'min:6|confirmed:password'"
                                        :append-icon="show ? 'visibility_off' : 'visibility'"
                                        :type="show ? 'text' : 'password'"
                                        :error-messages="errors.collect('password_confirmation')"
                                        label="confirmar password"
                                        hint="Confirmar password solo si va a modificarla"
                                        data-vv-name="password_confirmation"
                                        data-vv-as="password"
                                        v-on:keyup.enter="submit"
                                        @click:append="show = !show"
                                        >
                                    </v-text-field>
                                </v-flex>
                                <v-flex sm4>

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
                                            data-vv-as="F. bloqueo"
                                            @click:clear="clearDate"
                                            ></v-text-field>
                                        <v-date-picker
                                            v-model="user.blocked_at"
                                            no-title
                                            locale="es"
                                            first-day-of-week=1
                                            @input="menu2 = false"

                                        ></v-date-picker>
                                    </v-menu>
                                    <v-text-field v-if="!user.blocked"
                                        v-model="computedLoginAt"
                                        label="Ult. Login"
                                        readonly
                                    >
                                    </v-text-field>
                                     <v-text-field
                                        v-model="user.username_umod"
                                        label="Modificado"
                                        readonly
                                    >
                                    </v-text-field>
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
                                <v-flex sm1></v-flex>
                                <v-flex sm2 v-if="showPer">
                                    <div v-if="user.avatar == '#'">
                                        <vue-dropzone
                                            ref="myVueDropzone"
                                            id="dropzone"
                                            :options="dropzoneOptions"
                                            v-on:vdropzone-success="upload"
                                        ></vue-dropzone>
                                    </div>
                                    <div v-else>
                                        <v-avatar>
                                            <img class="img-fluid" :src="user.avatar" alt="avatar">
                                        </v-avatar>
                                        <v-btn @click="openDialog" flat icon color="red darken-4">
                                            <v-icon>delete</v-icon>
                                        </v-btn>
                                    </div>
                                </v-flex>

                            </v-layout>
                            <v-layout>
                                <v-flex sm4></v-flex>
                                <v-flex sm4>
                                    <div class="text-xs-center">
                                        <v-btn @click="submit"  round  :loading="loading" block  color="primary">
                                            Guardar Usuario
                                        </v-btn>
                                    </div>
                                </v-flex>
                            </v-layout>
                        </v-container>
                    </v-form>
                </v-tab-item>
                <v-tab-item>
                    <v-container v-if="showPer">
                        <user-emp :user="user" :emp_user="this.emp_user" :empresas="empresas"></user-emp>
                    </v-container>
                </v-tab-item>
                <v-tab-item>
                    <v-container>
                        <v-layout row wrap text-xs-center>
                            <v-flex sm12><h2>{{user.username}}</h2></v-flex>
                        </v-layout>
                        <v-layout row wrap>
                            <v-flex sm12>
                                <div v-if="showPer">
                                    <user-role :user_id="this.user.id" :role_user="this.role_user"></user-role>
                                    <user-permiso :user_id="this.user.id" :permisos="this.permisos" :permisos_selected="permisos_selected"></user-permiso>
                                </div>
                            </v-flex>
                        </v-layout>
                    </v-container>
                </v-tab-item>
                <v-tab-item>
                    <v-container v-if="user.id > 0">
                        <user-ip :user_id="user.id" :ips="ips"></user-ip>
                    </v-container>
                </v-tab-item>
            </v-tabs>
            </v-card>
	</div>
</template>
<script>
    import moment from 'moment'
    import MenuOpe from './MenuOpe'
    import UserEmp from './UserEmp'
    import {mapGetters} from 'vuex';
    import UserRole from './UserRole'
    import UserPermiso from './UserPermiso'
    import UserIp from './UserIp'
    import vue2Dropzone from 'vue2-dropzone'
    import MyDialog from '@/components/shared/MyDialog'
    import Loading from '@/components/shared/Loading'
    import 'vue2-dropzone/dist/vue2Dropzone.min.css'

	export default {
		$_veeValidate: {
      		validator: 'new'
    	},
        components: {
            'user-emp': UserEmp,
            'user-role': UserRole,
            'user-permiso': UserPermiso,
            'user-ip': UserIp,
            'my-dialog': MyDialog,
            'vueDropzone': vue2Dropzone,
            'menu-ope': MenuOpe,
            'loading': Loading

		},
    	data () {
      		return {
                user: {
                    id:       0,
                    name:     "",
                    lastname: "",
                    username: "",
                    email:    "",
                    avatar:   "",
                    blocked_at:"",
                    blocked:  "",
                    login_at: "",
                    huella: "",
                    empresa_id:"",
                    password: "",
                    password_confirmation:"",

                    // updated_at:"",
                    // created_at:"",
                },
                titulo:   "Usuarios",

                emp_user:[],
                role_user: [],
                permisos:[],
                permisos_selected:[],
                empresas: [],
                ips: [],

        		status: false,
        		msg : "",
                color: "error",
                icon: "warning",
                loading: false,

                show_loading: true,
                show: false,
                menu2: false,

                menu_fe: false,

                showPer: false,
                dialog: false,

                showDrop: false,
                dropzoneOptions: {
                    url: '/admin/users/'+this.$route.params.id+'/avatar',
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

            // if (!this.isRoot)
            //     this.items.splice(2,1)

            var id = this.$route.params.id;

            if (id > 0){
                var id = this.$route.params.id;
                axios.get('/admin/users/'+id+'/edit')
                    .then(res => {

                        this.empresas = res.data.empresas;
                        this.user = res.data.user;
                        this.role_user = res.data.role_user;
                        this.permisos = res.data.permisos;

                        this.ips = res.data.ips;

                        this.permisos_selected = res.data.permisos_user;

                        this.emp_user = res.data.emp_user;

                        this.showPer=true;
                        this.show_loading = false;

                    })
                    .catch(err => {
                        if (err.response.status != 401){
                           this.$toast.error(err.response.data.message);
                          //  this.$router.push({ name: 'users'});
                        }
                        if (err.response.status == 403){
                            this.$router.push({ name: 'users.index'});
                        }
                    })
                    .finally(()=> {
                             this.show_loading = false;
                        });
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
            computedDateExpira() {
                moment.locale('es');
                return this.user.fecha_expira ? moment(this.user.fecha_expira).format('L') : '';
            },
            computedFModFormat() {
                moment.locale('es');
                return this.user.updated_at ? moment(this.user.updated_at).format('D/MM/YYYY H:mm') : '';
            },
            computedFCreFormat() {
                moment.locale('es');
                return this.user.created_at ? moment(this.user.created_at).format('D/MM/YYYY H:mm') : '';
            },
            computedLoginAt() {
                moment.locale('es');
                return this.user.login_at ? moment(this.user.login_at).format('DD/MM/YYYY H:mm:ss') : '';
            },
            computedId(){
                if (this.user.id == 1) return true; else return false;
            }

        },
        watch: {
            menu_fe (val) {
                val && setTimeout(() => (this.$refs.picker.activePicker = 'YEAR'))
            }
        },
    	methods:{
            save (date) {
                this.$refs.menu_fe.save(date)
            },
            submit() {

                if (this.loading === false){
                    this.loading = true;

                    var url = '/admin/users/'+this.user.id;

                    this.$validator.validateAll().then((result) => {
                        if (result){

                            axios.put(url, this.user)
                                .then(response => {

                                    this.$toast.success(response.data.msg);
                                    if (response.data.status == "C"){
                                         this.$router.push({ name: 'users_edit', params: { id: response.data.user.id } })
                                    }
                                    else{
                                        this.user.blocked_at = response.data.user.blocked_at;
                                        this.user.updated_at = response.data.user.updated_at;
                                        this.user.username_umod = response.data.user.username_umod;
                                        this.password = this.password_confirmation = "";
                                    }
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
            },
            clearDateExp(){
                this.user.fecha_expira = null;
            },
            openDialog(){

                //this.$emit('update:dialog', true)
                this.dialog = true;
            },
            destroyReg(){
                 axios({
                    method: 'delete',
                    url: '/admin/avatars/'+this.user.id+'/delete',
                    })
                    .then(response => {
                        this.user.avatar = "#";
                        this.$toast.success(response.data.msg);
                    })
                    .catch(err => {
                        this.$toast.error(err.response.data);
                    });
            },
            upload(file, response){
                this.user.avatar = response.url;
            },
            reset(){
                if (confirm('¿Resetar password del usuario?')){
                    axios({
                        method: 'put',
                        url: '/admin/users/'+this.user.id+'/reset',
                        })
                        .then(res => {
                            this.user = res.data.user;
                            this.$toast.success(res.data.msg);
                        })
                        .catch(err => {
                            this.$toast.error(err.response.data);
                        });
                }
            },

    }
  }
</script>
