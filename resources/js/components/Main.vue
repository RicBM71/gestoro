<template>
    <v-container grid-list-md text-xs-center>
        <v-layout row wrap>
            <v-flex sm3 xs12>
                 <v-btn round block large color="grey" class="blue-grey lighten-3" @click="goFindCompra()">
                     Buscar Compra
                     <v-icon right dark>search</v-icon>
                </v-btn>
            </v-flex>

            <v-flex sm3 xs12>
                 <v-btn round block large color="grey" class="blue-grey lighten-3" @click="goCompra()">
                     Nueva Compra
                     <v-icon right dark>add_shopping_cart</v-icon>
                </v-btn>
            </v-flex>

            <v-flex sm3 xs12>
                 <v-btn round block large color="grey" class="blue-grey lighten-3" @click="goVenta()">
                     Nueva Venta
                     <v-icon right dark>credit_card</v-icon>
                </v-btn>
            </v-flex>
            <v-flex sm3 xs12>
                 <v-btn round block large color="grey" class="blue-grey lighten-3" @click="goFindVenta()">
                     Buscar Venta
                     <v-icon right dark>search</v-icon>
                </v-btn>
            </v-flex>
        </v-layout>
        <v-layout row wrap>
            <v-flex sm1 xs12></v-flex>
            <v-flex sm3 xs12>
                 <v-btn round block large color="grey" class="blue-grey lighten-3" @click="goCompraIndex()">
                     Consulta Compras
                     <v-icon right dark>shopping_cart</v-icon>
                </v-btn>
            </v-flex>
            <v-flex sm2 xs12>
                 <v-btn round block large color="grey" class="blue-grey lighten-3" @click="goCajaIndex()">
                     Caja
                     <v-icon right dark>euro_symbol</v-icon>
                </v-btn>
            </v-flex>
            <v-flex sm2 xs12>
                 <v-btn round block large color="grey" class="blue-grey lighten-3" @click="goTraspasos()">
                     Traspasos
                     <v-icon right dark>send</v-icon>
                </v-btn>
            </v-flex>
            <v-flex sm3 xs12>
                 <v-btn round block large color="grey" class="blue-grey lighten-3" @click="goVentaIndex()">
                     Consulta Ventas
                     <v-icon right dark>credit_card</v-icon>
                </v-btn>
            </v-flex>
        </v-layout>
        <v-layout row wrap>
            <v-flex sm2 xs12></v-flex>
            <v-flex sm2 xs12>
                <v-btn
                    round block large color="grey" class="blue-grey lighten-3"
                    @click="goClientes()">
                        Clientes
                    <v-icon right dark>group</v-icon>
                </v-btn>
            </v-flex>
            <v-flex sm2 xs12>
                <v-btn
                    round block large color="grey" class="blue-grey lighten-3"
                    @click="goRecogidas()">
                        Recogidas
                    <v-icon right dark>av_timer</v-icon>
                </v-btn>
            </v-flex>
            <v-flex sm2 xs12>
                <v-btn
                    round block large color="grey" class="blue-grey lighten-3"
                    @click="goOperaciones()">
                        Operaciones
                    <v-icon right dark>ballot</v-icon>
                </v-btn>
            </v-flex>
            <v-flex sm2 xs12>
                <v-btn
                    round block large color="grey" class="blue-grey lighten-3"
                    @click="goProductos()">
                    Productos
                    <v-icon right dark>local_offer</v-icon>
                 </v-btn>
            </v-flex>

        </v-layout>
        <v-layout row wrap v-if="logo!=null">
            <v-flex xs2></v-flex>
            <v-flex xs8>
                <v-responsive>
                    <v-layout column>
                        <v-img class="img-fluid" :src="logo"></v-img>
                    </v-layout>
                </v-responsive>
            </v-flex>
        </v-layout>
    </v-container>
</template>
<script>
import {mapActions} from "vuex";
import {mapGetters} from 'vuex';
export default {
    computed:{
        ...mapGetters([
            'isLoggedIn',
            'isRoot',
            'isAdmin',
            'empresaActiva',
            'imgFondo'
		]),
    },
    data: () => ({
        logo: ""
    }),
    mounted(){

        axios.get('/dash')
            .then(res => {

                this.setAuthUser(res.data.user);

                //this.logo = "/storage/logos/"+this.empresaActiva+".png";
                this.logo = this.imgFondo;
            })
            .catch(err => {
                console.log(err);
        })

    },
    watch: {
        empresaActiva: function (newValue) {

            //this.logo = "/storage/logos/"+newValue+".png";
            this.logo = this.imgFondo;
        }
    },
    methods:{
        ...mapActions([
				'setAuthUser'
            ]),
        goCompraIndex(){
            this.$router.push({ name: 'compra.index' })
        },
        goCompra(){
            this.$router.push({ name: 'compra.create' })
        },
        goClientes(){
            this.$router.push({ name: 'cliente.index' })
        },
        goProductos(){
            this.$router.push({ name: 'producto.index' })
        },
        goFindCompra(){
            this.$router.push({ name: 'find.compra' })
        },
        goCajaIndex(){
            this.$router.push({ name: 'caja.index' })
        },
        goTraspasos(){
            this.$router.push({ name: 'traspaso.index' })
        },
        goVenta(){
            this.$router.push({ name: 'albaran.create' })
        },
        goVentaIndex(){
            this.$router.push({ name: 'albaran.index' })
        },
         goFindVenta(){
            this.$router.push({ name: 'find.albaran' })
        },
         goOperaciones(){
            this.$router.push({ name: 'exportar.operaciones' })
        },
        goRecogidas(){
            this.$router.push({ name: 'exportar.recogidas' })
        },
    },

  }
</script>
