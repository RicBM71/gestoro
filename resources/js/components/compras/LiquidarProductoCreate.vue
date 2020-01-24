<template>
  <v-layout row justify-center>
    <v-dialog v-model="dialog_pro" persistent max-width="800px">

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
                                height="100"
                                :error-messages="errors.collect('nombre')"
                                label="Indicar nombre"
                                data-vv-name="nombre"
                            ></v-textarea>
                        </v-flex>
                    </v-layout>
                    <v-layout wrap>
                        <v-flex sm1></v-flex>
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
                        <v-flex sm7></v-flex>
                        <v-flex sm2>
                            <v-text-field
                                v-model="precio_coste"
                                v-validate="'required|decimal:2|min_value:1'"
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
        ir_a_edit: Boolean
    },
    data: () => ({
        loading: false,
        show_peso: false,
        show_quil: false,
        nombre:"",
        quilates:"",
        clase_id:0,
        imp_gr: 0,
        margen: 40.00,
        peso_gr: 0,
        precio_coste: 0,
        precio_venta:0,

        clases:[],
    }),
    beforeMount(){
        if (this.compra.grupo_id > 0){

            axios.get('/utilidades/helpgrupos/'+this.compra.grupo_id+'/clases')
                .then(res => {
                    this.clases = res.data.clases;
                    this.itemCreate.clase_id = this.clases[0].value;
                    this.selClase(this.itemCreate.clase_id);

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
            if (this.peso_gr > 0)
                this.precio_coste = Math.round(this.peso_gr * this.imp_gr);
        },
        precio_coste: function () {
            this.precio_venta = Math.round(this.precio_coste * (1 +(this.margen / 100)));
        },
        dialog_pro: function () {

            this.clase_id = this.itemCreate.clase_id;
            this.nombre = this.itemCreate.nombre;
            this.quilates = this.itemCreate.quilates;
            this.imp_gr = (this.itemCreate.peso_gr == 0) ? 0 : this.itemCreate.precio_coste / this.itemCreate.peso_gr;

            this.peso_gr = this.itemCreate.peso_gr;
            this.precio_coste = this.itemCreate.precio_coste;
            this.precio_venta = Math.round(this.precio_coste * (1 +(this.margen / 100)));
        },
        compra: function () {
            axios.get('/utilidades/helpgrupos/'+this.compra.grupo_id+'/clases')
                .then(res => {
                    this.clases = res.data.clases;
                    this.itemCreate.clase_id = this.clases[0].value;
                    this.selClase(this.itemCreate.clase_id);

                })
                .catch(err => {

                    this.$toast.error(err.response.data.message);
                    this.$router.go(-1)
                })
        },

    },
    methods:{
        closeDialog (){

            this.$emit('update:dialog_pro', false)
        },
        selClase(id){

            let index = this.clases.findIndex((item) => item.value === id);

            this.show_peso = this.clases[index].peso;
            this.show_quil = this.clases[index].quilates;

            if (!this.show_peso)
                this.itemCreate.peso_gr = 0;

            if (!this.show_quil)
                this.itemCreate.quilates = "";

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
                                    univen: 'U'
                                }
                            )
                            .then(res => {
                                this.$emit('update:dialog_pro', false)
                                this.loading = false;
                                if (this.ir_a_edit)
                                    this.$router.push({ name: 'producto.edit', params: { id: res.data.producto.id } })
                                else
                                    this.$emit('update:ir_a_edit', true)
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
