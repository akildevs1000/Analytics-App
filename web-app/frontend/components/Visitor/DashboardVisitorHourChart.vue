<template>
  <div style="width: 100%">
    <v-row>
      <v-col md="10"><h4 class="pl-2">Today Device Logs (in/out)</h4></v-col>
      <v-col md="2" class="menu-icon-right">
        <v-menu bottom left>
          <template v-slot:activator="{ on, attrs }">
            <v-btn dark-2 icon v-bind="attrs" v-on="on">
              <v-icon>mdi-dots-vertical</v-icon>
            </v-btn>
          </template>
          <v-list width="120" dense>
            <v-list-item @click="viewLogs()">
              <v-list-item-title style="cursor: pointer">
                View Logs
              </v-list-item-title>
            </v-list-item>
          </v-list>
        </v-menu>
      </v-col>
    </v-row>
    <div :id="name" style="width: 100%"></div>
  </div>
</template>

<script>
// import VueApexCharts from 'vue-apexcharts'
export default {
  props: ["name", "height", "branch_id"],
  data() {
    return {
      series: [
        {
          name: "Device Logs",
          data: [],
        },
      ],
      chartOptions: {
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
    };
  },
  watch: {
    branch_id() {
      try {
        this.$store.commit("dashboard/every_hour_count", null);
        this.getDataFromApi();
      } catch (e) {}
    },
  },

  created() {
    this.getDataFromApi();
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
    viewLogs() {
      this.$router.push("/visitor_logs");
    },
    async getDataFromApi() {
      const data = await this.$store.dispatch(
        "dashboard/visitor_every_hour_count"
      );
      this.renderChart(data);
    },
    renderChart(data) {
      let counter = 0;
      data.forEach((item) => {
        this.chartOptions.series[0]["data"][counter] = parseInt(item.count);
        this.chartOptions.xaxis.categories[counter] = item.hour;
        counter++;
      });
      try {
        new ApexCharts(
          document.querySelector("#" + this.name),
          this.chartOptions
        ).render();
      } catch (error) {}
    },
  },
};
</script>

<style>
.apexcharts-canvas {
  width: 100%;
}
</style>
