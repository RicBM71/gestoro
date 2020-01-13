<template>
    <v-form>
        <v-container>
            <v-layout row wrap>
               <v-flex sm8 d-flex></v-flex>
               <v-flex sm2 d-flex>
                    <v-select
                    v-model="situacion_id"
                    v-validate="'required'"
                    data-vv-name="situacion_id"
                    data-vv-as="Estado"
                    :error-messages="errors.collect('situacion_id')"
                    :items="situaciones"
                    label="Estado"
                    ></v-select>
                </v-flex>
                <v-flex sm1>
                    <v-btn @click="submit"  :loading="loading" round small block  color="info">
                        Filtrar
                    </v-btn>
                </v-flex>
            </v-layout>
        </v-container>
    </v-form>
</template>
<script>
import moment from 'moment'
export default {
    $_veeValidate: {
        validator: 'new'
    },
    props:{
        filtro: Boolean,
        arr_reg: Array
    },
    data () {
      return {

            loading: false,
            result: false,

            situaciones: [
                {value: '1', text:"Solicitado"},
                {value: '2', text:"Autorizado"},
                {value: '3', text:"Entregado"},
                ],
            situacion_id: '1'
      }
    },
    mounted(){

    },
    methods:{
        submit(){

            if (this.loading === false){
                this.loading = true;
                this.$validator.validateAll().then((result) => {
                    if (result){
                        axios.post('/mto/traspasos/filtrar',{situacion_id: this.situacion_id})
                        .then(res => {

                            this.$emit('update:arr_reg', res.data);

                            if (res.data.length == 0)
                                this.$toast.warning('No se han encontrado registros');

                            this.loading = false;

                        })
                        .catch(err => {
                            if (err.request.status == 422){ // fallo de validated.
                                const msg_valid = err.response.data.errors;
                                for (const prop in msg_valid) {
                                    // this.$toast.error(`${msg_valid[prop]}`);

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
                 });
            }
        },
    }
}
</script>
