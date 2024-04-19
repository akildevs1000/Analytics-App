<template>
  <div style="width: 101%">
    <v-col style="margin-top: -7px; width: 99%; padding: 0px">
      <!-- <fieldset
        style="
          width: 100%;
          border: 1px solid #a1a1a1;
          border-radius: 11px;
          height: 130px;
        "
      > -->
      <!-- <legend>Customers</legend> -->
      <div class="text-center" style="width: 100%; margin: auto">
        <v-row class="text-center">
          <v-col cols="4">
            <v-row>
              <v-col cols="5">
                <span>
                  <span>
                    <img
                      src="../../static/icons/vip.png"
                      style="width: 40px; padding-top: 5px"
                    />
                  </span>
                </span>

                <div style="font-size: 13px">VIP</div>
              </v-col>
              <v-col cols="7" class="text-left">
                <span
                  style="
                    margin: auto;
                    padding: 0px;
                    margin-left: 0px;
                    font-size: 30px;
                  "
                >
                  {{ data.vip_customer_count ?? "---" }}
                </span>
              </v-col>
            </v-row>
          </v-col>
          <v-col cols="4">
            <v-row>
              <v-col cols="5">
                <span>
                  <span>
                    <img
                      src="../../static/icons/repeated.png"
                      style="width: 40px; padding-top: 5px"
                    />
                  </span>
                </span>

                <div style="font-size: 13px">Repeated</div>
              </v-col>
              <v-col cols="7" class="text-left">
                <span
                  style="
                    margin: auto;
                    padding: 0px;
                    margin-left: 0px;
                    font-size: 30px;
                  "
                >
                  {{ data.repeated_customer_count ?? "---" }}
                </span>
              </v-col>
            </v-row>
          </v-col>
          <v-col cols="4">
            <v-row>
              <v-col cols="5">
                <span>
                  <span>
                    <img
                      src="../../static/icons/restricted.png"
                      style="width: 40px; padding-top: 5px"
                    />
                  </span>
                </span>

                <div style="font-size: 11px">Block&nbsp;Listed</div>
              </v-col>
              <v-col cols="7" class="text-left">
                <span
                  style="
                    margin: auto;
                    padding: 0px;
                    margin-left: 0px;
                    font-size: 30px;
                  "
                >
                  {{ data.blocked_customer_count ?? "---" }}
                </span>
              </v-col>
            </v-row>
          </v-col>
        </v-row>
      </div>
      <!-- </fieldset> -->
    </v-col>
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
