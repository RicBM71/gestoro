<template>
	<div>
        <loading :show_loading="show_loading"></loading>
        <v-card>
            <v-card-title color="indigo">
                <h2 color="indigo">{{titulo}}</h2>
                <v-spacer></v-spacer>
            </v-card-title>
        </v-card>
        <v-card>
            <v-form>
                 <v-container>
                     <v-layout row wrap>
                        <v-flex sm2>
                            <v-switch
                                :label="computedLabel"
                                v-model="reasignar"
                                color="primary">
                            ></v-switch>
                        </v-flex>
                        <v-flex sm2>
                            <v-switch
                                label="Filtrar Categoría"
                                v-model="filtrar"
                                color="primary">
                            ></v-switch>
                        </v-flex>
                        <v-flex xs4>
                            <v-text-field
                                v-model="texto"
                                v-validate="'required'"
                                data-vv-name="texto"
                                :error-messages="errors.collect('texto')"
                                label="Palabras clave"
                                hint='Indicar palabras separadas por comas'
                                single-line
                                v-on:keyup.enter="submit"
                            ></v-text-field>
                        </v-flex>
                        <v-flex sm2>
                            <v-select
                                v-model="categoria_id"
                                v-validate="'required'"
                                data-vv-name="categoria_id"
                                data-vv-as="categoría"
                                :error-messages="errors.collect('categoria_id')"
                                :items="categorias"
                                label="Categoría"
                                ></v-select>
                        </v-flex>
                        <v-flex xs1>
                            <v-text-field
                                v-model="resto"
                                label="Sin Asignar"
                                readonly
                            ></v-text-field>
                        </v-flex>
                        <v-flex sm1>
                            <div class="text-xs-center">
                                <v-btn @click="submit" round small flat :loading="loading"  block  color="primary">
                                    Enviar
                                </v-btn>
                            </div>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex xs12>
                            <v-data-table
                                :headers="headers"
                                :items="productos"
                                :search="search"
                                :pagination.sync="pagination"
                                rows-per-page-text="Registros por página"
                            >
                                <template slot="items" slot-scope="props">
                                    <td>{{ props.item.referencia }}</td>
                                    <td>{{ props.item.nombre }}</td>
                                    <td class="justify-center layout px-0">
                                        <v-icon
                                            small
                                            class="mr-2"
                                            @click="editItem(props.item)"
                                        >
                                            edit
                                        </v-icon>
                                    </td>
                                </template>
                                <template slot="pageText" slot-scope="props">
                                    Registros {{ props.pageStart }} - {{ props.pageStop }} de {{ props.itemsLength }}
                                </template>
                            </v-data-table>
                        </v-flex>
                     </v-layout>
                </v-container>
            </v-form>
        </v-card>
	</div>
</template>
<script>
import {mapGetters} from 'vuex';
import Loading from '@/components/shared/Loading'

	export default {
		$_veeValidate: {
      		validator: 'new'
        },
        components: {
            'loading': Loading
		},
    	data () {
      		return {
                reasignar: false,
                filtrar: false,
                pagination:{
                    descending: false,
                    page: 1,
                    rowsPerPage: 10,
                    sortBy: "nombre",
                },
                search:"",
                headers: [
                    {
                        text: 'Referencia',
                        align: 'left',
                        value: 'referencia'
                    },
                    {
                        text: 'Nombre',
                        align: 'left',
                        value: 'nombre'
                    },
                    {
                        text: 'Acciones',
                        align: 'Center',
                        value: 'id'
                    }
                ],

                url: "/utilidades/categoria/assign",

        		categorias: [],
                productos:[],
                categoria_id: '',
                texto: '',
                loading: false,
                resto: 0,

                show_loading: true,
                titulo:'Asignar Categorías'
      		}
        },
        mounted(){
            if (!this.hasEdtPro){
                this.$toast.error('No dispone de los permisos necesarios');
                this.$router.push({ name:'dash'})
            }
             axios.get(this.url)
                .then(res => {

                    this.categorias = res.data.categorias;
                    this.resto = res.data.resto;
                    this.productos = res.data.productos;

                })
                .catch(err => {
                    this.$toast.error(err.response.data.message);
                })
                .finally(()=> {
                    this.show_loading = false;
                });

        },
        computed: {
        ...mapGetters([
               'hasEdtPro'
           ]),
           computedLabel(){
               return this.reasignar == false ? 'Sin Asignar' : ' Reasignar';
           }
        },
    	methods:{
            editItem (item) {
                this.$router.push({ name: 'producto.edit', params: { id: item.id } })
            },
            submit() {
                if (this.loading === false){

                    this.loading = true;
                    this.show_loading = true;

                    this.$validator.validateAll().then((result) => {
                        if (result){
                            axios.post(this.url, {
                                categoria_id: this.categoria_id,
                                texto: this.texto,
                                reasignar: this.reasignar,
                                filtrar: this.filtrar
                            })
                                .then(res => {

                                    this.resto = res.data.resto;
                                    this.productos = res.data.productos;
                                    this.texto = '';

                                })
                                .catch(err => {

                                    if (err.request.status == 422){ // fallo de validated.
                                        const msg_valid = err.response.data.errors;
                                        for (const prop in msg_valid) {
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
                                    this.loading = this.show_loading = false;
                                });
                            }
                        else{
                            this.loading = this.show_loading = false;
                        }
                    });
                }

            },

        }
  }
</script>
