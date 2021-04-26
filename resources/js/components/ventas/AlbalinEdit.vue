<template>
  <v-layout v-if="dialog_edt" row justify-center>
    <v-dialog v-model="dialog_edt" persistent max-width="800px">
        <v-card>
            <v-card-title>
            <span class="headline">Editar producto</span>
            </v-card-title>
            <v-card-text>
                <v-form>
                    <v-container grid-list-md>
                        <v-layout wrap>
                            <v-flex sm2>
                                <v-text-field
                                    v-model="editedItem.producto.referencia"
                                    v-validate="'required|alpha_num'"
                                    :error-messages="errors.collect('referencia')"
                                    label="Referencia "
                                    data-vv-name="referencia"
                                    data-vv-as="referencia"
                                    readonly
                                >
                                </v-text-field>
                            </v-flex>
                            <v-flex sm10>
                                <v-text-field
                                    v-model="editedItem.producto.nombre"
                                    label="Producto"
                                    readonly
                                >
                                </v-text-field>
                            </v-flex>
                        </v-layout>
                        <v-layout wrap>
                            <v-flex sm2></v-flex>
                            <v-flex sm10>
                                <v-text-field
                                    :value="computedCarateristicas"
                                    label="Detalles"
                                    readonly
                                >
                                </v-text-field>
                            </v-flex>
                        </v-layout>
                        <v-layout wrap>
                            <v-flex sm2 d-flex>
                                <v-text-field
                                    v-model="editedItem.unidades"
                                    v-validate="'required|decimal:2'"
                                    :error-messages="errors.collect('unidades')"
                                    label="Unidades/Peso "
                                    data-vv-name="unidades"
                                    data-vv-as="Unidades/Peso"
                                    class="inputPrice"
                                    type="number"
                                    :disabled="albaran.tipo_id==3 && editedItem.producto.stock == 1"
                                    v-on:keyup.enter="submit"
                                >
                                </v-text-field>
                            </v-flex>
                            <v-flex sm2 d-flex>
                                <v-text-field
                                    v-show="editedItem.producto.univen=='U'"
                                    v-model="editedItem.precio_coste"
                                    label="Precio Coste"
                                    class="inputPrice"
                                    :disabled="!computedCoste"
                                >
                                </v-text-field>
                            </v-flex>
                            <v-flex sm2>
                                <v-text-field
                                    v-if="albaran.tipo_id==3"
                                    v-model="editedItem.importe_unidad"
                                    v-validate="'required|decimal:2'"
                                    :error-messages="errors.collect('importe_unidad')"
                                    label="Importe Ud."
                                    data-vv-name="importe_unidad"
                                    data-vv-as="importe"
                                    class="inputPrice"
                                    type="number"
                                    v-on:keyup.enter="submit"
                                >
                                </v-text-field>
                                <v-text-field
                                    v-else
                                    v-model="editedItem.importe_unidad"
                                    v-validate="'required|decimal:4'"
                                    :error-messages="errors.collect('importe_unidad')"
                                    label="Importe Ud. "
                                    data-vv-name="importe_unidad"
                                    data-vv-as="importe"
                                    class="inputPrice"
                                    type="number"
                                    v-on:keyup.enter="submit"
                                >
                                </v-text-field>
                            </v-flex>
                            <v-flex sm2 d-flex v-if="albaran.tipo_id >= 3">
                                <v-text-field
                                    v-model="editedItem.descuento"
                                    v-validate="'required|decimal:2'"
                                    :error-messages="errors.collect('descuento')"
                                    label="% Descuento"
                                    data-vv-name="descuento"
                                    data-vv-as="descuento"
                                    class="inputPrice"
                                    type="number"
                                    :readonly="editedItem.iva_id==2"
                                    v-on:keyup.enter="submit"
                                >
                                </v-text-field>
                            </v-flex>
                            <v-flex sm3>
                                <v-text-field
                                    :value="computedImporte"
                                    label="Importe"
                                    disabled
                                    class="inputPrice"
                                >
                                </v-text-field>
                            </v-flex>
                        </v-layout>
                        <v-layout wrap v-if="albaran.tipo_id>=4">
                            <v-flex sm12 d-flex>
                                <v-text-field
                                    v-model="editedItem.notas"
                                    v-validate="'max:190'"
                                    :error-messages="errors.collect('notas')"
                                    label="Notas"
                                    data-vv-name="notas"
                                    data-vv-as="notas"
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
            <v-btn color="blue darken-1" round flat @click="submit" :disabled="computedSubmit" :loading="loading">Guardar</v-btn>
            </v-card-actions>
      </v-card>
    </v-dialog>
  </v-layout>
</template>
<script>
import {mapGetters} from 'vuex';
export default {
    $_veeValidate: {
        validator: 'new'
    },
    props:{
        albaran: Object,
        dialog_edt: Boolean,
        editedItem: Object,
        refresh_lineas: Number,
    },
    data: () => ({
        loading: false,
        search:"",
    }),
    computed:{
         ...mapGetters([
            'hasEdtFac',
        ]),
        computedSubmit(){
            if (this.albaran.tipo_id >= 4){
                return (this.editedItem.producto_id==0);
            }
            else
                return (this.editedItem.producto_id==0 || this.editedItem.importe_venta == 0)

        },
        computedImporte(){
            var importe = (this.editedItem.importe_unidad * this.editedItem.unidades).toFixed(2);
            return this.editedItem.importe_venta = (importe - (importe * this.editedItem.descuento / 100)).toFixed(2);
        },
        computedCarateristicas(){

            const quilates = this.editedItem.producto.quilates > 0 ? this.editedItem.producto.quilates+"K" : "";

            const peso = this.editedItem.producto.univen =="G" ? this.editedItem.producto.peso_gr+" gr." : "";

            const caracteristicas = this.editedItem.producto.caracteristicas != null ? this.editedItem.producto.caracteristicas : "";

            return this.editedItem.producto.clase.nombre+" "+quilates+" "+caracteristicas+" "+peso;
        },
        computedCoste(){
             return (this.albaran.tipo_id == 5 || this.hasEdtFac);
        }
    },
    methods:{
        closeDialog (){
            this.$emit('update:dialog_edt', false)
        },
        submit() {
            if (this.loading === false){
                this.loading = true;

                var url = "/ventas/albalins/"+this.editedItem.id;

                this.$validator.validateAll().then((result) => {
                    if (result){

                        axios.put(url, this.editedItem)
                            .then(res => {

                                this.$emit('update:refresh_lineas', this.refresh_lineas+1);
                                this.$emit('update:dialog_edt', false)
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
.v-form .container{
    padding: 4px;
}
.v-text-field {
    padding-top: 4px;
    margin-top: 2px;
}

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
