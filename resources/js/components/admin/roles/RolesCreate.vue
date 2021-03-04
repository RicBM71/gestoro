<template>
	<div>
        <v-form>
            <v-container>
                <v-layout row wrap>
                    <v-flex sm3></v-flex>
                    <v-flex sm3>
                        <v-text-field
                            v-model="role.name"
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
                        <div class="text-xs-center">
                                    <v-btn @click="submit" small flat round  :loading="loading" block  color="primary">
                            Guardar Role
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
            role: {
                id:       0,
                name:     "",
                guard_name: "web",
            },
            titulo:   "Roles",
            role_id: "",

            status: false,
            loading: false,

            show: false

        }
    },
    mounted(){
        axios.get('/admin/roles/create')
            .then(res => {
                this.show = true;
            })
            .catch(err => {
                this.$toast.error(err.response.data.message);
                this.$router.push({ name: 'roles.index'})
            })
    },

    computed: {
    },
    methods:{
        submit() {

                if (this.loading === false){
                    this.loading = true;

                    var url = "/admin/roles";

                    this.$validator.validateAll().then((result) => {
                        if (result){
                            axios.post(url,this.role)
                                .then(response => {

                                    this.loading = false;
                                    this.$router.push({ name: 'roles.edit', params: { id: response.data.id } })
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
        }
    }
}
</script>
