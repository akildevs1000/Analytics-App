<template>
  <div style="width: 101%; margin-top: 4px">
    <v-row style="width: 101%; margin-right: 0px" class="pt-2">
      <v-col lg="3" md="3" sm="12" xs="12">
        <v-row style="width: 100%; height: 130px">
          <v-card class="py-2" style="width: 100%; border-radius: 8px">
            <v-row>
              <v-col cols="4" class="pl-6 text-center">
                <div style="width: 100px">
                  <div style="height: 70px">
                    <img
                      src="../../static/icons/inside.png"
                      style="width: 60px; padding-top: 10px"
                    />
                  </div>
                  <div
                    class="pt-2 statisticsPageFont"
                    style="
                      font-size: 20px;
                      font-family: OpenSansCondensedBold1 !important;
                    "
                  >
                    Inside
                  </div>
                </div>
              </v-col>

              <v-col cols="8" class="pl-6 text-center fontforNumber">
                <div style="width: 250px">
                  <div style="height: 70px; display: flex">
                    <div
                      style="
                        font-weight: bold;
                        width: 100%;
                        margin: auto;
                        font-size: 30px;
                      "
                    >
                      {{ data.live_total_count ?? "---" }}
                    </div>
                  </div>
                  <div class="pt-2" style="font-size: 23px; color: #a19d9d">
                    <div>
                      <span>{{ data.live_male_count ?? "---" }}</span>
                      <v-divider
                        class="ml-2 mr-2"
                        style="height: 13px; vertical-align: inherit"
                        vertical
                      ></v-divider>
                      <span>{{ data.live_female_count ?? "---" }}</span>
                      <v-divider
                        class="ml-2 mr-2"
                        style="height: 20px"
                        vertical
                      ></v-divider>
                      <span>{{ data.live_child_count ?? "---" }}</span>
                    </div>
                  </div>
                </div>
              </v-col>
            </v-row>
          </v-card>
        </v-row>
      </v-col>
      <v-col lg="3" md="3" sm="12" xs="12">
        <v-row style="width: 100%; height: 130px">
          <v-card class="py-2" style="width: 100%; border-radius: 8px">
            <v-row>
              <v-col cols="4" class="pl-6 text-center">
                <div style="width: 100px">
                  <div style="height: 70px">
                    <img
                      src="../../static/icons/ouside.png"
                      style="width: 60px; padding-top: 10px"
                    />
                  </div>
                  <div
                    class="pt-2 statisticsPageFont"
                    style="
                      font-size: 20px;
                      font-family: OpenSansCondensedBold1 !important;
                    "
                  >
                    Out
                  </div>
                </div>
              </v-col>

              <v-col cols="8" class="pl-6 text-center fontforNumber">
                <div style="width: 250px">
                  <div style="height: 70px; display: flex">
                    <div
                      style="
                        font-weight: bold;
                        width: 100%;
                        margin: auto;
                        font-size: 30px;
                      "
                    >
                      {{ data.out_total_count ?? "---" }}
                    </div>
                  </div>
                  <div class="pt-2" style="font-size: 23px; color: #a19d9d">
                    <div>
                      <span>{{ data.out_male_count ?? "---" }}</span>
                      <v-divider
                        class="ml-2 mr-2"
                        style="height: 13px; vertical-align: inherit"
                        vertical
                      ></v-divider>
                      <span>{{ data.out_female_count ?? "---" }}</span>
                      <v-divider
                        class="ml-2 mr-2"
                        style="height: 20px"
                        vertical
                      ></v-divider>
                      <span>{{ data.out_child_count ?? "---" }}</span>
                    </div>
                  </div>
                </div>
              </v-col>
            </v-row>
          </v-card>
        </v-row>
      </v-col>
      <v-col lg="2" md="2" sm="12" xs="12">
        <v-row style="width: 100%; height: 130px">
          <v-card class="py-2" style="width: 100%; border-radius: 8px">
            <v-row>
              <v-col cols="4" class="pl-6 text-center statisticsPageFont">
                <div style="width: 60px">
                  <div style="height: 70px">
                    <img
                      src="../../static/icons/footfall.png"
                      style="width: 60px; padding-top: 10px"
                    />
                  </div>
                  <div
                    class="pt-2"
                    style="
                      font-size: 20px;
                      font-family: OpenSansCondensedBold1 !important;
                    "
                  >
                    Total
                  </div>
                </div>
              </v-col>

              <v-col cols="8" class="pl-0 pr-0 text-center fontforNumber">
                <div style="width: 100%">
                  <div style="height: 70px; display: flex">
                    <div
                      style="
                        font-weight: bold;
                        width: 100%;
                        margin: auto;
                        font-size: 30px;
                      "
                    >
                      {{ data.total_footfall_count ?? "---" }}
                    </div>
                  </div>
                  <div class="pt-2" style="font-size: 23px; color: #a19d9d">
                    <div>
                      <span>{{ data.total_male_count ?? "---" }}</span>
                      <v-divider
                        class="ml-2 mr-2"
                        style="height: 13px; vertical-align: inherit"
                        vertical
                      ></v-divider>
                      <span>{{ data.total_female_count ?? "---" }}</span>
                      <v-divider
                        class="ml-2 mr-2"
                        style="height: 20px"
                        vertical
                      ></v-divider>
                      <span>{{ data.total_child_count ?? "---" }}</span>
                    </div>
                  </div>
                </div>
              </v-col>
            </v-row>
          </v-card>
        </v-row>
      </v-col>
      <v-col lg="2" md="2" sm="12" xs="12">
        <v-row style="width: 100%; height: 130px">
          <v-card class="py-2" style="width: 100%; border-radius: 8px">
            <v-row>
              <v-col cols="6" class="pl-6 text-center statisticsPageFont">
                <div style="width: 100%">
                  <div style="height: 70px">
                    <img
                      src="../../static/icons/occupancy.png"
                      style="width: 60px; padding-top: 10px"
                    />
                  </div>
                  <div
                    class="pt-2"
                    style="
                      font-size: 20px;
                      font-family: OpenSansCondensedBold1 !important;
                    "
                  >
                    Occupancy
                  </div>
                </div>
              </v-col>

              <v-col cols="6" class="pl-0 pr-0 text-center fontforNumber">
                <div style="width: 100%">
                  <div style="height: 70px; display: flex">
                    <div
                      style="
                        font-weight: bold;
                        width: 100%;
                        margin: auto;
                        font-size: 30px;
                      "
                    >
                      {{ data.room_occupancy_percentage ?? "---" }}%
                    </div>
                  </div>
                </div>
              </v-col>
            </v-row>
          </v-card>
        </v-row>
      </v-col>
      <v-col lg="2" md="2" sm="12" xs="12">
        <v-row style="width: 100%; height: 130px">
          <v-card class="py-2" style="width: 100%; border-radius: 8px">
            <v-row>
              <v-col cols="6" class="pl-6 text-center statisticsPageFont">
                <div style="width: 100%">
                  <div style="height: 70px">
                    <img
                      src="../../static/icons/timespent.png"
                      style="width: 60px; padding-top: 10px"
                    />
                  </div>
                  <div
                    class="pt-2"
                    style="
                      font-size: 19px;
                      font-family: OpenSansCondensedBold1 !important;
                    "
                  >
                    Avg.Time
                  </div>
                </div>
              </v-col>

              <v-col
                cols="6"
                class="pl-0 pr-0 text-center fontforNumber"
                style="padding: 0px"
              >
                <div style="width: 100%">
                  <div style="height: 87px; display: flex">
                    <div
                      style="
                        font-weight: bold;
                        width: 100%;
                        margin: auto;
                        font-size: 30px;
                      "
                    >
                      {{
                        data.avg_time_spent
                          ? $dateFormat.minutesToHHMM(
                              Math.round(data.avg_time_spent)
                            )
                          : "---"
                      }}
                    </div>
                  </div>
                  <div class="pt-2" style="font-size: 20px; color: #a19d9d">
                    <div>
                      <span>{{
                        data.min_time_spent
                          ? $dateFormat.minutesToHHMM(data.min_time_spent)
                          : "---"
                      }}</span>
                      <v-divider
                        class="ml-0 mr-0"
                        style="height: 13px; vertical-align: inherit"
                        vertical
                      ></v-divider>
                      <span>{{
                        data.avg_time_spent
                          ? $dateFormat.minutesToHHMM(data.max_time_spent)
                          : "---"
                      }}</span>
                    </div>
                  </div>
                </div>
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
