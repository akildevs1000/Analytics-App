<template>
  <div
    style="width: 100%"
    v-if="can('dashboard_access') && can('dashboard_view')"
  >
    <v-row class="">
      <v-col
        cols="12"
        class="pt-0"
        style="padding-right: 0px; margin-right: 0px"
      >
        <v-row justify="end" style="padding-top: 10px">
          <v-col cols="4" class="pb-0" style="min-width: 670px">
            <span style="float: left; width: 200px">
              <v-select
                @change="changeBranch(filter_branch_id)"
                class="ma-2"
                v-model="filter_branch_id"
                item-text="name"
                item-value="id"
                :items="[{ name: `All Branches`, id: null }, ...branches_list]"
                dense
                small
                outlined
                hide-details
                label="Branches"
              >
              </v-select>
            </span>
            <span style="float: left; width: 200px">
              <v-select
                style="z-index: 9999"
                @change="ChangeDevice(filter_device_serial_number)"
                v-model="filter_device_serial_number"
                :items="[
                  { name: 'All Locations', serial_number: null },
                  ...devicesList,
                ]"
                dense
                small
                outlined
                hide-details
                class="ma-2"
                label="Location/Camera"
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
                    v-model="filter_from_date"
                    v-bind="attrs"
                    v-on="on"
                    dense
                    class="custom-text-box shadow-none"
                    label="Date "
                  ></v-text-field>
                </template>
                <v-date-picker
                  no-title
                  scrollable
                  v-model="filter_from_date"
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
            <v-card
              class="py-2"
              style="width: 100%; height: 500px; border-radius: 8px"
            >
              <div class="pl-3" style="font-size: 18px">Demographics</div>
              <DashboardDemographics />
            </v-card>
          </v-col>
          <v-col cols="6" style="padding-left: 6px">
            <v-card
              class="py-2"
              style="width: 100%; height: 500px; border-radius: 8px"
            >
              <DashboardChart
                :filter_device_serial_number="filter_device_serial_number"
                :name="'AlarmDashboardHourlyChart'"
                :height="'400px'"
                :key="keyChart3"
                :date_from="from_date"
                :date_to="to_date"
                :filter_from_date="filter_from_date"
                :branch_id="filter_branch_id"
              />
            </v-card>
          </v-col>
        </v-row>
      </v-col>
      <v-col cols="12" class="pt-0">
        <v-row>
          <v-col cols="4" class="pl-0">
            <v-card
              class="py-2"
              style="width: 100%; height: 130px; border-radius: 8px"
            >
              <div class="pl-3" style="font-size: 18px">Customers</div>
              <DashboardFooterCustomerStats />
            </v-card>
          </v-col>
          <v-col cols="8" class="pl-0">
            <v-card
              class="py-2"
              style="width: 100%; height: 130px; border-radius: 8px"
            >
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
      filter_device_serial_number: null,
      from_date: "",
      to_date: "",
      from_menu: false,
      topMenu: 0,
      devicesList: [],
      keyChart3: 1,
      loading: false,
      filter_from_date: "",
      branches_list: [],
      filter_branch_id: null,
    };
  },
  watch: {},
  mounted() {},
  async created() {
    this.keyChart3++;
    const today = new Date();

    // this.from_date = today.toISOString().slice(0, 10);
    // this.to_date = today.toISOString().slice(0, 10);
    this.filter_from_date = today.toISOString().slice(0, 10);
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

      this.branches_list = await this.$store.dispatch("fetchDropDowns", {
        key: "branchList",
        endpoint: "branch-list",
      });

      this.getDataFromApi();
    } catch (error) {
      console.error("Error fetching data:", error);
    }
    this.getDataFromApi(1);
    setInterval(() => {
      if (this.$route.name == "statistics") {
        this.getDataFromApi(1);
      }
    }, 1000 * 60);
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
    changeBranch(filter_branch) {
      this.devicesList = this.$store.state.deviceList;
      if (filter_branch) {
        this.devicesList = this.devicesList.filter(
          (item) => item.branch_id == filter_branch
        );
      }
      this.getDataFromApi();
      this.getEmployeeStats();
    },
    ChangeDevice(filter_device_serial_number) {
      try {
        this.filter_device_serial_number = filter_device_serial_number;
        // this.key++;
        // this.keyChart2++;

        this.getDataFromApi(1);
        //console.log(this.device_serial_number, " this.device_serial_number");
      } catch (e) {}
    },

    getDataFromApi() {
      if (this.loading) return false;
      this.loading = true;
      let options = {
        params: {
          per_page: 1000,
          company_id: this.$auth.user.company_id,
          DeviceID: this.filter_device_serial_number,
          filter_from_date: this.filter_from_date,
          branch_id: this.filter_branch_id,
        },
      };
      this.$axios.get(`/dashboard-statistics`, options).then(({ data }) => {
        this.$store.commit("dashboard/customerDashboardData", data);
        setTimeout(() => {
          this.loading = false;
        }, 2000);
      });

      this.getEmployeeStats();
    },

    getEmployeeStats(repeat) {
      let options = {
        params: {
          per_page: 1000,
          company_id: this.$auth.user.company_id,
          branch_id: this.filter_branch_id,
          filter_from_date: this.filter_from_date,
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
