<template>
  <div style="width: 101%">
    <div class="text-center" style="width: 100%; margin: auto">
      <v-row class="text-center">
        <v-col cols="4">
          <v-row>
            <v-col cols="12" class="pb-0">
              <span>
                <span>
                  <img
                    src="../../static/icons/customer_vip.png"
                    style="width: 40px; padding-top: 5px"
                  />
                </span>
                <span
                  style="
                    margin: auto;
                    padding: 0px;
                    margin-left: 0px;
                    font-size: 50px;
                  "
                >
                  {{ data.vip_customer_count ?? "---" }}
                </span>
              </span>
            </v-col>
            <v-col cols="12" class="pt-0" style="margin-top: -5px">
              <div style="font-size: 12px">VIP customers</div>
            </v-col>
          </v-row>
        </v-col>
        <v-col cols="4">
          <v-row>
            <v-col cols="12" class="pb-0">
              <span>
                <span>
                  <img
                    src="../../static/icons/customer_repeated.png"
                    style="width: 40px"
                  />
                </span>
                <span
                  style="
                    margin: auto;
                    padding: 0px;
                    margin-left: 0px;
                    font-size: 50px;
                  "
                  >{{ data.repeated_customer_count ?? "---" }}
                </span>
              </span>
            </v-col>
            <v-col cols="12" class="pt-0" style="margin-top: -5px">
              <div style="font-size: 12px">Repeated Customer</div>
            </v-col>
          </v-row>
        </v-col>
        <v-col cols="4">
          <v-row>
            <v-col cols="12" class="pb-0">
              <span>
                <span>
                  <img
                    src="../../static/icons/customer_blocked.png"
                    style="width: 40px"
                  />
                </span>
                <span
                  style="
                    margin: auto;
                    padding: 0px;
                    margin-left: 0px;
                    font-size: 50px;
                  "
                  >{{ data.blocked_customer_count ?? "---" }}
                </span>
              </span>
            </v-col>
            <v-col cols="12" class="pt-0" style="margin-top: -5px">
              <div style="font-size: 12px">Block Listed</div>
            </v-col>
          </v-row>
        </v-col>
      </v-row>
    </div>
  </div>
</template>

<script>
// import DashboardlastMultiStatistics from "../../components/dashboard2/DashboardlastMultiStatistics.vue";
export default {
  components: {},
  data() {
    return {
      device_serial_number: "",
      from_date: "",
      from_date: "",
      from_menu: false,
      topMenu: 0,
      devicesList: [],
      data: {},
    };
  },
  watch: {},
  mounted() {},
  created() {
    if (this.$store.state.dashboard.customerDashboardData)
      this.data = this.$store.state.dashboard.customerDashboardData;
    setInterval(() => {
      if (this.$store.state.dashboard.customerDashboardData)
        this.data = this.$store.state.dashboard.customerDashboardData;
    }, 1000);
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

        //this.getDataFromApi(1);
        //console.log(this.device_serial_number, " this.device_serial_number");
      } catch (e) {}
    },
  },
};
</script>
