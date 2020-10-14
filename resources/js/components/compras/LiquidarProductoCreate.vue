<template>
  <v-layout row justify-center>
    <v-dialog v-model="dialog_pro" persistent max-width="900px">

      <v-card>
        <v-card-title>
          <span class="headline">Crear Producto</span>
        </v-card-title>
        <v-card-text>
            <v-form>
                <v-container grid-list-md>
                    <v-layout wrap>
                        <v-flex sm12>
                            <v-textarea
                                v-model="nombre"
                                v-validate="'required'"
                                ref="nombre"
                                outline
                                height="90"
                                :error-messages="errors.collect('nombre')"
                                label="Indicar nombre"
                                data-vv-name="nombre"
                            ></v-textarea>
                        </v-flex>
                        <v-flex sm12>
                            <v-text-field
                                v-model="nombre_interno"
                                v-validate="'max:190'"
                                ref="nombre_interno"
                                :error-messages="errors.collect('nombre_interno')"
                                label="Indicar Nombre Interno"
                                data-vv-name="nombre_interno"
                            ></v-text-field>
                        </v-flex>
                    </v-layout>
                    <v-layout wrap>
                        <v-flex sm4 d-flex>
                                <v-select
                                v-model="clase_id"
                                :items="clases"
                                label="Clase"
                                @change="selClase(clase_id)"
                                ></v-select>
                        </v-flex>
                        <v-flex sm2>
                            <v-text-field
                                :readonly="!show_quil"
                                v-model="quilates"
                                v-validate="'max:30'"
                                :error-messages="errors.collect('quilates')"
                                label="Quilates "
                                data-vv-name="quilates"
                                data-vv-as="quilates"
                                required
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm2>
                            <v-text-field
                                :readonly="!show_peso"
                                v-model="peso_gr"
                                v-validate="'decimal:2'"
                                :error-messages="errors.collect('peso_gr')"
                                label="Peso gr. "
                                data-vv-name="peso_gr"
                                data-vv-as="Peso_gr"
                                class="inputPrice"
                                required
                                type="number"
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                         <v-flex sm2>
                            <v-text-field
                                v-model="precio_venta"
                                v-validate="'required|decimal:2'"
                                :error-messages="errors.collect('precio_venta')"
                                label="PVP"
                                data-vv-name="precio_venta"
                                data-vv-as="PVP"
                                type="number"
                                required
                                class="inputPrice"
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                    </v-layout>
                    <v-layout wrap>
                        <v-flex sm4 d-flex>
                                <v-select
                                v-model="destino_empresa_id"
                                :items="empresas"
                                label="Destino venta"
                                ></v-select>
                        </v-flex>
                        <v-flex sm2>
                            <v-text-field
                                v-model="precio_gr"
                                v-validate="'decimal:2'"
                                :error-messages="errors.collect('precio_gr')"
                                label="Precio Gr. (PVP)"
                                data-vv-name="precio_gr"
                                data-vv-as="precio gramo"
                                type="number"
                                class="inputPrice"
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm2>
                            <v-text-field
                                v-model="precio_coste"
                                v-validate="'required|decimal:2|min_value:0.01'"
                                :error-messages="errors.collect('precio_coste')"
                                label="Precio Coste"
                                data-vv-name="precio_coste"
                                data-vv-as="coste"
                                type="number"
                                required
                                class="inputPrice"
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                         <v-flex sm2>
                            <v-text-field
                                v-model="margen"
                                v-validate="'required|decimal:2'"
                                :error-messages="errors.collect('margen')"
                                label="Márgen"
                                data-vv-name="margen"
                                data-vv-as="márgen"
                                type="number"
                                required
                                class="inputPrice"
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                         <v-flex sm2 v-if="computedStock">
                            <v-text-field
                                v-model="stock"
                                v-validate="'required|numeric'"
                                :error-messages="errors.collect('stock')"
                                label="Stock"
                                data-vv-name="stock"
                                data-vv-as="márgen"
                                type="number"
                                required
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                    </v-layout>
                    <v-layout wrap>
                        <v-flex sm4 d-flex>
                            <v-select
                                v-model="categoria_id"
                                v-validate="'numeric'"
                                data-vv-name="categoria_id"
                                data-vv-as="categoria"
                                :error-messages="errors.collect('categoria_id')"
                                :items="categorias"
                                label="Categoría"
                            ></v-select>
                        </v-flex>
                        <v-flex sm4 d-flex>
                            <v-select
                                v-model="marca_id"
                                v-validate="'numeric'"
                                data-vv-name="marca_id"
                                data-vv-as="marca"
                                :error-messages="errors.collect('marca_id')"
                                :items="marcas"
                                label="Marca"
                            ></v-select>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-form>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="blue darken-1" round flat @click="closeDialog">Cerrar</v-btn>
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
        compra: Object,
        itemCreate: Object,
        dialog_pro: Boolean,
        ir_a_edit: Boolean,
    },
    data: () => ({
        loading: false,
        show_peso: false,
        show_quil: false,
        nombre:"",
        nombre_interno:"",
        destino_empresa_id:"",
        quilates:"",
        clase_id:0,
        categoria_id: null,
        marca_id: null,
        imp_gr: 0,
        margen: 0,
        peso_gr: 0,
        precio_coste: 0,
        precio_venta:0,
        stock:1,
        precio_gr: 0,
        clases:[],
        empresas:[],
        marcas:[],
        categorias:[],
        show_stock: false
    }),
    beforeMount(){

        if (this.compra.grupo_id > 0){

            axios.get('/utilidades/helpgrupos/'+this.compra.grupo_id+'/clases')
                .then(res => {
                    this.destino_empresa_id = this.compra.empresa_id;
                    this.clases = res.data.clases;
                    this.empresas = res.data.empresas;
                    this.itemCreate.clase_id = this.clases[0].value;

                    this.selClase(this.itemCreate.clase_id);

                    this.marcas = res.data.marcas;
                    this.categorias = res.data.categorias;

                    this.marcas.push({value: null, text: '-'});
                    this.categorias.push({value: null, text: '-'});


                })
                .catch(err => {

                    this.$toast.error(err.response.data.message);
                    this.$router.go(-1)
                })

        }

    },
    watch:{
        margen: function () {
            this.precio_venta = Math.round(this.precio_coste * (1 +(this.margen / 100)));
        },
        peso_gr: function () {
            if (this.peso_gr > 0){
                this.precio_coste = Math.round(this.peso_gr * this.imp_gr);

                if (this.precio_coste < 100){
                    this.precio_coste = (this.peso_gr * this.imp_gr).toFixed(2);
                }
            }

            this.precio_venta = Math.round(this.peso_gr * this.precio_gr);
        },
        precio_gr: function () {
            this.precio_venta = Math.round(this.peso_gr * this.precio_gr);
        },
        imp_gr: function () {
            this.precio_venta = Math.round(this.precio_coste * this.imp_gr);
        },
        // precio_coste: function () {
        //     if (this.imp_gr > 0)
        //         this.precio_venta = Math.round(this.precio_coste * this.imp_gr);
        //     else
        //     this.precio_venta = Math.round(this.precio_coste * (1 +(this.margen / 100)));
        // },
        dialog_pro: function () {

            this.clase_id = this.itemCreate.clase_id;
            this.nombre = this.itemCreate.nombre;
            this.quilates = this.itemCreate.quilates;
            this.imp_gr = (this.itemCreate.peso_gr == 0) ? 0 : this.itemCreate.precio_coste / this.itemCreate.peso_gr;
            this.nombre_interno = '';

            this.peso_gr = this.itemCreate.peso_gr;
            this.precio_coste = this.itemCreate.precio_coste;
            this.precio_venta = Math.round(this.precio_coste * (1 +(this.margen / 100)));

            this.selClase(this.itemCreate.clase_id);

        },
        compra: function () {
            axios.get('/utilidades/helpgrupos/'+this.compra.grupo_id+'/clases')
                .then(res => {
                    this.clases = res.data.clases;
                    //this.itemCreate.clase_id = this.clases[0].value;
                    this.marcas = res.data.marcas;
                    this.categorias = res.data.categorias;

                    this.marcas.push({value: null, text: '-'});
                    this.categorias.push({value: null, text: '-'});

                    this.selClase(this.itemCreate.clase_id);

                })
                .catch(err => {

                    this.$toast.error(err.response.data.message);
                    this.$router.go(-1)
                })
        },

    },
    computed:{
        computedStock(){
            return this.show_stock;
        }
    },
    methods:{
        closeDialog (){

            this.$emit('update:dialog_pro', false)
        },
        selClase(id){

            let index = this.clases.findIndex((item) => item.value == id);

            if (index >= 0){

                this.show_peso = this.clases[index].peso;
                this.show_quil = this.clases[index].quilates;
                this.show_stock = this.clases[index].stockable;

                if (!this.show_peso)
                    this.itemCreate.peso_gr = 0;

                if (!this.show_quil)
                    this.itemCreate.quilates = "";
            }

        },
        submit() {

            if (this.loading === false){
                this.loading = true;

                var url = "/mto/productos";

                this.$validator.validateAll().then((result) => {
                    if (result){

                        axios.post(url, {
                                compra_id: this.compra.id,
                                ref_pol: this.compra.alb_ser,
                                nombre: this.nombre,
                                clase_id: this.clase_id,
                                iva_id: 2,
                                estado_id: 2,
                                precio_coste: this.precio_coste,
                                precio_venta: this.precio_venta,
                                peso_gr: this.peso_gr,
                                quilates: this.quilates,
                                etiqueta_id: 3,
                                univen: 'U',
                                destino_empresa_id: this.destino_empresa_id,
                                nombre_interno: this.nombre_interno,
                                stock: this.stock,
                                marca_id: this.marca_id,
                                categoria_id: this.categoria_id
                            })
                            .then(res => {
                                this.$emit('update:dialog_pro', false)
                                this.loading = false;
                                if (this.ir_a_edit)
                                    this.$router.push({ name: 'producto.edit', params: { id: res.data.producto.id } })
                                else{
                                    //this.$forceUpdate()

                                    this.$emit('update:itemCreate', res.data.producto)
                                    this.$emit('update:ir_a_edit', true)
                                }
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

            this.itemCreate.nombre =  null;
            this.itemCreate.colores = null;
            this.itemCreate.peso = 0;
            this.itemCreate.stock = 1;
            this.itemCreate.precio_venta = 0;
            this.itemCreate.quilates = null;

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
