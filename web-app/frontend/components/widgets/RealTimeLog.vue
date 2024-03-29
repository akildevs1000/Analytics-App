<template>
  <v-card class="mb-5 rounded-md" elevation="1">
    <v-toolbar class="rounded-md" color="background" dense flat dark>
      <v-toolbar-title><span> RealTime Log List</span></v-toolbar-title>
      <v-tooltip top color="primary">
        <template v-slot:activator="{ on, attrs }">
          <v-btn
            dense
            class="ma-0 px-0"
            x-small
            :ripple="false"
            text
            v-bind="attrs"
            v-on="on"
          >
            <v-icon color="white" class="ml-2" @click="getDataFromApi()" dark
              >mdi mdi-reload</v-icon
            >
          </v-btn>
        </template>
        <span>Reload</span>
      </v-tooltip>
      <v-spacer></v-spacer>
      <v-tooltip top color="primary">
        <template v-slot:activator="{ on, attrs }">
          <v-btn
            to="/devicelogs"
            dense
            class="ma-0 px-0"
            x-small
            :ripple="false"
            text
            v-bind="attrs"
            v-on="on"
          >
            <v-icon color="white" class="ml-2" dark>mdi mdi-eye-outline</v-icon>
          </v-btn>
        </template>
        <span>Raw Log List</span>
      </v-tooltip>
    </v-toolbar>
    <div class="center-both" style="min-height: 300px">
      <ComonPreloader icon="face-scan" v-if="loading" />
      <div v-else-if="!logs.length">No record found</div>
      <v-slide-group v-else center-active multiple show-arrows>
        <v-slide-item class="ma-5" v-for="(item, i) in logs" :key="i">
          <div
            style="
              display: flex;
              flex-direction: column;
              align-items: center;
              justify-content: center;
            "
          >
            <v-img
              :src="item.image"
              style="
                max-width: 125px;
                max-height: 125px;
                object-fit: contain;
                border-radius: 50%;
              "
            />
            <div style="margin-top: 10px">
              {{ item.name }} -
              {{ item.UserCode }}
            </div>
            <div>
              <v-btn x-small class="ma-2" outlined color="background">
                <span>{{ item.time }}</span>
              </v-btn>
            </div>
          </div>
        </v-slide-item>
      </v-slide-group>
    </div>
  </v-card>
</template>
<script>
export default {
  data() {
    return {
      loading: false,
      items: [],
      emptyLogmessage: "",
      number_of_records: 10,
      logs: [],
      url: process.env.SOCKET_ENDPOINT,
      socket: null,
    };
  },
  mounted() {
    this.socketConnection();
  },

  created() {
    this.getDataFromApi();
  },
  computed: {
    employees() {
      return this.$store.state.employeeList.map((e) => ({
        system_user_id: e.system_user_id,
        first_name: e.first_name,
        last_name: e.last_name,
        display_name: e.display_name,
      }));
    },
    devices() {
      if (this.$store.state.devices)
        return this.$store.state.devices.map((e) => e.device_id);
    },
  },
  methods: {
    getDataFromApi() {
      this.loading = true;
      this.$axios
        .get(
          `device/getLastRecordsByCount/${this.$auth.user.company_id}/${this.number_of_records}`
        )
        .then(async ({ data }) => {
          this.loading = false;

          this.logs = await data.map((e) => ({
            UserCode: e.UserID,
            time: e.time,
            name:
              e.employee &&
              (e.employee.display_name ||
                e.employee.first_name ||
                e.employee.last_name),
            image:
              (e.employee && e.employee.profile_picture) ||
              "/no-profile-image.jpg",
          }));
        });
    },
    socketConnection() {
      this.socket = new WebSocket(this.url);

      this.socket.onmessage = ({ data }) => {
        let json = JSON.parse(data).Data;
        if (json && json.UserCode > 0) {
          this.getDetails(json);
        }
      };
    },
    getDetails({ SN, RecordImage, UserCode, RecordDate }) {
      if (this.devices)
        if (this.devices.includes(SN)) {
          let employee = this.employees.find(
            (e) => e.system_user_id == UserCode && e.first_name !== null
          );

          let item = {
            UserCode,
            image:
              "data:image;base64," + RecordImage || "/no-profile-image.jpg",
            time: this.setTime(RecordDate),
            name:
              employee &&
              (employee.display_name ||
                employee.first_name ||
                employee.last_name),
          };
          this.logs.unshift(item);
        }
    },
    setTime(dateTimeString) {
      const dateTime = new Date(dateTimeString);
      const hours = dateTime.getHours().toString().padStart(2, "0");
      const minutes = dateTime.getMinutes().toString().padStart(2, "0");
      return `${hours}:${minutes}`;
    },
  },
};
</script>
<style scoped>
.center-both {
  height: 31vh; /* Adjust the height as needed */
  display: flex;
  align-items: center;
  justify-content: center;
}
</style>
