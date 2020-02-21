<template>
	<div>
        <loading :show_loading="show_loading"></loading>
        <v-card>
            <v-card-title color="indigo">
                <h2 color="indigo">{{titulo}}</h2>
                <v-spacer></v-spacer>
                <menu-ope></menu-ope>
            </v-card-title>
        </v-card>
        <v-card>
            <v-form>
                 <v-container>
                     <v-layout row wrap>
                        <v-flex sm2>
                            <v-menu
                                v-model="menu_d"
                                :close-on-content-click="false"
                                :nudge-right="40"
                                lazy
                                transition="scale-transition"
                                offset-y
                                full-width
                                min-width="290px"
                            >
                                <v-text-field
                                    slot="activator"
                                    :value="computedFechaD"
                                    label="Desde"
                                    append-icon="event"
                                    v-validate="'date_format:dd/MM/yyyy'"
                                    data-vv-name="fecha_d"
                                    :error-messages="errors.collect('fecha_d')"
                                    data-vv-as="Desde"
                                    readonly
                                    ></v-text-field>
                                <v-date-picker
                                    v-model="fecha_d"
                                    no-title
                                    locale="es"
                                    first-day-of-week=1
                                    @input="menu_d = false"
                                    ></v-date-picker>
                            </v-menu>
                        </v-flex>
                        <v-flex sm1>
                            <v-text-field
                                v-model="prefijo"
                                v-validate="'alpha|max:3'"
                                :error-messages="errors.collect('prefijo')"
                                label="Prefijo"
                                data-vv-name="prefijo"
                                data-vv-as="prefijo"
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                         <v-flex sm2>
                            <v-text-field
                                v-model="referencia"
                                v-validate="'required|numeric'"
                                :error-messages="errors.collect('referencia')"
                                label="Referencia"
                                data-vv-name="referencia"
                                data-vv-as="referencia"
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm2></v-flex>
                        <v-flex sm2>
                            <div class="text-xs-center">
                                <v-btn @click="submit" round small :loading="loading" block  color="primary">
                                    Guardar
                                </v-btn>
                            </div>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>
                            <v-flex xs12>
                                <v-data-table
                                    :headers="headers"
                                    :items="items"
                                    :pagination.sync="pagination"
                                    rows-per-page-text="Registros por página"
                                >
                                    <template slot="items" slot-scope="props">
                                        <td v-if="props.item.producto != null">{{props.item.producto.referencia }}</td>
                                        <td v-else>ID:{{ props.item.producto_id}}</td>
                                        <td v-if="props.item.producto != null">{{ props.item.producto.nombre }}</td>
                                        <td v-else>empresa: {{props.item.destino_empresa_id}}</td>
                                        <td v-if="props.item.estado != null">{{ props.item.estado.nombre }}</td>
                                        <td v-else>{{props.item.estado_id}}</td>
                                        <td v-if="props.item.rfid != null">{{ props.item.rfid.nombre }}</td>
                                        <td v-else>{{props.item.rfid_id}}</td>
                                        <td class="justify-center layout px-0">
                                            <v-icon
                                                small
                                                class="mr-2"
                                                @click="goProducto(props.item)"
                                            >
                                                local_offer
                                            </v-icon>
                                            <v-icon
                                                v-if="props.item.rfid_id == 3"
                                                small
                                                class="mr-2"
                                                @click="update(props.item)"
                                            >
                                                warning
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
import MenuOpe from './MenuOpe'
import Loading from '@/components/shared/Loading'
import moment from 'moment'
import {mapGetters} from 'vuex';

	export default {
		$_veeValidate: {
      		validator: 'new'
        },
        components: {
            'menu-ope': MenuOpe,
            'loading': Loading
		},
    	data () {
      		return {
                titulo:"Recuento manual",
                recuento: {
                    id:       0,
                },
                pagination:{
                    model: "recuentos",
                    descending: false,
                    page: 1,
                    rowsPerPage: 10,
                    sortBy: "id",
                    search: ""
                },
                headers: [
                    {
                    text: 'Referencia',
                    align: 'left',
                    value: 'producto.referencia'
                    },
                    {
                    text: 'Producto',
                    align: 'left',
                    value: 'producto.nombre'
                    },
                    {
                    text: 'Estado',
                    align: 'left',
                    value: 'estado.nombre'
                    },
                    {
                    text: 'Situación',
                    align: 'left',
                    value: 'rfid.nombre'
                    },
                    {
                    text: 'Acciones',
                    align: 'Center',
                    value: ''
                    }
                ],
                prefijo: null,
                referencia: null,
        		status: false,
                loading: false,

                items: [],
                fecha_d: new Date().toISOString().substr(0, 7)+"-01",
                menu_d: false,

                show: false,
                show_loading: true,
      		}
        },
        mounted(){
            this.show_loading = false;
        },
        computed: {
        ...mapGetters([
        ]),
        computedFechaD() {
                moment.locale('es');
                return this.fecha_d ? moment(this.fecha_d).format('L') : '';
            }
        },
    	methods:{
            submit() {


                if (this.loading === false){
                    this.loading = true;

                    var url = "/mto/recuentos";

                    this.$validator.validateAll().then((result) => {
                        if (result){
                            axios.post(url,{
                                fecha: this.fecha_d,
                                prefijo: this.prefijo,
                                referencia: this.referencia
                            })
                                .then(res => {

                                    this.items.push(res.data.recuento);
                                    this.referencia = null;

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
                                    this.loading = false;
                                })
                                .finally(()=> {
                                    this.loading = false;
                                });
                            }
                        else{
                            this.loading = false;
                        }
                    });
                }

            },
        goProducto(item) {

            this.setPagination(this.paginaActual);

            this.$router.push({ name: 'producto.edit', params: { id: item.producto_id } })
        },
        update(item) {
        }

    }
  }
</script>
