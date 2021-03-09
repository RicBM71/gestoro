
<template>
    <div>
        <loading :show_loading="show_loading"></loading>
        <v-container v-if="registros">
            <my-dialog :dialog.sync="dialog" registro="registro" @destroyReg="destroyReg"></my-dialog>
            <v-card>
                <v-card-title>
                    <h2>{{titulo}}</h2>
                    <v-spacer></v-spacer>
                    <v-tooltip bottom>
                            <template v-slot:activator="{ on }">
                                <v-btn
                                    v-on="on"
                                    color="white"
                                    icon
                                    @click="filtro = !filtro"
                                >
                                    <v-icon color="primary">filter_list</v-icon>
                                </v-btn>
                            </template>
                            <span>Filtros</span>
                        </v-tooltip>
                    <menu-ope></menu-ope>
                </v-card-title>
            </v-card>
            <v-card v-show="filtro">
                <v-form>
                    <v-container>
                        <v-layout row wrap>
                            <v-flex sm8></v-flex>
                            <v-flex sm2>
                                <v-text-field
                                    v-model="ejercicio"
                                    v-validate="'numeric'"
                                    type="number"
                                    :error-messages="errors.collect('ejercicio')"
                                    data-vv-name="ejercicio"
                                    label="Ejercicio"
                            ></v-text-field>
                            </v-flex>
                            <v-flex sm1>
                                <v-btn @click="filtrar"  round  block small color="info">
                                    Filtrar
                                </v-btn>
                            </v-flex>
                        </v-layout>
                    </v-container>
                </v-form>
            </v-card>
            <v-card>
                <v-container>
                    <v-layout row wrap>
                        <v-flex xs6></v-flex>
                        <v-flex xs6>
                            <v-spacer></v-spacer>
                            <v-text-field
                                v-model="search"
                                append-icon="search"
                                label="Buscar"
                                single-line
                                hide-details
                            ></v-text-field>
                        </v-flex>
                    </v-layout>
                    <br/>
                    <v-layout row wrap>
                        <v-flex xs12>
                            <v-data-table
                            :headers="headers"
                            :items="this.arr_reg"
                            :search="search"
                            @update:pagination="updateEventPagina"
                            :pagination.sync="pagination"
                            rows-per-page-text="Registros por página"
                            >
                                <template slot="items" slot-scope="props">
                                    <td>{{ props.item.tipo.nombre }}</td>
                                    <td>{{ props.item.ejercicio }} <span v-if="props.item.cerrado==1" class="red--text">CERRADO</span></td>
                                    <td>{{ props.item.alb_ser }}</td>
                                    <td>{{ props.item.fac_ser }}</td>
                                    <td>{{ props.item.fac_auto }}</td>
                                    <td>{{ props.item.abo_ser }}</td>
                                    <td class="justify-center layout px-0">
                                        <v-icon
                                            small
                                            class="mr-2"
                                            @click="editItem(props.item.id)"
                                        >
                                            edit
                                        </v-icon>


                                        <v-icon v-if="isRoot"
                                            small
                                            @click="openDialog(props.item.id)"
                                            >
                                            delete
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
            </v-card>
        </v-container>
    </div>
</template>
<script>
import MyDialog from '@/components/shared/MyDialog'
import Loading from '@/components/shared/Loading'
import MenuOpe from './MenuOpe'
import {mapGetters} from 'vuex'
import {mapActions} from "vuex";
  export default {
    $_veeValidate: {
        validator: 'new'
    },
    components: {
        'my-dialog': MyDialog,
        'menu-ope': MenuOpe,
        'loading': Loading
    },
    data () {
      return {
        titulo: "Contadores",
        paginaActual:{},
        pagination:{
            model: "contador",
            descending: true,
            page: 1,
            rowsPerPage: 10,
            sortBy: "ejercicio",
        },
        search:"",
        headers: [
            {
                text: 'Tipo',
                align: 'left',
                value: 'tipo.nombre'
            },
            {
                text: 'Ejercicio',
                align: 'left',
                value: 'ejercicio'
            },
            {
                text: 'Ult. Albarán',
                align: 'left',
                value: 'alb_ser'
            },
            {
                text: 'Ult. Factura',
                align: 'left',
                value: 'fac_ser'
            },
            {
                text: 'Ult. Factura Auto',
                align: 'left',
                value: 'fac_auto'
            },
            {
                text: 'Ult. Abono',
                align: 'left',
                value: 'abo_ser'
            },
            {
                text: 'Acciones',
                align: 'Center',
                value: ''
            }
        ],
        arr_reg:[],
        status: false,
		registros: false,
        dialog: false,
        registro_id: 0,
        show_loading: true,
        url: "/mto/contadores",
        ruta: "contador",

        ejercicio: new Date().toISOString().substr(0, 4),
        filtro: false

      }
    },
    mounted()
    {

        // TODO: Error, falla filtro al pulsar enter, revisar.
        if (this.getPagination.model == this.pagination.model)
            this.updatePosPagina(this.getPagination);
        else
            this.unsetPagination();

        axios.get(this.url)
            .then(res => {
                this.arr_reg = res.data;
                this.registros = true;
                this.show_loading = false;
            })
            .catch(err =>{
               ;
                this.$toast.error(err.response.data.message);
                this.$router.push({ name: 'dash' })
            })
    },
    computed: {
        ...mapGetters([
            'isRoot',
            'getPagination'
        ])
    },
    methods:{
        ...mapActions([
            'setPagination',
            'unsetPagination'
		]),
        updateEventPagina(obj){

            this.paginaActual = obj;

        },
        updatePosPagina(pag){

            this.pagination.page = pag.page;
            this.pagination.descending = pag.descending;
            this.pagination.rowsPerPage= pag.rowsPerPage;
            this.pagination.sortBy = pag.sortBy;

        },
        create(){
            this.$router.push({ name: this.ruta+'.create'})
        },
        editItem (id) {
            this.setPagination(this.paginaActual);
            this.$router.push({ name: this.ruta+'.edit', params: { id: id } })
        },
        openDialog (id){
            this.dialog = true;
            this.registro_id = id;
        },
        destroyReg () {
            this.dialog = false;

            axios.post(this.url+'/'+this.registro_id,{_method: 'delete'})
                .then(response => {

                if (response.status == 200){
                    this.$toast.success('Registro eliminado!');
                    this.arr_reg = response.data;
                }
                })
            .catch(err => {
                this.status = true;

                var msg = err.response.data.message;
                this.$toast.error(msg);

            });

        },
        filtrar(){

            this.$validator.validateAll().then((result) => {
                    if (result){
                        this.show_loading = true;
                        axios.post(this.url+'/filtrar',
                                {
                                    ejercicio: this.ejercicio,
                                }
                            )
                            .then(res => {

                                this.pagination.page = 1;
                                this.filtro = false;

                                this.arr_reg = res.data;
                                this.show_loading = false;

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
                                this.loading = this.show_loading = false;
                            });
                    }
            });

        },

    }
  }
</script>

<style scoped>
  .tachado{text-decoration:line-through;}
</style>
