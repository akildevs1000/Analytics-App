<template>
  <div>
    <v-row>
      <v-col md="10" sm="10" xs="10" class="pl-5">
        <h4>Recent Employee Logs on Devices</h4>
      </v-col>

      <v-col md="2" sm="2" xs="2" class="text-end">
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
    <v-dialog persistent v-model="dialogEmployeeAttendance" width="1200px">
      <v-card>
        <v-card-title dense class="primary white--text background">
          Employee Attendance
          <v-spacer></v-spacer>
          <v-icon
            @click="dialogEmployeeAttendance = false"
            outlined
            dark
            color="white"
          >
            mdi mdi-close-circle
          </v-icon>
        </v-card-title>
        <v-card-text>
          <v-container>
            <DashboardEmployeeAttendanceLog
              :system_user_id="system_user_id"
              :key1="componentKey"
              :key="componentKey"
            />
          </v-container>
        </v-card-text>
      </v-card>
    </v-dialog>
    <ComonPreloader icon="face-scan" v-if="loading" />

    <v-data-table
      dense
      :headers="headers_table"
      :items="logs"
      :loading="tableloading"
      :options.sync="options"
      :footer-props="{
        itemsPerPageOptions: [5, 10, 50, 100, 500, 1000],
      }"
      class="elevation-0"
      :server-items-length="totalRowsCount"
    >
      <template v-slot:item.sno="{ item, index }">
        {{
          currentPage
            ? (currentPage - 1) * perPage +
              (cumulativeIndex + logs.indexOf(item))
            : ""
        }}
      </template>

      <template v-slot:item.employee.first_name="{ item, index }">
        <v-row no-gutters>
          <v-col
            style="
              padding: 5px;
              padding-left: 0px;
              width: 40px;
              max-width: 40px;
            "
          >
            <v-img
              style="
                border-radius: 50%;
                height: auto;
                width: 40px;
                max-width: 40px;
              "
              :src="
                item.employee && item.employee.profile_picture
                  ? item.employee.profile_picture
                  : '/no-profile-image.jpg'
              "
            >
            </v-img>
          </v-col>
          <v-col style="padding: 10px">
            {{ item.employee ? item.employee.first_name : "---" }}
            {{ item.employee ? item.employee.last_name : "---" }}
            <div class="secondary-value">
              {{
                item.employee && item.employee.designation
                  ? caps(item.employee.designation.name)
                  : "---"
              }}
            </div>
          </v-col>
        </v-row>
      </template>
      <template v-slot:item.employee.department="{ item }">
        {{
          item.employee && item.employee.department
            ? caps(item.employee.department.name)
            : "---"
        }}
        <div class="secondary-value">
          {{
            item.employee && item.employee.sub_department
              ? caps(item.employee.sub_department.name)
              : "---"
          }}
        </div>
      </template>

      <template v-slot:item.UserID="{ item }"> #{{ item.UserID }} </template>
      <template v-slot:item.employee.employee_id="{ item }">
        {{ item.employee && item.employee.employee_id }}
      </template>
      <template v-slot:item.LogTime="{ item }">
        {{ item.LogTime }}
      </template>

      <template v-slot:item.online="{ item }">
        <v-icon v-if="item.device && item.device.location" color="green" fill
          >mdi-map-marker-radius</v-icon
        >
        <v-icon v-else color="red" fill>mdi-map-marker-radius</v-icon>
      </template>
      <template v-slot:item.device.device_name="{ item }">
        <div>
          {{ item.device ? caps(item.device.name) : "---" }} <br />
          <div class="secondary-value">
            {{
              item.device && item.device.location ? item.device.location : "---"
            }}
          </div>
        </div>
      </template>
      <!-- <template v-slot:item.log="{ item }">
        <v-btn @click="viewLog(item.UserID)">View Log</v-btn>
      </template> -->
    </v-data-table>
  </div>
</template>

<script>
export default {
  props: ["system_user_id"],
  data() {
    return {
      perPage: 10,
      cumulativeIndex: 1,
      currentPage: 1,

      componentKey: 1,

      dialogEmployeeAttendance: false,
      loading: false,
      tableloading: false,
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
        // {
        //   text: "#E.ID",
        //   align: "left",
        //   sortable: true,
        //   filterable: true,

        //   value: "UserID",
        // },
        {
          text: "#",
          align: "left",
          sortable: true,
          filterable: true,

          value: "sno",
        },
        {
          text: "Employee   Id",
          align: "left",
          sortable: true,
          filterable: true,

          value: "employee.employee_id",
        },

        {
          text: "Employee Name",
          align: "left",
          sortable: true,
          filterable: true,

          value: "employee.first_name",
        },
        {
          text: "Department",
          align: "left",
          sortable: false,
          filterable: true,

          value: "employee.department",
        },

        {
          text: "Time",
          align: "left",
          sortable: true,
          filterable: true,

          value: "LogTime", //edit purpose
        },
        {
          text: "Device Name",
          align: "left",
          sortable: true,
          filterable: true,

          value: "device.device_name",
        },
        {
          text: "Online/Offline",
          align: "left",
          sortable: true,
          filterable: true,

          value: "online",
        },
        // {
        //   text: "Log",
        //   align: "left",
        //   sortable: true,
        //   filterable: true,

        //   value: "log",
        // },
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
  mounted() {
    setTimeout(() => {
      this.socketConnection();
    }, 1000 * 5);

    //this.getRecords();
  },
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
      this.$router.push("/attendance_report");
    },
    viewLog(system_user_id) {
      this.dialogEmployeeAttendance = true;
      this.componentKey = this.componentKey + 1;
      this.system_user_id = system_user_id;
      console.log(this.system_user_id, this.componentKey);
    },
    caps(str) {
      if (str == "" || str == null) {
        return "---";
      } else {
        let res = str.toString();
        return res.replace(/\b\w/g, (c) => c.toUpperCase());
      }
    },
    getRecords(filter_column = "", filter_value = "") {
      this.tableloading = true;
      //let filter_value = this.datatable_search_textbox;
      let { sortBy, sortDesc, page, itemsPerPage } = this.options;

      if (page == 1) {
        this.loading = true;
      }

      let sortedBy = sortBy ? sortBy[0] : "";
      let sortedDesc = sortDesc ? sortDesc[0] : "";

      // if (this.filters) {
      //   page = 1;
      // }

      let itemsPerPage1 = itemsPerPage;
      if (!itemsPerPage1) itemsPerPage1 = 5;
      let options = {
        params: {
          page: page,
          sortBy: sortedBy,
          sortDesc: sortedDesc,
          per_page: itemsPerPage1,
          filter_column: filter_value,
          system_user_id: this.system_user_id,
          ...this.filters,
        },
      };

      if (filter_column != "") options.params[filter_column] = filter_value;
      this.$axios
        .get(
          `device/getLastRecordsHistory/${this.$auth.user.company_id}/${itemsPerPage1}`,
          options
        )
        .then(({ data }) => {
          this.totalRowsCount = data.total;
          this.logs = data.data;
          this.loading = false;
          this.tableloading = false;
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
      this.tableloading = true;
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
          this.tableloading = false;
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
<style scoped>
.center-both {
  height: 31vh;
  /* Adjust the height as needed */
  display: flex;
  align-items: center;
  justify-content: center;
}
</style>
