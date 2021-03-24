<template>
	<div>
        <loading :show_loading="show_loading"></loading>
        <my-dialog :dialog.sync="dialog" registro="registro" @destroyReg="destroyReg"></my-dialog>
        <v-card>
            <v-card-title color="indigo">
                <h2 color="indigo">{{titulo}}</h2>
                <v-spacer></v-spacer>
                <v-tooltip bottom>
                    <template v-slot:activator="{ on }">
                        <v-btn
                            v-on="on"
                            color="white"
                            icon
                            @click="goClose()"
                        >
                            <v-icon color="orange">explore</v-icon>
                        </v-btn>
                    </template>
                        <span>Cerrar Recuento</span>
                </v-tooltip>
                <v-tooltip bottom>
                    <template v-slot:activator="{ on }">
                        <v-btn
                            v-on="on"
                            color="white"
                            icon
                            @click="goReset()"
                        >
                            <v-icon color="red darken-4">delete_forever</v-icon>
                        </v-btn>
                    </template>
                        <span>Eliminar recuento</span>
                </v-tooltip>
                <v-tooltip bottom>
                    <template v-slot:activator="{ on }">
                        <v-btn
                            v-on="on"
                            color="white"
                            icon
                            @click="goResetPerdidas()"
                        >
                            <v-icon color="orange darken-4">bug_report</v-icon>
                        </v-btn>
                    </template>
                        <span>Eliminar SOLO perdidas</span>
                </v-tooltip>
                <menu-ope></menu-ope>
            </v-card-title>
        </v-card>
        <v-card>
            <v-form>
                 <v-container>
                     <v-layout row wrap>
                        <v-flex sm1>
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
                                    label="Recuento"
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
                                v-show="entrada_manual"
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
                                v-show="entrada_manual"
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
                        <v-flex sm2>
                            <v-switch
                                :label="computedEntrada"
                                v-model="entrada_manual"
                                color="primary"
                            ></v-switch>
                        </v-flex>
                        <v-flex sm2></v-flex>
                        <v-flex sm1 v-if="entrada_manual">
                            <div class="text-xs-center">
                                <v-btn @click="submit" round small :loading="loading" block  color="primary">
                                    Guardar
                                </v-btn>
                            </div>
                        </v-flex>
                        <v-flex sm1 v-else>
                            <div class="text-xs-center">
                                <v-btn @click="test"
                                    :disabled="codigos==null"
                                     round
                                     small
                                     :loading="loading"
                                      block
                                      color="primary">
                                    Procesar
                                </v-btn>
                            </div>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex sm5></v-flex>
                        <v-flex sm1>
                            <v-textarea
                                class="caption"
                                v-show="!entrada_manual"
                                label="Buffer Pistola"
                                v-model="codigos"
                                hint="Download códigos pistola"
                                rows="20"
                                row-height="12"
                                outline
                            ></v-textarea>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap v-show="entrada_manual">
                            <v-flex xs12>
                                <v-data-table
                                    :headers="headers"
                                    :items="items"
                                    :pagination.sync="pagination"
                                    rows-per-page-text="Registros por página"
                                >
                                    <template slot="items" slot-scope="props">
                                        <td v-if="props.item.producto != null">{{ props.item.producto.referencia }}</td>
                                        <td v-else>{{props.item.producto_id}}</td>
                                        <td v-if="props.item.producto != null">{{ props.item.producto.nombre }}</td>
                                        <td v-else>¿?</td>
                                        <td v-if="props.item.producto != null">{{ props.item.estado.nombre }}</td>
                                        <td v-else>¿?</td>
                                        <td v-if="props.item.producto != null">{{ props.item.rfid.nombre }}</td>
                                        <td v-else>¿?</td>
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
                                            <v-icon
                                                small
                                                @click="openDialog(props.item)"
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
            </v-form>
        </v-card>
	</div>
</template>
<script>
import MenuOpe from './MenuOpe'
import Loading from '@/components/shared/Loading'
import moment from 'moment'
import {mapGetters} from 'vuex';
import MyDialog from '@/components/shared/MyDialog'
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
                codigos:null,
                entrada_manual: true,
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
                url: "/mto/recuentos",
                dialog: false,

                items: [],
                fecha_d: new Date().toISOString().substr(0, 7)+"-01",
                menu_d: false,

                show: false,
                show_loading: true,
      		}
        },
        mounted(){

            axios.get('/mto/recuentos/create')
                .then(res => {
                    this.items = res.data.recuentos;

                })
                .catch(err =>{
                    this.$toast.error(err.response.data.message);
                    this.$router.push({ name: 'dash' })
                })
                .finally(()=> {
                    this.show_loading = false;
                    this.registros = true;
                });
        },
        computed: {
        ...mapGetters([
        ]),
        computedEntrada(){
            return this.entrada_manual ? 'Entrada Manual' : 'Buffer Pistola';
        },
        computedFechaD() {
                moment.locale('es');
                return this.fecha_d ? moment(this.fecha_d).format('L') : '';
            }
        },
    	methods:{
            submit() {


                if (this.loading === false){
                    this.loading = true;

                    this.$validator.validateAll().then((result) => {
                        if (result){
                            axios.post(this.url,{
                                fecha: this.fecha_d,
                                prefijo: this.prefijo,
                                referencia: this.referencia
                            })
                                .then(res => {

                                    this.items.push(res.data.recuento);
                                    this.referencia = null;
                                    this.$validator.reset();

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



            this.$router.push({ name: 'producto.edit', params: { id: item.producto_id } })
        },
        openDialog (item){
            this.dialog = true;
            this.item_destroy = item;
        },
        destroyReg () {
            this.dialog = false;

            axios.post(this.url+'/'+this.item_destroy.id,{_method: 'delete'})
                .then(response => {

                    const index = this.items.indexOf(this.item_destroy)
                    this.items.splice(index, 1)

                    this.$toast.success(response.data.msg);
                })
            .catch(err => {
                this.status = true;
                //console.log(err);
                var msg = err.response.data.message;
                this.$toast.error(msg);

            });

        },
        goClose () {

            this.show_loading = true;
            axios.post(this.url+'/close',{fecha: this.fecha_d})
                .then(response => {

                    this.items = response.data;

                    this.$toast.success('Recuento cerrado!');
                })
            .catch(err => {
                this.status = true;
                //console.log(err);
                var msg = err.response.data.message;
                this.$toast.error(msg);

            })
            .finally(()=> {
                this.show_loading = false;
            });

        },
        goExcel(){

            this.show_loading = true;
            axios({
                url: "mto/recuentos/excel",
                method: 'POST',
                responseType: 'blob', // important
                data:{ data: this.arr_reg }
                })
            .then(response => {

                let blob = new Blob([response.data])
                let link = document.createElement('a')
                link.href = window.URL.createObjectURL(blob)

                link.download = "Recuento."+new Date().getFullYear()+(new Date().getMonth()+1)+(new Date().getDate())+'.xlsx';

                document.body.appendChild(link);
                link.click()
                document.body.removeChild(link);

                this.$toast.success('Download Ok! '+link.download);

                this.show_loading = false;

            })
            .catch(err => {
                this.$toast.error(err.response.data.message);
                this.show_loading = false;
            });
        },
        goReset() {
            if (confirm('Eliminar TODO el recuento')){
                this.show_loading = true;
                axios.post("/mto/recuentos/reset",{reset: true})
                    .then(res => {

                        this.$toast.success(res.data);
                        this.items = [];

                    })
                    .catch(err => {
                        this.$toast.error(err.response.data.message);
                    })
                    .finally(()=> {
                        this.show_loading = false;
                    });
            }
        },
        goResetPerdidas() {
            if (confirm('Eliminar PERDIDAS del recuento')){
                this.show_loading = true;
                axios.post("/mto/recuentos/reset",{reset: false})
                    .then(res => {

                        this.$toast.success(res.data);
                        this.items = [];

                    })
                    .catch(err => {
                        this.$toast.error(err.response.data.message);
                    })
                    .finally(()=> {
                        this.show_loading = false;
                    });
            }
        },
        update(item) {

            this.editedIndex = this.items.indexOf(item)
            //this.editedItem = Object.assign({}, item)

                    axios.put("/mto/recuentos/"+item.id, {rfid_id : item.rfid_id})
                        .then(res => {

                            Object.assign(this.items[this.editedIndex], res.data.recuento)

                            this.$toast.success(res.data.message);
                            this.loading = false;

                        })
                        .catch(err => {
                            this.$toast.error(err.response.data.message);
                        })
                        .finally(()=> {
                            this.show_loading = false;
                        });

            },
        test(){
            this.loading = this.show_loading = true;
            axios.post('/mto/recuentos/test',{
                                codigos: this.codigos,
                                fecha: this.fecha_d
                            })
                .then(res => {
                    this.$router.push({ name: 'recuento.index' })
                })
                .catch(err => {
                    this.$toast.error(err.response.data.message);
                })
                .finally(()=> {
                    this.loading = this.show_loading = false;
                });
        }

    }
  }
</script>
