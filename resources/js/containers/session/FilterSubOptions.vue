<template>
    <div class="d-inline-block float-md-left mr-1 align-top">
        <b-dropdown
            id="ddwn2"
            :text="filterSubText"
            class="btn-group"
            variant="outline-dark"
            size="xs"
            :style="styleObject"
        >
            <template v-if="this.dataIndex == 1">
                <b-dropdown-item 
                    v-for="data in filterData"
                    :key="data.id"
                    @click="filterSubText = data.title, $emit('set-status', data.id)"
                >
                <div class="d-flex">
                    <track-color-item :color="data.color" />
                    {{data.title}}
                </div>
                </b-dropdown-item>
            </template>
            <template v-if="this.dataIndex == 2">
                <b-dropdown-item 
                    v-for="data in filterData"
                    :key="data.id"
                    @click="filterSubText = data.date, $emit('set-status', data.id)"
                >
                    {{data.date | moment('MMMM Do YYYY')}}
                </b-dropdown-item>
            </template>
        </b-dropdown>
    </div>
</template>

<script>
import TrackColorItem from '../../components/TrackColorItem'
export default {
    props: [
        'filterData', 'styleObject', 'dataIndex'
    ],
    components: {
        TrackColorItem
    },
    data() {
        return {
            filterSubText: 'Select'
        }
    },
    watch: {
        dataIndex(val) {
            this.filterSubText = 'Select'
        }
    }
}
</script>