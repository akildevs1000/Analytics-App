<template>
  <div>
    <v-row>
      <v-col md="10">
        <h4 class="pl-2">Web Login Activities</h4>
      </v-col>
      <v-col md="2" class="text-end">
        <v-menu bottom left>
          <template v-slot:activator="{ on, attrs }">
            <v-btn dark-2 icon v-bind="attrs" v-on="on">
              <v-icon>mdi-dots-vertical</v-icon>
            </v-btn>
          </template>
          <v-list width="120" dense>
            <v-list-item @click="viewLogs()">
              <v-list-item-title style="cursor: pointer">
                View Logs
              </v-list-item-title>
            </v-list-item>
          </v-list>
        </v-menu>
      </v-col>
    </v-row>

    <ComonPreloader icon="face-scan" v-if="loading" />

    <v-data-table
      dense
      :headers="headers_table"
      :items="logs"
      :loading="loading"
      :options.sync="options"
      :footer-props="{
        itemsPerPageOptions: [5, 50],
      }"
      :server-items-length="totalRowsCount"
      hide-default-header
    >
      <template v-slot:item.employee.pic="{ item, index }">
        <v-row no-gutters>
          <v-col
            style="
              padding: 5px;
              padding-left: 0px;
              width: 30px;
              max-width: 30px;
            "
          >
            <v-img
              style="
                border-radius: 50%;
                height: auto;
                width: 30px;
                max-width: 30px;
              "
              :src="
                item.user.employee
                  ? item.user.employee.profile_picture
                  : '/no-profile-image.jpg'
              "
            >
            </v-img>
          </v-col>
        </v-row>
      </template>
      <template v-slot:item.employee.first_name="{ item, index }">
        {{ item.user.employee ? item.user.employee.first_name : "Admin" }}
        {{ item.user.employee ? item.user.employee.last_name : " " }}

        <div class="secondary-value">
          {{
            item.user.employee && item.user.employee.department
              ? caps(item.user.employee.department.name)
              : "---"
          }}
        </div>
      </template>

      <template v-slot:item.UserID="{ item }"> #{{ item.UserID }} </template>
      <template v-slot:item.employee.employee_id="{ item }">
        {{ item.employee && item.employee.employee_id }}
      </template>
      <template v-slot:item.LogTime="{ item }" style="color: green">
        <v-icon color="green" fill>mdi-clock-outline</v-icon>
        {{ item.date_time }}
      </template>

      <template v-slot:item.online="{ item }">
        <v-icon v-if="item.device.location" color="green" fill
          >mdi-map-marker-radius</v-icon
        >
        <v-icon v-else color="red" fill>mdi-map-marker-radius</v-icon>
      </template>
      <template v-slot:item.device.device_name="{ item }">
        <div :style="item.device.location ? 'color:green' : 'color: red;'">
          {{ item.device ? caps(item.device.name) : "---" }} <br />

          {{ item.device.location ? item.device.location : "---" }}
        </div>
      </template>
    </v-data-table>
  </div>
</template>
>
<script>
export default {
  data() {
    return {
      loading: false,
      items: [],
      emptyLogmessage: "",
      number_of_records: 5,
      logs: [],
      url: process.env.SOCKET_ENDPOINT,
      socket: null,
      totalRowsCount: 0,

      total: 0,
      options: {},
      headers_table: [
        {
          text: "Pic",
          align: "left",
          sortable: true,
          filterable: true,

          value: "employee.pic",
        },
        {
          text: "Employee Name",
          align: "left",
          sortable: true,
          filterable: true,

          value: "employee.first_name",
        },

        {
          text: "Time",
          align: "left",
          sortable: true,
          filterable: true,

          value: "LogTime", //edit purpose
        },
      ],
    };
  },
  watch: {
    options: {
      handler() {
        this.getRecords();
      },
      deep: true,
    },
  },
  mounted() {},
  created() {},
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
    viewLogs() {
      this.$router.push("/web_login_logs");
    },
    caps(str) {
      if (str == "" || str == null) {
        return "";
      } else {
        let res = str.toString();
        return res.replace(/\b\w/g, (c) => c.toUpperCase());
      }
    },
    getRecords(filter_column = "", filter_value = "") {
      this.loading = true;

      //let filter_value = this.datatable_search_textbox;
      let { sortBy, sortDesc, page, itemsPerPage } = this.options;

      let sortedBy = sortBy ? sortBy[0] : "";
      let sortedDesc = sortDesc ? sortDesc[0] : "";

      // if (this.filters) {
      //   page = 1;
      // }

      let itemsPerPage1 = 5; // itemsPerPage;
      if (!itemsPerPage1) itemsPerPage1 = 5;
      let options = {
        params: {
          page: page,
          sortBy: sortedBy,
          sortDesc: sortedDesc,
          per_page: itemsPerPage1,
          filter_column: filter_value,
          company_id: this.$auth.user.company_id,
          ...this.filters,
        },
      };

      if (filter_column != "") options.params[filter_column] = filter_value;
      this.$axios.get(`activity`, options).then(({ data }) => {
        this.totalRowsCount = data.total;
        this.logs = data.data;
        this.loading = false;
      });
    },
    socketConnection() {
      this.socket = new WebSocket(this.url);

      this.socket.onmessage = ({ data }) => {
        let json = JSON.parse(data);

        if (json.Status == 200 && json.Data.UserCode > 0) {
          this.getDetails(json.Data);
        }
      };
    },
    getDetails(item) {
      item.company_id = this.$auth.user.company_id;

      this.$axios.post(`/device/details`, item).then(({ data }) => {
        if (
          data.device &&
          this.$auth.user &&
          data.device.company_id == this.$auth.user.company_id
        ) {
          this.logs.unshift(data);
        }
      });
    },
    getDataFromApi() {
      this.loading = true;

      const { page, itemsPerPage } = this.options;

      let options = {
        params: {
          page: page,
          per_page: itemsPerPage,
        },
      };
      this.$axios
        .get(
          `device/getLastRecordsHistory/${this.$auth.user.company_id}/${this.number_of_records}`,
          options
        )
        .then(async ({ data }) => {
          this.loading = false;
          this.total = data.total;
          this.logs = await data.data.map((e) => ({
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

<style>
.v-application--is-ltr .v-data-footer__pagination {
  margin: 0px;
}
</style>
