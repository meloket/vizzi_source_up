import Axios from 'axios'
import { mapGetters } from 'vuex'
import UserPageHeading from '../containers/Users/UserPageHeading'
import UserPageListing from '../containers/Users/UserPageListing'
import UserActionModal from '../containers/Users/UserActionModal'

export default {
    components: {
        UserPageHeading, UserPageListing, UserActionModal
    },
    data() {
        return {
            newItem: {
                id: 0,
                name: "",
                email: "",
                code: "",
                role: null,
                type: null,
                bio: ''
            },
            filter: {
                status: 2,
                input: ''
            },
            userData: [],
            adminUser: {},
            users: [],
            totalUsers: [],
            selectedUsers: [],
            codeList: [],
            role: 2,
            apiBase: '/users/',
            displayMode: 'list',
            statusOptions: [
                {text: 'All', value: 2},
                {text: 'Active', value: 1},
                {text: 'Inactive', value: 0}
            ],
            isOpen: false,
            isConfirmed: false,
            modalTitle: 'Add New User',
            submitText: 'Create',
            page: 1,
            perPage: 10,
            from: 0,
            to: 0,
            total: 0,
            lastPage: 0,
            type: 0,
            reg: /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,24}))$/,
        }
    },
    mounted() {
        this.init()
    },
    computed: {
        ...mapGetters({
            currentUser: 'auth/user',
            siteId: 'site/getSiteId'
        }),
        isSelectedAll() {
            return this.selectedUsers.length >= this.users.length;
        },
        isAnyItemSelected() {
            return (
              this.selectedUsers.length > 0 &&
              this.selectedUsers.length < this.users.length
            );
        }
    },
    watch: {
        filter: {
            deep: true,
            handler() {
                this.init()
            }
        }
    },
    methods: {
        async init() {
            const { data } = await Axios.get(
                this.apiBase + 'get', 
                {params: {
                    siteId: this.siteId, 
                    role: this.role, 
                    perPage: this.perPage, 
                    type: this.type, 
                    status: this.filter.status, 
                    input: this.filter.input
                }}
            )
            if (data.userData) {
                this.users = data.userData.data
                this.from = data.userData.from
                this.to = data.userData.to
                this.total = data.userData.total
                this.lastPage = data.userData.last_page
                this.page = data.userData.current_page
            } else {
                this.users = []
                this.from = 0
                this.to = 0
                this.total = 0
                this.lastPage = 0
                this.page = 0
            }
            this.totalUsers = data.allUserData
            this.adminUser = data.adminData
        },
        async addNewItem(item, role) {
            var flag = false
            var text = 'Please Insert all Fields'
            if (!role && this.role == 0) {
                text = 'Please Select User Role'
            }
            if (item.name == '' || item.email == '') {
                flag = true
            }
            if (item.id != 0 && item.code == '') {
                flag = true
            }
            var count = 0
            if (item.email == this.adminUser.email && item.email != this.currentUser.email) {
                count++
                text = 'Email has been taken!'
            }
            this.totalUsers.forEach(user => {
                if (item.email == user.email) {
                    count++
                    text = 'Email has been taken!'
                }
            })
            if (item.id == 0) {
                if (count > 0) {
                    flag = true
                }
            } else {
                if (count > 1) {
                    flag = true
                }
            }
            if (!this.reg.test(item.email)) {
                flag = true
                text = 'Please insert right Email!'
            }
            if (flag) {
                this.$notify('primary filled', '', text, { duration: 3000, permanent: false })
            } else {
                item.role = this.role
                if (this.role != 0) {
                    item.type = this.type
                } else {
                    item.userRole = role.value
                    item.type = role.type
                }
                item.siteId = this.siteId
                item.perPage = this.perPage
                item.status = this.filter.status
                item.input = this.filter.input
                const { data } = await Axios.post(this.apiBase + 'set', item)
                if (data.userData) {
                    this.users = data.userData.data
                    this.from = data.userData.from
                    this.to = data.userData.to
                    this.total = data.userData.total
                    this.lastPage = data.userData.last_page
                    this.page = data.userData.current_page
                    this.totalUsers = data.allUserData
                    var notifyText = 'Successfully Created!'
                    if (item.id != 0) {
                        notifyText = 'Successfully Changed!'
                    }
                    this.$notify('primary filled', '', notifyText, { duration: 3000, permanent: false })
                    this.newItem = {
                        id: 0,
                        name: "",
                        email: "",
                        code: "",
                        role: null,
                        type: null,
                        bio: ''
                    }
                } else {
                    this.$notify('primary filled', 'Server Error!', 'Please try a few hours later!', { duration: 3000, permanent: false })
                }
                this.isOpen = true
                this.modalTitle = 'Add New User'
                this.submitText = 'Create'
            }
        },
        initItem() {
            this.newItem = {
                id: 0,
                name: "",
                email: "",
                code: "",
                role: null,
                type: null,
                bio: ''
            }
            this.modalTitle = 'Add New User'
            this.submitText = 'Create'
        },
        modalClosed() {
            this.isOpen = false
        },
        confirm() {
            this.isConfirmed = true
            this.setStatus(2)
        },
        async setStatus(status) {
            if (this.selectedUsers.length) {
                if (status == 2 && !this.isConfirmed) {
                    this.$refs.confirm_modal.show()
                } else {
                    const { data } = await Axios.post(this.apiBase + 'set-status', {
                        list: this.selectedUsers, 
                        status: status, 
                        role: this.role, 
                        siteId: this.siteId, 
                        perPage: this.perPage, 
                        type: this.type,
                        input: this.filter.input,
                        search: this.filter.status
                    })
                    if (data.userData && data.userData.data) {
                        this.users = data.userData.data
                        this.from = data.userData.from
                        this.to = data.userData.to
                        this.total = data.userData.total
                        this.lastPage = data.userData.last_page
                        this.page = data.userData.current_page
                        var notifyText = 'Successfully Changed!'
                        if (status == 2) {
                            notifyText = 'Successfully Deleted!'
                        }
                        this.$notify('primary filled', '', notifyText, { duration: 3000, permanent: false })
                    } else {
                        this.$notify('primary filled', 'Server Error!', 'Please Try a few hours later!', { duration: 3000, permanent: false })
                    }
                    this.$refs.confirm_modal.hide()
                    this.isConfirmed = false
                    this.selectedUsers = []
                }
            }
        },
        edit(user) {
            this.submitText = 'Change'
            this.modalTitle = 'Edit This User'
            this.newItem = {
                id: user.id,
                name: user.name,
                email: user.email,
                code: user.verify_code,
                role: user.role,
                type: user.type,
                bio: user.bio
            }
        },
        resend() {
            var flag = true
            this.codeList = []
            this.users.forEach(user => {
                this.selectedUsers.forEach(id => {
                    if (id == user.id) {
                        if (user.verify_code == '') {
                            flag = false
                        } else {
                            this.codeList.push(user.verify_code)
                        }
                    }
                })
            })
            if (flag == false) {
                this.$notify('primary filled', '', 'Please Insert Values!', { duration: 3000, permanent: false })
            } else {
                Axios.put(this.apiBase + 'resend', {codeList: this.codeList, list: this.selectedUsers}).then(res => {
                    if (res.status == 200) {
                        this.$notify('primary filled', '', 'Successfully Done!', { duration: 3000, permanent: false })
                    } else {
                        this.$notify('primary filled', 'Server Error!', 'Please try a few hours later!', { duration: 3000, permanent: false })
                    }
                })
            }
        },
        changeDisplayMode(displayType) {
            this.displayMode = displayType;
        },
        resetUsers(users, totalUsers) {
            this.users = users
            this.totalUsers = totalUsers
        },
        selectAll(isToggle) {
            if (this.selectedUsers.length >= this.users.length) {
              if (isToggle) this.selectedUsers = [];
            } else {
              this.selectedUsers = this.users.map(x => x.id);
            }
        },
        keymap(event) {
            switch (event.srcKey) {
                case "select":
                    this.selectAll(false);
                    break;
                case "undo":
                    this.selectedUsers = [];
                    break;
            }
        },
        toggleItem(event, itemId) {
            if (event.shiftKey && this.selectedUsers.length > 0) {
                let itemsForToggle = this.users;
                var start = this.getIndex(itemId, itemsForToggle, "id");
                var end = this.getIndex(
                    this.selectedUsers[this.selectedUsers.length - 1],
                    itemsForToggle,
                    "id"
                );
                itemsForToggle = itemsForToggle.slice(
                    Math.min(start, end),
                    Math.max(start, end) + 1
                );
                this.selectedUsers.push(
                    ...itemsForToggle.map(item => {
                        return item.id;
                    })
                )
            } else {
                if (this.selectedUsers.includes(itemId)) {
                    this.selectedUsers = this.selectedUsers.filter(x => x !== itemId);
                } else this.selectedUsers.push(itemId);
            }
        },
        changePageSize(perPage) {
            this.page = 1;
            this.perPage = perPage;
            this.init()
        },
        async changePage(pageNum) {
            this.page = pageNum;
            const { data } = await Axios.get(
                this.apiBase + 'get?page=' + pageNum, 
                {params: {
                    siteId: this.siteId,
                    role: this.role,
                    perPage: this.perPage,
                    type: this.type,
                    status: this.filter.status,
                    input: this.filter.input
                }}
            )
            this.users = data.userData.data
            this.from = data.userData.from
            this.to = data.userData.to
            this.lastPage = data.userData.last_page
            this.page = data.userData.current_page
        },
        getIndex(value, arr, prop) {
            for (var i = 0; i < arr.length; i++) {
                if (arr[i][prop] === value) {
                    return i;
                }
            }
            return -1;
        }
    }
}