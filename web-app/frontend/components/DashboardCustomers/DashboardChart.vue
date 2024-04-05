<template>
  <div>
    <v-row style="width: 100%; height: 100%">
      <v-col cols="12">
        <v-row style="text-align: right" justify="end">
          <v-col cols="2">
            <v-select
              @change="getDataFromApi()"
              v-model="filterINOut"
              :items="[
                {
                  id: 'in',
                  name: 'IN',
                },
                {
                  id: 'out',
                  name: 'Out',
                },
              ]"
              label="In/Out"
              dense
              placeholder="In/Out"
              outlined
              :hide-details="true"
              item-text="name"
              item-value="id"
            ></v-select>
          </v-col>

          <v-col cols="3">
            <v-select
              @change="getDataFromApi()"
              v-model="filterDuration"
              :items="[
                {
                  id: null,
                  name: 'All',
                },
                {
                  id: '0-5',
                  name: 5,
                },
                {
                  id: '5-10',
                  name: '5 to 10 ',
                },
                {
                  id: '10-30',
                  name: '10 to 30 ',
                },
                {
                  id: '30-60',
                  name: '30 to 60 ',
                },
                {
                  id: '60-1000',
                  name: 'Above 60 ',
                },
              ]"
              label="Time Spent   Minutes"
              dense
              placeholder="Time Spent   Minutes"
              outlined
              :hide-details="true"
              item-text="name"
              item-value="id"
            ></v-select>
          </v-col>
        </v-row>

        <div :id="name" style="width: 100%; height: 400px" :key="key"></div>
      </v-col>
    </v-row>
  </div>
</template>

<script>
// import VueApexCharts from 'vue-apexcharts'
export default {
  props: [
    "height",
    "branch_id",
    "date_from",
    "date_to",
    "filter_device_serial_number",
    "filter_from_date",
  ],
  data() {
    return {
      key: 1,
      name: "apexDashboardHour",
      filterDeviceId: null,
      devices: [],
      loading: false,
      display_title: "Alarm Events",
      filterType: "",
      filterINOut: "in",
      filterDuration: null,

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
          name: "Kids",
          data: [],
        },
      ],
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
            name: "Kids",
            data: [],
          },
        ],
        colors: ["#01b0f0", "#f75b95", "#16b16d"],
        chart: {
          toolbar: {
            show: false,
          },
          type: "bar",
          width: "98%",
        },
        plotOptions: {
          bar: {
            horizontal: false,
            columnWidth: "25%",
            endingShape: "rounded",
          },
        },
        dataLabels: {
          enabled: false,
        },
        stroke: {
          show: true,
          width: 2,
          colors: ["transparent"],
        },
        xaxis: {
          categories: [],
        },
        yaxis: {
          title: {
            text: " ",
          },
        },
        fill: {
          opacity: 1,
        },
        tooltip: {
          y: {
            formatter: function (val) {
              return val;
            },
          },
        },
      },
      chartOptions2: {
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
            name: "Kids",
            data: [],
          },
        ],
        colors: ["#01b0f0", "#f75b95", "#16b16d"],
        chart: {
          type: "bar",
          width: "98%",
          toolbar: {
            show: false,
          },
        },
        plotOptions: {
          bar: {
            horizontal: false,
            columnWidth: "25%",
            endingShape: "rounded",
          },
        },
        dataLabels: {
          enabled: false,
        },
        stroke: {
          show: true,
          width: 2,
          colors: ["transparent"],
        },
        xaxis: {
          categories: [],
        },
        yaxis: {
          title: {
            text: " ",
          },
        },
        fill: {
          opacity: 1,
        },
        tooltip: {
          y: {
            formatter: function (val) {
              return val;
            },
          },
        },
      },

      ApexCharts1: {},
    };
  },
  watch: {
    async filter_from_date() {
      await this.getDataFromApi();
    },
    async branch_id(val) {
      this.$store.commit("CommDashboard/setDashboardData", null);
      //this.$store.commit("setDashboardData", null);
      await this.getDataFromApi();
    },
  },
  mounted() {
    this.chartOptions1.chart.height = this.height;
    this.chartOptions1.series = this.series;
    this.ApexCharts1 = new ApexCharts(
      document.querySelector("#" + this.name),
      this.chartOptions1
    ); //.render();
    this.ApexCharts1.render();

    setInterval(() => {
      this.getDataFromApi();
    }, 1000 * 60 * 15);
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

    setTimeout(() => {
      this.getDataFromApi();
    }, 1000 * 3);
    this.getDeviceList();
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
      console.log(this.date_from, this.date_to);

      this.loading = true;

      if (this.filterDuration) {
        this.filterINOut = "in";
      }

      let options = {
        params: {
          per_page: 1000,
          company_id: this.$auth.user.company_id,
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
        if (this.filterINOut == "out") {
          this.$axios
            .get(`/dashboard-get-hourly-out-data`, options)
            .then(({ data }) => {
              this.renderChart1(data.houry_data);
            });
        } else if (this.filterINOut == "in") {
          this.$axios
            .get(`/dashboard-get-hourly-in-data`, options)
            .then(({ data }) => {
              this.renderChart1(data.houry_data);
            });
        }
      } else {
        this.$axios
          .get(`/dashboard-get-hourly-in-data`, options)
          .then(({ data }) => {
            this.renderChart2(data);
          });
      }
    },

    renderChart1(data) {
      console.log("data", data);
      let counter = 0;

      let Series;
      data.forEach((item) => {
        this.chartOptions1.series[0]["data"][counter] = parseInt(
          item.maleCount
        );

        this.chartOptions1.series[1]["data"][counter] = parseInt(
          item.femaleCount
        );
        this.chartOptions1.series[2]["data"][counter] = parseInt(
          item.kidsCount
        );

        this.chartOptions1.xaxis.categories[counter] = item.hour;
        counter++;
      });

      this.ApexCharts1.updateOptions(this.chartOptions1);

      this.loading = false;
    },
    renderChart2(data) {
      try {
        this.chartOptions2.chart.height = this.height;
        this.chartOptions2.series = this.series;

        let counter = 0;

        this.chartOptions2.series = [
          {
            name: "Male",
            data: [],
          },

          {
            name: "Female",
            data: [],
          },
          {
            name: "Kids",
            data: [],
          },
        ];

        this.chartOptions2.xaxis = {
          categories: [],
        };
        data.forEach((item) => {
          this.chartOptions2.series[0]["data"][counter] = parseInt(item.count);

          this.chartOptions2.series[1]["data"][counter] = parseInt(
            item.batteryCount
          );

          this.chartOptions2.xaxis.categories[counter] =
            this.$dateFormat.format2(item.date);

          counter++;
        });
        this.loading = false;

        new ApexCharts(
          document.querySelector("#" + this.name),
          this.chartOptions2
        ).render();
      } catch (error) {}
    },
  },
};
</script>
