<template>
	<div>
        <v-form>
            <v-container>
                <v-layout row wrap>
                    <v-flex sm2></v-flex>
                    <v-flex sm3>
                        <v-text-field
                            v-model="permisos.name"
                            v-validate="'required'"
                            :error-messages="errors.collect('name')"
                            label="Nombre"
                            data-vv-name="name"
                            data-vv-as="nombre"
                            required
                        >
                        </v-text-field>
                    </v-flex>
                    <v-flex sm1>
                        <v-text-field
                            v-model="permisos.guard_name"
                            v-validate="'required'"
                            :error-messages="errors.collect('guard_name')"
                            label="Guard"
                            data-vv-name="guard_name"
                            data-vv-as="Guard"
                            required
                        >
                        </v-text-field>
                    </v-flex>
                    <v-flex sm1>
                    </v-flex>
                    <v-flex sm1>
                        <div class="text-xs-center">
                                    <v-btn @click="submit" small  round  :loading="enviando" block  color="primary">
                            Guardar
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

	export default {
		$_veeValidate: {
      		validator: 'new'
        },
        components: {

		},
    	data () {
      		return {
                permisos: {
                    id:       0,
                    name:     "",
                    guard_name: "web",
                    updated_at:"",
                    created_at:"",
                },
                titulo:   "Permisos",
                permisos_id: "",

        		status: false,
                enviando: false,

                show: false

      		}
        },
        mounted(){
            axios.get('/admin/permisos/create')
                .then(res => {
                    this.show = true;
                })
                .catch(err => {
                    this.$toast.error(err.response.data.message);
                    this.$router.push({ name: 'permisos'})
                })
        },

    	methods:{
            submit() {

                this.enviando = true;

                var url = "/admin/permissions";
                var metodo = "post";

                this.$validator.validateAll().then((result) => {
                    if (result){
                        axios({
                            method: metodo,
                            url: url,
                            data:
                                {
                                    name: this.permisos.name,
                                    nombre: this.permisos.name,
                                    guard_name: this.permisos.guard_name,
                                }
                            })
                            .then(response => {

                                this.enviando = false;

                                this.$router.push({ name: 'permisos.edit', params: { id: response.data.id } })
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
