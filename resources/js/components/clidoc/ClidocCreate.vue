<template>
	<div>
        <v-card>
            <v-card-title color="indigo">
                <h2 color="indigo">{{titulo}}</h2>
                <v-spacer></v-spacer>
                <menu-ope :id="id"></menu-ope>
                <v-tooltip bottom>
                    <template v-slot:activator="{ on }">
                        <v-btn
                            v-on="on"
                            color="white"
                            icon
                            @click="goBack()"
                        >
                            <v-icon color="primary">arrow_back</v-icon>
                        </v-btn>
                    </template>
                        <span>Volver</span>
                </v-tooltip>
            </v-card-title>
        </v-card>
        <v-card>
            <v-container grid-list-md text-xs-left>
                <v-layout row wrap>
                    <v-flex sm10>
                        <h3 class="blue--text">{{cliente.dni}} - {{cliente.razon}}</h3>
                    </v-flex>
                </v-layout>
                <v-layout row wrap>
                    <v-flex sm4  v-if="img_anverso!=null">
                            <h3>Anverso</h3>
                            <figure>
                                <v-img :src="img_anverso"/>
                            </figure>
                    </v-flex>
                    <v-flex v-else sm4></v-flex>
                    <v-flex sm4 v-if="img_reverso!=null">
                            <h3>Reverso</h3>
                            <figure>
                                <v-img :src="img_reverso"/>
                            </figure>
                    </v-flex>
                    <v-flex v-else sm4></v-flex>
                    <v-flex sm4>
                        <v-btn v-show="!loading" fab small :disabled="disabled_cap" @click="onCapture"><v-icon color="red darken-4">camera</v-icon></v-btn>
                        <v-btn v-show="!loading" flat round @click="reset()"><v-icon dark color="gray">adjust</v-icon>Reset</v-btn>
                        <v-btn :loading="loading" flat round :disabled="disabled_save" @click="submit"><v-icon dark color="primary">save</v-icon>Guardar</v-btn>
                            <!-- <code v-if="device">{{ device.label }}</code> -->
                                <vue-web-cam
                                    ref="webcam"
                                    :device-id="deviceId"
                                    height="250"
                                    width="100%"
                                    @started="onStarted"
                                    @stopped="onStopped"
                                    @error="onError"
                                    @cameras="onCameras"
                                    @camera-change="onCameraChange"
                                >
                                </vue-web-cam>
                    </v-flex>
                </v-layout>
            </v-container>
        </v-card>
	</div>
</template>
<script>
import MenuOpe from './MenuOpe'
import { WebCam } from "vue-web-cam"
export default {
    components: {
        'vue-web-cam': WebCam,
        'menu-ope': MenuOpe,
        },
    data () {
        return {
            id: 0,
            titulo: "Scanear Documentación",
            cliente_id: 0,
            compra_id: 0,
            camera: null,
            deviceId: null,
            devices: [],
            disabled_cap: true,
            disabled_save: true,
            loading: false,
            img_anverso: null,
            img_reverso: null,
            cliente: {},
            ruta_ant: {}
        }
    },
    mounted(){

        this.cliente_id = parseInt(this.$route.params.cliente_id,10);
        this.compra_id =  parseInt(this.$route.params.compra_id,10);

        axios.get('/mto/clidocs/'+this.cliente_id+'/'+this.compra_id+'/create')
            .then(res => {
                this.cliente = res.data.cliente;

            })
            .catch(err => {
                this.$toast.error(err.response.data.message);
                this.$router.push({ name: 'cliente.index'})
            })
    },
    computed: {
        device: function() {
            return this.devices.find(n => n.deviceId === this.deviceId);
        }
    },
    watch: {
        camera: function(id) {
            this.deviceId = id;
        },
        devices: function() {
            // Once we have a list select the first one
            const [first, ...tail] = this.devices;
            if (first) {
                this.camera = first.deviceId;
                this.deviceId = first.deviceId;
            }
        }
    },
    methods: {
        goBack(){
           this.$router.go(-1)
        },
        onCapture() {
            if (this.img_anverso == null)
                this.img_anverso = this.$refs.webcam.capture();
            else
                this.img_reverso = this.$refs.webcam.capture();

            if (this.cliente.tipodoc == "N"){
                if (this.img_anverso != null && this.img_reverso != null){
                    this.disabled_save = false;
                }
            }
            else{
                if (this.img_anverso != null)
                    this.disabled_save = false;
            }



        },
        submit(){

            if (this.loading === false){
                this.loading = true;
                axios.post('/mto/clidocs', {
                    cliente_id: this.cliente.id,
                    img1: this.img_anverso,
                    img2: this.img_reverso
                })
                    .then(res => {

                        this.goBack();

                        this.loading = false;

                    })
                    .catch(err => {

                        if (err.request.status == 422){ // fallo de validated.
                            const msg_valid = err.response.data.errors;
                            for (const prop in msg_valid) {
                                this.$toast.error(`${msg_valid[prop]}`);
                            }
                        }else{
                            this.$toast.error(err.response.data.message);
                        }
                        this.loading = false;
                    });
                }


        },
        onStarted(stream) {
         //   console.log("On Started Event", stream);
            this.disabled_cap = false;
        },
        onStopped(stream) {
           // console.log("On Stopped Event", stream);
        },
        onStop() {
            this.$refs.webcam.stop();
            this.$emit('update:show_webcam', false);
        },
        onStart() {
            this.$refs.webcam.start();
        },
        onError(error) {
            console.log("On Error Event", error);
        },
        onCameras(cameras) {
            this.devices = cameras;
          //  console.log("On Cameras Event", cameras);
        },
        onCameraChange(deviceId) {
            this.deviceId = deviceId;
            this.camera = deviceId;
          // console.log("On Camera Change Event", deviceId);
        },
        reset(){
            this.img_anverso = this.img_reverso = null;
        }
    }
}

</script>
