<template>
	<div v-show="show">
        <v-card>
            <v-card-title color="indigo">
                <h2 color="indigo">{{titulo}}</h2>
                <v-spacer></v-spacer>
                <menu-ope :id="role.id"></menu-ope>
            </v-card-title>
        </v-card>
        <v-card>
            <v-form>
                <v-container>
                    <v-layout row wrap>
                        <v-flex sm1></v-flex>
                        <v-flex sm3>
                            <v-text-field
                                v-model="role.name"
                                v-validate="'required'"
                                :error-messages="errors.collect('name')"
                                label="Nombre"
                                data-vv-name="name"
                                data-vv-as="nombre"
                                readonly
                                disabled
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm1>
                            <v-text-field
                                v-model="role.guard_name"
                                v-validate="'required'"
                                :error-messages="errors.collect('guard_name')"
                                label="Guard"
                                data-vv-name="guard_name"
                                data-vv-as="Guard"
                                disabled
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
                    <v-layout row wrap v-if="this.role.id > 0">
                        <v-flex sm12>
                            <h3>Permisos</h3>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap v-if="this.role.id > 0">
                        <v-flex sm3
                            v-for="item in permisos"
                            :key="item"
                        >
                            <v-switch
                                v-model="permiso_role"
                                :label="item"
                                :value="item"
                                color="primary">
                            ></v-switch>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex sm9></v-flex>
                        <v-flex sm2>
                            <div class="text-xs-center">
                                        <v-btn @click="submit"  round  :loading="enviando" block  color="primary">
                                Guardar Role
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
import MenuOpe from './MenuOpe'
import RolePermisos from './RolePermisos'

	export default {
		$_veeValidate: {
      		validator: 'new'
        },
        components: {
            'role-permisos': RolePermisos,
            'menu-ope': MenuOpe
		},
    	data () {
      		return {
                role: {
                    id:       0,
                    name:     "",
                    guard_name: "",
                    updated_at:"",
                    created_at:"",
                },
                titulo:   "Roles",
                role_id: "",

                permiso_role:[],
                permisos:[],

        		status: false,
                enviando: false,

                show: false

      		}
        },
        mounted(){
            var id = this.$route.params.id;

            if (id > 0)
                axios.get('/admin/roles/'+id+'/edit')
                    .then(res => {
                        this.role = res.data.role;
                        this.permiso_role = res.data.permiso_role;
                        this.permisos = res.data.permisos;
                        this.show = true;
                    })
                    .catch(err => {
                        this.$toast.error(err.response.data.message);
                        this.$router.push({ name: 'roles'})
                    })
        },

        computed: {
            computedFModFormat() {
                moment.locale('es');
                return this.role.updated_at ? moment(this.role.updated_at).format('D/MM/YYYY H:mm') : '';
            },
            computedFCreFormat() {
                moment.locale('es');
                return this.role.created_at ? moment(this.role.created_at).format('D/MM/YYYY H:mm') : '';
            }

        },
    	methods:{
            submit() {

                this.enviando = true;

                var url = "/admin/roles";
                var metodo = "post";

                if (this.role.id > 0){
                    url += '/'+this.role.id;
                    metodo = "put";
                }

                this.$validator.validateAll().then((result) => {
                    if (result){
                        axios({
                            method: metodo,
                            url: url,
                            data:
                                {
                                    name: this.role.name,
                                    guard_name: this.role.guard_name,
                                    permissions: this.permiso_role
                                }
                            })
                            .then(response => {
                                this.$toast.success(response.data);
                                this.enviando = false;
                            })
                            .catch(err => {

                                if (err.request.status == 422){ // fallo de validated.
                                    const msg_valid = err.response.data.errors;
                                    for (const prop in msg_valid) {
                                        this.$toast.error(`${msg_valid[prop]}`);
                                    }
                                }else{
                                    this.$toast.error(err.response.data.message);
                                }
                                this.enviando = false;
                            });
                        }
                    else{
                        this.enviando = false;
                    }
                });

            },

    }
  }
</script>
