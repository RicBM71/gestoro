<template>
	<div>
        <v-card>
            <v-card-title color="indigo">
                <h2 color="indigo">{{titulo}}</h2>
                <v-spacer></v-spacer>
                <menu-ope :id="empresa.id"></menu-ope>
            </v-card-title>
        </v-card>
        <v-card>
            <v-form>
                <v-container>
                    <v-layout row wrap>
                        <v-flex sm3>
                            <v-text-field
                                v-model="empresa.nombre"
                                v-validate="'required'"
                                :error-messages="errors.collect('nombre')"
                                label="Nombre"
                                data-vv-name="nombre"
                                data-vv-as="nombre"
                                required
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm3>
                            <v-text-field
                                v-model="empresa.razon"
                                v-validate="'required'"
                                :error-messages="errors.collect('razon')"
                                label="Razon"
                                data-vv-name="razon"
                                required
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm3>
                            <v-text-field
                                v-model="empresa.titulo"
                                v-validate="'required'"
                                :error-messages="errors.collect('titulo')"
                                label="Tíitulo"
                                data-vv-name="titulo"
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm5>
                        </v-flex>
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
        </v-card>
	</div>
</template>
<script>
import moment from 'moment'
import MenuOpe from './MenuOpe'
import {mapActions} from "vuex";
import {mapGetters} from 'vuex';

	export default {
		$_veeValidate: {
      		validator: 'new'
        },
        components: {
            'menu-ope': MenuOpe
		},
    	data () {
      		return {
                titulo:"Empresas",
                empresa: {
                    id: 0,
                    nombre: "",
                    razon: "",
                    cif: "",
                    poblacion: "",
                    direccion: "",
                    cpostal: "",
                    provincia: "",
                    telefono1: "",
                    telefono2: "",
                    contacto: "",
                    email: "",
                    web: "",
                    txtpie1: "",
                    txtpie2: "",
                    flags: "",
                    sigla: "",
                    path_archivo: "",
                    titulo: "",
                    logo:"",
                    certificado:"",
                    passwd_cer:"",
                    carext_id:"",
                    carn43_id:"",
                    username: "",
                    updated_at:"",
                    created_at:"",
                },
                empresa_id: "",

        		status: false,
                loading: false,

                show: false,
      		}
        },
        mounted(){
            axios.get('/admin/empresas/create')
                .then(res => {
                    this.show = true;
                })
                .catch(err => {
                    this.$toast.error(err.response.data.message);
                    this.$router.push({ name: 'empresa.index'})
                })
        },

        computed: {
            ...mapGetters([
                'userId'
		    ]),
            computedFModFormat() {
                moment.locale('es');
                return this.empresa.updated_at ? moment(this.empresa.updated_at).format('D/MM/YYYY H:mm') : '';
            },
            computedFCreFormat() {
                moment.locale('es');
                return this.empresa.created_at ? moment(this.empresa.created_at).format('D/MM/YYYY H:mm') : '';
            }

        },
    	methods:{
             ...mapActions([
				'setAuthUser'
			]),
            submit() {

                if (this.loading === false){
                    this.loading = true;

                    var url = "/admin/empresas";

                    this.$validator.validateAll().then((result) => {
                        if (result){
                            axios.post(url, this.empresa)
                                .then(response => {

                                    // this.setEmpresa(response.data.empresa.id);
                                    this.$toast.success('Cambiar a la nueva empresa para configurar');

                                    this.loading = false;
                                    this.$router.push({ name: 'dash' })
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
            setEmpresa(empresa_id){

                // this.empresas.map((e) =>{
                //     if (e.id == this.empresa_id)
                //             this.empresaTxt = e.name;
                //     });

                axios({
                        method: 'put',
                        url: '/admin/users/'+this.userId+'/empresa',
                        data:{ empresa_id: empresa_id }
                    })
                    .then(res => {

                        this.setAuthUser(res.data.user);


                        this.$toast.success("Cambiando de empresa...");
                        this.$router.push({name: 'dash'});
                    })
                    .catch(err => {
                        this.$toast.error("No se ha podido seleccionar la empresa");
                    });

                this.myEmpresa=false;
            },

    }
  }
</script>
