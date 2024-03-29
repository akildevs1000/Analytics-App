<template>
  <div style="width: 100%; height: 500px">
    <v-row>
      <v-col md="5">
        <h4>{{ display_title }}</h4></v-col
      >

      <v-col md="4">
        <CustomFilter
          style="float: right; padding-top: 5px"
          @filter-attr="filterAttr"
          :default_date_from="date_from"
          :default_date_to="date_to"
          :defaultFilterType="1"
          :height="'40px'"
      /></v-col>

      <v-col md="3">
        <v-autocomplete
          dense
          outlined
          label="Door"
          @change="filterDevice"
          v-model="filterDeviceId"
          :items="[{ name: 'All Doors', device_id: null }, ...devices]"
          item-value="device_id"
          item-text="name"
        >
        </v-autocomplete>
      </v-col>
    </v-row>
    <ComonPreloader icon="face-scan" v-if="loading" />
    <div :id="name" style="width: 100%" :key="display_title"></div>
  </div>
</template>

<script>
// import VueApexCharts from 'vue-apexcharts'
export default {
  props: ["name", "height", "branch_id"],
  data() {
    return {
      filterDeviceId: null,
      devices: [],
      loading: false,
      display_title: "Recent 7 days Attendance",
      date_from: "",
      date_to: "",

      series: [
        {
          name: "Tenants",
          data: [],
        },
        {
          name: "Visitors",
          data: [],
        },
        {
          name: "Employees",
          data: [],
        },
        {
          name: "Denied",
          data: [],
        },
      ],
      chartOptions1: {
        series: [
          {
            name: "Tenants",
            data: [],
          },
          {
            name: "Visitors",
            data: [],
          },
          {
            name: "Employees",
            data: [],
          },
          {
            name: "Denied",
            data: [],
          },
        ],
        colors: ["#14B012", "#FF0000", "#FFB600"],
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
            name: "Tenants",
            data: [],
          },
          {
            name: "Visitors",
            data: [],
          },
          {
            name: "Employees",
            data: [],
          },
          {
            name: "Denied",
            data: [],
          },
        ],
        colors: ["#14B012", "#FFB600", "#2196F3", "#FF0000"],
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
    this.chartOptions1.chart.height = 310;
    this.chartOptions1.series = this.series;
    // new ApexCharts(
    //   document.querySelector("#" + this.name),
    //   this.chartOptions
    // ).render();
  },
  async created() {
    // Get today's date
    let today = new Date();

    // Subtract 7 days from today
    let sevenDaysAgo = new Date(today);
    sevenDaysAgo.setDate(today.getDate() - 0);

    // Format the dates (optional)
    this.date_to = today.toISOString().split("T")[0];
    this.date_from = sevenDaysAgo.toISOString().split("T")[0];
    // this.display_title =
    //   "Attendance : " + this.date_from + " to " + this.date_to;

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

      this.$store.dispatch("CommDashboard/setDates", {
        date_from: this.date_from,
        date_to: this.date_to,
        branch_id: this.branch_id,
        system_user_id: null,
        device_id: this.filterDeviceId,
      });
      if (this.date_from == this.date_to) {
        this.renderChart1(
          await this.$store.dispatch("CommDashboard/every_hour_count")
        );
      } else
        this.renderChart2(
          await this.$store.dispatch("CommDashboard/states_for_7_days")
        );
    },

    renderChart1(data) {
      let counter = 0;
      data.forEach((item) => {
        this.chartOptions1.series[0]["data"][counter] = parseInt(
          item.TenantCount
        );

        this.chartOptions1.series[1]["data"][counter] = parseInt(
          item.VisitorCount
        );
        this.chartOptions1.series[2]["data"][counter] = parseInt(
          item.EmployeeCount
        );
        this.chartOptions1.series[3]["data"][counter] = parseInt(
          item.DeniedCount
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
            name: "Tenants",
            data: [],
          },
          {
            name: "Visitors",
            data: [],
          },
          {
            name: "Employees",
            data: [],
          },
          {
            name: "Denied",
            data: [],
          },
        ];

        this.chartOptions2.xaxis = {
          categories: [],
        };
        data.forEach((item) => {
          this.chartOptions2.series[0]["data"][counter] = parseInt(
            item.TenantCount
          );

          this.chartOptions2.series[1]["data"][counter] = parseInt(
            item.VisitorCount
          );
          this.chartOptions2.series[2]["data"][counter] = parseInt(
            item.EmployeeCount
          );
          this.chartOptions2.series[3]["data"][counter] = parseInt(
            item.DeniedCount
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
