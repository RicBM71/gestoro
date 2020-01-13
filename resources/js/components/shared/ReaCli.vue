<template>
    <v-dialog v-model="dialog_reacli" persistent max-width="420">
        <v-card>
            <v-card-title class="headline">Reasignar Cliente</v-card-title>
            <v-card-text>
                <v-layout row wrap>
                    <v-flex sm2></v-flex>
                    <v-flex sm8>
                        <v-text-field
                            v-model="dni"
                            v-validate="'required|alpha_num|min:4'"
                            :error-messages="errors.collect('dni')"
                            label="Nº Documento"
                            required
                            data-vv-name="dni"
                            data-vv-as="Documento"
                            v-on:keyup.enter="goUpdate"
                        >
                        </v-text-field>
                    </v-flex>
                </v-layout>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue darken-1" round flat @click="closeDialog()">Cancelar</v-btn>
                <v-btn color="blue darken-1" round flat @click="goUpdate">Aceptar</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
<script>
  export default {
    props:{
            dialog_reacli: Boolean,
            cliente_id_nuevo: Number
    },
      data () {
      return {
            dni: "",
        }
    },
    methods:{
        closeDialog (){
            this.$emit('update:dialog_reacli', false)
        },
        goUpdate () {
             axios.post('/utilidades/helpcli/dni',{dni: this.dni})
                .then(res => {

                    this.$emit('update:dialog_reacli', false);
                    this.$emit('update:cliente_id_nuevo', res.data.id);
                    this.$emit('goReaCli');

                })
                .catch(err =>{
                    this.$toast.error(err.response.data.message);
                })
        }
      }
  }
  </script>
