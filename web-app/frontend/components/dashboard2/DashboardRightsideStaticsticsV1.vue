<template>
  <div>
    <div v-if="!loading">
      <v-card class="py-2 mb-2">
        <v-row align-self="center">
          <v-col
            style="padding-left: 25px"
            lg="6"
            md="6"
            sm="6"
            xs="6"
            class="laptop-padding"
            align-self="center"
            >Total Employees</v-col
          >

          <v-col
            lg="4"
            md="4"
            sm="4"
            xs="4"
            class="text-red bold text-h3 red--text laptop-padding"
            align-self="center"
            style="margin-left: 5px"
            >{{ data.employeeCount }}</v-col
          >
        </v-row>
      </v-card>

      <v-row>
        <v-col md="6" xs="12" sm="12" cols="12">
          <v-card class="py-2">
            <v-row class="pa-2">
              <v-row class="pa-0 ma-0" style="width: 100%">
                <v-col md="6">
                  <v-avatar color="green">
                    <v-icon size="30" style="color: #fff"
                      >mdi-account-outline</v-icon
                    >
                  </v-avatar>
                </v-col>

                <v-col md="6" class="menu-icon-right text-end">
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
              <v-row>
                <v-col md="12" style="text-align: center; padding-top: 0px">
                  <div class="bold text-h3 green--text">
                    {{ data.presentCount }}
                  </div>

                  <div style="text-align: center">Today Presents</div>
                </v-col>
              </v-row>
            </v-row>
          </v-card>
        </v-col>

        <v-col md="6" xs="12" sm="12" cols="12">
          <v-card class="py-2">
            <v-row class="pa-2">
              <v-row class="pa-0 ma-0" style="width: 100%">
                <v-col md="6">
                  <v-avatar color="red">
                    <v-icon size="30" style="color: #fff"
                      >mdi-account-minus-outline</v-icon
                    >
                  </v-avatar>
                </v-col>

                <v-col md="6" class="menu-icon-right text-end">
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
              <v-row>
                <v-col md="12" style="text-align: center; padding-top: 0px">
                  <div class="bold text-h3 red--text">
                    {{ data.absentCount }}
                  </div>

                  <div style="text-align: center">Today Absensts</div>
                </v-col>
              </v-row>
            </v-row>
          </v-card>
        </v-col>
      </v-row>

      <v-row>
        <v-col md="6" xs="12" sm="12" cols="12">
          <v-card class="py-2">
            <v-row class="pa-2">
              <v-row class="pa-0 ma-0" style="width: 100%">
                <v-col md="6">
                  <v-avatar color="blue">
                    <v-icon size="30" style="color: #fff"
                      >mdi-airplane-takeoff</v-icon
                    >
                  </v-avatar>
                  <!-- <v-icon color="blue" size="30" fill>mdi-airplane-takeoff</v-icon> -->
                </v-col>

                <v-col md="6" class="menu-icon-right">
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
              <v-row>
                <v-col md="12" style="text-align: center; padding-top: 0px">
                  <div class="bold text-h3 blue--text">
                    {{ data.vaccationCount }}
                  </div>

                  <div style="text-align: center">On Vacation</div>
                </v-col>
              </v-row>
            </v-row>
          </v-card>
        </v-col>
        <v-col md="6" xs="12" sm="12" cols="12">
          <v-card class="py-2">
            <v-row class="pa-2">
              <v-row class="pa-0 ma-0" style="width: 100%">
                <v-col md="6">
                  <v-avatar color="yellow">
                    <v-icon size="30" style="color: #fff"
                      >mdi-clock-time-eight-outline</v-icon
                    >
                  </v-avatar>
                </v-col>

                <v-col md="6" class="menu-icon-right">
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
              <v-row>
                <v-col md="12" style="text-align: center; padding-top: 0px">
                  <div class="bold text-h3 orange--text">
                    {{ data.leaveCount }}
                  </div>

                  <div style="text-align: center">On Leave</div>
                </v-col>
              </v-row>
            </v-row>
          </v-card>
        </v-col>
      </v-row>

      <v-row>
        <v-col md="6" xs="12" sm="12" cols="12">
          <v-card class="py-2">
            <v-row class="pa-2">
              <v-row class="pa-0 ma-0" style="width: 100%">
                <v-col md="6">
                  <v-avatar color="#E65100">
                    <v-icon size="30" style="color: #fff"
                      >mdi-cellphone-information</v-icon
                    >
                  </v-avatar>
                </v-col>

                <v-col md="6" class="menu-icon-right">
                  <v-menu bottom left>
                    <template v-slot:activator="{ on, attrs }">
                      <v-btn dark-2 icon v-bind="attrs" v-on="on">
                        <v-icon>mdi-dots-vertical</v-icon>
                      </v-btn>
                    </template>
                    <v-list width="120" dense>
                      <v-list-item @click="viewDevices()">
                        <v-list-item-title style="cursor: pointer">
                          View Devices
                        </v-list-item-title>
                      </v-list-item>
                    </v-list>
                  </v-menu>
                </v-col>
              </v-row>
              <v-row>
                <v-col md="12" style="text-align: center; padding-top: 0px">
                  <div class="bold text-h3 red--text">{{ offlineDevices }}</div>

                  <div style="text-align: center">Offline Devices</div>
                </v-col>
              </v-row>
            </v-row>
          </v-card>
        </v-col>

        <v-col md="6" xs="12" sm="12" cols="12">
          <v-card class="py-2">
            <v-row class="pa-2">
              <v-row class="pa-0 ma-0" style="width: 100%">
                <v-col md="6">
                  <v-avatar color="black">
                    <v-icon size="30" style="color: #fff"
                      >mdi-card-account-mail-outline</v-icon
                    >
                  </v-avatar>
                </v-col>

                <v-col md="6" class="menu-icon-right">
                  <v-menu bottom left>
                    <template v-slot:activator="{ on, attrs }">
                      <v-btn dark-2 icon v-bind="attrs" v-on="on">
                        <v-icon>mdi-dots-vertical</v-icon>
                      </v-btn>
                    </template>
                    <v-list width="120" dense>
                      <v-list-item @click="viewLeaves()">
                        <v-list-item-title style="cursor: pointer">
                          View Leaves
                        </v-list-item-title>
                      </v-list-item>
                    </v-list>
                  </v-menu>
                </v-col>
              </v-row>
              <v-row>
                <v-col md="12" style="text-align: center; padding-top: 0px">
                  <div class="bold text-h3 black--text">
                    {{ leaveRequests }}
                  </div>

                  <div style="text-align: center">Leave Requests</div>
                </v-col>
              </v-row>
            </v-row>
          </v-card>
        </v-col>
      </v-row>
    </div>
    <ComonPreloader icon="face-scan" v-if="loading" />
  </div>
</template>

<script>
// import VueApexCharts from 'vue-apexcharts'

export default {
  props: ["branch_id"],
  data() {
    return {
      offlineDevices: 0,
      leaveRequests: 0,
      data: {
        employeeCount: 0,
        totalIn: 0,
        totalOut: 0,
        presentCount: 0,
        absentCount: 0,
        missingCount: 0,
        offCount: 0,
        holidayCount: 0,
        leaveCount: 0,
        vaccationCount: 0,
      },
      loading: false,
    };
  },
  watch: {
    branch_id() {
      this.loading = true;
      this.$store.commit("dashboard/attendance_count", null);
      this.$store.commit("dashboard/off_devices_count", null);

      this.$store.commit("dashboard/leaves_request_count", null);

      this.getDataFromApi();
      this.loading = false;
    },
  },
  created() {
    setTimeout(() => {
      this.getDataFromApi();
    }, 1000 * 16);
  },

  methods: {
    viewLeaves() {
      this.$router.push("/leaves");
    },
    viewLogs() {
      this.$router.push("/attendance_report");
    },
    viewDevices() {
      this.$router.push("/device");
    },
    getDataFromApi() {
      let { attendance_count, off_devices_count, leaves_request_count } =
        this.$store.state.dashboard;

      if (
        attendance_count &&
        off_devices_count &&
        leaves_request_count != null
      ) {
        this.data = attendance_count;
        this.offlineDevices = off_devices_count;
        this.leaveRequests = leaves_request_count;
      }

      setTimeout(() => {
        this.$axios
          .get("dashbaord_attendance_count", {
            params: {
              company_id: this.$auth.user.company_id,
              branch_id: this.branch_id > 0 ? this.branch_id : null,
            },
          })
          .then(({ data }) => {
            this.data = data;
            this.$store.commit("dashboard/attendance_count", data);
            this.getDeviceStatus();
            this.getPendingLeavesCount();
          });
      }, 1000 * 4);
    },

    getDeviceStatus() {
      this.$axios
        .get(`/devcie_count_Status/${this.$auth.user.company_id}`, {
          params: {
            company_id: this.$auth.user.company_id,
            branch_id: this.branch_id > 0 ? this.branch_id : null,
          },
        })
        .then(({ data }) => {
          this.offlineDevices = data.series ? data.series[1] : 0;
          this.$store.commit(
            "dashboard/off_devices_count",
            this.offlineDevices
          );
        });
    },
    getPendingLeavesCount() {
      let options = {
        params: {
          company_id: this.$auth.user.company_id || 0,
          branch_id: this.branch_id > 0 ? this.branch_id : null,
        },
      };

      this.$axios.get(`employee_leaves_new`, options).then(({ data }) => {
        this.leaveRequests = data.total_pending_count;
        this.$store.commit(
          "dashboard/leaves_request_count",
          data.total_pending_count
        );
      });
    },
  },
};
</script>

<style>
.menu-icon-right {
  margin: auto;
  text-align: right;
}
</style>
