<template>
  <div>
    <v-row>
      <v-col cols="8" style=""></v-col>
      <v-col cols="4" class="pull-end" style="float: right">
        <v-spacer></v-spacer>
        <v-row>
          <v-col cols="4">
            <v-select
              v-if="isCompany"
              label="Branch"
              outlined
              dense
              small
              v-model="branch_id"
              x-small
              :items="[{ id: null, branch_name: `All Branches` }, ...branches]"
              item-value="id"
              item-text="branch_name"
              :hide-details="true"
            ></v-select>
          </v-col>
          <v-col cols="4" style="padding-left: 0px">
            <CustomFilter
              @filter-attr="filterAttr"
              :default_date_from="date_from"
              :default_date_to="date_to"
              :defaultFilterType="1"
              :height="'40px '"
            />
          </v-col>
          <!-- <v-col>
            <v-select
              v-if="isCompany"
              @change="getDataFromApi()"
              label="Days"
              outlined
              dense
              small
              v-model="dayFilter"
              x-small
              :items="[
                { id: '', branch_name: `All Days` },
                { id: 'holidays', branch_name: `Only Holidays` },
                { id: 'weekends', branch_name: `Only Weekends` },
              ]"
              item-value="id"
              item-text="branch_name"
              :hide-details="true"
            ></v-select>
          </v-col> -->

          <v-col cols="4">
            <v-btn
              style="margin-left: 25px"
              @click="getDataFromApi()"
              color="primary"
              primary
              fill
              >Submit
            </v-btn>
          </v-col>
        </v-row>
      </v-col>
    </v-row>
    <v-row style="min-height: 800px"
      ><v-col cols="6">
        <v-card
          ><v-card-text>
            <h3>Weekly Chart - Weekdays</h3>

            <v-row>
              <v-col cols="7">
                <div
                  v-if="totalCountWeekDay == 0"
                  style="margin: auto; padding: 15%"
                >
                  No Data
                </div>

                <div
                  :style="'display:' + totalCountWeekDay > 0 ? 'block' : 'none'"
                >
                  <div
                    :id="weekdaysChartName"
                    style="width: 100%"
                    :key="key"
                  ></div>
                </div>
              </v-col>
              <v-col cols="5" style="font-family: sans-serif; font-size: 20px">
                <div
                  style="
                    height: 50px;
                    width: 300px;
                    background-color: #db4437;
                    margin: 5px;
                  "
                >
                  <v-row style="color: #fff">
                    <v-col cols="2" class="pt-1 pl-5" style="width: 100%">
                      <span>
                        <img
                          src="../../static/icons/male51.png"
                          style="width: 35px"
                        />
                      </span>
                    </v-col>
                    <v-col
                      cols="5"
                      class="pt-1 pl-5"
                      style="width: 100%; margin: auto"
                    >
                      Male
                    </v-col>
                    <v-col cols="1" class="pt-1">
                      <v-divider color="white" vertical></v-divider>
                    </v-col>
                    <v-col
                      cols="4"
                      class="pt-1 pr-4"
                      style="
                        font-family: sans-serif;
                        text-align: center;
                        margin: auto;
                      "
                    >
                      {{ data.WeekDay?.Male ?? "---" }}</v-col
                    >
                  </v-row>
                </div>
                <div
                  style="
                    height: 50px;
                    width: 300px;
                    background-color: #0f9d58;
                    margin: 5px;
                    margin-top: 17px;
                  "
                >
                  <v-row style="color: #fff">
                    <v-col cols="2" class="pt-1 pl-5" style="width: 100%">
                      <span>
                        <img
                          src="../../static/icons/female51.png"
                          style="width: 35px"
                        />
                      </span>
                    </v-col>
                    <v-col
                      cols="5"
                      class="pt-1 pl-5"
                      style="width: 100%; margin: auto"
                      >Female
                    </v-col>
                    <v-col cols="1" class="pt-1">
                      <v-divider color="white" vertical></v-divider>
                    </v-col>
                    <v-col
                      cols="4"
                      class="pt-1 pr-4"
                      style="
                        font-family: sans-serif;
                        text-align: center;
                        margin: auto;
                      "
                    >
                      {{ data.WeekDay?.Female ?? "---" }}</v-col
                    >
                  </v-row>
                </div>
                <div
                  style="
                    height: 50px;
                    width: 300px;
                    background-color: #4285f4;
                    margin: 5px;
                    margin-top: 17px;
                  "
                >
                  <v-row style="color: #fff">
                    <v-col cols="2" class="pt-1 pl-5" style="width: 100%">
                      <span>
                        <img
                          src="../../static/icons/kids51.png"
                          style="width: 30px"
                        />
                      </span>
                    </v-col>
                    <v-col
                      cols="5"
                      class="pt-1 pl-5"
                      style="width: 100%; margin: auto"
                      >Kids
                    </v-col>
                    <v-col cols="1" class="pt-1">
                      <v-divider color="white" vertical></v-divider>
                    </v-col>
                    <v-col
                      cols="4"
                      class="pt-1 pr-4"
                      style="
                        font-family: sans-serif;
                        text-align: center;
                        margin: auto;
                      "
                    >
                      {{ data.WeekDay?.Child ?? "---" }}</v-col
                    >
                  </v-row>
                </div>
              </v-col>
            </v-row>
          </v-card-text></v-card
        >
      </v-col>
      <v-col cols="6">
        <v-card
          ><v-card-text
            ><h3>Weekly Chart - Weekend</h3>

            <v-row>
              <v-col cols="7">
                <div
                  v-if="totalCountWeekEnd == 0"
                  style="margin: auto; padding: 15%"
                >
                  No Data
                </div>

                <div
                  :style="'display:' + totalCountWeekEnd > 0 ? 'block' : 'none'"
                >
                  <div
                    :id="weekendsChartName"
                    style="width: 100%"
                    :key="key"
                  ></div>
                </div>
              </v-col>
              <v-col cols="5" style="font-family: sans-serif; font-size: 20px">
                <div
                  style="
                    height: 50px;
                    width: 300px;
                    background-color: #db4437;
                    margin: 5px;
                  "
                >
                  <v-row style="color: #fff">
                    <v-col cols="2" class="pt-1 pl-5" style="width: 100%">
                      <span>
                        <img
                          src="../../static/icons/male51.png"
                          style="width: 35px"
                        />
                      </span>
                    </v-col>
                    <v-col
                      cols="5"
                      class="pt-1 pl-5"
                      style="width: 100%; margin: auto"
                    >
                      Male
                    </v-col>
                    <v-col cols="1" class="pt-1">
                      <v-divider color="white" vertical></v-divider>
                    </v-col>
                    <v-col
                      cols="4"
                      class="pt-1 pr-4"
                      style="
                        font-family: sans-serif;
                        text-align: center;
                        margin: auto;
                      "
                    >
                      {{ data.WeekEnd?.Male ?? "---" }}</v-col
                    >
                  </v-row>
                </div>
                <div
                  style="
                    height: 50px;
                    width: 300px;
                    background-color: #0f9d58;
                    margin: 5px;
                    margin-top: 17px;
                  "
                >
                  <v-row style="color: #fff">
                    <v-col cols="2" class="pt-1 pl-5" style="width: 100%">
                      <span>
                        <img
                          src="../../static/icons/female51.png"
                          style="width: 35px"
                        />
                      </span>
                    </v-col>
                    <v-col
                      cols="5"
                      class="pt-1 pl-5"
                      style="width: 100%; margin: auto"
                      >Female
                    </v-col>
                    <v-col cols="1" class="pt-1">
                      <v-divider color="white" vertical></v-divider>
                    </v-col>
                    <v-col
                      cols="4"
                      class="pt-1 pr-4"
                      style="
                        font-family: sans-serif;
                        text-align: center;
                        margin: auto;
                      "
                    >
                      {{ data.WeekEnd?.Female ?? "---" }}</v-col
                    >
                  </v-row>
                </div>
                <div
                  style="
                    height: 50px;
                    width: 300px;
                    background-color: #4285f4;
                    margin: 5px;
                    margin-top: 17px;
                  "
                >
                  <v-row style="color: #fff">
                    <v-col cols="2" class="pt-1 pl-5" style="width: 100%">
                      <span>
                        <img
                          src="../../static/icons/kids51.png"
                          style="width: 30px"
                        />
                      </span>
                    </v-col>
                    <v-col
                      cols="5"
                      class="pt-1 pl-5"
                      style="width: 100%; margin: auto"
                      >Kids
                    </v-col>
                    <v-col cols="1" class="pt-1">
                      <v-divider color="white" vertical></v-divider>
                    </v-col>
                    <v-col
                      cols="4"
                      class="pt-1 pr-4"
                      style="
                        font-family: sans-serif;
                        text-align: center;
                        margin: auto;
                      "
                    >
                      {{ data.WeekEnd?.Child ?? "---" }}</v-col
                    >
                  </v-row>
                </div>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </div>
</template>

<script>
// import VueApexCharts from 'vue-apexcharts'
export default {
  data() {
    return {
      isCompany: true,
      data: {},

      key: 1,
      branch_id: null,
      weekdaysChartName: "apexDashboardWeekDaysChart",
      weekendsChartName: "apexDashboardWeekendsChart",

      filterDeviceId: null,
      devices: [],
      loading: false,
      display_title: "Alarm Events",
      filterType: "",
      filterINOut: "in",
      filterDuration: null,
      branches: [],
      chartOptions1: {
        series: [],
        colors: ["#DB4437", "#0F9D58", "#4285F4"],
        labels: ["Male", "Female", "Child"],
        // labels: {
        //   show: true,
        // },
        toolbar: {
          show: false,
        },

        chart: {
          type: "pie",
          width: "100%",
        },
        dataLabels: {
          enabled: true,
        },
        legend: {
          show: false,
        },
      },
      chartOptionsWeekEnd: {
        series: [],
        colors: ["#DB4437", "#0F9D58", "#4285F4"],
        labels: ["Male", "Female", "Child"],
        // labels: {
        //   show: true,
        // },
        toolbar: {
          show: false,
        },

        chart: {
          type: "pie",
          width: "100%",
        },
        dataLabels: {
          enabled: true,
        },
        legend: {
          show: false,
        },
      },
      totalCountWeekDay: 1,
      totalCountWeekEnd: 1,
      ApexCharts1: {},
      ApexChartsWeekEnd: {},
    };
  },
  watch: {},
  mounted() {
    setTimeout(() => {
      this.loadCharts();
      this.getDataFromApi();
    }, 1000 * 3);
  },
  async created() {
    // Get today's date
    let today = new Date();
    // Subtract 7 days from today
    let sevenDaysAgo = new Date(today);
    sevenDaysAgo.setDate(today.getDate() - 7);
    // Format the dates (optional)
    this.date_to = today.toISOString().split("T")[0];
    this.date_from = sevenDaysAgo.toISOString().split("T")[0];
    // this.ApexCharts1 = new ApexCharts(
    //   document.querySelector("#" + this.weekdaysChartName),
    //   this.chartOptions1
    // ).render();
    this.getBranches();
  },

  methods: {
    loadCharts() {
      this.ApexCharts1 = new ApexCharts(
        document.querySelector("#" + this.weekdaysChartName),
        this.chartOptions1
      );
      this.ApexCharts1.render();

      //
      this.ApexChartsWeekEnd = new ApexCharts(
        document.querySelector("#" + this.weekendsChartName),
        this.chartOptionsWeekEnd
      );
      this.ApexChartsWeekEnd.render();
    },
    getBranches() {
      if (this.$auth.user.branch_id) {
        this.branch_id = this.$auth.user.branch_id;
        this.isCompany = false;
        return;
      }

      this.$axios
        .get("branch", {
          params: {
            per_page: 1000,
            company_id: this.$auth.user.company_id,
          },
        })
        .then(({ data }) => {
          this.branches = data.data;
        });
    },
    getDeviceList() {
      let options = {
        params: {
          company_id: this.$auth.user.company_id,
          branch_id: this.branch_id,
        },
      };
      this.$axios.get(`/device_list`, options).then(({ data }) => {
        this.devices = data;
      });
    },
    filterDevice() {
      this.getDataFromApi();
    },
    filterAttr(data) {
      this.date_from = data.from;
      this.date_to = data.to;

      //this.getDataFromApi();
    },
    async getDataFromApi() {
      this.loading = true;

      let options = {
        params: {
          company_id: this.$auth.user.company_id,
          branch_id: this.branch_id,
          from_date: this.date_from,
          to_date: this.date_to,
        },
      };

      await this.$axios
        .get(`/customers-stats-between-dates`, options)
        .then(({ data }) => {
          this.data = data;

          try {
            this.renderChart1(data);
          } catch (e) {}
        });
    },
    async renderChart1(data) {
      let counter = 0;

      this.chartOptions1.series = [
        parseInt(data.WeekDay.Male),
        parseInt(data.WeekDay.Female),
        parseInt(data.WeekDay.Child),
      ];

      this.totalCountWeekDay =
        parseInt(data.WeekDay.Male) +
        parseInt(data.WeekDay.Female) +
        parseInt(data.WeekDay.Child);
      this.ApexCharts1.updateOptions(this.chartOptions1);

      //
      this.chartOptionsWeekEnd.series = [
        parseInt(data.WeekEnd.Male),
        parseInt(data.WeekEnd.Female),
        parseInt(data.WeekEnd.Child),
      ];

      this.totalCountWeekEnd =
        parseInt(data.WeekEnd.Male) +
        parseInt(data.WeekEnd.Female) +
        parseInt(data.WeekEnd.Child);

      try {
        this.ApexChartsWeekEnd.updateOptions(this.chartOptionsWeekEnd);
      } catch (e) {
        setTimeout(() => {
          this.loadCharts();
          setTimeout(() => {
            this.ApexChartsWeekEnd.updateOptions(this.chartOptionsWeekEnd);
          }, 1000 * 2);
        }, 1000 * 2);
      }

      this.loading = false;
    },
  },
};
</script>
