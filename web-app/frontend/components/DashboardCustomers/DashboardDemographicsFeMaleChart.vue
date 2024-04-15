<template>
  <div>
    <div :id="name" style="width: 100%" :key="key"></div>
  </div>
</template>

<script>
// import VueApexCharts from 'vue-apexcharts'
export default {
  props: ["height", "data"],
  data() {
    return {
      key: 1,
      name: "apexDashboardFeMaleChart",
      filterDeviceId: null,
      devices: [],
      loading: false,
      display_title: "Alarm Events",
      filterType: "",
      filterINOut: "in",
      filterDuration: null,

      chartOptions1: {
        series: [],
        colors: ["#DB4437", "#0F9D58", "#4285F4"],
        labels: ["Young", "Adult", "Senior"],
        // labels: {
        //   show: true,
        // },
        toolbar: {
          show: false,
        },

        chart: {
          type: "donut",
          width: "100%",
        },
        dataLabels: {
          enabled: true,
        },
        legend: {
          show: false,
        },

        dataLabels: {
          enabled: true,
        },

        plotOptions: {
          pie: {
            donut: {
              labels: {
                show: true,
                name: {
                  show: true,
                  fontSize: "22px",
                  fontFamily: "Rubik",
                  color: "#dfsda",
                  offsetY: -10,
                },
                value: {
                  show: true,
                  fontSize: "16px",
                  fontFamily: "Roboto, sans-serif",
                  color: undefined,
                  offsetY: 16,
                  formatter: function (val) {
                    return val;
                  },
                },
                total: {
                  show: true,
                  label: "Total",
                  color: "#373d3f",
                  fontFamily: "Roboto, sans-serif",
                  formatter: function (w) {
                    return w.globals.seriesTotals.reduce((a, b) => {
                      return a + b;
                    }, 0);
                  },
                },
              },
            },
          },
        },
      },

      ApexCharts1: {},
    };
  },
  watch: {
    data() {
      try {
        this.chartOptions1.series = [
          parseInt(this.data.female_younger_count),
          parseInt(this.data.female_adult_count),
          parseInt(this.data.female_senior_count),
        ];
        this.ApexCharts1.updateOptions(this.chartOptions1);
      } catch (e) {}
    },
    async branch_id(val) {
      this.$store.commit("CommDashboard/setDashboardData", null);
      //this.$store.commit("setDashboardData", null);
      await this.getDataFromApi();
    },
  },
  mounted() {
    setTimeout(() => {
      if (this.data) {
        this.chartOptions1.series = [
          parseInt(this.data.female_younger_count),
          parseInt(this.data.female_adult_count),
          parseInt(this.data.female_senior_count),
        ];
      }

      this.ApexCharts1 = new ApexCharts(
        document.querySelector("#" + this.name),
        this.chartOptions1
      ); //.render();
      this.ApexCharts1.render();
    }, 1000);

    // setInterval(() => {
    //   if (this.$route.name == "statistics") {
    //     this.getDataFromApi();
    //   }
    // }, 1000 * 60);
  },
  async created() {
    // // Get today's date
    // let today = new Date();
    // // Subtract 7 days from today
    // let sevenDaysAgo = new Date(today);
    // sevenDaysAgo.setDate(today.getDate() - 0);
    // // Format the dates (optional)
    // this.date_to = today.toISOString().split("T")[0];
    // this.date_from = sevenDaysAgo.toISOString().split("T")[0];
    // // this.display_title =
    // //   "Attendance : " + this.date_from + " to " + this.date_to;
    // const today = new Date();
    // this.date_from = today.toISOString().slice(0, 10);
    // this.date_to = today.toISOString().slice(0, 10);
    // setTimeout(() => {
    //   if (this.$route.name == "statistics") {
    //     this.getDataFromApi();
    //   }
    // }, 1000 * 3);
    // this.getDeviceList();
  },

  methods: {
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
      this.$store.commit("CommDashboard/setDashboardData", null);
      this.$store.commit("CommDashboard/every_hour_count", null);

      this.getDataFromApi();
    },
    filterAttr(data) {
      this.date_from = data.from;
      this.date_to = data.to;

      this.filterType = "Monthly"; // data.type;
      if (this.date_from != this.date_to)
        this.display_title =
          "Access  : " + this.date_from + " to " + this.date_to;
      else this.display_title = "Access  : " + this.date_from;

      this.$store.commit("CommDashboard/setDashboardData", null);
      this.$store.commit("CommDashboard/every_hour_count", null);
      this.getDataFromApi();
    },
    async getDataFromApi() {
      this.loading = true;

      if (this.filterDuration) {
        this.filterINOut = "in";
      }

      let options = {
        params: {
          per_page: 1000,
          company_id: this.$auth.user.company_id,
          branch_id: this.branch_id,
          filter_duration_min: this.filterDuration
            ? this.filterDuration.split("-")[0]
            : null,
          filter_duration_max: this.filterDuration
            ? this.filterDuration.split("-")[1]
            : null,
          //date_from: this.date_from,
          //date_to: this.date_to,
          DeviceID: this.filter_device_serial_number,
          filter_from_date: this.filter_from_date,
        },
      };
      if (this.date_from == this.date_to) {
        let InData = [];
        let OutData = [];
        await this.$axios
          .get(`/dashboard-get-hourly-out-data`, options)
          .then(({ data }) => {
            InData = data.houry_data;
            //this.renderChart1(data.houry_data);
          });

        await this.$axios
          .get(`/dashboard-get-hourly-in-data`, options)
          .then(({ data }) => {
            OutData = data.houry_data;
          });

        this.renderChartGroupChart(InData, OutData);
      }
      //}
      // if (this.date_from == this.date_to) {
      //   if (this.filterINOut == "out") {
      //     this.$axios
      //       .get(`/dashboard-get-hourly-out-data`, options)
      //       .then(({ data }) => {
      //         this.renderChart1(data.houry_data);
      //       });
      //   } else if (this.filterINOut == "in") {
      //     this.$axios
      //       .get(`/dashboard-get-hourly-in-data`, options)
      //       .then(({ data }) => {
      //         this.renderChart1(data.houry_data);
      //       });
      //   }
      // } else {
      //   this.$axios
      //     .get(`/dashboard-get-hourly-in-data`, options)
      //     .then(({ data }) => {
      //       this.renderChart2(data);
      //     });
      // }
    },
    renderChartGroupChart(InData, OutData) {
      let counter = 0;

      let Series;
      InData.forEach((item) => {
        //male
        this.chartOptions1.series[0]["data"][counter] = parseInt(
          item.maleCount
        );

        this.chartOptions1.series[1]["data"][counter] = parseInt(
          OutData[counter].maleCount
        );

        //female
        this.chartOptions1.series[2]["data"][counter] = parseInt(
          item.femaleCount
        );

        this.chartOptions1.series[3]["data"][counter] = parseInt(
          OutData[counter].femaleCount
        );

        //female
        this.chartOptions1.series[4]["data"][counter] = parseInt(
          item.kidsCount
        );

        this.chartOptions1.series[5]["data"][counter] = parseInt(
          OutData[counter].kidsCount
        );

        this.chartOptions1.xaxis.categories[counter] = item.hour;
        counter++;
      });

      this.ApexCharts1.updateOptions(this.chartOptions1);

      this.loading = false;
    },
    //   renderChart1(data) {
    //     let counter = 0;

    //     let Series;
    //     data.forEach((item) => {
    //       this.chartOptions1.series[0]["data"][counter] = parseInt(
    //         item.maleCount
    //       );

    //       this.chartOptions1.series[1]["data"][counter] = parseInt(
    //         item.femaleCount
    //       );
    //       this.chartOptions1.series[2]["data"][counter] = parseInt(
    //         item.kidsCount
    //       );

    //       this.chartOptions1.xaxis.categories[counter] = item.hour;
    //       counter++;
    //     });

    //     this.ApexCharts1.updateOptions(this.chartOptions1);

    //     this.loading = false;
    //   },
    //   renderChart2(data) {
    //     try {
    //       this.chartOptions2.chart.height = this.height;
    //       this.chartOptions2.series = this.series;

    //       let counter = 0;

    //       this.chartOptions2.series = [
    //         {
    //           name: "Male",
    //           data: [],
    //         },

    //         {
    //           name: "Female",
    //           data: [],
    //         },
    //         {
    //           name: "Kids",
    //           data: [],
    //         },
    //       ];

    //       this.chartOptions2.xaxis = {
    //         categories: [],
    //       };
    //       data.forEach((item) => {
    //         this.chartOptions2.series[0]["data"][counter] = parseInt(item.count);

    //         this.chartOptions2.series[1]["data"][counter] = parseInt(
    //           item.batteryCount
    //         );

    //         this.chartOptions2.xaxis.categories[counter] =
    //           this.$dateFormat.format2(item.date);

    //         counter++;
    //       });
    //       this.loading = false;

    //       new ApexCharts(
    //         document.querySelector("#" + this.name),
    //         this.chartOptions2
    //       ).render();
    //     } catch (error) {}
    //   },
  },
};
</script>
