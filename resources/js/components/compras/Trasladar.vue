<template>
  <v-layout row justify-center>
    <v-dialog v-model="dialog_trasladar" persistent max-width="400px">

      <v-card>
        <v-card-title>
          <span class="headline">Trasladar Compra</span>
        </v-card-title>
        <v-card-text>
            <v-form>
                <v-container grid-list-md>
                    <v-layout wrap>
                        <v-flex sm10 d-flex>
                            <v-select
                                v-model="empresa_id"
                                :error-messages="errors.collect('empresa_id')"
                                :items="empresas"
                                label="Empresa Destino"
                            ></v-select>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-form>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="blue darken-1" flat round @click="closeDialog">Cerrar</v-btn>
          <v-btn color="blue darken-1" flat round @click="submit" :disabled="loading" :loading="loading">Trasladar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-layout>
</template>
<script>

  export default {
    props:{
        compra: Object,
        dialog_trasladar: Boolean,
    },
    data: () => ({
        empresas:[],
        empresa_id: "",
        url: "/compras/trasladar",
        loading: false
    }),
    mounted(){
        axios.get(this.url)
            .then(res => {
                this.empresas = res.data.empresas;
                this.empresa_id = this.empresas[0].value;
            })
            .catch(err => {
                this.loading = false;
            });
    },
    methods:{
        closeDialog (){
            this.$emit('update:dialog_trasladar', false)
        },
        submit() {

             if (this.loading === false){
                this.loading = true;

                axios.put(this.url+"/"+this.compra.id,{destino_empresa_id: this.empresa_id} )
                    .then(res => {

                       // this.$emit('update:compra', this.compra)
                        this.$emit('update:dialog_trasladar', false)

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
                    })
                    .finally(()=> {
                        this.loading = false;
                    });
            }

        }
      }
  }
</script>
