<template>
  <v-layout row justify-center>
    <v-dialog v-model="dialog_fas" persistent max-width="400px">

      <v-card>
        <v-card-title>
          <span class="headline">Corregir Fase</span>
        </v-card-title>
        <v-card-text>
            <v-form>
                <v-container grid-list-md>
                    <v-layout wrap>
                        <v-flex sm10 d-flex>
                            <v-select
                                v-model="fase_id"
                                :items="fases"
                                label="Fase"
                            ></v-select>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-form>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="blue darken-1" flat @click="closeDialog">Cerrar</v-btn>
          <v-btn color="blue darken-1" flat @click="submit" :disabled="loading" :loading="loading">Guardar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-layout>
</template>
<script>

  export default {
    props:{
        albaran: Object,
        dialog_fas: Boolean,
    },
    data: () => ({
        fases:[],
        fase_id: "",
        url: "/ventas/albaranes",
        loading: false
    }),
    mounted(){
        
        axios.get('/utilidades/helpfases/venta')
            .then(res => {
                this.fases = res.data.fases;
                this.fase_id = this.albaran.fase_id;
            })
            .catch(err => {
                this.loading = false;
            });
    },
    methods:{
        closeDialog (){
            this.$emit('update:dialog_fas', false)
        },
        submit() {
            var idx = this.fases.map(x => x.value).indexOf(this.fase_id);
//            this.compra.fase.nombre = this.fases[idx].text;

             if (this.loading === false){
                this.loading = true;

                axios.put(this.url+"/"+this.albaran.id+"/fase",{fase_id: this.fase_id} )
                    .then(res => {
                        this.albaran.fase_id = this.fase_id;
                        this.albaran.fase.nombre = this.fases[idx].text;
                       // this.$emit('update:compra', this.compra)
                        this.$emit('update:dialog_fas', false)
                        this.loading = false;
                    })
                    .catch(err => {
                        this.$toast.error(err.response.data.message);
                        this.loading = false;
                    });
            }

        }
      }
  }
</script>
