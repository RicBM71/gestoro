<template>
  <v-layout row justify-center>
    <v-dialog v-model="dialog_lin" persistent max-width="800px">

      <v-card>
        <v-card-title>
          <span class="headline">Líneas</span>
        </v-card-title>
        <v-card-text>
            <v-form>
                <v-container grid-list-md>
                    <v-layout wrap>
                        <v-flex sm12>
                            <v-textarea
                                v-model="editedItem.concepto"
                                v-validate="'required'"
                                ref="concepto"
                                outline
                                :error-messages="errors.collect('concepto')"
                                label="Indicar piezas del lote"
                                data-vv-name="concepto"
                            ></v-textarea>
                        </v-flex>
                    </v-layout>
                    <v-layout wrap v-if="show_grab">
                        <v-flex sm6>
                            <v-textarea
                                v-model="editedItem.grabaciones"
                                height="80"
                                outline
                                hint="Detallar pieza por grabación"
                                label="Indicar grabaciones"
                            ></v-textarea>
                        </v-flex>
                        <v-flex sm6>
                            <v-textarea
                                v-model="editedItem.colores"
                                hint="Agrupar piezas por colores"
                                height="80"
                                outline
                                label="Indicar colores"
                            ></v-textarea>
                        </v-flex>
                    </v-layout>
                    <v-layout wrap>
                        <v-flex sm4 d-flex>
                                <v-select
                                v-model="editedItem.clase_id"
                                :items="clases"
                                label="Clase"
                                @change="selClase(editedItem.clase_id)"
                                ></v-select>
                        </v-flex>
                        <v-flex sm2 d-flex>
                            <v-select
                                v-model="editedItem.quilates"
                                :disabled="!show_quil"
                                :items="quilates"
                                label="Quilates"
                                v-validate="'numeric'"
                                :error-messages="errors.collect('quilates')"
                                data-vv-name="quilates"
                                data-vv-as="quilates"
                            ></v-select>
                        </v-flex>
                        <v-flex sm2>
                            <v-text-field
                                v-model="editedItem.peso_gr"
                                v-validate="'decimal:2'"
                                :disabled="!show_peso"
                                :error-messages="errors.collect('peso_gr')"
                                label="Peso "
                                data-vv-name="peso_gr"
                                data-vv-as="Peso"
                                class="inputPrice"
                                type="number"
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm3>
                            <v-text-field
                                v-model="editedItem.importe"
                                v-validate="'required|decimal:2'"
                                :error-messages="errors.collect('importe')"
                                label="Importe"
                                data-vv-name="importe"
                                data-vv-as="importe"
                                type="number"
                                required
                                class="inputPrice"
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-form>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="blue darken-1" round flat @click="closeDialog">Cerrar</v-btn>
          <v-btn color="blue darken-1" round flat @click="submit" :disabled="loading" :loading="loading">Guardar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-layout>
</template>
<script>

  export default {
    $_veeValidate: {
        validator: 'new'
    },
    props:{
        compra_id: Number,
        dialog_lin: Boolean,
        show_grab: Boolean,
        clases: Array,
        quilates: Array

    },
    data: () => ({
        loading: false,
        show_peso: false,
        show_quil: false,

        editedItem: {
            id:0,
            compra_id:"",
            concepto: "",
            grabaciones:"",
            colores:"",
            peso:"",
            importe:"",
            quilates:18,
        },
    }),
    mounted(){

        this.$refs.concepto.focus();

        this.editedItem.clase_id = this.clases[0].value;
        this.selClase(this.editedItem.clase_id);

    },
    // beforeUpdate(){
    //     if (this.dialog_lin){
    //         this.selClase(this.editedItem.clase_id);
    //     }
    // },
    methods:{
        closeDialog (){

            this.$emit('update:dialog_lin', false)
        },
        selClase(id){

            let index = this.clases.findIndex((item) => item.value === id);

            this.show_peso = this.clases[index].peso;
            this.show_quil = this.clases[index].quilates;

            if (!this.show_peso)
                this.editedItem.peso_gr = '';

            if (!this.show_quil)
                this.editedItem.quilates = '';

        },
        submit() {
            if (this.loading === false){
                this.loading = true;

                this.editedItem.compra_id = this.compra_id;

                var url = "/compras/comlines";

                 if (this.editedItem.clase_id != 1)
                    this.editedItem.quilates = 0;


                this.$validator.validateAll().then((result) => {

                    if (result){

                        axios.post(url, this.editedItem)
                            .then(res => {
                                this.$emit('update:dialog_lin', false)
                                //this.$router.push({ name: 'albaran.edit', params: { id: this.compra_id } })
                                this.loading = false;
                                this.$emit('reLoadLineas', res.data.lineas, res.data.totales)

                                this.reset();
                                this.$validator.reset();

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
                    else{
                        this.loading = false;
                    }
                });
            }
        },
        reset(){

            this.editedItem.concepto =  null;
            this.editedItem.grabaciones = null;
            this.editedItem.colores = null;
            this.editedItem.peso_gr = 0;
            this.editedItem.importe = 0;
            this.editedItem.quilates = 0;

        }
      }
  }
</script>

<style scoped>


.inputPrice >>> input {
  text-align: center;
  -moz-appearance:textfield;
}

input[type=number]::-webkit-inner-spin-button,
input[type=number]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    margin: 0;
}
</style>
