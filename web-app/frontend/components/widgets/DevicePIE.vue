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
            to="/device"
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
      <div v-else-if="!loading && !dataLength">No record found</div>
      <div v-else id="DeviceStatusPieId"></div>
    </div>
  </v-card>
</template>
<script>
export default {
  data: () => ({
    Model: "Device Status",
    dataLength: 0,
    data: [],
    chartOptions: {
      title: {
        align: "center",
        margin: 0,
      },
      colors: ["#23bdb8", "#f48665"],

      series: [],
      chart: {
        toolbar: {
          show: false,
        },
        width: 300, //200 //275
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
  mounted() {
    this.getDataFromApi();
  },
  methods: {
    getDataFromApi() {
      this.loading = true;
      this.$axios
        .get(`devcieCountByStatus/${this.$auth.user.company_id}`)
        .then(async ({ data }) => {
          this.loading = false;
          this.dataLength = await data.total;
          this.chartOptions.labels = await data.labels;
          this.chartOptions.series = await data.series;
          try {
            await new ApexCharts(
              document.querySelector("#DeviceStatusPieId"),
              this.chartOptions
            ).render();
          } catch (error) {}
        });
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
