<template>
    <div>
        <form id="login-form" method="POST" :action="action" v-on:keyup.enter="submit">
            <input type="hidden" name="_token" :value="token">
            <v-card-text>
                <v-text-field
                    prepend-icon="person"
                    v-model="username"
                    name="username"
                    label="Usuario"
                    :error-messages="errors.collect('username')"
                    data-vv-name="username"
                    data-vv-as="usuario"
                    :value="old_usr"
                    v-validate="'required'"
                    :disabled="loading"
                >
                </v-text-field>
                <div v-if="err_usr" class="v-messages theme--light error--text text-xs-center">
                    <strong>{{ err_usr }}</strong>
                </div>
                <v-text-field
                    prepend-icon="lock"
                    v-model="password"
                    name="password"
                    label="Password"
                    :error-messages="errors.collect('password')"
                    data-vv-name="password"
                    data-vv-as="contraseña"
                    id="password"
                    v-validate="'required'"
                    type="password"
                    :disabled="loading"
                ></v-text-field>
                <div v-if="err_pas" class="v-messages theme--light error--text  text-xs-center">
                    <strong>{{ err_pas }}</strong>
                </div>
            </v-card-text>

            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn
                    :disabled="loading"
                    @click="submit"
                    round
                    flat
                    color="primary">
                        <span v-show="loading">Espere...</span>
                        <span v-show="!loading">Login</span>
                </v-btn>
            </v-card-actions>
            <input type="hidden" name="blocked" value="0" />

        </form>
        <v-btn icon large  v-on:click="reset">
            <v-avatar size="32px" tile>
                <v-icon color="grey">help</v-icon>
            </v-avatar>
        </v-btn>
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
        old_usr: String,
        err_usr: String,
        err_pas: String
    },
    data() {
        return {
            username:this.old_usr,
            //username:"ricardo.bm",
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
        reset(){
            window.open('password/reset');
        }
    }
}
</script>

