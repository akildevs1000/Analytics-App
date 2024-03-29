<template>
  <div style="width: 100%">
    <v-row>
      <v-col md="6">
        <h4>{{ display_title }}</h4></v-col
      >

      <v-col md="6">
        <CustomFilter
          style="float: right"
          @filter-attr="filterAttr"
          :default_date_from="date_from"
          :default_date_to="date_to"
          :defaultFilterType="1"
          :height="'35px '"
      /></v-col>
    </v-row>
    <ComonPreloader icon="face-scan" v-if="loading" />
    <div :id="name" style="width: 100%" :key="display_title"></div>
  </div>
</template>

<script>
// import VueApexCharts from 'vue-apexcharts'
export default {
  props: ["name", "height"],
  data() {
    return {
      loading: false,
      display_title: "Recent 7 days Attendance",
      date_from: "",
      date_to: "",
      series: [
        {
          name: "Present",
          data: [],
        },
        {
          name: "Absent",
          data: [],
        },
        {
          name: "Missing",
          data: [],
        },
        {
          name: "WeekOff",
          data: [],
        },
      ],

      chartOptions: {
        colors: ["#14B012", "#FF0000", "#FFB600", "#2196F3"],
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
    };
  },
  watch: {
    display_title() {
      this.getDataFromApi();
    },
  },
  mounted() {},
  created() {
    // Get today's date
    let today = new Date();

    // Subtract 7 days from today
    let sevenDaysAgo = new Date(today);
    sevenDaysAgo.setDate(today.getDate() - 6);

    // Format the dates (optional)
    this.date_to = today.toISOString().split("T")[0];
    this.date_from = sevenDaysAgo.toISOString().split("T")[0];
    // this.display_title =
    //   "Attendance : " + this.date_from + " to " + this.date_to;
    setTimeout(() => {
      this.getDataFromApi();
    }, 1000 * 2);
  },
  mounted() {
    this.chartOptions.chart.height = this.height;
    this.chartOptions.series = this.series;
    // new ApexCharts(
    //   document.querySelector("#" + this.name),
    //   this.chartOptions
    // ).render();
  },

  methods: {
    filterAttr(data) {
      this.date_from = data.from;
      this.date_to = data.to;
      this.filterType = "Monthly"; // data.type;

      this.display_title =
        "Attendance : " + this.date_from + " to " + this.date_to;
    },
    getDataFromApi() {
      this.loading = true;
      let options = {
        params: {
          company_id: this.$auth.user.company_id,
          date_from: this.date_from,
          date_to: this.date_to,
        },
      };

      this.$axios
        .get("dashboard_counts_last_7_days", options)
        .then(({ data }) => {
          let counter = 0;

          this.chartOptions.series = [
            {
              name: "Present",
              data: [],
            },
            {
              name: "Absent",
              data: [],
            },
            {
              name: "Missing",
              data: [],
            },
            {
              name: "WeekOff",
              data: [],
            },
          ];

          this.chartOptions.xaxis = {
            categories: [],
          };
          data.forEach((item) => {
            this.chartOptions.series[0]["data"][counter] = parseInt(
              item.presentCount
            );
            this.chartOptions.series[1]["data"][counter] = parseInt(
              item.absentCount
            );
            this.chartOptions.series[2]["data"][counter] = parseInt(
              item.missingCount
            );
            this.chartOptions.series[3]["data"][counter] = parseInt(
              item.offCount
            );
            this.chartOptions.xaxis.categories[counter] =
              this.$dateFormat.format2(item.date);

            counter++;
          });
          this.loading = false;
          try {
            new ApexCharts(
              document.querySelector("#" + this.name),
              this.chartOptions
            ).render();
          } catch (error) {}
        });
    },
  },
};
</script>

<style>
.apexcharts-canvas {
  width: 100%;
}
</style>
