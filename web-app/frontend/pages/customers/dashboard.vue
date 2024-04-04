<template>
  <div
    style="width: 100%"
    v-if="can('dashboard_access') && can('dashboard_view')"
  >
    <v-row>
      <v-col
        cols="12"
        class="pt-0"
        style="padding-right: 0px; margin-right: 0px"
      >
        <v-row justify="end">
          <v-col cols="3" class="pb-0">
            <span style="float: left; width: 200px">
              <v-select
                style="z-index: 9999"
                @change="ChangeDevice(device_serial_number)"
                v-model="device_serial_number"
                :items="devicesList"
                dense
                small
                outlined
                hide-details
                class="ma-2"
                label="Room"
                item-value="serial_number"
                item-text="name"
              ></v-select>
            </span>
            <span style="float: left">
              <v-menu
                style="z-index: 9999"
                v-model="from_menu"
                :close-on-content-click="false"
                :nudge-left="50"
                transition="scale-transition"
                offset-y
                min-width="auto"
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field
                    style="
                      width: 230px;
                      float: right;
                      z-index: 9999;
                      height: 5px;
                      padding-top: 8px;
                    "
                    outlined
                    v-model="from_date"
                    v-bind="attrs"
                    v-on="on"
                    dense
                    class="custom-text-box shadow-none"
                    label="Date Filter"
                  ></v-text-field>
                </template>
                <v-date-picker
                  no-title
                  scrollable
                  v-model="from_date"
                  @input="from_menu = false"
                  @change="getDataFromApi()"
                ></v-date-picker>
              </v-menu>
            </span>
          </v-col>
        </v-row>
      </v-col>
      <v-col cols="12" class="pt-0">
        <DashboardTopStats />
      </v-col>
      <v-col cols="12">
        <v-row>
          <v-col cols="6" class="pl-0">
            <v-card class="py-2" style="width: 100%; height: 500px">
              <div class="pl-3" style="font-size: 18px">Demographics</div>
              <DashboardDemographics />
            </v-card>
          </v-col>
          <v-col cols="6" style="padding-left: 6px">
            <v-card class="py-2" style="width: 100%; height: 500px">
              <DashboardChart
                :name="'AlarmDashboardHourlyChart'"
                :height="'400px'"
                :key="keyChart3"
                :date_from="from_date"
                :date_to="to_date"
              />
            </v-card>
          </v-col>
        </v-row>
      </v-col>
      <v-col cols="12" class="pt-0">
        <v-row>
          <v-col cols="4" class="pl-0">
            <v-card class="py-2" style="width: 100%; height: 130px">
              <div class="pl-3" style="font-size: 18px">Customers</div>
              <DashboardFooterCustomerStats />
            </v-card>
          </v-col>
          <v-col cols="8" class="pl-0">
            <v-card class="py-2" style="width: 100%; height: 130px">
              <div class="pl-3" style="font-size: 18px">Employees</div>
              <DashboardFooterEmployeeStats />
            </v-card>
          </v-col>
        </v-row>
      </v-col>
    </v-row>
  </div>
  <NoAccess v-else />
</template>

<script>
//import DashboardTopStats from "../../components/DashboardCustomers/DashboardTopStats.vue";
export default {
  //components: { DashboardTopStats },
  data() {
    return {
      device_serial_number: "",
      from_date: "",
      to_date: "",
      from_menu: false,
      topMenu: 0,
      devicesList: [],
      keyChart3: 1,
    };
  },
  watch: {},
  mounted() {},
  async created() {
    this.keyChart3++;
    const today = new Date();

    this.from_date = today.toISOString().slice(0, 10);
    this.to_date = today.toISOString().slice(0, 10);
    if (this.$auth.user.branch_id == 0 && this.$auth.user.is_master == false) {
      alert("You do not have permission to access this branch");
      //this.$router.push("/login");
      this.$axios.get(`/logout`).then(({ res }) => {
        this.$auth.logout();
        this.$router.push(`/login`);
      });

      this.$router.push(`/login`);
      return "";
    }

    try {
      await this.$store.dispatch("fetchDropDowns", {
        key: "deviceList",
        endpoint: "device-list",
        refresh: true,
      });
      this.devicesList = this.$store.state.deviceList;

      this.devicesList = this.devicesList.filter(
        (item) => item.serial_number != null
      );
      if (this.$store.state.deviceList && this.$store.state.deviceList[0]) {
        this.device_serial_number = this.$store.state.deviceList[0].device_id;
        //this.getDataFromApi();
      }
      this.getDataFromApi();
      // await this.$store.dispatch("fetchDropDowns", {
      //   key: "employeeList",
      //   endpoint: "employee-list",
      //   refresh: true,
      // });
      // this.branchList = await this.$store.dispatch("fetchDropDowns", {
      //   key: "branchList",
      //   endpoint: "branch-list",
      //   refresh: true,
      // });
    } catch (error) {
      console.error("Error fetching data:", error);
    }
    this.getDataFromApi(1);
    setInterval(() => {
      this.getDataFromApi(1);
    }, 1000 * 10);
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
    ChangeDevice(serial_number) {
      try {
        this.device_serial_number = serial_number;
        // this.key++;
        // this.keyChart2++;

        this.getDataFromApi(1);
        //console.log(this.device_serial_number, " this.device_serial_number");
      } catch (e) {}
    },

    getDataFromApi(repeat) {
      let options = {
        params: {
          per_page: 1000,
          company_id: this.$auth.user.company_id,

          date: this.from_date,
        },
      };
      this.$axios.get(`/dashboard-statistics`, options).then(({ data }) => {
        this.$store.commit("dashboard/customerDashboardData", data);
      });

      this.getEmployeeStats();
    },

    getDataFromApi(repeat) {
      let options = {
        params: {
          per_page: 1000,
          company_id: this.$auth.user.company_id,
          date: this.from_date,
        },
      };
      this.$axios
        .get(`/dashbaord_attendance_count`, options)
        .then(({ data }) => {
          this.$store.commit("dashboard/customerDashboardEmployeeData", data);
        });
    },
  },
};
</script>
