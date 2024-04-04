<template>
  <div>
    <v-row style="width: 100%; height: 100%">
      <v-col cols="12">
        <v-row style="text-align: right" justify="end">
          <v-col cols="2">
            <v-select
              v-model="filterType"
              :items="[
                {
                  id: '',
                  name: 'All',
                },
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

          <v-col cols="2">
            <v-select
              v-model="filterType"
              :items="[
                {
                  id: '',
                  name: 'All',
                },
                {
                  id: 5,
                  name: 5,
                },
                {
                  id: 10,
                  name: '>10',
                },
                {
                  id: 30,
                  name: '>30',
                },
                {
                  id: 60,
                  name: '>60',
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

        <div
          :id="name"
          style="width: 100%; height: 400px"
          :key="display_title"
        ></div>
      </v-col>
    </v-row>
  </div>
</template>

<script>
// import VueApexCharts from 'vue-apexcharts'
export default {
  props: ["height", "branch_id", "date_from", "date_to"],
  data() {
    return {
      name: "apexDashboardHour",
      filterDeviceId: null,
      devices: [],
      loading: false,
      display_title: "Alarm Events",
      filterType: "",

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
        colors: ["#fe0000", "#14B012"],
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
        colors: ["#fe0000", "#14B012"],
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
    };
  },
  watch: {
    async display_title() {
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
    // new ApexCharts(
    //   document.querySelector("#" + this.name),
    //   this.chartOptions
    // ).render();
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

    await this.getDataFromApi();
    this.getDeviceList();
  },

  methods: {
    getDeviceList() {
      let options = {
        params: {
          company_id: this.$auth.user.company_id,
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

      let options = {
        params: {
          per_page: 1000,
          company_id: this.$auth.user.company_id,

          date_from: this.date_from,
          date_to: this.date_to,
        },
      };
      if (this.date_from == this.date_to) {
        this.$axios
          .get(`/alarm_dashboard_get_hourly_data`, options)
          .then(({ data }) => {
            this.renderChart1(data.houry_data);
          });
      } else
        this.$axios
          .get(`/alarm_dashboard_get_monthly_data`, options)
          .then(({ data }) => {
            this.renderChart2(data);
          });

      let data = [
        {
          date: "2023-12-01",
          count: 0,
          batteryCount: 0,
        },
        {
          date: "2023-12-02",
          count: 0,
          batteryCount: 0,
        },
        {
          date: "2023-12-03",
          count: 0,
          batteryCount: 0,
        },
        {
          date: "2023-12-04",
          count: 0,
          batteryCount: 0,
        },
        {
          date: "2023-12-05",
          count: 0,
          batteryCount: 0,
        },
        {
          date: "2023-12-06",
          count: 0,
          batteryCount: 0,
        },
        {
          date: "2023-12-07",
          count: 0,
          batteryCount: 0,
        },
        {
          date: "2024-05-30",
          count: 0,
          batteryCount: 0,
        },
      ];

      this.renderChart2(data);
    },

    renderChart1(data) {
      let counter = 0;
      data.forEach((item) => {
        this.chartOptions1.series[0]["data"][counter] = parseInt(item.count);

        this.chartOptions1.series[1]["data"][counter] = parseInt(
          item.batteryCount
        );

        this.chartOptions1.xaxis.categories[counter] = item.hour;
        counter++;
      });
      try {
        new ApexCharts(
          document.querySelector("#" + this.name),
          this.chartOptions1
        ).render();
        this.loading = false;
      } catch (error) {}
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
