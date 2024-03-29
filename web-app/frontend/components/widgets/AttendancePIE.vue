<template>
  <v-card class="mb-5 rounded-md" elevation="1">
    <v-toolbar class="rounded-md" color="background" dense flat dark>
      <v-toolbar-title
        ><span> {{ Model }} </span></v-toolbar-title
      >
      <!-- <v-tooltip top color="primary">
        <template v-slot:activator="{ on, attrs }">
          <v-btn
            dense
            class="ma-0 px-0"
            x-small
            :ripple="false"
            text
            v-bind="attrs"
            v-on="on"
          >
            <v-icon color="white" class="ml-2" @click="getDataFromApi()" dark
              >mdi mdi-reload</v-icon
            >
          </v-btn>
        </template>
        <span>Reload</span>
      </v-tooltip> -->
      <v-spacer></v-spacer>
      <v-tooltip top color="primary">
        <template v-slot:activator="{ on, attrs }">
          <v-btn
            to="/attendance_report"
            dense
            class="ma-0 px-0"
            x-small
            :ripple="false"
            text
            v-bind="attrs"
            v-on="on"
          >
            <v-icon color="white" class="ml-2" dark>mdi mdi-eye-outline</v-icon>
          </v-btn>
        </template>
        <span>Reports</span>
      </v-tooltip>
    </v-toolbar>
    <div class="center-both" style="min-height: 300px">
      <ComonPreloader icon="pie-chart" v-if="loading" />
      <div v-else-if="!data.length">No record found</div>
      <div v-else id="AttendancePie"></div>
    </div>
  </v-card>
</template>
<script>
export default {
  props: ["page"],
  data: () => ({
    Model: "Attendance",
    data: [],
    chartOptions: {
      title: {
        align: "center",
        margin: 0,
      },
      colors: [],

      series: [],
      chart: {
        toolbar: {
          show: false,
        },
        width: 350, //200 //275
        type: "pie",
      },
      labels: [],
      // plotOptions: {
      //   pie: {
      //     startAngle: -90,
      //     endAngle: 270,
      //   },
      // },
      dataLabels: {
        enabled: true,
        style: {
          fontSize: "10px",
        },
      },
      legend: {
        show: true,
        fontSize: "10px",
      },
      responsive: [
        {
          breakpoint: 480,
          options: {
            chart: {
              toolbar: {
                show: false,
              },
              width: 250, //200 //275
            },
            legend: {
              position: "bottom",
            },
          },
        },
      ],
    },
    loading: true,
  }),
  created() {
    this.getDataFromApi();
  },
  methods: {
    getDataFromApi() {
      let options = {
        params: {
          page: this.page || "dashboard2",
          type: "card",
          company_id: this.$auth.company_id,
        },
      };

      this.$axios
        .get("theme", options)
        .then(async ({ data }) => {
          this.loading = false;
          this.data = data;
          this.chartOptions.colors = await data.map((e) => e.color);
          this.chartOptions.labels = await data.map((e) => e.title);
          this.chartOptions.series = await data.map((e) =>
            parseInt(e.calculated_value)
          );
          new ApexCharts(
            document.querySelector("#AttendancePie"),
            this.chartOptions
          ).render();
        })
        .catch((e) => console.log(e));
    },
  },
};
</script>
<style scoped>
.center-both {
  height: 31vh;
  /* Adjust the height as needed */
  display: flex;
  align-items: center;
  justify-content: center;
}
</style>
