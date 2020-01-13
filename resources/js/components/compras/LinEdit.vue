<template>
  <v-layout v-if="dialog_edt" row justify-center>
    <v-dialog v-model="dialog_edt" persistent max-width="800px">
      <v-card>
        <v-card-title>
          <span class="headline">Editar línea</span>
        </v-card-title>
        <v-card-text>
            <v-form>
                <v-container grid-list-md>
                    <v-layout wrap>
                        <v-flex sm12>
                            <v-textarea
                                v-model="editedItem.concepto"
                                outline
                                :error-messages="errors.collect('concepto')"
                                label="Indicar piezas del lote"
                                data-vv-name="concepto"
                                required
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
                                v-if="show_peso"
                                v-model="editedItem.peso_gr"
                                v-validate="'decimal:2'"
                                :disabled="!show_peso"
                                :error-messages="errors.collect('peso_gr')"
                                label="Peso "
                                data-vv-name="peso_gr"
                                data-vv-as="Peso"
                                class="inputPrice"
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
                                required
                                :disabled="compra.fase_id!=1"
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
          <v-btn color="blue darken-1" round flat @click="closeDialog">Cancelar</v-btn>
          <v-btn color="blue darken-1" round flat @click="submit" :loading="loading">Guardar</v-btn>
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
        dialog_edt: Boolean,
        editedItem: Object,
        show_grab: Boolean,
        clases: Array,
        compra: Object,
        quilates: Array
    },
    data: () => ({
        show: false,
        show_peso: true,
        show_quil: true,
        loading: false,
    }),
    mounted(){
        this.show_quil = this.editedItem.clase.quilates;
        this.show_peso = this.editedItem.clase.peso;
        this.comline = this.editedItem;
    },
    computed:{
        computedImporte(){
            if (this.comline.unidades != ""){
                this.comline.importe =  parseFloat(this.comline.unidades) * parseFloat(this.comline.impuni);
                return this.comline.importe;
            }
        }
    },
    methods:{
        closeDialog (){
            this.$emit('update:dialog_edt', false)
        },
        selClase(id){

            let index = this.clases.findIndex((item) => item.value === id);

            this.show_peso = this.clases[index].peso;
            this.show_quil = this.clases[index].quilates;

            if (!this.show_peso)
                this.editedItem.peso = '';

            if (!this.show_quil)
                this.editedItem.quilates = '';
        },
        submit() {
            if (this.loading === false){
                this.loading = true;

                if (this.editedItem.clase_id != 1)
                    this.editedItem.quilates = 0;

                var url = "/compras/comlines/"+this.editedItem.id;

                this.$validator.validateAll().then((result) => {
                    if (result){

                        axios.put(url, this.editedItem)
                            .then(res => {

                                this.$emit('update:dialog_edt', false)
                                this.loading = false;

                                this.$emit('reLoadLineas', res.data.lineas, res.data.totales)
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
