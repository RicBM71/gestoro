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
                    <v-layout wrap>
                        <v-flex sm10 d-flex>
                            <v-select
                                v-model="grupo_id"
                                :error-messages="errors.collect('grupo_id')"
                                :items="grupos"
                                label="Libro Destino"
                            ></v-select>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-form>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="blue darken-1" flat round @click="closeDialog">Cerrar</v-btn>
          <v-btn color="blue darken-1" flat round @click="submit" :disabled="disabled" :loading="loading">Trasladar</v-btn>
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
        grupos:[],
        empresa_id: "",
        grupo_id:"",
        url: "/compras/trasladar",
        loading: false,
        disabled: false,
    }),
    mounted(){
        axios.get(this.url)
            .then(res => {
                this.empresas = res.data.empresas;
                if (this.empresas.length > 0){
                    this.empresa_id = this.empresas[0].value;
                }
                else
                    this.disabled = true;
            })
            .catch(err => {
                this.loading = false;
            });
    },
    watch:{
        empresa_id: function () {
            axios.get(this.url+"/"+this.empresa_id+"/grupo")
                .then(res => {
                    this.grupos = res.data.grupos;
                    this.grupo_id = this.grupos[0].value;
                })
                .catch(err => {
                    this.loading = false;
                });
        }
    },
    methods:{
        closeDialog (){
            this.$emit('update:dialog_trasladar', false)
        },
        submit() {

             if (this.loading === false){
                this.loading = true;

                axios.put(this.url+"/"+this.compra.id,
                            {destino_empresa_id: this.empresa_id,
                             destino_grupo_id: this.grupo_id })
                    .then(res => {
                       // this.$emit('update:compra', this.compra)
                        this.$emit('update:dialog_trasladar', false)

                        if (res.data.estado == true)
                            this.$toast.success('Se ha trasladado la compra!');
                        else
                            this.$toast.warning('Check contadores!!, traslado con incidencias!');

                        this.$router.push({ name: 'dash' })

                        // if (this.compra.tipo_id == 1)
                        //     this.$router.push({ name: 'recompra.close', params: { id: this.compra.id } })
                        // else
                        //     this.$router.push({ name: 'compra.close', params: { id: this.compra.id } })




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
