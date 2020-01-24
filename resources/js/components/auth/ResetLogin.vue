<template>

    <form id="login-form" method="POST" :action="action" v-on:keyup.enter="submit">
        <input type="hidden" name="_token" :value="token">
        <input type="hidden" name="token"  :value="token_mail">
            <v-container>
                <v-layout row wrap>
                    <v-flex xs2></v-flex>
                    <v-flex xs8>
                        <v-text-field
                            prepend-icon="mail"
                            v-model="f_email"
                            id="email"
                            name="email"
                            label="email"
                            data-vv-name="email"
                            data-vv-as="email"
                            v-validate="'required|email'"
                            :error-messages="errors.collect('email')"
                            :value="f_email"
                            required
                        >
                        </v-text-field>

                        <div class="v-messages theme--light error--text text-xs-center">
                            {{ err_mail }}
                        </div>

                    </v-flex>
                </v-layout>
                <v-layout row wrap>
                    <v-flex xs2></v-flex>
                        <v-flex xs8>
                            <v-text-field
                                prepend-icon="lock"
                                v-model="password"
                                ref="password"
                                :counter="8"
                                :append-icon="show ? 'visibility_off' : 'visibility'"
                                :type="show ? 'text' : 'password'"
                                :error-messages="errors.collect('password')"
                                v-validate="'min:6'"
                                label="password"
                                name="password"
                                data-vv-name="password"
                                @click:append="show = !show"
                                >
                            </v-text-field>
                            <div class="v-messages theme--light error--text text-xs-center">
                                {{ err_pass }}
                            </div>
                    </v-flex>
                </v-layout>
                <v-layout row wrap>
                    <v-flex xs2></v-flex>
                        <v-flex xs8>
                            <v-text-field
                                prepend-icon="lock"
                                v-model="password_confirmation"
                                v-validate="'min:8|confirmed:password'"
                                :append-icon="show ? 'visibility_off' : 'visibility'"
                                :type="show ? 'text' : 'password'"
                                :error-messages="errors.collect('password_confirmation')"
                                label="confirmar password"
                                hint="Confirmar password solo si va a modificarla"
                                name="password_confirmation"
                                data-vv-name="password_confirmation"
                                data-vv-as="password"
                                @click:append="show = !show"
                                >
                            </v-text-field>
                        </v-flex>
                    </v-layout>
                <v-layout row wrap>
                    <v-flex xs6></v-flex>
                    <v-btn
                    :disabled="loading"
                    @click="submit"
                    color="primary">
                        <span v-show="loading">Espere...</span>
                        <span v-show="!loading">Enviar</span>
                </v-btn>
                </v-layout>
        </v-container>
    </form>

</template>
<script>
export default {
    $_veeValidate: {
      		validator: 'new'
    	},
    props:{
        action: String,
        token: String,
        token_mail: String,
        email: String,
        err_mail: String,
        err_pass: String
    },
    data() {
        return {
            show: false,
            loading: false,
            f_email: this.email,
            password: "",
            password_confirmation: ""
        }
    },
    mounted(){
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

        }
    }
}
</script>


