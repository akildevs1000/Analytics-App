<template>
  <div
    style="width: 100%"
    v-if="can('attendance_report_access') && can('attendance_report_view')"
  >
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ snackbarMessage }}
      </v-snackbar>
    </div>
    <v-card>
      <v-toolbar dense flat>
        <span class="headline black--text"> Missing Attendance Logs </span>
      </v-toolbar>
      <v-card-text>
        <v-row>
          <v-col cols="4">
            <v-autocomplete
              v-model="payload.device_id"
              label="Select Device"
              :hide-details="true"
              outlined
              dense
              small
              item-text="name"
              item-value="device_id"
              :items="devices"
              placeholder="Device Name"
              solo
              flat
            >
              <template #selection="{ item }">
                <span
                  >{{ devices.indexOf(item) + 1 }} - {{ item.company.name }} -
                  {{ item.name }}</span
                >
              </template>
              <!-- Custom template to display both name and id in dropdown options -->
              <template #item="{ item }">
                <v-list-item-content>
                  <v-list-item-title
                    >{{ devices.indexOf(item) + 1 }} - {{ item.company.name }} -
                    {{ item.name }}
                  </v-list-item-title>
                </v-list-item-content>
              </template></v-autocomplete
            >
            <span v-if="errors && errors.device_id" class="text-danger mt-2">{{
              errors.device_id[0]
            }}</span>
          </v-col>
          <v-col cols="3">
            <v-menu
              v-model="from_menu"
              :close-on-content-click="false"
              :nudge-right="40"
              transition="scale-transition"
              offset-y
              min-width="auto"
            >
              <template v-slot:activator="{ on, attrs }">
                <v-text-field
                  outlined
                  label="Select Date"
                  v-model="payload.date"
                  readonly
                  v-bind="attrs"
                  v-on="on"
                  dense
                  :hide-details="true"
                  class="custom-text-box shadow-none"
                  solo
                ></v-text-field>
              </template>
              <v-date-picker
                no-title
                scrollable
                v-model="payload.date"
                @input="from_menu = false"
              ></v-date-picker>
            </v-menu>
            <span v-if="errors && errors.date" class="text-danger mt-2">{{
              errors.date[0]
            }}</span>
          </v-col>
          <v-col md="2" sm="2">
            <v-btn @click="getMissingLogs()" color="primary" primary fill
              >Submit
            </v-btn>
          </v-col>
          <v-col md="2" sm="2">
            <v-btn @click="automate()" color="primary" primary fill
              ><v-icon class="">mdi mdi-head-cog-outline</v-icon> Select date
              and Automate
            </v-btn>
          </v-col>
        </v-row></v-card-text
      >
    </v-card>

    <v-card class="mt-5">
      <v-toolbar dense flat>
        <span
          class="bold text--green"
          style="color: green"
          v-if="snackbarMessage"
        >
          Message: {{ snackbarMessage }}
        </span>
      </v-toolbar>
      <v-card-text>
        <v-data-table
          dense
          :headers="headers"
          :items="data"
          :loading="loading"
          class="elevation-0"
          model-value="data.id"
          height="800"
          no-data-text="No Data available.  "
          :footer-props="{
            itemsPerPageOptions: [100, 500, 1000],
          }"
          :options.sync="options"
          :server-items-length="totalRowsCount"
        >
          <template v-slot:item.sno="{ item, index }" style="padding: 0px">
            {{ ++index }}
          </template>
          <template
            v-slot:item.employee_id="{ item, index }"
            style="padding: 0px"
          >
            {{ item.UserID }}
          </template>
          <template v-slot:item.date="{ item, index }" style="padding: 0px">
            {{ item.LogTime }}
          </template>
          <template
            v-slot:item.serial_number="{ item, index }"
            style="padding: 0px"
          >
            {{ item.SerialNumber }}
          </template>

          <template v-slot:item.message="{ item, index }" style="padding: 0px">
            Success
          </template>
        </v-data-table>
      </v-card-text>
    </v-card>
  </div>

  <NoAccess v-else />
</template>

<script>
// import DashboardlastMultiStatistics from "../../components/dashboard2/DashboardlastMultiStatistics.vue";
export default {
  layout({ $auth }) {
    let { user_type } = $auth.user;
    if (user_type == "master") {
      return "master";
    } else if (user_type == "employee") {
      return "employee";
    } else if (user_type == "master") {
      return "default";
    }
  },
  components: {},
  data() {
    return {
      options: {},
      errors: {},
      snackbarMessage: "",
      snackbar: false,
      devices: [],
      from_date: "",
      from_menu: false,
      totalRowsCount: 0,
      loading: false,
      payload: {},
      data: [],
      headers: [
        {
          text: "#",
          align: "left",
          sortable: false,
          value: "sno",
        },
        {
          text: "Employee Id",
          align: "left",
          sortable: false,
          value: "employee_id",
          filterable: false,
          key: "employee_id",
        },

        {
          text: "Date",
          align: "left",
          sortable: false,
          filterable: false,
          value: "date",
        },
        {
          text: "SerialNumber",
          align: "left",
          sortable: false,
          filterable: false,
          value: "serial_number",
        },
        {
          text: "Message",
          align: "left",
          sortable: false,
          filterable: false,
          value: "message",
        },
      ],
    };
  },
  watch: {},
  mounted() {},
  created() {
    const today = new Date();

    this.payload.date = today.toISOString().slice(0, 10);

    this.getDeviceList();
  },
  // watch: {
  //   overlay(val) {
  //     val &&
  //       setTimeout(() => {
  //         this.overlay = false;
  //       }, 3000);
  //   },
  // },
  methods: {
    can(per) {
      return this.$pagePermission.can(per, this);
    },
    getDeviceList() {
      let payload = {
        params: {
          company_id: this.$auth.user.company_id,
        },
      };
      this.$axios.get(`/device_list_master`, payload).then(({ data }) => {
        this.devices = data;
      });
    },
    automate() {
      // this.devices.forEach((element) => {

      //   this.payload.device_id = element.device_id;
      //   this.getMissingLogs();

      // });
      if (!this.payload.date) {
        this.errors = {};
        this.errors["date"] = ["The date field is required."];
        return false;
      }

      this.snackbarMessage =
        "Automation Missing Logs process started...Please wait";
      let counter = 0;
      let processComplatedStatus = true;
      setInterval(() => {
        if (counter <= this.devices.length) {
          if (processComplatedStatus) {
            processComplatedStatus = false;
            this.payload.device_id = this.devices[counter].device_id;
            processComplatedStatus = this.getMissingLogs();
            counter++;
          } else {
            alert("Verified All Devices");
          }
        }
      }, 1000 * 10);
    },

    getMissingLogs() {
      this.snackbarMessage = "";

      if (!this.payload.device_id) {
        this.errors = {};
        this.errors["device_id"] = ["The Device field is required."];

        return false;
      } else if (!this.payload.date) {
        this.errors = {};
        this.errors["date"] = ["The date field is required."];
        return false;
      }
      this.errors = {};
      let payload = {
        params: {
          company_id: this.$auth.user.company_id,
          device_id: this.payload.device_id,
          date: this.payload.date,
        },
      };
      this.loading = true;
      this.snackbar = true;
      this.data = [];
      this.snackbarMessage = "Finding missing logs. Please wait ..... ";
      this.$axios.get(`/attendance-logs-missing`, payload).then(({ data }) => {
        this.loading = false;
        if (data.status == 120) {
          this.snackbarMessage = "";
          this.snackbar = true;
          this.snackbarMessage = data.message + " ";
        } else if (data.status != 200) {
          this.snackbarMessage = "";
          this.snackbar = true;
          this.snackbarMessage = data.message + " . Try again";
        } else {
          this.snackbarMessage = "";
          this.snackbar = true;
          this.snackbarMessage = data.message;

          if (data.updated_records.length == 0) {
            this.snackbarMessage =
              this.snackbarMessage + ". All Attendance logs are upto date";
          } else if (data.updated_records.length > 0) {
            this.snackbarMessage =
              this.snackbarMessage +
              ". Total Updated Missing logs count is " +
              data.updated_records.length;
          }
          this.data = data.updated_records;
          this.totalRowsCount = data.length;
          this.loading = false;
        }
      });

      return true;
    },
  },
};
</script>
