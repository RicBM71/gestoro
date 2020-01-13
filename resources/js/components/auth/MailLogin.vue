<template>
    <div>
        <form id="login-form" method="POST" :action="action" v-on:keyup.enter="submit">
            <input type="hidden" name="_token" :value="token">
            <v-card-text>
                <v-text-field
                    prepend-icon="mail"
                    v-model="username"
                    name="email"
                    label="email"
                    :error-messages="errors.collect('email')"
                    data-vv-name="email"
                    data-vv-as="email"
                    :value="old_mail"
                    v-validate="'required|email'"
                    :disabled="loading"
                >
                </v-text-field>
                <div v-if="err_mail" class="v-messages theme--light error--text text-xs-center">
                    <strong>{{ err_mail }}</strong>
                </div>
            </v-card-text>

            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn
                    :disabled="loading"
                    @click="submit"
                    color="primary">
                        <span v-show="loading">Espere...</span>
                        <span v-show="!loading">Enviar</span>
                </v-btn>
            </v-card-actions>
        </form>
    </div>
</template>
<script>
export default {
    $_veeValidate: {
      		validator: 'new'
    	},
    props:{
        action: String,
        token: String,
        old_mail: String,
        err_mail: String,
        err_pas: String
    },
    data() {
        return {
            username:this.old_mail,
            password:"",
            loading: false
        }
    },
    methods:{
        submit() {
            this.$validator.validateAll().then((result) => {
                if (result){
                    this.loading = true;
                    document.getElementById('login-form').submit()
                }
                else{
                    this.loading = false;
                }
            });

        },
    }
}
</script>

