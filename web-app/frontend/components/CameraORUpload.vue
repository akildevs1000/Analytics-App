<template>
  <div class="text-center">
    <v-avatar style="border: 1px solid #6946dd" size="175">
      <img v-show="isUpload" :src="PlaceHolderImage" ref="img" alt="" />

      <video
        v-show="!isUpload"
        width="100%"
        ref="video"
        autoplay
        playsinline
      ></video>
      <canvas ref="canvas" style="display: none"></canvas>
    </v-avatar>

    <div class="pa-5">
      <v-icon large color="primary" @click="toggleCamera">mdi-camera</v-icon>
      <v-icon large color="primary" @click="uploadPicture"
        >mdi-folder-open</v-icon
      >
    </div>

    <input
      type="file"
      ref="fileInput"
      style="display: none"
      @change="handleFileUpload"
    />
  </div>
</template>
<script>
export default {
  auth: false,
  layout: "login",

  props: {
    PreviewImage: {
      type: String,
      default: "/no-profile-image.jpg",
    },
  },

  data: () => ({
    isClicked: false,
    isCamera: false,
    videoStream: null,
    isUpload: true,
    PlaceHolderImage: null,
  }),

  mounted() {
    this.setupCamera();

    this.PlaceHolderImage = this.PreviewImage;
  },
  methods: {
    async setupCamera() {
      try {
        const stream = await navigator.mediaDevices.getUserMedia({
          video: true,
        });
        const video = this.$refs.video;
        video.srcObject = stream;
        this.videoStream = stream;
        video.play();
      } catch (error) {
        console.error("Error accessing the camera: ", error);
      }
    },
    toggleCamera() {
      this.isClicked = true;

      this.isUpload = false;
      this.isCamera = !this.isCamera;
      const video = this.$refs.video;

      const canvas = this.$refs.canvas;
      const context = canvas.getContext("2d");
      canvas.width = video.videoWidth;
      canvas.height = video.videoHeight;
      context.drawImage(video, 0, 0, canvas.width, canvas.height);
      this.$emit("imageSrc", canvas.toDataURL("image/png"));

      if (this.isCamera) {
        video.play();
      } else if (!this.isCamera) {
        this.isUpload = false;
        video.pause();
      }
    },
    uploadPicture() {
      const fileInput = this.$refs.fileInput;
      fileInput.click();
    },
    handleFileUpload(event) {
      const file = event.target.files[0];
      const reader = new FileReader();
      reader.onload = (e) => {
        this.isUpload = true;
        this.PlaceHolderImage = e.target.result;
        this.$emit("imageSrc", e.target.result);
      };
      reader.readAsDataURL(file);
    },
    beforeDestroy() {
      if (this.videoStream) {
        this.videoStream.getTracks().forEach((track) => track.stop());
      }
    },
  },
};
</script>
