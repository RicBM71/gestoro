<template>
  <v-layout row justify-center>
    <v-dialog v-model="dialog_lin" persistent max-width="800px">

      <v-card>
        <v-card-title>
          <span class="headline">Nuevo Producto</span>
        </v-card-title>
        <v-card-text>
            <v-form>
                <v-container grid-list-md>
                    <v-layout wrap>
                        <v-flex sm2>
                            <v-text-field
                                v-model="referencia"
                                v-validate="'required'"
                                :error-messages="errors.collect('referencia')"
                                label="Referencia "
                                data-vv-name="referencia"
                                data-vv-as="referencia"
                                hint="pulsa enter para buscar"
                                v-on:keyup.enter="getReferencia"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm10>
                           <v-select
                                @change="selRef()"
                                clearable
                                v-model="editedItem.producto_id"
                                :items="items"
                                :label="computedProductosLabel"
                            ></v-select>
                        </v-flex>
                    </v-layout>
                    <v-layout wrap>
                         <v-flex sm2></v-flex>
                         <v-flex sm10>
                            <v-text-field
                                :value="computedCarateristicas"
                                label="Detalles"
                                disabled
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
                                :readonly="editedItem.iva_id==2 && editedItem.stock == 1"
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm2 d-flex>
                            <v-text-field
                                v-show="producto.univen=='U'"
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
                                label="Importe Ud. "
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
                                v-validate="'max:150'"
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
          <v-btn color="blue darken-1" round flat @click="submit" :disabled="computedSubmit"  :loading="loading">Guardar</v-btn>
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
        albaran: Object,
        dialog_lin: Boolean,
        refresh_lineas: Number,
    },
    data: () => ({
        loading: false,
        isLoading: false,
        items:[],
        search:"",
        model:"",
        referencia: "",
        producto: {
            id: 0,
            univen: 'U'
        },
        editedItem: {
            albaran_id:"",
            producto_id: "",
            unidades:0,
            iva:0,
            iva_id:0,
            precio_coste:0,
            importe_unidad:0,
            descuento: 0,
            importe_venta: "",
            notas: "",
            stock:1,
        },
    }),
    beforeMount(){

        //  this.isLoading = true


        //         axios.post('/utilidades/helppro/vendibles',{
        //                 tipo_id: this.albaran.tipo_id,
        //                 referencia: this.referencia,
        //             })
        //             .then(res => {
        //                 this.items = res.data;
        //                 if (this.items.length > 0){
        //                     this.editedItem.producto_id=this.items[0].value;
        //                 }else{
        //                     this.reset();
        //                 }

        //             })
        //             .catch(err => {
        //                 console.log(err)
        //             })
        //             .finally(() => (this.selRef()))

    },
    // beforeUpdate(){
    //     if (this.dialog_lin){
    //         this.selClase(this.editedItem.clase_id);
    //     }
    // },
    computed:{
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

            if (this.producto.id == 0) return null;
            const quilates = this.producto.quilates > 0 ? this.producto.quilates+"K" : "";
            //const peso = this.producto.peso_gr > 0 ? this.producto.peso_gr+" gr." : "";
            const peso = this.producto.univen =="G" ? this.producto.peso_gr+" gr." : "";
            const caracteristicas = this.producto.caracteristicas != null ? this.producto.caracteristicas : "";
            return this.producto.clase.nombre+" "+quilates+" "+caracteristicas+" "+peso;
        },
        computedProductosLabel(){
            return this.items.length == 0 ? "Productos" : "Productos ("+this.items.length+")";
        },
        computedCoste(){
            return this.albaran.tipo_id == 5;
        }
    },
    watch: {
        // referencia: function () {

        //     this.isLoading = true
        //     if (this.referencia.length > 2){

        //         axios.post('/utilidades/helppro/vendibles',{
        //                 tipo_id: this.albaran.tipo_id,
        //                 referencia: this.referencia,
        //             })
        //             .then(res => {
        //                 this.items = res.data;
        //                 if (this.items.length > 0){
        //                     this.editedItem.producto_id=this.items[0].value;
        //                 }else{
        //                     this.reset();
        //                 }

        //             })
        //             .catch(err => {
        //                 console.log(err)
        //             })
        //             .finally(() => (this.selRef()))
        //     }
        //},
        // referencia: function (newQuestion, oldQuestion) {

        //     this.debouncedGetAnswer()
        //     }


    },
    // created: function () {

    //     this.debouncedGetAnswer = _.debounce(this.getReferencia, 500)

    // },
    methods:{
        getReferencia() {

                axios.post('/utilidades/helppro/vendibles',{
                    tipo_id: this.albaran.tipo_id,
                    referencia: this.referencia,
                })
                .then(res => {
                    this.items = res.data;
                    if (this.items.length > 0){
                        this.editedItem.producto_id=this.items[0].value;
                    }else{
                        this.reset();
                    }

                })
                .catch(err => {
                    console.log(err)
                })
                .finally(() => (this.selRef()))

        },
        closeDialog (){

            this.$emit('update:dialog_lin', false)
        },
        selRef(){
            if (this.editedItem.producto_id > 0){

                axios.post('/utilidades/helppro/producto',{
                        producto_id: this.editedItem.producto_id,
                    })
                    .then(res => {
                        this.producto = res.data;
                        this.editedItem.iva_id = this.producto.iva_id;
                        this.editedItem.iva = this.producto.iva.importe;
                        this.editedItem.importe_unidad = this.producto.precio_venta;
                        this.editedItem.descuento = this.albaran.cliente.descuento;
                        this.editedItem.stock = this.producto.stock;

                        if (this.producto.univen == 'U'){
                            this.editedItem.unidades = 1;
                            this.editedItem.precio_coste = this.producto.precio_coste;
                        }else{
                            this.editedItem.unidades = 0;
                            this.editedItem.precio_coste = 0;
                        }

                    })
                .catch(err => {
                    console.log(err)
                })
            }
        },
        submit() {
            if (this.loading === false){
                this.loading = true;

                this.editedItem.albaran_id = this.albaran.id;

                if (this.albaran.cliente.iva_no_residente){
                    this.editedItem.iva = 0;
                    this.editedItem.iva_id = 3;
                }

                var url = "/ventas/albalins";

                this.$validator.validateAll().then((result) => {

                    if (result){

                        axios.post(url, this.editedItem)
                            .then(res => {
                                //this.$router.push({ name: 'albaran.edit', params: { id: this.compra_id } })
                                this.loading = false;
                                this.$emit('update:refresh_lineas', this.refresh_lineas+1);
                                this.referencia = '';
                                this.reset();
                                this.$validator.reset();
                                this.$emit('update:dialog_lin', false)

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
            this.producto.id = 0;
            this.editedItem.producto_id = 0;
            this.editedItem.unidades = 0;
            this.editedItem.importe_unidad = 0;
            this.editedItem.precio_coste = 0;
            this.editedItem.importe_venta = 0;
            this.editedItem.stock = 1;
            this.items = [];

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
