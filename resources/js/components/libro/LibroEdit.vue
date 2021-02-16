<template>
	<div v-show="show">
        <loading :show_loading="show_loading"></loading>
        <v-card>
            <v-card-title color="indigo">
                <h2 color="indigo">{{titulo}}</h2>
                <v-spacer></v-spacer>
                <menu-ope :id="libro.id"></menu-ope>
            </v-card-title>
        </v-card>
        <v-card>
            <v-form>
                 <v-container>
                     <v-layout row wrap>
                        <v-flex sm2 d-flex>
                            <v-select
                            v-model="libro.grupo_id"
                            v-validate="'required'"
                            data-vv-name="grupo_id"
                            data-vv-as="grupo"
                            :error-messages="errors.collect('grupo_id')"
                            :items="grupos"
                            label="Grupo"
                            required
                            ></v-select>
                        </v-flex>

                        <v-flex sm2>
                            <v-text-field
                                v-model="libro.nombre"
                                v-validate="'required'"
                                :error-messages="errors.collect('nombre')"
                                label="Nombre"
                                data-vv-name="nombre"
                                data-vv-as="nombre"
                                required
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                         <v-flex sm1>
                            <v-text-field
                                v-model="libro.ejercicio"
                                v-validate="'required|integer'"
                                :error-messages="errors.collect('ejercicio')"
                                label="Ejercicio"
                                data-vv-name="ejercicio"
                                data-vv-as="ejercicio"
                                required
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm1></v-flex>
                        <v-flex sm2>
                            <v-switch
                                v-model="libro.peso_frm"
                                color="primary"
                                label="Peso en Frm"
                            ></v-switch>
                        </v-flex>
                        <v-flex sm2>
                            <v-switch
                                v-model="libro.recompras"
                                color="primary"
                                label="Cambio a Recompras"
                            ></v-switch>
                        </v-flex>
                         <v-flex sm2>
                            <v-switch
                                label="Cerrado"
                                v-model="libro.cerrado"
                                color="primary">
                            ></v-switch>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex sm2>
                            <v-text-field
                                v-model="libro.codigo_pol"
                                :error-messages="errors.collect('codigo_pol')"
                                label="Código G. XI"
                                data-vv-name="codigo_pol"
                                data-vv-as="código"
                                required
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm2>
                            <v-text-field
                                v-model="libro.nombre_csv"
                                :error-messages="errors.collect('nombre_csv')"
                                label="Nombre CSV"
                                data-vv-name="nombre_csv"
                                data-vv-as="nombre csv"
                                required
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm1>
                            <v-text-field
                                v-model="libro.plantilla_excel"
                                v-validate="'alpha_num'"
                                :error-messages="errors.collect('plantilla_excel')"
                                label="Libro Excel"
                                data-vv-name="plantilla_excel"
                                data-vv-as="plantilla_excel"
                                :readonly="!isRoot"
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm3>
                            <v-text-field
                                v-model="libro.establecimiento"
                                v-validate="'max:50'"
                                :error-messages="errors.collect('establecimiento')"
                                label="Establecimiento"
                                data-vv-name="establecimiento"
                                data-vv-as="establecimiento"
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm1>
                            <v-text-field
                                v-model="libro.semdia_bloqueo"
                                v-validate="'required|max:3'"
                                :error-messages="errors.collect('semdia_bloqueo')"
                                label="Bloqueo Lotes"
                                data-vv-name="semdia_bloqueo"
                                data-vv-as="Bloqueo Lote"
                                hint="Semanas/Nº de día o Nº días"
                                required
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm1></v-flex>
                        <v-flex sm2>
                            <v-switch
                                v-model="libro.grabaciones"
                                color="primary"
                                label="Grabaciones"
                            ></v-switch>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex sm1>
                            <v-text-field
                                v-model="libro.serie_com"
                                v-validate="{ required: true, regex:/^[A-Za-z]$/ }"
                                :error-messages="errors.collect('serie_com')"
                                label="Serie Compra"
                                hint="Formato: A-Z"
                                data-vv-name="serie_com"
                                data-vv-as="serie"
                                required
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm1>
                            <v-text-field
                                v-model="libro.ult_compra"
                                v-validate="'required|integer'"
                                :error-messages="errors.collect('ult_compra')"
                                label="Ult. Compra"
                                data-vv-name="ult_compra"
                                data-vv-as="Compra"
                                required
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm1>
                            <v-text-field
                                v-model="libro.serie_fac"
                                v-validate="'required|max:5'"
                                :error-messages="errors.collect('serie_fac')"
                                label="Serie Fac. Compra"
                                data-vv-name="serie_fac"
                                data-vv-as="serie"
                                required
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm1>
                            <v-text-field
                                v-model="libro.ult_factura"
                                v-validate="'required|integer'"
                                :error-messages="errors.collect('ult_factura')"
                                label="Ult. F. Compra"
                                data-vv-name="ult_factura"
                                data-vv-as="Factura"
                                required
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm1>
                            <v-text-field
                                v-model="libro.interes"
                                v-validate="'required|decimal'"
                                :error-messages="errors.collect('interes')"
                                label="Interés Renovación"
                                data-vv-name="interes"
                                data-vv-as="interés"
                                required
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm1 v-if="parametros.doble_interes">
                            <v-text-field
                                v-model="libro.interes_recuperacion"
                                v-validate="'required|decimal'"
                                :error-messages="errors.collect('interes_recuperacion')"
                                label="Interés Recuperación"
                                data-vv-name="interes_recuperacion"
                                data-vv-as="interés"
                                required
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm1 v-else>
                        </v-flex>


                        <v-flex sm1>
                            <v-text-field
                                v-model="libro.dias_custodia"
                                v-validate="'required|integer'"
                                :error-messages="errors.collect('dias_custodia')"
                                label="Días Custodia"
                                data-vv-name="dias_custodia"
                                data-vv-as="días custodia"
                                required
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm1>
                            <v-text-field
                                v-model="libro.dias_cortesia"
                                v-validate="'required|integer'"
                                :error-messages="errors.collect('dias_cortesia')"
                                label="Días Cortesía"
                                data-vv-name="dias_cortesia"
                                data-vv-as="días Cortesía"
                                required
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm1>
                            <v-text-field
                                v-model="libro.tramo"
                                v-validate="'required|decimal'"
                                :error-messages="errors.collect('tramo')"
                                label="Tramo Compra - €"
                                data-vv-name="tramo"
                                data-vv-as="tramo"
                                hint="Valor compra mínimo"
                                required
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm1>
                            <v-text-field
                                v-model="libro.interes_min"
                                v-validate="'required|decimal'"
                                :error-messages="errors.collect('interes_min')"
                                label="Interés Mínimo %"
                                data-vv-name="interes_min"
                                data-vv-as="interés"
                                hint='Aplicable a tamo <= x'
                                required
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>


                    </v-layout>
                    <v-layout row wrap>
                         <v-flex sm2>
                            <v-text-field
                                v-model="libro.username"
                                :error-messages="errors.collect('username')"
                                label="Usuario"
                                data-vv-name="username"
                                readonly
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm2>
                            <v-text-field
                                v-model="computedFModFormat"
                                label="Modificado"
                                readonly
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm2>
                            <v-text-field
                                v-model="computedFCreFormat"
                                label="Creado"
                                readonly
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm4></v-flex>
                        <v-flex sm1>
                            <div class="text-xs-center">
                                        <v-btn @click="submit"  round  :loading="loading" block  color="primary">
                                Guardar
                                </v-btn>
                            </div>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-form>        </v-card>
	</div>
</template>
<script>
import moment from 'moment'
import Loading from '@/components/shared/Loading'
import MenuOpe from './MenuOpe'
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
                titulo:"Libro",
                libro: {},
                url: "/mto/libros",
                ruta: "libro",
                grupos: [],

        		status: false,
                loading: false,

                show: false,
                show_loading: true,
      		}
        },
        mounted(){
            var id = this.$route.params.id;

            if (id > 0)
                axios.get(this.url+'/'+id+'/edit')
                    .then(res => {

                        this.libro = res.data.libro;
                        this.grupos = res.data.grupos;
                        this.show = true;
                        this.show_loading = false;
                    })
                    .catch(err => {
                        this.$toast.error(err.response.data.message);
                        this.$router.push({ name: this.ruta+'.index'})
                    })
        },
        computed: {
            ...mapGetters([
                'parametros',
                'isRoot'
            ]),
            computedFModFormat() {
                moment.locale('es');
                return this.libro.updated_at ? moment(this.libro.updated_at).format('D/MM/YYYY H:mm') : '';
            },
            computedFCreFormat() {
                moment.locale('es');
                return this.libro.created_at ? moment(this.libro.created_at).format('D/MM/YYYY H:mm') : '';
            }

        },
    	methods:{
            submit() {

                if (this.loading === false){
                    this.loading = true;

                    this.$validator.validateAll().then((result) => {
                        if (result){
                             axios.put(this.url+"/"+this.libro.id, this.libro)
                                .then(res => {

                                    this.$toast.success(res.data.message);
                                    this.libro = res.data.libro;
                                    this.loading = false;
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

    }
  }
</script>
