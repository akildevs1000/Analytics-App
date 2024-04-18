<template>
  <div style="width: 101%">
    <v-row style="width: 101%; margin-right: 0px" class="mt-8">
      <v-col cols="12" class="pa-0 ma-0">
        <v-row class="text-center pt-0">
          <v-col cols="4" class="text-center pa-0">
            <div
              class="text-center"
              style="width: 65%; min-width: 170px; margin: auto; color: #01b0f0"
            >
              <v-row class="text-center">
                <v-col cols="12">
                  <span style="float: left; width: 30%; height: 65px">
                    <img
                      src="../../static/icons/male61.png"
                      style="width: 58px"
                    />
                  </span>

                  <span style="float: left; width: 70%">
                    <div style="font-size: 30px">
                      {{ data.total_male_count ?? "---" }}
                      <span style="font-size: 14px"
                        >{{ data.male_percentage }}%
                      </span>
                    </div>
                    <div
                      style="font-size: 12px; text-align: left; color: #a3a3a3"
                    >
                      <img
                        v-if="data.male_percentage_yesterday > 0"
                        src="../../static/icons/up1.png"
                        style="width: 20px"
                      /><img
                        v-if="data.male_percentage_yesterday <= 0"
                        src="../../static/icons/down1.png"
                        style="width: 20px"
                      />
                      {{ data.male_percentage_yesterday ?? "---" }}% vs
                      Yesterday
                    </div>
                  </span>
                </v-col>
              </v-row>
              <!-- <v-row class="text-center">
                <v-col cols="12">
                  <span style="float: left; width: 100%">
                    <div>
                      <span style="font-size: 22px; font-weight: 300">
                        {{ data.total_male_count ?? "---" }}
                      </span>
                      <span style="font-size: 14px"
                        >{{ data.male_percentage ?? "---" }}%</span
                      >
                    </div>
                    <div style="font-size: 12px">TOTAL MALE</div>
                  </span>
                </v-col>
              </v-row> -->
              <v-row>
                <v-col cols="12" style="padding-left: 0px">
                  <DashboardDemographicsMaleChart
                    style="padding: 0px; width: 100%; margin-left: -0%"
                    :data="data"
                    :height="200"
                  />
                </v-col>
              </v-row>
              <!-- <v-row style="font-size: 12px">
                <v-col>
                  <v-row class="text-center">
                    <v-col
                      cols="2"
                      class="pb-0"
                      style="padding-left: 0px; padding-right: 0px"
                    >
                      <v-icon color="#DB4437">mdi mdi-checkbox-blank</v-icon>
                    </v-col>
                    <v-col cols="4" class="pb-0 text-left" style="margin: auto"
                      >Young
                    </v-col>
                    <v-col cols="3" class="pb-0 pl-0 pr-0" style="margin: auto"
                      >{{ data.male_younger_count }}
                    </v-col>
                    <v-col cols="3" class="pb-0 pl-0 pr-0" style="margin: auto"
                      >{{
                        data.total_male_count > 0
                          ? roundToTwo(
                              (data.male_younger_count * 100) /
                                data.total_male_count
                            )
                          : "---"
                      }}%
                    </v-col>
                  </v-row>
                  <v-row class="text-center">
                    <v-col
                      cols="2"
                      class="pb-0"
                      style="padding-left: 0px; padding-right: 0px"
                    >
                      <v-icon color="#0F9D58"
                        >mdi mdi-checkbox-blank</v-icon
                      ></v-col
                    >
                    <v-col cols="4" class="pb-0 text-left" style="margin: auto"
                      >Mid Age
                    </v-col>
                    <v-col cols="3" class="pb-0 pl-0 pr-0" style="margin: auto"
                      >{{ data.male_adult_count }}
                    </v-col>
                    <v-col cols="3" class="pb-0 pl-0 pr-0" style="margin: auto"
                      >{{
                        data.total_male_count > 0
                          ? roundToTwo(
                              (data.male_adult_count * 100) /
                                data.total_male_count
                            )
                          : "---"
                      }}%
                    </v-col>
                  </v-row>
                  <v-row class="text-center">
                    <v-col
                      cols="2"
                      style="padding-left: 0px; padding-right: 0px"
                    >
                      <v-icon color="#F4B400"
                        >mdi mdi-checkbox-blank</v-icon
                      ></v-col
                    >
                    <v-col
                      cols="4"
                      class="pb-0 text-left pt-0"
                      style="margin: auto"
                      >Senior
                    </v-col>
                    <v-col cols="3" class="p0" style="margin: auto"
                      >{{ data.male_senior_count }}
                    </v-col>
                    <v-col cols="3" class="p0" style="margin: auto"
                      >{{
                        data.total_male_count > 0
                          ? roundToTwo(
                              (data.male_senior_count * 100) /
                                data.total_male_count
                            )
                          : "---"
                      }}%
                    </v-col>
                  </v-row>
                </v-col>
              </v-row> -->

              <!-- <v-row class="text-center">
                <v-col cols="4" style="padding-left: 0px; padding-right: 0px">
                  <span style="float: left; width: 100%">
                    <div>
                      <span style="font-size: 22px; font-weight: 300"
                        >{{ data.male_younger_count ?? "---" }}
                      </span>
                      <span style="font-size: 14px"
                        >{{
                          data.total_male_count > 0
                            ? Math.round(
                                (data.male_younger_count * 100) /
                                  data.total_male_count
                              )
                            : "---"
                        }}%</span
                      >
                    </div>
                    <div style="font-size: 12px">Young</div>
                    <div style="font-size: 10px; color: #a6a6a6">
                      15- 25 YEARS
                    </div>
                  </span>
                </v-col>

                <v-col cols="4" style="padding-left: 0px; padding-right: 0px">
                  <span style="float: left; width: 100%">
                    <div>
                      <span style="font-size: 22px; font-weight: 300">
                        {{ data.male_adult_count ?? "---" }}
                      </span>
                      <span style="font-size: 14px"
                        >{{
                          data.total_male_count > 0
                            ? Math.round(
                                (data.male_adult_count * 100) /
                                  data.total_male_count
                              )
                            : "---"
                        }}%</span
                      >
                    </div>
                    <div style="font-size: 12px">Adult</div>
                    <div style="font-size: 10px; color: #a6a6a6">
                      26-59 YEARS
                    </div>
                  </span>
                </v-col>

                <v-col cols="4" style="padding-left: 0px; padding-right: 0px">
                  <span style="float: left; width: 100%">
                    <div>
                      <span style="font-size: 22px; font-weight: 300">
                        {{ data.male_senior_count ?? "---" }}
                      </span>
                      <span style="font-size: 14px"
                        >{{
                          data.total_male_count > 0
                            ? Math.round(
                                (data.male_senior_count * 100) /
                                  data.total_male_count
                              )
                            : "---"
                        }}%</span
                      >
                    </div>
                    <div style="font-size: 12px">Senior</div>
                    <div style="font-size: 10px; color: #a6a6a6">>60 YEARS</div>
                  </span>
                </v-col>
              </v-row> -->
            </div>
          </v-col>
          <v-col cols="4" class="text-center pa-0">
            <div
              class="text-center"
              style="width: 65%; min-width: 170px; margin: auto; color: #f75b95"
            >
              <v-row class="text-center">
                <v-col cols="12">
                  <span style="float: left; width: 30%; height: 65px">
                    <img
                      src="../../static/icons/female61.png"
                      style="width: 58px"
                    />
                  </span>

                  <span style="float: left; width: 70%">
                    <div style="font-size: 30px">
                      {{ data.total_female_count ?? "---" }}

                      <span style="font-size: 14px"
                        >{{ data.female_percentage }}%
                      </span>
                    </div>
                    <div
                      style="font-size: 12px; text-align: left; color: #a3a3a3"
                    >
                      <img
                        v-if="data.female_percentage_yesterday > 0"
                        src="../../static/icons/up1.png"
                        style="width: 20px"
                      /><img
                        v-if="data.female_percentage_yesterday <= 0"
                        src="../../static/icons/down1.png"
                        style="width: 20px"
                      />
                      {{ data.female_percentage_yesterday ?? "---" }}% vs
                      Yesterday
                    </div>
                  </span>
                </v-col>
              </v-row>

              <v-row>
                <v-col cols="12" style="padding-left: 0px">
                  <DashboardDemographicsFeMaleChart
                    style="padding: 0px; width: 100%; margin-left: -20%"
                    :data="data"
                    :height="200"
                  />
                </v-col>
              </v-row>
              <!-- <v-row>
                <v-col cols="4" style="padding-left: 0px; padding-right: 0px">
                  <span style="float: left; width: 100%">
                    <div>
                      <span style="font-size: 22px; font-weight: 300">
                        {{ data.female_younger_count ?? "---" }}
                      </span>
                      <span style="font-size: 14px">
                        {{
                          data.total_male_count > 0
                            ? Math.round(
                                (data.female_younger_count * 100) /
                                  data.total_female_count
                              )
                            : "---"
                        }}%</span
                      >
                    </div>
                    <div style="font-size: 12px">Young</div>
                    <div style="font-size: 10px; color: #a6a6a6">
                      15- 25 YEARS
                    </div>
                  </span>
                </v-col>

                <v-col cols="4" style="padding-left: 0px; padding-right: 0px">
                  <span style="float: left; width: 100%">
                    <div>
                      <span style="font-size: 22px; font-weight: 300"
                        >{{ data.female_adult_count ?? "---" }}
                      </span>
                      <span style="font-size: 14px">
                        {{
                          data.total_female_count > 0
                            ? Math.round(
                                (data.female_adult_count * 100) /
                                  data.total_female_count
                              )
                            : "---"
                        }}%</span
                      >
                    </div>
                    <div style="font-size: 12px">Adult</div>
                    <div style="font-size: 10px; color: #a6a6a6">
                      26-59 YEARS
                    </div>
                  </span>
                </v-col>

                <v-col cols="4" style="padding-left: 0px; padding-right: 0px">
                  <span style="float: left; width: 100%">
                    <div>
                      <span style="font-size: 22px; font-weight: 300"
                        >{{ data.female_senior_count ?? "---" }}
                      </span>
                      <span style="font-size: 14px"
                        >{{
                          data.total_male_count > 0
                            ? Math.round(
                                (data.female_senior_count * 100) /
                                  data.total_female_count
                              )
                            : "---"
                        }}%</span
                      >
                    </div>
                    <div style="font-size: 12px">Senior</div>
                    <div style="font-size: 10px; color: #a6a6a6">>60 YEARS</div>
                  </span>
                </v-col>
              </v-row> -->
            </div>
          </v-col>
          <v-col cols="4" class="text-center pa-0">
            <div
              class="text-center"
              style="width: 65%; min-width: 170px; margin: auto"
            >
              <v-row class="text-center">
                <v-col cols="12">
                  <span style="float: left; width: 30%; height: 65px">
                    <img
                      src="../../static/icons/kids61.png"
                      style="width: 65px"
                    />
                  </span>

                  <span style="float: left; width: 70%">
                    <div style="font-size: 30px">
                      {{ data.total_child_count ?? "---" }}

                      <span style="font-size: 14px"
                        >{{ data.child_percentage }}%
                      </span>
                    </div>
                    <div
                      style="font-size: 12px; text-align: left; color: #a3a3a3"
                    >
                      <img
                        v-if="data.female_percentage_yesterday > 0"
                        src="../../static/icons/up1.png"
                        style="width: 20px"
                      /><img
                        v-if="data.female_percentage_yesterday <= 0"
                        src="../../static/icons/down1.png"
                        style="width: 20px"
                      />
                      {{ data.child_percentage_yesterday ?? "---" }}% vs
                      Yesterday
                    </div>
                  </span>
                </v-col>
              </v-row>

              <v-row>
                <v-col cols="12" style="padding-left: 0px">
                  <DashboardDemographicsKidsChart
                    style="padding: 0px; width: 100%; margin-left: -20%"
                    :data="data"
                    :height="200"
                  />
                </v-col>
              </v-row>
              <!-- <v-row class="text-center">
                <v-col cols="6">
                  <span style="float: left; width: 100%">
                    <div>
                      <span style="font-size: 22px; font-weight: 300"
                        >{{ data.child_female_count ?? "---" }}
                      </span>
                      <span style="font-size: 14px"
                        >{{
                          data.total_child_count > 0
                            ? Math.round(
                                (data.child_female_count * 100) /
                                  data.total_child_count
                              )
                            : "---"
                        }}%</span
                      >
                    </div>
                    <div style="font-size: 12px">Female</div>
                    <div style="font-size: 10px; color: #a6a6a6"><15 YEARS</div>
                  </span>
                </v-col>

                <v-col cols="6">
                  <span style="float: left; width: 100%">
                    <div>
                      <span style="font-size: 22px; font-weight: 300"
                        >{{ data.child_male_count ?? "---" }}
                      </span>
                      <span style="font-size: 14px"
                        >{{
                          data.total_child_count > 0
                            ? Math.round(
                                (data.child_male_count * 100) /
                                  data.total_child_count
                              )
                            : "---"
                        }}%</span
                      >
                    </div>
                    <div style="font-size: 12px">Male</div>
                    <div style="font-size: 10px; color: #a6a6a6"><15 YEARS</div>
                  </span>
                </v-col>
              </v-row> -->
            </div>
          </v-col>
        </v-row>

        <v-row class="pt-5">
          <v-col cols="12" style="padding-left: 5%; padding-right: 5%">
            <table style="width: 100%; font-size: 16px" class="tablebordertop">
              <tr>
                <td style="width: 50px">
                  <v-icon color="#DB4437">mdi mdi-checkbox-blank</v-icon>
                </td>
                <td>Young</td>
                <td>{{ data.male_younger_count }}</td>
                <td>
                  {{
                    data.total_male_count > 0
                      ? roundToTwo(
                          (data.male_younger_count * 100) /
                            data.total_male_count
                        )
                      : "---"
                  }}%
                </td>
                <td></td>
                <td>Young</td>
                <td>{{ data.female_younger_count }}</td>
                <td>
                  {{
                    data.total_female_count > 0
                      ? roundToTwo(
                          (data.female_younger_count * 100) /
                            data.total_female_count
                        )
                      : "---"
                  }}%
                </td>
                <td></td>
                <td>Girls</td>
                <td>{{ data.child_female_count }}</td>
                <td>
                  {{
                    data.total_child_count > 0
                      ? roundToTwo(
                          (data.child_female_count * 100) /
                            data.total_child_count
                        )
                      : "---"
                  }}%
                </td>
              </tr>
              <tr>
                <td style="width: 50px">
                  <v-icon color="#0F9D58">mdi mdi-checkbox-blank</v-icon>
                </td>
                <td>Mid Age</td>
                <td>{{ data.male_adult_count }}</td>
                <td>
                  {{
                    data.total_male_count > 0
                      ? roundToTwo(
                          (data.male_adult_count * 100) / data.total_male_count
                        )
                      : "---"
                  }}%
                </td>
                <td></td>
                <td>Mid Age</td>
                <td>{{ data.female_adult_count }}</td>
                <td>
                  {{
                    data.total_female_count > 0
                      ? roundToTwo(
                          (data.female_adult_count * 100) /
                            data.total_female_count
                        )
                      : "---"
                  }}%
                </td>
                <td></td>

                <td>Boys</td>
                <td>{{ data.child_male_count }}</td>
                <td>
                  {{
                    data.total_child_count > 0
                      ? roundToTwo(
                          (data.child_male_count * 100) / data.total_child_count
                        )
                      : "---"
                  }}%
                </td>
              </tr>
              <tr class="last-row">
                <td style="width: 50px">
                  <v-icon color="#F4B400">mdi mdi-checkbox-blank</v-icon>
                </td>
                <td>Senior</td>
                <td>{{ data.male_senior_count }}</td>
                <td>
                  {{
                    data.total_male_count > 0
                      ? roundToTwo(
                          (data.male_senior_count * 100) / data.total_male_count
                        )
                      : "---"
                  }}%
                </td>
                <td></td>
                <td>Senior</td>
                <td>{{ data.female_senior_count }}</td>
                <td>
                  {{
                    data.total_female_count > 0
                      ? roundToTwo(
                          (data.female_senior_count * 100) /
                            data.total_female_count
                        )
                      : "---"
                  }}%
                </td>
                <td></td>

                <td>---</td>
                <td>---</td>
                <td>---</td>
              </tr>
            </table>
          </v-col>
        </v-row>
      </v-col>
      <v-col cols="6"> </v-col>
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
    roundToTwo(num) {
      return +(Math.round(num + "e+2") + "e-2");
    },
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
