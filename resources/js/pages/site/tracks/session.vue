<template>
    <div>
        <b-row>
            <b-colxx xxs="12">
                <breadcrumb heading="Agenda Sessions" />
            </b-colxx>
        </b-row>
        <b-card v-if="!addNew">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="my-auto mr-auto">Sessions</h2>
                <b-card class="mb-4 text-center mr-1 ml-auto">
                    <h6 class="card-text font-weight-semibold mb-0">Total Sessions</h6>
                    <h2>{{items.length}}</h2>
                </b-card>
                <b-card class="mb-4 text-center mx-1">
                    <h6 class="card-text font-weight-semibold mb-0">Total Tracks</h6>
                    <h2>{{trackData.length}}</h2>
                </b-card>
                <b-card class="mb-4 text-center ml-1 mr-auto">
                    <h6 class="card-text font-weight-semibold mb-0">Timezone</h6>
                    <h2></h2>
                </b-card>
                <div class="ml-auto my-auto">
                    <b-button
                        variant="primary"
                        style="border-radius: 6px"
                        @click="addNewItem()"
                    >Add Session</b-button>
                </div>
            </div>
            <div class="table-responsive">
                <b-table
                    hover
                    striped
                    :fields="fields"
                    :items="items"
                    v-if="items.length"
                    class="my-5"
                >
                    <template v-slot:cell(#)="data">
                        <div class="mt-2">{{data.index + 1}}</div>
                    </template>
                    <template v-slot:cell(day)="data">
                        <div class="mt-2" v-if="data.item.event">
                            {{data.item.event.date}}
                        </div>
                        <div class="mt-2" v-else>Not set</div>
                    </template>    
                    <template v-slot:cell(sessionTitle)="data">
                        <div class="mt-2">{{data.item.title}}</div>
                    </template>
                    <template v-slot:cell(sessionDescription)="data">
                        <div class="mt-2">{{data.item.description}}</div>
                    </template>
                    <template v-slot:cell(sessionTime)="data">
                        <div class="mt-2" v-if="data.item.event">
                            {{data.item.event.date + ' ' + data.item.start | moment('h:mm A')}} ~ {{data.item.event.date + ' ' + data.item.end | moment('h:mm A')}} 
                        </div>
                        <div class="mt-2" v-else>Not set</div>
                    </template>
                    <template v-slot:cell(track)="data">
                        <div class="mt-2>
                        <span class="track-name">{{ data.item.track.title }}???</span>
                        <div class="mt-2 w-100" v-if="!data.item.track">Not set</div>
   
                         <div class="mt-2 w-100" :style="'background:' + ((data.item.track.color) ? data.item.track.color : '#fff') + ';height:1rem'" v-else/>
                        </div>
                    </template>
                    <template v-slot:cell(status)="data">
                        <div class="mt-2">{{getStatus(data.item)}}</div>
                    </template>
                    <template v-slot:cell(pageType)="data">
                        <div class="mt-2" style="text-transform: capitalize">{{data.item.pageType}}</div>
                    </template>
                    <template v-slot:cell(management)="data">
                        <b-dropdown size="sm" variant="link" toggle-class="text-decoration-none" no-caret>
                            <template v-slot:button-content>
                                <i class="simple-icon-options manage-btn"></i>
                            </template>
                            <b-dropdown-item @click="edit(data.item)">Edit</b-dropdown-item>
                            <b-dropdown-item @click="clone(data.item)">Clone</b-dropdown-item>
                            <b-dropdown-item @click="active(data.item)">MAKE ACTIVE</b-dropdown-item>
                            <b-dropdown-item @click="remove(data.item)">Delete</b-dropdown-item>
                        </b-dropdown>
                    </template>
                </b-table>
            </div>
            <paginate-nav
                :lastPage='lastPage'
                :page='page'
                :changePage='changePage'
            />
        </b-card>
        <b-card v-else>
            <div class="container mb-2 mx-auto">
                <b-row>
                    <b-colxx md="6" sm="12">
                        <b-form-group label="Session Title*" class="my-2">
                            <b-form-input v-model="item.title" />
                        </b-form-group>
                        <b-form-group label="Session Category*" class="my-2">
                            <b-form-select v-model="item.category_id">
                                <option :value="data.id" v-for="data in categoryData" :key="data.id">
                                    {{data.name}} 
                                    {{data.stream_active ? " -- ACTIVE" : ""}}
                                </option>
                            </b-form-select>
                        </b-form-group>
                        <b-row>
                            <b-colxx sm="6">
                                <b-form-group label="Session Day*" class="my-2">
                                    <b-form-select v-model="item.event_id">
                                        <option :value="data.id" v-for="data in eventData" :key="data.id">
                                            {{data.date | moment('dddd, MMMM Do YYYY')}}
                                        </option>
                                    </b-form-select>
                                </b-form-group>
                            </b-colxx>
                            <b-colxx sm="6">
                                <b-form-group label="Session Track*" class="my-2">
                                    <b-form-select v-model="item.track_id">
                                        <option :value="data.id" v-for="data in trackData" :key="data.id">
                                            {{data.title}}
                                        </option>
                                    </b-form-select>
                                </b-form-group>
                            </b-colxx>
                            <b-colxx sm="6">
                                <b-form-group label="Start Time*" class="my-2">
                                    <vue-timepicker 
                                        format="hh:mm:A"
                                        placeholder="Please Insert Start Time"
                                        v-model="item.start"
                                        class="custom-time"
                                    />
                                </b-form-group>
                            </b-colxx>
                            <b-colxx sm="6">
                                <b-form-group label="End Time*" class="my-2">
                                    <vue-timepicker 
                                        format="hh:mm:A"
                                        placeholder="Please Insert End Time"
                                        v-model="item.end"
                                        class="custom-time"
                                    />
                                </b-form-group>
                            </b-colxx>
                        </b-row>
                        <b-form-group label="Active Button Time(min)" class="my-2">
                            <b-form-input type="number" v-model="item.button" min="0"/>
                        </b-form-group>
                        <b-form-group label="Vimeo Video ID" class="my-2">
                            <b-form-input type="text" v-model="item.vimeo_video_id"/>
                        </b-form-group>
                    </b-colxx>
                    <b-colxx md="6" sm="12">
                        <b-form-group label="Session Description" class="my-2">
                            <b-form-textarea v-model="item.description" />
                        </b-form-group>
                        <b-form-group label="Session Hashtags" class="my-2">
                            <b-form-textarea v-model="item.hashtag" />
                        </b-form-group>
                        <b-form-group label="Tag Presenters" class="my-2 mb-3">
                            <multiselect
                                v-model="item.presenter"
                                :options="options"
                                :multiple="true"
                            />
                        </b-form-group>
                        <div class="mt-2">Upload Assets</div>
                        <b-form-group class="mb-0">
                            <b-form-radio-group v-model="item.option">
                                <b-form-radio :value="0">Upload</b-form-radio>
                                <b-form-radio :value="1">Link</b-form-radio>
                            </b-form-radio-group>
                            <b-input-group :prepend="$t('input-groups.upload')" class="my-2" v-if="item.option == 0">
                                <b-form-file ref="file" :placeholder="$t('input-groups.choose-file')" @change="onFileUpload" />
                            </b-input-group>
                            <b-form-input placeholder="Insert Url" v-model="item.link" class="my-2" v-else />
                        </b-form-group>
                    </b-colxx>
                    <b-colxx md="6" sm="12">
                        <b-form-group label="Page Format*" class="my-2">
                            <b-form-select v-model="pageType">
                                <option v-for="(data, index) in formatData" :key="index" :value="data">{{data}}</option>
                            </b-form-select>
                        </b-form-group>
                    </b-colxx>
                    <b-colxx md="6" sm="12">
                        <b-form-group label="Link to Existing Page" class="my-2">
                            <b-form-input v-model="item.url" v-if="pageType != 'Exhibit' && pageType != 'Page'"/>
                            <b-form-select v-model="item.url" v-else-if="pageType == 'Exhibit'">
                                <option :value="data" v-for="(data, index) in pageEnableData" :key="index">
                                    {{data}}
                                </option>
                            </b-form-select>
                            <b-form-select v-model="item.url" v-else>
                                <option :value="data.url" v-for="data in pageData" :key="data.id">
                                    {{data.url}}
                                </option>
                            </b-form-select>
                        </b-form-group>
                    </b-colxx>
                </b-row>
                <p class="text-muted">
                    Video Page, Live Session, Expo page, Exhibit, Networking
                </p>
            </div>
            <b-button variant="primary" style="border-radius: 6px" class="float-right" @click="addSession">Add Session</b-button>
            <b-button variant="outline-primary" style="border-radius: 6px" class="mr-auto" @click="addNew = false">Cancel</b-button>
        </b-card>
        <b-modal id="confirm_modal" ref="confirm_modal" title="Are you sure?" centered>
            If you click confirm, you can't recover this data anymore!
            <template slot="modal-footer">
                <b-button variant="primary" @click="confirm()" class="mr-1">Confirm</b-button>
                <b-button variant="secondary" @click="hideModal('confirm_modal')">Cancel</b-button>
            </template>
        </b-modal>
    </div>
</template>

<script>
import VueTimepicker from 'vue2-timepicker'
import 'vue2-timepicker/dist/VueTimepicker.css'
import Axios from 'axios'
import { mapGetters } from 'vuex'
import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.min.css'
import PaginateNav from '../../../components/Common/PaginateNav'

export default {
    metaInfo () {
        return { title: `Agenda Sessions` }
    },
    components: {
        VueTimepicker, Multiselect, PaginateNav
    },
    data() {
        return {
            apiBase: '/agenda/session/',
            fields: [
                '#',
                { key: 'day',                   sortable: true },
                { key: 'sessionTitle',          sortable: true },
                { key: 'sessionDescription',    sortable: true },
                { key: 'sessionTime',           sortable: true },
                { key: 'Track',                 sortable: true },
                { key: 'status',                sortable: true },
                { key: 'pageType',              sortable: true },
                { key: 'management',            sortable: false}
            ],
            items: [],
            item: {
                id: 0,
                title: '',
                category_id: '',
                event_id: '',
                track_id: '',
                url: '/session',
                pageType: 'Video',
                start: '',
                end: '',
                button: 0,
                vimeo_video_id: '',
                option: 0,
                link: ''
            },
            categoryData: [],
            trackData: [],
            pageData: [],
            pageEnableData: [],
            presenterUserData: [],
            options: [],
            eventData: [],
            notifyText: 'Successfully Created!',
            selectedItem: {},
            isConfirmed: false,
            addNew: false,
            formatData: [ 'Video', 'Exhibit', 'Lounge', 'Networking', 'Page' ],
            pageType: 'Video',
            page: 0,
            lastPage: 0,
            formData: new FormData(),
            tz: null
        }
    },
    mounted() {
        this.init()
    },
    methods: {
        init() {

            Axios.post(this.apiBase + 'get', {id: this.siteId}).then(response => {
                let data = response.data;
                console.log(data);
                this.items = data.items
                this.page = data.items.current_page
                this.lastPage = data.items.last_page
                this.categoryData = data.categoryData
                console.log("cat", data.categoryData);
                this.trackData = data.trackData
                this.pageData = data.pageData
                this.eventData = data.eventData
                console.log("evt", data.eventData);
                this.presenterUserData = data.domain.user ? data.domain.user : []
                console.log("evt", this.presenterUserData);
                if (this.presenterUserData && this.presenterUserData.length) {
                    this.presenterUserData.forEach(user => {
                        this.options.push(user.id)
                    });
                    this.options.push(data.domain.admin.id)
                }
            }).catch(error => {
                    console.log(error);
                    app.error = true
                    console.log(error)
            })
        },
        addSession() {
            if (
                this.item.title == '' ||
                this.item.category_id == '' ||
                this.item.event_id == '' ||
                this.item.track_id == '' ||
                this.item.start == '' ||
                this.item.end == ''
            ) {
                this.$notify('primary filled', '', 'Please insert Required(*) fields!', { duration: 3000, permanent: false })
            } else {
                this.item.siteId = this.siteId
                this.formData.append('item', JSON.stringify(this.item))
                Axios.post(this.apiBase + 'set', this.formData).then(res => {
                    console.log(res)
                    if (res.status == 200) {
                        this.items = res.data.items
                        this.page = res.data.current_page
                        this.lastPage = res.data.last_page
                        this.addNew = false
                        this.$notify('primary filled', '', this.notifyText, { duration: 3000, permanent: false })
                    } else {
                        this.$notify('primary filled', 'Server Error!', 'Please try a few hours later!', { duration: 3000, permanent: false })
                    }
                    this.notifyText = 'Successfully Created!'
                    this.item = {
                        id: 0,
                        title: '',
                        category_id: '',
                        event_id: '',
                        track_id: '',
                        url: '/session',
                        pageType: 'Video',
                        start: '',
                        end: '',
                        button: 0,
                        vimeo_video_id: '',
                        option: 0,
                        link: ''
                    }
                })
            }
        },
        edit(editItem) {
            this.notifyText = 'Successfully Changed!'
            var s = editItem.start.split(':')
            if (s[0] == 0) {s[0] = 12}
            if (s[0] > 12) {
                s[2] = 'PM'
                s[0] = s[0] - 12
            } else {
                s[2] = 'AM'
            }
            var e = editItem.end.split(':')
            if (e[0] == 0) {e[0] = 12}
            if (e[0] > 12) {
                e[2] = 'PM'
                e[0] = e[0] - 12
            } else {
                e[2] = 'AM'
            }
            var presenter = []
            if (editItem.presenter) {
                presenter = JSON.parse(editItem.presenter)
            }
            var type = 0
            if (editItem.upload_type) {
                type = editItem.upload_type
            }
            this.item = {
                id: editItem.id,
                title: editItem.title,
                track_id: editItem.track_id,
                category_id: editItem.category_id,
                event_id: editItem.event_id,
                start: s[0] + ':' + s[1] + ':' + s[2],
                end: e[0] + ':' + e[1] + ':' + e[2],
                presenter: presenter,
                hashtag: editItem.hashtag,
                pageType: editItem.pageType,
                url: editItem.url,
                button: editItem.button,
                vimeo_video_id: editItem.vimeo_video_id,
                description: editItem.description,
                option: type,
                link: editItem.asset
            }
            this.pageType = editItem.pageType
            this.addNew = true
        },
        clone(item) {
            Axios.post(this.apiBase + 'clone', {id: item.id, siteId: this.siteId}).then(res => {
                this.items = res.data.data
                this.page = res.data.current_page
                this.lastPage = res.data.last_page
            })
        },
        active(item) {
            Axios.post(this.apiBase + 'active', {id: item.id, siteId: this.siteId}).then(res => {
                this.items = res.data.data
                this.page = res.data.current_page
                this.lastPage = res.data.last_page
            })
        },
        confirm() {
            this.notifyText = 'Successfully Deleted!'
            this.isConfirmed = true
            this.remove(this.selectedItem)
        },
        remove(item) {
            this.selectedItem = item
            if (!this.isConfirmed) {
                this.$refs['confirm_modal'].show()
            } else {
                Axios.post(this.apiBase + 'del', {id: this.selectedItem.id, siteId: this.siteId}).then(res => {
                    if (res.status == 200) {
                        this.items = res.data.data
                        this.page = res.data.current_page
                        this.lastPage = res.data.last_page
                        this.$notify('primary filled', '', this.notifyText, { duration: 3000, permanent: false })
                    } else {
                        this.$notify('primary filled', 'Server Error!', 'Successfully Deleted!', { duration: 3000, permanent: false })
                    }
                })
                this.isConfirmed = false
                this.$refs['confirm_modal'].hide()
            }
        },
        onFileUpload(e) {
            const file = e.target.files[0]
            if (file.size / 10000 > 5) {
                this.$notify('primary filled', 'File is not optimized for the sytem', 'Please reduce reupload with a size of less than 500kb.', { duration: 3000, permanent: false })
            }
            this.formData.append('file', file)
        },
        async changePage(pageNum) {
            const { data } = await Axios.get(
                this.apiBase + 'get-sessions?page=' + pageNum, 
                {params: {
                    id: this.siteId
                }}
            )
            this.items = []
            for (let i = 8 * (pageNum - 1) ; i < 8 * pageNum; i++) {
                this.items.push(data.data[i])
            }
            this.page = data.current_page
            this.lastPage = data.last_page
        },
        hideModal(refName) {
            this.$refs[refName].hide()
            this.selectedItem = {id: 0}
        },
        getStatus(item) {
            var currentTime = new Date().getTime()
            console.log('status', item.event, item);
            if (item.event) {
                var startTime = Date.parse(item.event.date + ' ' + item.start)
                var endTime = Date.parse(item.event.date + ' ' + item.end)
                if (currentTime < startTime) {
                    return 'On Deck'
                } else if (currentTime > endTime) {
                    return 'Complete'
                } else {
                    return 'Active'
                }
            } else {
                return 'Not set'
            }
        },
        addNewItem() {
            this.addNew = true
            this.item = {
                id: 0,
                title: '',
                category_id: '',
                event_id: '',
                track_id: '',
                url: '/session',
                pageType: 'Video',
                start: '',
                end: '',
                button: 0,
                option: 0,
                link: ''
            }
        }
    },
    watch: {
        pageType(val) {
            this.item.pageType = val
            switch (val) {
                case 'Video': 
                    this.item.url = '/session'
                    break
                case 'Exhibit': 
                    this.pageEnableData  = [ '/exhibit/booth',  '/exhibit/sponsor', '/exhibit/poster' ]
                    break
                case 'Lounge': 
                    this.item.url = ''
                    break
                case 'Networking': 
                    this.item.url = '/network'
                    break
            }
        }
    },
    computed: {
        ...mapGetters({
            siteId: 'site/getSiteId'
        })
    },
}
</script>

<style lang="css">
    .vue__time-picker, .vue__time-picker input.display-time {
        width: auto!important;
    }
    .custom-time, .custom-time input.display-time {
        width: 100%!important
    }
    nav {
        width: 100%
    }
</style>>
