<template>
<div>
	  <v-app light>
	    <v-toolbar class="white">
	      <v-toolbar-title>{{this.empresa.nombre}}</v-toolbar-title>
	      <v-spacer></v-spacer>
            <v-btn v-if="!isLoggedIn" icon v-on:click="login">
                <v-icon>person</v-icon>
            </v-btn>
            <v-btn v-else icon :to="{name: 'dash'}">
                <v-icon>desktop_windows</v-icon>
            </v-btn>

	    </v-toolbar>
	    <v-content>
	      <section>
	        <v-parallax :src="empresa.img1" height="600">
	          <v-layout
	            column
	            align-center
	            justify-center
	            :class="computedColorTitulo"
	          >

	            <h1 class="mb-2 display-1 text-xs-center">{{this.empresa.nombre}}</h1>
	            <div class="subheading mb-3 text-xs-center">Compra - Venta de joyas y metales preciosos</div>
	          </v-layout>
	        </v-parallax>
	      </section>

	      <section>
	        <v-layout
	          column
	          wrap
	          class="my-5"
	          align-center
	        >
	          <v-flex xs12 sm4 class="my-3">
	            <div class="text-xs-center">
	              <h2 class="headline">Tasación sin compromiso</h2>
	              <span class="subheading">
	                Precio más alto en sus joyas
	              </span>
	            </div>
	          </v-flex>
	          <v-flex xs12>
	            <v-container grid-list-xl>
	              <v-layout row wrap align-center>
	                <v-flex xs12 md4>
	                  <v-card class="elevation-0 transparent">
	                    <v-card-text class="text-xs-center">
	                      <v-icon x-large class="blue--text text--lighten-2">shopping_cart</v-icon>
	                    </v-card-text>
	                    <v-card-title primary-title class="layout justify-center">
	                      <div class="headline text-xs-center">Compra</div>
	                    </v-card-title>
	                    <v-card-text>
	                      Compra de joyas en cualquier estado. Oro, plata, platino, relojes...
	                    </v-card-text>
	                  </v-card>
	                </v-flex>
	                <v-flex xs12 md4>
	                  <v-card class="elevation-0 transparent">
	                    <v-card-text class="text-xs-center">
	                      <v-icon x-large class="blue--text text--lighten-2">payment</v-icon>
	                    </v-card-text>
	                    <v-card-title primary-title class="layout justify-center">
	                      <div class="headline">Venta</div>
	                    </v-card-title>
	                    <v-card-text>
	                      Disponemos de un amplio catálogo de piezas renovadas a precios muy competitivos.
	                    </v-card-text>
	                  </v-card>
	                </v-flex>
	                <v-flex xs12 md4>
	                  <v-card class="elevation-0 transparent">
	                    <v-card-text class="text-xs-center">
	                      <v-icon x-large class="blue--text text--lighten-2">mood</v-icon>
	                    </v-card-text>
	                    <v-card-title primary-title class="layout justify-center">
	                      <div class="headline text-xs-center">Garantía</div>
	                    </v-card-title>
	                    <v-card-text>
	                      Comprometidos de principio a fin con el proceso de compra, venta y custodia de joyas.
	                    </v-card-text>
	                  </v-card>
	                </v-flex>
	              </v-layout>
	            </v-container>
	          </v-flex>
	        </v-layout>
	      </section>
	      <section>
	        <v-container grid-list-xl>
	          <v-layout row wrap justify-center class="my-5">
	            <v-flex xs12 sm4>
	            </v-flex>
	            <v-flex xs12 sm4 offset-sm1>
	              <v-card class="elevation-0 transparent">
	                <v-card-title primary-title class="layout justify-center">
	                  <div class="headline">Contactar</div>
	                </v-card-title>
	                <v-list class="transparent">
	                  <v-list-tile>
	                    <v-list-tile-action>
	                      <v-icon class="blue--text text--lighten-2">phone</v-icon>
	                    </v-list-tile-action>
	                    <v-list-tile-content>
	                      <v-list-tile-title>Tf: {{this.empresa.telefono}}</v-list-tile-title>
	                    </v-list-tile-content>
	                  </v-list-tile>
	                  <v-list-tile>
	                    <v-list-tile-action>
	                      <v-icon class="blue--text text--lighten-2">place</v-icon>
	                    </v-list-tile-action>
	                    <v-list-tile-content>
	                      <v-list-tile-title>{{this.empresa.direccion+' '+this.empresa.poblacion}}</v-list-tile-title>
	                    </v-list-tile-content>
	                  </v-list-tile>
	                  <v-list-tile>
	                    <v-list-tile-action>
	                      <v-icon class="blue--text text--lighten-2">email</v-icon>
	                    </v-list-tile-action>
	                    <v-list-tile-content>
	                      <v-list-tile-title>{{this.empresa.email}}</v-list-tile-title>
	                    </v-list-tile-content>
	                  </v-list-tile>
	                </v-list>
	              </v-card>
	            </v-flex>
	          </v-layout>
	        </v-container>
	      </section>
	    </v-content>
	  </v-app>

	 </div>
</template>
<script>
import {mapGetters} from 'vuex';
	export default {
        computed:{
            ...mapGetters([
				'isLoggedIn'
            ]),
            computedColorTitulo(){
                if (this.empresa.img1 == null)
                    return "black--text";
                else
                    return "white--text";
            }
        },
        data: () => ({
            empresa: {}
        }),
        beforeMount(){
            axios.get('api/emp')
                .then(res => {
                    this.empresa = res.data;
                })
                .catch(err => {
                    console.log(err);
            })
        },
        methods:{
            login(){
                window.location = '/login';
            }
        }
	}
</script>
