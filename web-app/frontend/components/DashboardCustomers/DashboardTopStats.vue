<template>
  <div style="width: 101%">
    <v-row style="width: 101%; margin-right: 0px" class="pt-2">
      <v-col lg="3" md="3" sm="12" xs="12">
        <v-row style="width: 100%; height: 150px">
          <v-card class="py-2" style="width: 100%">
            <v-row style="height: 75px">
              <v-col cols="4" style="padding-left: 25px; font-size: 18px"
                ><span>Live IN</span>
              </v-col>
              <v-col cols="8" style="font-size: 60px; color: #c55a11">
                <span>1000</span>
              </v-col>
            </v-row>
            <v-row
              style="
                vertical-align: bottom;
                font-size: 30px;
                font-weight: 300;
                color: #c55a11;
              "
            >
              <v-col cols="4">
                <span style="display: flex; float: right">
                  <span>
                    <img
                      src="../../static/icons/male.png"
                      style="width: 25px"
                    />
                  </span>
                  <span style="margin: auto; padding: 0px; margin-left: 0px"
                    >1111
                  </span>
                </span>
              </v-col>
              <v-col cols="4">
                <span style="display: flex; float: right">
                  <span>
                    <img
                      src="../../static/icons/female.png"
                      style="width: 25px"
                    />
                  </span>
                  <span style="margin: auto; padding: 0px; margin-left: 0px"
                    >1111
                  </span>
                </span>
              </v-col>

              <v-col cols="4">
                <span style="display: flex; float: left; padding-left: 10px">
                  <span>
                    <img
                      src="../../static/icons/male.png"
                      style="width: 13px; padding-top: 18px"
                    />
                  </span>
                  <span
                    style="
                      margin: auto;
                      padding: 0px;
                      margin-left: 0px;
                      padding-top: 4px;
                    "
                    >1111</span
                  >
                </span>
              </v-col>
            </v-row>
          </v-card>
        </v-row>
      </v-col>
      <v-col lg="3" md="3" sm="12" xs="12">
        <v-row style="width: 100%; height: 150px">
          <v-card class="py-2" style="width: 100%">
            <v-row style="height: 75px">
              <v-col cols="4" style="padding-left: 25px; font-size: 18px"
                ><span>Out</span>
              </v-col>
              <v-col cols="8" style="font-size: 60px; color: #c55a11">
                <span>1000</span>
              </v-col>
            </v-row>
            <v-row
              style="
                vertical-align: bottom;
                font-size: 30px;
                font-weight: 300;
                color: #c55a11;
              "
            >
              <v-col cols="4">
                <span style="display: flex; float: right">
                  <span>
                    <img
                      src="../../static/icons/male.png"
                      style="width: 25px"
                    />
                  </span>
                  <span style="margin: auto; padding: 0px; margin-left: 0px"
                    >1111</span
                  >
                </span>
              </v-col>
              <v-col cols="4">
                <span style="display: flex; float: right">
                  <span>
                    <img
                      src="../../static/icons/female.png"
                      style="width: 25px"
                    />
                  </span>
                  <span style="margin: auto; padding: 0px; margin-left: 0px"
                    >1111
                  </span>
                </span>
              </v-col>

              <v-col cols="4">
                <span style="display: flex; float: left; padding-left: 10px">
                  <span>
                    <img
                      src="../../static/icons/male.png"
                      style="width: 13px; padding-top: 18px"
                    />
                  </span>
                  <span
                    style="
                      margin: auto;
                      padding: 0px;
                      margin-left: 0px;
                      padding-top: 4px;
                    "
                    >1111</span
                  >
                </span>
              </v-col>
            </v-row>
          </v-card>
        </v-row>
      </v-col>
      <v-col lg="2" md="2" sm="12" xs="12">
        <v-row style="width: 100%; height: 150px">
          <v-card class="py-2" style="width: 100%">
            <div style="font-size: 20px; padding-left: 25px; font-size: 18px">
              Total FootFall
            </div>
            <v-row style="height: 75px">
              <v-col
                cols="12"
                style="font-size: 60px; color: #c55a11; text-align: center"
              >
                <span style="">
                  <span>
                    <img
                      src="../../static/icons/foot.png"
                      style="width: 35px"
                    />
                  </span>
                  <span
                    style="
                      margin: auto;
                      padding: 0px;
                      margin-left: 0px;
                      font-weight: 300;
                    "
                    >1111</span
                  >
                </span>
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="12" style="text-align: center">
                <span style="">
                  <span>
                    <img
                      src="../../static/icons/stats.png"
                      style="width: 15px"
                    />
                  </span>
                  <span
                    style="
                      margin: auto;
                      padding: 0px;
                      margin-left: 0px;
                      font-weight: 300;
                    "
                    >20% vs Yesterday</span
                  >
                </span>
              </v-col>
            </v-row>
          </v-card>
        </v-row>
      </v-col>
      <v-col lg="2" md="2" sm="12" xs="12">
        <v-row style="width: 100%; height: 150px">
          <v-card class="py-2" style="width: 100%">
            <div style="font-size: 20px; padding-left: 25px; font-size: 18px">
              Occupancy
            </div>
            <v-row style="height: 75px">
              <v-col
                cols="12"
                style="font-size: 50px; color: #c55a11; text-align: center"
              >
                <span style="">
                  <span>
                    <!-- <img
                        src="../../static/icons/foot.png"
                        style="width: 35px"
                      /> -->
                    <v-icon size="40" style="margin-top: -10px"
                      >mdi mdi-account-group</v-icon
                    >
                  </span>
                  <span
                    style="
                      margin: auto;
                      padding: 0px;
                      margin-left: 0px;
                      font-weight: 300;
                      font-size: 50px;
                    "
                    >50%</span
                  >
                </span>
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="12" style="text-align: center">
                <span style="">
                  <span>
                    <img
                      src="../../static/icons/stats.png"
                      style="width: 15px"
                    />
                  </span>
                  <span
                    style="
                      margin: auto;
                      padding: 0px;
                      margin-left: 0px;
                      font-weight: 300;
                    "
                    >20% vs Yesterday</span
                  >
                </span>
              </v-col>
            </v-row>
          </v-card>
        </v-row>
      </v-col>
      <v-col lg="2" md="2" sm="12" xs="12">
        <v-row style="width: 100%; height: 150px">
          <v-card class="py-2" style="width: 100%">
            <div style="font-size: 20px; padding-left: 25px; font-size: 18px">
              Average Time Spent
            </div>
            <v-row style="height: 75px">
              <v-col
                cols="12"
                style="font-size: 50px; color: #c55a11; text-align: center"
              >
                <span style="">
                  <span>
                    <!-- <img
                        src="../../static/icons/foot.png"
                        style="width: 35px"
                      /> -->
                    <!-- <v-icon size="40" style="margin-top: -10px"
                        >mdi mdi-account-group</v-icon
                      > -->
                  </span>
                  <span
                    style="
                      margin: auto;
                      padding: 0px;
                      margin-left: 0px;
                      font-weight: 300;
                      font-size: 50px;
                    "
                    >00:45
                  </span>
                </span>
                <span style="font-size: 25px"> min</span>
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="6">
                <span style="text-align: left; padding-left: 10%">
                  <span>
                    <img
                      src="../../static/icons/stats-up.png"
                      style="width: 17px"
                    />
                  </span>
                  <span
                    style="
                      margin: auto;
                      padding: 0px;
                      margin-left: 0px;
                      font-weight: 300;
                    "
                    >01:15</span
                  >
                </span>
              </v-col>
              <v-col cols="6" style="text-align: right; padding-right: 10%">
                <span>
                  <span>
                    <img
                      src="../../static/icons/stats-down.png"
                      style="width: 17px"
                    />
                  </span>
                  <span
                    style="
                      margin: auto;
                      padding: 0px;
                      margin-left: 0px;
                      font-weight: 300;
                    "
                    >01:15</span
                  >
                </span>
              </v-col>
            </v-row>
          </v-card>
        </v-row>
      </v-col>
    </v-row>
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
    };
  },
  watch: {},
  mounted() {},
  async created() {
    this.branchList = await this.$store.dispatch("fetchDropDowns", {
      key: "branchList",
      endpoint: "branch-list",
      refresh: true,
    });

    const today = new Date();

    this.from_date = today.toISOString().slice(0, 10);
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
