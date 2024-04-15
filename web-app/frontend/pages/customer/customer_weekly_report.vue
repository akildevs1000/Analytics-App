<template>
  <div
    style="width: 100%"
    v-if="can('dashboard_access') && can('dashboard_view')"
  >
    <!-- <table style="width: 100%">
      <tr v-for="item2 in [1, 2, 3, 4, 5]">
        <td v-for="item in [1, 2, 3, 4, 5]">{{ item2 }}-{{ item }}</td>
      </tr>
    </table> -->
    <v-card class="py-2 pl-2">
      <v-row>
        <v-col cols="6" style=""><h3>Weekly Analysis</h3></v-col>
        <v-col cols="6" class="pull-end" style="float: right">
          <v-spacer></v-spacer>
          <v-row>
            <v-col>
              <v-select
                v-if="isCompany"
                label="Branch"
                outlined
                dense
                small
                v-model="branch_id"
                x-small
                :items="[
                  { id: null, branch_name: `All Branches` },
                  ...branches,
                ]"
                item-value="id"
                item-text="branch_name"
                :hide-details="true"
              ></v-select>
            </v-col>
            <v-col style="padding-left: 0px">
              <CustomFilter
                @filter-attr="filterAttr"
                :default_date_from="date_from"
                :default_date_to="date_to"
                :defaultFilterType="1"
                :height="'40px '"
                :maximum_days="10"
              />
            </v-col>
            <v-col>
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
            </v-col>

            <v-col>
              <v-btn
                style="margin-left: 5px"
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
      <v-row style="font-size: 14px; margin-top: 0px; padding-top: 0px">
        <v-col cols="11" class="pt-1" style="overflow-x: auto">
          <table style="width: 100%" class="weekly-report-table">
            <tr v-for="(counts, index) in data">
              <td style="font-size: 12px">
                {{ hours ? hours[counts.hour] : "---" }}
              </td>
              <td
                :style="'text-align:center;  ' + getColor(count)"
                v-for="count in counts.value"
              >
                {{ count }}
              </td>
              <td style="font-weight: bold; text-align: center">
                {{ hourTotals[index] }}
              </td>
            </tr>
            <tr
              style="
                font-weight: bold;
                text-align: center;
                background-color: #adadad;
              "
            >
              <td>Total</td>
              <td
                style="text-align: center"
                v-for="(date, index) in dates"
                v-if="filterDays(date)"
              >
                <div v-if="dateTotals[index] >= 0">
                  {{ dateTotals[index] ?? "--" }}
                </div>
                <div v-else>0</div>
              </td>
              <td>Total</td>
            </tr>

            <tr>
              <td></td>
              <td
                style="text-align: center"
                v-for="(date, index) in dates"
                v-if="filterDays(date)"
              >
                {{ $dateFormat.format_date66(date) }}
                <span style="font-size: 10px"
                  >({{ $dateFormat.getDayName(date) }})</span
                >
                <div>
                  <span style="color: blueviolet">
                    {{ holidaysList[date] ?? " " }}
                  </span>
                  <span style="color: brown">
                    {{
                      weekendsList[$dateFormat.getDayFullName(date)]
                        ? "Weekend "
                        : " "
                    }}
                  </span>
                </div>
              </td>
              <td></td>
            </tr>
          </table>
        </v-col>

        <v-col>
          <div
            style="width: 80px"
            v-for="colorRow in colorCodes"
            v-if="colorCodes.length > 0"
          >
            <div
              :style="
                'text-align:center;margin:auto;padding:0px;width: 80px;height:50px;color:#FFF; background-color:' +
                colorRow.color +
                ';height:' +
                360 / colorCodes.length +
                'px'
              "
            >
              {{ colorRow.min }} to {{ colorRow.max }}
            </div>
          </div>
        </v-col>
      </v-row>
    </v-card>

    <v-row class="mt-1">
      <v-col cols="12">
        <v-card
          class="py-2 pl-2 text-center align=center"
          style="text-align: center"
        >
          <div :id="name" style="width: 100%; height: 200px"></div>
        </v-card>
      </v-col>
    </v-row>
  </div>

  <NoAccess v-else />
</template>

<script>
// import DashboardlastMultiStatistics from "../../components/dashboard2/DashboardlastMultiStatistics.vue";
export default {
  components: {},
  data() {
    return {
      dayFilter: "",
      isCompany: true,
      branch_id: null,
      branches: [],
      hourTotals: [],
      dateTotals: [],
      name: "chart1",
      data: [],
      colorCodes: [],
      hours: [],
      dates: [],
      ApexCharts1: null,
      date_to: "",
      date_from: "",
      holidaysList: [],
      weekendsList: [],
      chartOptions1: {
        series: [
          {
            name: "Male",
            data: [],
          },
          {
            name: "Female",
            data: [],
          },
          {
            name: "Child",
            data: [],
          },
        ],
        dataLabels: {
          enabled: true,
        },
        colors: ["#01b0f0", "#f75b95", "#16b16d"],
        chart: {
          toolbar: {
            show: false,
          },
          height: 200,
          type: "line",
          zoom: {
            enabled: false,
          },
        },

        stroke: {
          curve: "straight",
        },
        title: {},
        grid: {
          row: {
            colors: ["#f3f3f3", "transparent"], // takes an array which will be repeated on columns
            opacity: 0.5,
          },
        },
        xaxis: {
          categories: [],
        },
      },
    };
  },
  watch: {},
  mounted() {
    setTimeout(() => {
      this.ApexCharts1 = new ApexCharts(
        document.querySelector("#" + this.name),
        this.chartOptions1
      ); //.render();
      this.ApexCharts1.render();
    }, 2000);
  },
  created() {
    let today = new Date();

    // Subtract 7 days from today
    let sevenDaysAgo = new Date(today);
    sevenDaysAgo.setDate(today.getDate() - 6);

    // Format the dates (optional)
    this.date_to = today.toISOString().split("T")[0];
    this.date_from = sevenDaysAgo.toISOString().split("T")[0];
    this.getBranches();
    this.getDataFromApi();
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

    filterDays(date) {
      if (this.dayFilter == "") return true;
      if (this.dayFilter == "holidays") {
        return this.holidaysList[date] ? true : false;
      }
      if (this.dayFilter == "weekends") {
        return this.weekendsList[this.$dateFormat.getDayFullName(date)] == 1
          ? true
          : false;
      }
    },
    getBranches() {
      if (this.$auth.user.branch_id) {
        this.payload.branch_id = this.$auth.user.branch_id;
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
    getRowTotal(count) {
      this.rowTotal = this.rowTotal + count;
    },
    filterAttr(data) {
      this.date_from = data.from;
      this.date_to = data.to;

      //this.getDataFromApi();
    },
    getColor(value) {
      //return "#FFF";
      //   const colorRanges = [
      //     { min: 1, max: 5, color: "red" },
      //     { min: 6, max: 20, color: "green" },
      //   ];

      for (let i = 0; i < this.colorCodes.length; i++) {
        if (
          value >= this.colorCodes[i].min &&
          value <= this.colorCodes[i].max
        ) {
          return "color:#FFF;background-color:" + this.colorCodes[i].color;
        }
      }

      return "#FFF";
    },
    getDataFromApi() {
      let options = {
        params: {
          company_id: this.$auth.user.company_id,
          from_date: this.date_from,
          to_date: this.date_to,
          branch_id: this.branch_id,
          day_filter: this.dayFilter,
        },
      };

      this.$axios.get("dashboard-get-weekly-data", options).then(({ data }) => {
        this.data = data.data;
        this.colorCodes = data.colorCodes;
        this.hours = data.hours;
        this.dates = data.dates;

        this.holidaysList = data.holidaysList;
        this.weekendsList = data.weekendsList;

        this.hourTotals = [];

        this.data.forEach((entry) => {
          const hour = entry.hour;
          const total = entry.value.reduce((acc, val) => acc + val, 0);
          this.hourTotals[hour] = total;
        });

        this.dateTotals = new Array(this.data.length).fill(0);

        this.data.forEach((obj) => {
          obj.value.forEach((val, index) => {
            this.dateTotals[index] += val;
          });
        });
      });

      this.getDataFromApi2();
    },
    getDataFromApi2() {
      let options = {
        params: {
          company_id: this.$auth.user.company_id,
          noPagination: true,
          from_date: this.date_from,
          to_date: this.date_to,
          branch_id: this.branch_id,
        },
      };

      this.$axios.get("customer-stats-report", options).then(({ data }) => {
        const countsByDate = {};
        let counter = 0;
        let datesArray = this.getDatesArray(this.date_from, this.date_to);

        datesArray.forEach((item) => {
          let filterItem = data.filter((e) => {
            return e.date === item;
          });

          //  if (this.filterDays(item))
          {
            if (filterItem.length > 0) {
              const { date, male_count, female_count, child_count } =
                filterItem[0];
              this.chartOptions1.series[0]["data"][counter] =
                parseInt(male_count);

              this.chartOptions1.series[1]["data"][counter] =
                parseInt(female_count);
              this.chartOptions1.series[2]["data"][counter] =
                parseInt(child_count);
            } else {
              this.chartOptions1.series[0]["data"][counter] = parseInt(0);

              this.chartOptions1.series[1]["data"][counter] = parseInt(0);
              this.chartOptions1.series[2]["data"][counter] = parseInt(0);
            }
            let name = this.$dateFormat.format_date66(item);
            // if (this.holidaysList[item]) {
            //   name = name + " - " + this.holidaysList[item];
            // }
            this.chartOptions1.xaxis.categories[counter] = name;
            counter++;
          }
        });
        if (this.ApexCharts1) {
          //this.ApexCharts1.render();
          this.ApexCharts1.updateOptions(this.chartOptions1);

          // setTimeout(() => {
          //   this.ApexCharts1.render();
          // }, 2000);
        }
      });
    },
    getDatesArray(startDate, endDate) {
      startDate = new Date(startDate);
      endDate = new Date(endDate);

      const datesArray = [];
      let currentDate = new Date(startDate);

      while (currentDate <= endDate) {
        datesArray.push(new Date(currentDate).toISOString().split("T")[0]);
        currentDate.setDate(currentDate.getDate() + 1);
      }

      return datesArray;
    },
    // renderChart1(data) {
    //   console.log("data", data);
    //   let counter = 0;

    //   let Series;
    //   data.forEach((item) => {
    //     this.chartOptions1.series[0]["data"][counter] = parseInt(
    //       item.maleCount
    //     );

    //     this.chartOptions1.series[1]["data"][counter] = parseInt(
    //       item.femaleCount
    //     );
    //     this.chartOptions1.series[2]["data"][counter] = parseInt(
    //       item.kidsCount
    //     );

    //     this.chartOptions1.xaxis.categories[counter] = item.hour;
    //     counter++;
    //   });

    //   this.ApexCharts1.updateOptions(this.chartOptions1);

    //   this.loading = false;
    // },
  },
};
</script>
