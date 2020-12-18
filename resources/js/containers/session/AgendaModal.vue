<template>
    <b-modal ref="agenda_modal" id="agenda_modal" size="lg" centered hide-footer class="modal-class">
        <div slot="modal-header" :style="agendaHeader()">
            <i class="simple-icon-close position-absolute close-btn" :style="'background:' + color" @click="$emit('agenda-close'), $refs.agenda_modal.hide()"></i>
            <div class="d-flex justify-content-end align-items-center w-100">
                <h2 class="mr-auto">Agenda</h2>
                <filter-options
                    :statusOptions="statusOptions"
                    :styleObject="styleObject"
                    :agendaFilter="agendaFilter"
                    :filterValue="filterValue"
                    @set-value="setValue"
                />
                <filter-sub-options
                    v-if="filter.value > 0 && filter.value < 3"
                    :styleObject="styleObject"
                    :filterData="filterData[filter.value]"
                    :dataIndex="filter.value"
                    @set-status="setStatus"
                />
                <search-item @set-status="setStatus" v-if="filter.value == 3" />
            </div>
        </div>
        <div class="agenda-body" :style="agendaBody()">
            <template v-if="items.length">
                <div class="p-4 m-2" v-for="item in items" :key="item.id" :style="getStatuBar(item)">
                    <!-- <div class="float-right">
                        <div class="d-flex">
                        <i class="iconsminds-checkout icon-btn mr-2"></i>
                        </div>
                    </div> -->
                    <div>
                        <p>
                            <template v-if="item.event && item.track">
                                {{item.event.date | moment('dddd, MMMM Do YYYY')}} {{item.event.date + ' ' + item.start | moment('h:mm A')}}-{{item.event.date + ' ' + item.end | moment('h:mm A')}}: {{item.track.title}}
                            </template>
                            <template v-else>
                                Event Or Track Data is not correct!
                            </template>
                        <h4>{{item.title}}</h4>
                        <h6>{{item.description}}</h6>
                        <div class="d-flex">
                            <div class="position-relative mx-1" v-for="(user, index) in selectedUsers(item)" :key="index">
                                <img :src="'/assets/img/avatar/' + user.avatar" class="user-avatar" alt="">
                                <span v-if="user.role == 2" :style="adminMark()" v-b-tooltip.hover title="Admin"/>
                            </div>
                        </div>
                    </div>
                    <b-button variant="primary" size="sm" class="float-right" style="margin-top: -24px; border-radius: 6px" :disabled="isDisable(item) == true" @click="$refs.agenda_modal.hide()">
                        <template v-if="isPast(item)">
                            <router-link :to="'/session/' + item.id" class="text-white">
                            Watch On Demand
                            </router-link>
                        </template>
                        <template v-else>
                            <template>
                                <template v-if="isDisable(item)">
                                    Watch Live&nbsp;
                                    <i class="simple-icon-camrecorder"></i>
                                </template>
                                <router-link :to="'/session/' + item.id" class="text-white" v-else>
                                    Watch Live&nbsp;
                                    <i class="simple-icon-camrecorder"></i>
                                </router-link>
                            </template>
                        </template>
                    </b-button>
                </div>
            </template>
            <div class="py-5 text-center" v-else>
                
                <h4>There is no available Data!</h4>
            </div>
        </div>
  </b-modal>
</template>

<script>
import TrackColorItem from '../../components/TrackColorItem'
import SearchItem from '../../components/SearchItem'
import FilterOptions from './FilterOptions'
import FilterSubOptions from './FilterSubOptions'

export default {
    props: [
        'color',
        'setValue',
        'filter',
        'filterData',
        'setStatus',
        'items',
        'agendaType',
        'userData',
        'is-detail',
        'isDetail',
        'adminData',
        'agendaOpenTrue',
        'agenda-opened',
        'agendaFilter',
        'filterValue',
        'agenda-close'
    ],
    components: {
        TrackColorItem,
        SearchItem,
        FilterOptions,
        FilterSubOptions
    },
    watch: {
        agendaOpenTrue(val) {
            this.$refs.agenda_modal.show()
            this.$emit('agenda-opened')
        }
    },
    data() {
        return {
            statusOptions: [
                {value: 0, text: 'All'},
                {value: 1, text: 'Track'},
                {value: 2, text: 'Date'},
                {value: 3, text: 'Name'},
                {value: 4, text: 'Previous Sessions'}
            ],
            filterSubText: '',
            sessionFilterItems: []
        }
    },
    computed: {
        styleObject() {
            return {
                '--color': this.color
            }
        }
    },
    methods: {
        agendaHeader() {
            return {
                'width': '100%',
                'height': '100%',
                'border': '8px solid ' + this.color,
                'border-bottom': 0,
                'border-radius': '16px 16px 0 0',
                'padding': '1.75rem'
            }
        },
        agendaBody() {
            return {
                'width': '100%',
                'height': '100%',
                'border': '8px solid ' + this.color,
                'border-top': 0,
                'border-radius': '0 0 16px 16px'
            }
        },
        agendaView(item) {
            if (this.agendaType == 0) {
                this.sessionFilterItems = []
                if (this.items && this.items.length) this.items.forEach(session => {
                    if (session.date == item.date) {
                        this.sessionFilterItems.push(session)
                    }
                });
                this.$emit('is-detail')
            } else {
                this.sessionFilterItems = this.items
            }
        },
        getStatuBar(item) {
            var display = ''
            var currentTime = Math.round(new Date().getTime() / 1000)
            if (item.event) {
                var endTime = Date.parse(item.event.date + ' ' + item.end) / 1000
                if (endTime < currentTime) {
                    display = 'hidden'
                } else {
                    display = 'block'
                }
            } else {
                display = 'hidden'
            }
            var border = ''
            if (item.track) {
                border = item.track.color
            }
            return {
                'border-left': '4px solid ' + border,
                'display': display
            }
        },
        adminMark() {
            return {
                'position': 'absolute',
                'background': this.color,
                'width': '12px',
                'height': '12px',
                'box-shadow': '0 0 5px 0 ' +  this.color,
                'margin-left': '-10px',
                'border': '2px solid white',
                'border-radius': '50%'
            }
        },
        selectedUsers(item) {
            var selectedUsers = []
            var users = []
            if (item.presenter) {
                users = JSON.parse(item.presenter)
                if (!users) {
                    users = []
                }
            }
            this.userData.forEach(element => {
                users.forEach(current => {
                    if (current == element.id && element.avatar != 'default.jpg') {
                        selectedUsers.push(element)
                    }
                })
            });
            users.forEach(current => {
                if (current == this.adminData.id && this.adminData.avatar != 'default.jpg') {
                    selectedUsers.push(this.adminData)
                }
            })
            return selectedUsers
        },
        isDisable(item) {
            return parseInt(item.stream_active) == 1;
        },
        isPast(item) {
            if (item.event) {
                if (this.isDisable(item)) return false;
                var currentTime = Math.round(new Date().getTime() / 1000)
                var endTime = Date.parse(item.event.date + ' ' + item.end) / 1000
                if (endTime < currentTime) {
                    return true
                } else {
                    return false
                }
            } else {
                return false
            }
        }
    }
}
</script>

<style scoped>
    .tooptip {
        top: 0!important
    }
</style>