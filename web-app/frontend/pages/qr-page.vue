<template>
  <v-row justify="center" align="center" class="background-container">
    <v-col cols="12" sm="8" md="6" class="d-flex flex-column">
      <v-img v-if="qrCodeDataURL" :src="qrCodeDataURL" width="150" alt="logo" class="mx-auto my-5" />

      <v-text-field v-model="text" outlined placeholder="Enter text" />

      <v-alert v-if="msg" type="error" class="mb-4">{{ msg }}</v-alert>

      <v-btn @click="generateQRCode" color="primary"> Generate QR Code </v-btn>
    </v-col>
  </v-row>
</template>

<script>
export default {
  auth: false,
  layout: "login",
  data() {
    return {
      msg: "",
      text: "",
      qrCodeDataURL: null,
    };
  },
  methods: {
    async generateQRCode() {
      try {
        this.qrCodeDataURL = await this.$qrcode.generate(this.text);
      } catch (error) {
        console.error("Error generating QR code:", error);
      }
    },
  },
};
</script>
