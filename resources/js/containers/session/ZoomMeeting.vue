<template>
  <div class="iframe-container">
    <meta charset="utf-8">
    <link type="text/css" rel="stylesheet" href="https://dmogdx0jrul3u.cloudfront.net/1.3.7/css/bootstrap.css">
    <link
      type="text/css"
      rel="stylesheet"
      href="https://dmogdx0jrul3u.cloudfront.net/1.3.7/css/react-select.css"
    >

    <meta name="format-detection" content="telephone=no">
  </div>
</template>

<script>
import { ZoomMtg } from '@zoomus/websdk'

ZoomMtg.setZoomJSLib('https://source.zoom.us/1.7.10/lib', '/av'); 

ZoomMtg.preLoadWasm()

ZoomMtg.prepareJssdk()

var API_KEY = 'nv7daGMaxdDJA3Q7YrMoUyfpFj6D57JZcMqu';

var API_SECRET = 'UKswaQLuyYvrkxzYXgmzTZfoV7J2aqfJrTGA';

export default {
  name: "ZoomFrame",
  data() {
    return {
      src: "",
      meetConfig: {},
      signature: {}
    };
  },
  props: {
    meetingId: String
  },
  created() {
    // Meeting config object
    this.meetConfig = {
      apiKey: API_KEY,
      apiSecret: API_SECRET,
      meetingNumber: this.meetingId,
      userName: this.nickname,
      passWord: "",
      leaveUrl: "https://zoom.us",
      role: 0
    };

    // Generate Signature function
    this.signature = ZoomMtg.generateSignature({
      meetingNumber: this.meetConfig.meetingNumber,
      apiKey: this.meetConfig.apiKey,
      apiSecret: this.meetConfig.apiSecret,
      role: this.meetConfig.role,
      success: function(res) {
        // eslint-disable-next-line
      }
    });

    // join function
    ZoomMtg.init({
      leaveUrl: "http://www.zoom.us",
      isSupportAV: true,
      success: () => {
        ZoomMtg.join({
          meetingNumber: '123456',
          userName: this.meetConfig.userName,
          signature: this.signature,
          apiKey: this.meetConfig.apiKey,
          userEmail: "puchenglie@gmail.com",
          passWord: this.meetConfig.passWord,
          success: function(res) {
            // eslint-disable-next-line
          },
          error: function(res) {
            // eslint-disable-next-line
          }
        });
      },
      error: function(res) {
        // eslint-disable-next-line
      }
    });
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
  .iframe-container {
    width: 100px;
    height: 100px;
  }
</style>
