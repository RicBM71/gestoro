
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
                    <v-tooltip bottom>
                        <template v-slot:activator="{ on }">
                            <v-btn
                                v-show="arr_reg.length > 0 && hasExcel"
                                v-on="on"
                                color="white"
                                icon
                                @click="goExcel()"
                            >
                                <v-avatar size="32px">
                                    <img class="img-fluid" src="/assets/excel.png">
                                </v-avatar>
                            </v-btn>
                        </template>
                        <span>Exportar a Excel</span>
                    </v-tooltip>
                    <menu-ope></menu-ope>
                </v-card-title>
            </v-card>
             <v-card v-show="filtro">
                <filtro-pro :filtro.sync="filtro" :arr_reg.sync="arr_reg" :mi_filtro.sync="mi_filtro"></filtro-pro>
            </v-card>
            <v-card  v-show="!filtro">
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
                                :items="arr_reg"
                                :search="search"
                                @update:pagination="updateEventPagina"
                                :pagination.sync="pagination"
                                :expand="expand"
                                rows-per-page-text="Registros por página"
                            >
                                <template slot="items" slot-scope="props">
                                    <td><v-icon
                                        v-if="props.item.online"
                                        small
                                        class="blue--text lighten-5 mr-2"
                                    >
                                        star
                                    </v-icon>{{ props.item.referencia }}</td>
                                    <td v-if="props.item.deleted_at==null">{{ props.item.nomcar }}</td>
                                    <td v-else class="tachado">{{ props.item.nomcar }}</td>
                                    <td>{{ props.item.clase.nombre }} <span v-if="props.item.quilates != 0">{{props.item.quilates}}</span></td>
                                    <td class="text-xs-right">{{ props.item.peso_gr | currency('', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false })}}</td>
                                    <td class="text-xs-right">{{ props.item.precio_coste | currency('€', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false })}}</td>
                                    <td class="text-xs-right">{{ props.item.precio_venta | currency('€', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false })}}</td>
                                    <td :class="props.item.estado.color">{{ props.item.estado.nombre }}</td>
                                    <td v-if="props.item.empresa != null && props.item.destino != null">{{ props.item.empresa.sigla }}/{{ props.item.destino.sigla}}</td>
                                    <td v-else>ERROR AL ASIGNAR O/D {{ props.item.empresa_id + "/" + props.item.destino_empresa_id}}</td>
                                    <td class="justify-center layout px-0">
                                        <v-icon
                                            v-if="props.item.deleted_at==null || hasEdtPro"
                                            small
                                            class="mr-2"
                                            @click="editItem(props.item)"
                                        >
                                            edit
                                        </v-icon>
                                        <v-icon
                                            v-if="props.item.deleted_at==null && hasEdtPro &&  props.item.estado_id <= 2"
                                            small
                                            @click="openDialog(props.item)"
                                        >
                                            delete
                                        </v-icon>
                                        <v-icon
                                            v-if="props.item.deleted_at!=null && hasEdtPro"
                                            small
                                            @click="openDialog(props.item)"
                                        >
                                            restore
                                        </v-icon>
                                        <v-icon
                                            v-if="showExpand(props.item)"
                                            small
                                            @click="props.expanded = !props.expanded"
                                        >
                                            visibility
                                        </v-icon>
                                    </td>
                                </template>
                                <template v-slot:expand="props">
                                    <v-card flat>
                                        <v-card-text class="font-italic">
                                            {{ destino(props.item) }}
                                        </v-card-text>
                                    </v-card>
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
import FiltroPro from './FiltroPro'
import {mapGetters} from 'vuex'
import {mapActions} from "vuex";
  export default {
    $_veeValidate: {
        validator: 'new'
    },
    components: {
        'my-dialog': MyDialog,
        'menu-ope': MenuOpe,
        'filtro-pro': FiltroPro,
        'loading': Loading
    },
    data () {
      return {
        titulo: "Productos",
        paginaActual:{},
        pagination:{
            model: "producto",
            descending: false,
            page: 1,
            rowsPerPage: 10,
            sortBy: "id",
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
                value: 'nomcar'
            },
            {
                text: 'Clase',
                align: 'left',
                value: 'clase.nombre'
            },
            {
                text: 'Peso',
                align: 'left',
                value: 'peso_gr'
            },
            {
                text: 'P.Coste',
                align: 'left',
                value: 'precio_coste'
            },
            {
                text: 'PVP',
                align: 'left',
                value: 'precio_venta'
            },
            {
                text: 'Estado',
                align: 'left',
                value: 'estado.nombre'
            },
            {
                text: 'O/D',
                align: 'left',
                sortable: false,
            },
            {
                text: 'Acciones',
                align: 'Center',
                value: ''
            }
        ],
        arr_reg:[],
        mi_filtro:[],
        status: false,
		registros: false,
        dialog: false,
        item_destroy: 0,
        show_loading: true,
        url: "/mto/productos",
        ruta: "producto",

        filtro: true,
        expand: false,

      }
    },
    mounted()
    {
        this.pagination.model="producto"+this.empresaActiva;
        if (this.getPagination.model == this.pagination.model){
            this.filtro = false;
            this.updatePosPagina(this.getPagination);
        }
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
            'hasEdtPro',
            'hasExcel',
            'getPagination',
            'empresaActiva'
        ]),
    },
    methods:{
        ...mapActions([
            'setPagination',
            'unsetPagination'
        ]),
        showExpand(item){
            if (item.empresa_id != item.destino_empresa_id || item.notas != null)
                return true;

            return false;
        },
        destino(item){

            var dest = '';
            var nota = '';
            if (item.empresa_id != item.destino_empresa_id){
                dest = ' Destino Venta: '+item.destino.nombre + ' Procede de '+item.empresa.nombre.toUpperCase();
            }
            if (item.notas != null)
                nota = item.notas;

            return nota + dest;
        },
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
        editItem (item) {
            this.setPagination(this.paginaActual);
            if (item.deleted_at == null)
                this.$router.push({ name: this.ruta+'.edit', params: { id: item.id } })
            else
                this.$router.push({ name: this.ruta+'.show', params: { id: item.id } })
        },
        openDialog (item){
            this.dialog = true;
            this.item_destroy = item;
        },
        destroyReg () {
            this.dialog = false;

            axios.post(this.url+'/'+this.item_destroy.id,{_method: 'delete'})
                .then(response => {
                    if (this.item_destroy.deleted_at == null){
                            const index = this.arr_reg.indexOf(this.item_destroy)
                            this.arr_reg.splice(index, 1)
                        }else{
                            this.item_destroy.deleted_at = null;
                            const index = this.arr_reg.indexOf(this.item_destroy)
                            this.arr_reg[index]=this.item_destroy;
                        }
                    this.$toast.success(response.data.msg);
                })
            .catch(err => {
                this.status = true;
                //console.log(err);
                var msg = err.response.data.message;
                this.$toast.error(msg);

            });

        },
        goExcel(){
            console.log(this.mi_filtro);

            this.show_loading = true;
            axios({
                url: this.url+"/excel",
                method: 'POST',
                responseType: 'blob', // important
                data: this.mi_filtro
                })
            .then(response => {

                let blob = new Blob([response.data])
                let link = document.createElement('a')
                link.href = window.URL.createObjectURL(blob)

                link.download = "Productos."+new Date().getFullYear()+(new Date().getMonth()+1)+(new Date().getDate())+'.xlsx';

                document.body.appendChild(link);
                link.click()
                document.body.removeChild(link);

                this.$toast.success('Download Ok! '+link.download);

                this.show_loading = false;

            })
            .catch(err => {
                console.log(err.response);
                this.$toast.error(err.response.data.message);
                this.show_loading = false;
            });
        },
    }
  }
</script>

<style scoped>
  .tachado{text-decoration:line-through;}
</style>
