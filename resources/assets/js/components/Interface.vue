<template>
    <form class="network-manager">

        <div class="form-group">
            <label for="type">Type</label>
            <select v-model="network.type" name="type" id="type" class="form-control">
                <option value="dhcp">DCHP</option>
                <option value="static">Static</option>
            </select>
        </div>

        <div class="form-group">
            <label for="ip_address">IP Address</label>
            <input type="text" v-model="network.ip_address" :readonly="!isStatic" name="ip_address" id="ip_address" class="form-control" :required="isStatic">
        </div>

        <div class="form-group">
            <label for="netmask">Netmask</label>
            <input type="text" v-model="network.netmask" :readonly="!isStatic" name="netmask" id="netmask" class="form-control" :required="isStatic">
        </div>

        <div class="form-group">
            <label for="gateway">Gateway</label>
            <input type="text" v-model="network.gateway" :readonly="!isStatic" name="gateway" id="gateway" class="form-control" :required="isStatic">
        </div>

        <div class="form-group">
            <label for="dns">DNS</label>
            <input type="text" v-model="network.dns" :readonly="!isStatic" name="dns" id="dns" class="form-control" :required="isStatic">
        </div>

        <div class="form-group">
            <button class="btn btn-primary" :readonly="!isStatic" type="button" :disabled="canSave" @click="save()">
                Save
            </button>
        </div>

    </form>
</template>

<script>
    export default {

        props: ['interface'],

        data(){
            return {
                network: {
                    name: '',
                    type: 'dhcp',
                    ip_address: '',
                    netmask: '',
                    gateway: '',
                    dns: '',
                    metric: '',
                    mode: '',
                    mac: '',
                }
            }
        },

        created(){
          this.network = this.interface;
        },

        methods:{
            save(){
                axios.post('/api/network-interface/' + this.interface.name, this.network).then(response => {
                    Alert.success('System rebooting, please wait!');
                });
            }
        },

        computed: {

            isStatic(){
                return this.network.type == 'static';
            },

            canSave(){
                if (!this.isStatic) return false;
                for (var key in this.network) {
                    if(typeof this.network[key] == 'string') {
                        if (this.network[key].trim() == '') return true;
                    }
                }
                return false;
            }
        }
    }
</script>
