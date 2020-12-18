<template>
<div>
	<video
        id="session-video"
        class="h-100 w-100 video-js vjs-default-skin" 
        controls
        v-if="videoReady && type == 'live'"
    ></video>
    <iframe
        v-else-if="videoReady && type == 'vimeo'"
        :src="url"
        width="auto"
        height="auto"
        frameborder="0"
        allow="autoplay; fullscreen"
        allowfullscreen
        class="bg-dark"
        style="height: 64vh; width: 74vw"
    ></iframe>
    <div class="h-100 w-100" controls v-else>
    	Please wait...
    </div>
    <div class="float-right avatar-part d-flex">
        <div v-for="user in presenterData" :key="user.id" class="position-relative mx-2">
            <img :src="'/assets/img/avatar/' + user.avatar" class="user-avatar cursor-pointer" v-b-tooltip.hover :title="user.bio" />
            <span v-if="user.role == 2" :style="adminMark()" />
        </div>
        <div :style="'background:' + color" class="live-btn">Live Q & A</div>
    </div>
    <div class="p-4" v-if="session">
        <div class="d-flex my-3">
            <h2 class="text-muted" v-if="session.title">{{session.title}}</h2>
            <h2 class="text-muted" v-else>Sessin Title</h2>
        </div>
        <div class="d-flex my-3">
            <h4 class="text-muted" v-if="session.description">{{session.description}}</h4>
            <h4 class="text-muted" v-else>Session Description</h4>
        </div>
    </div>
</div>

</template>

<script>
// import ZoomMeeting from './ZoomMeeting'

import Axios from 'axios'

export default {
  	props: ['id', 'color'],
    // components: {
    //     'v-meeting': ZoomMeeting
    // }
    data() {
    	return {
            videoReady: false,
            streamRunning: false,
            sources: [],
            type: 'vimeo',
            url: false,
            session: {},
            presenterData: []
    	}
    },
    mounted() {
        this.init()
    },
    methods: {
        init() {
            this.videoReady = true;
            this.checkVideo();
            window.setInterval(app.checkVideo, 3000)
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
        checkVideo() {
            let app = this;
            let streamActive = false;
            let type = 'vimeo';
            let sources = [];
            Axios.post('/site/session', {id: this.id}).then(res => {
                app.session = res.data.session
                app.presenterData = res.data.presenterData
                if (app.session.vimeo_video_id && app.session.vimeo_video_id.length) {
                    app.url = 'https://player.vimeo.com/video/' + app.session.vimeo_video_id;
                } else {
                    app.session.videos.forEach(video => {
                        if (parseInt(video.stream_active) == 1) {
                            streamActive = true;
                            sources[sources.length] = {
                                src: video.stream_url,
                                type: video.format
                            };
                        }
                    });
                }

                if (type == 'vimeo' && app.url) {
                    if (!app.streamRunning && streamActive) {
                        app.streamRunning = true;
                    }
                } else if (sources.length) {
                    if (!app.streamRunning && streamActive) {
                            app.player = videojs('session-video');
                                
                            app.player.ready(function() {
                            app.player.src(sources);

                            app.player.autoplay('any');
                            app.streamRunning = true;
                        });
                    }
                }
            })	
        }
    }
}
</script>

<style>
    .avatar-part {
        background: rgba(0, 0, 0, .4);
        padding: 0.6rem 1.2rem;
        border-radius: 8px;
        margin-right: 8px;
    }
    .live-btn {
        color: white;
        padding: 8px;
        font-weight: 500;
        border-radius: 8px;
        cursor: pointer;
    }
    .live-btn:hover {
        box-shadow: 0 0 1px;
    }
    .tooltip {
        top: 0!important
    }
</style>