<template>
	<div v-show="show">
        <v-card>
            <v-card-title color="indigo">
                <h2 color="indigo">{{titulo}}</h2>
                <v-spacer></v-spacer>
                <menu-ope :id="permiso.id"></menu-ope>
            </v-card-title>
        </v-card>
        <v-card>
            <v-form>
                <v-container>
                    <v-layout row wrap>
                        <v-flex sm1></v-flex>
                        <v-flex sm2>
                            <v-text-field
                                v-model="permiso.name"
                                v-validate="'required'"
                                :error-messages="errors.collect('name')"
                                label="Name"
                                data-vv-name="name"
                                data-vv-as="nombre"

                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm2>
                            <v-text-field
                                v-model="permiso.nombre"
                                v-validate="'required'"
                                :error-messages="errors.collect('nombre')"
                                label="Nombre"
                                data-vv-name="nombre"
                                data-vv-as="nombre"
                                
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm1>
                            <v-text-field
                                v-model="permiso.guard_name"
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
                    <v-layout row wrap>
                        <v-flex sm9></v-flex>
                        <v-flex sm2>
                            <div class="text-xs-center">
                                        <v-btn @click="submit"  round  :loading="enviando" block  color="primary">
                                Guardar
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
	export default {
		$_veeValidate: {
      		validator: 'new'
        },
        components: {
            'menu-ope': MenuOpe
		},
    	data () {
      		return {
                permiso: {
                },
                titulo:"Permisos",

        		status: false,
                enviando: false,

                show: false

      		}
        },
        mounted(){
            var id = this.$route.params.id;

            if (id > 0)
                axios.get('/admin/permissions/'+id+'/edit')
                    .then(res => {
                        console.log(res);
                        this.permiso = res.data;
                        this.show = true;
                    })
                    .catch(err => {
                        this.$toast.error(err.response.data.message);
                        this.$router.push({ name: 'permisos.index'})
                    })
        },

        computed: {
            computedFModFormat() {
                moment.locale('es');
                return this.permiso.updated_at ? moment(this.permiso.updated_at).format('D/MM/YYYY H:mm') : '';
            },
            computedFCreFormat() {
                moment.locale('es');
                return this.permiso.created_at ? moment(this.permiso.created_at).format('D/MM/YYYY H:mm') : '';
            }

        },
    	methods:{
            submit() {

                this.enviando = true;

                var url = "/admin/roles";
                var metodo = "post";

                if (this.permiso.id > 0){
                    url += '/'+this.permiso.id;
                    metodo = "put";
                }

                this.$validator.validateAll().then((result) => {
                    if (result){
                        axios({
                            method: metodo,
                            url: url,
                            data:
                                {
                                    name: this.permiso.name,
                                    guard_name: this.permiso.guard_name,
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
