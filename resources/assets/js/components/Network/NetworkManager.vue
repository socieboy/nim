<template>
    <div class="network-manager">
        <ul class="nav nav-tabs" id="Tabs" role="tablist">
            <li class="nav-item" v-for="(interface, key, index) in interfaces">
                <a class="nav-link"
                   :class="{'active' : index == 0}"
                   :id="interface.device + '-tab'"
                   :href="'#' + interface.device"
                   :aria-controls="interface.device"
                   data-toggle="tab"
                   role="tab"
                   aria-selected="true">
                   {{ interface.mode + ' ' + interface.device }}
                </a>
            </li>
        </ul>
        <div class="tab-content" id="TabContent">
            <div class="tab-pane fade"
                 v-for="(interface, key, index) in interfaces"
                 :class="{' show active' : index == 0}"
                 :id="interface.device"
                 :aria-labelledby="interface.device + '-tab'"
                 role="tabpanel">
                <network-interface :interface="interface"></network-interface>
            </div>
        </div>
    </div>
</template>

<script>
    export default {

        data(){
            return {
                interfaces: []
            }
        },

        created(){
            axios.get('/api/network-interfaces').then(response => {
                this.interfaces = response.data;
            });
        },

    }
</script>
