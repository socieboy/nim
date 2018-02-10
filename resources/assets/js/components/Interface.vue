<template>
    <form class="network-manager">

        <div class="form-group">
            <label for="type">Type</label>
            <select v-model="network.type" name="type" id="type" class="form-control">
                <option value="dhcp">DCHP</option>
                <option value="static">Static</option>
            </select>
        </div>

        <div class="form-group" v-if="isStatic">
            <label for="ip_address">IP Address</label>
            <input type="text" v-model="network.ip_address" name="ip_address" id="ip_address" class="form-control" :required="isStatic">
        </div>

        <div class="form-group" v-if="isStatic">
            <label for="mask">Mask</label>
            <input type="text" v-model="network.mask" name="mask" id="mask" class="form-control" :required="isStatic">
        </div>

        <div class="form-group" v-if="isStatic">
            <label for="gateway">Gateway</label>
            <input type="text" v-model="network.gateway" name="gateway" id="gateway" class="form-control" :required="isStatic">
        </div>

        <div class="form-group" v-if="isStatic">
            <label for="dns">DNS</label>
            <input type="text" v-model="network.dns" name="dns" id="dns" class="form-control" :required="isStatic">
        </div>

        <div class="form-group">
            <button class="btn btn-primary" type="button" :disabled="canSave" @click="save()">
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
                network:{
                    name: '',
                    type: 'dhcp',
                    ip_address: '',
                    mask: '',
                    gateway: '',
                    dns: '',
                }
            }
        },

        created(){
          this.network = this.interface;
        },

        methods:{
            save(){
                axios.post('/api/network-interface/' + this.interface.name).then(response => {
                    console.log(response)
                })
            }
        },

        computed: {

            isStatic(){
                return this.network.type == 'static';
            },

            canSave(){
                if (!this.isStatic) return false;
                for (var key in this.network) {
                    if (this.network[key].trim() == '') return true;
                }
                return false;
            }
        }
    }
</script>
