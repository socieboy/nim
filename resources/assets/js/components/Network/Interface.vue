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

        <button class="btn btn-primary pull-right" type="button" :disabled="!canSave || submitted" @click="save()">
            <i class="fa fa-spin fa-spinner" v-if="submitted"></i> <span v-text="submitted ? 'Saving...' : 'Save'"></span>
        </button>

        <ping :from="network"></ping>

    </form>
</template>

<script>
    export default {

        props: ['interface'],

        data(){
            return {
                submitted: false,
                network: this.interface,
            }
        },

        methods:{
            save(){
                this.submitted = true;
                axios.post('/api/network-interface/' + this.interface.name, this.network).then(response => {
                    Alert.success('Your network has been updated!');
                    this.submitted = false;
                }).catch(({response}) => {
                    this.submitted = false;
                    Alert.error(response.data.message);
                });
            }
        },

        computed: {

            isStatic(){
                return this.network.type == 'static';
            },

            isDhcp(){
                return !this.isStatic;
            },

            canSave(){
                if (this.isDhcp) return true;
                for (var key in this.network) {
                    if (typeof this.network[key] == 'string') {
                        if (this.network[key].trim() == '') return false;
                    }
                }
                return true;
            }
        }
    }
</script>
