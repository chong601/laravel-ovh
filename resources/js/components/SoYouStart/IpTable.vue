<template>
    <table class="table">
        <thead>
            <tr>
                <th style="width: 10px"></th>
                <th>IP</th>
                <th style="width: 10%">Type</th>
                <th style="width: 20px">Country</th>
                <th style="text-align: center; width: 30%">Reverse</th>
                <th style="text-align: center; width: 30%">MAC</th>
                <th style="width: 50px"></th>
            </tr>
        </thead>
        <tbody>
        <!-- begin loop -->
        <tr v-if="!ipBlockDetails.length">
            <td colspan="7" style="text-align: center">Loading, hang tight!</td>
        </tr>
        <tr v-else v-for="(value) in ipBlockDetails" v-bind:key="value.ip">
            <td></td>
            <td>{{ value.ip }}</td>
            <td><span class="badge" v-bind:class="{'text-bg-primary': value.type === 'dedicated', 'text-bg-success': value.type === 'failover'}">{{ value.type }}</span></td>
            <td><span v-if="value.country !== null" class="badge text-bg-secondary">{{ value.country.toUpperCase() }}</span></td>
            <td style="text-align: right"><ip-reverse></ip-reverse></td>
            <td style="text-align: right"><virtual-mac v-if="value.type === 'failover'"></virtual-mac></td>
            <td style="text-align: right; width: 30px"><em v-if="value.description !== null" class="bi bi-info-circle-fill" data-bs-toggle="popover" data-bs-trigger="hover focus" v-bind:data-bs-content="value.description"></em> <em class="bi bi-gear-fill"></em></td>
        </tr>
        <!-- end loop -->
    </tbody>
    </table>
</template>

<script>
import axios from "axios"
import VirtualMac from './VirtualMac.vue'
import IpReverse from './IpReverse.vue'

export default {
  components: { VirtualMac, IpReverse },
    data() {
        return {
            ipBlocks: [],
            ipBlockDetails: []
        }
    },
    methods: {
        async queryIps() {
            let ipBlockPromise = []
            axios.get('/api/ip').then(response => {
                // this.ipBlocks = response.data
                response.data.forEach(ip => {
                    ipBlockPromise.push(axios.get('/api/ip/' + encodeURIComponent(ip)))
                });
                Promise.all(ipBlockPromise).then(fulfilledPromises => {
                    console.log(fulfilledPromises)
                    fulfilledPromises.forEach(fulfilledPromise => {
                        this.ipBlockDetails.push(fulfilledPromise.data)})
                })
            });

        },
    },
    computed: {

    },
    created() {
        this.queryIps()
    },
}
</script>

<style>

</style>
