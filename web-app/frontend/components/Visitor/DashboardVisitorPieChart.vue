<template>
  <div style="padding: 0px; width: 100%">
    <div id="visitors" name="visitors" height="200" width="200"></div>
    <div
      v-if="totalCount == 0"
      style="
        padding: 0px;
        margin: auto;
        text-align: center;
        vertical-align: middle;
        height: 300px;
        padding-top: 36%;
      "
    >
      No Data available
    </div>
  </div>
</template>

<script>
export default {
  props: ["items"],
  data() {
    return {
      //   items: [
      //     { title: "Title1", value: 20 },
      //     { title: "Title2", value: 30 },
      //     { title: "Title3", value: 40 },
      //     { title: "Title4", value: 50 },
      //   ],
      totalCount: 0,
      options: {
        noData: {
          text: "There's no data",
          align: "center",
          verticalAlign: "middle",
          offsetX: 0,
          offsetY: 0,
        },
        title: {
          text: "Visitors",
          align: "left",
          margin: 0,
        },
        //colors: ["#033F9B", "#DC7633", "#02B64B", "#ff0000", "#808080", ""],
        colors: ["#033F9B", "#02B64B", "#ff0000", "#808080", ""],

        series: [],
        chart: {
          toolbar: {
            show: false,
          },
          width: "100%", //200 //275
          type: "donut",
          height: 350,
        },
        customTotalValue: 0,
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
                  fontFamily: "Helvetica, Arial, sans-serif",
                  color: undefined,
                  offsetY: 16,
                  formatter: function (val) {
                    return val;
                  },
                },
                total: {
                  show: true,
                  label: "Total Expecting",
                  color: "#373d3f",
                  formatter: function (val) {
                    return val.config.customTotalValue;
                  },
                },
              },
            },
          },
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
              },
              legend: {
                position: "bottom",
              },
            },
          },
        ],
      },
    };
  },
  mounted() {
    let stats = this.items.visitorCounts.filter((e) => e.title != "Expected");
    let statsTotal = 0;
    try {
      stats.forEach((element) => {
        statsTotal += element.value;
      });
      this.totalCount = this.items.visitorCounts.filter(
        (e) => e.title == "Expected"
      )[0].value;
      this.options.customTotalValue = this.totalCount;

      this.options.labels = stats.map((e) => e.title);
      this.options.series = stats.map((e) => e.value);

      this.options.labels.push("waiting");
      this.options.series.push(this.totalCount - statsTotal);

      //this.options.plotOptions.pie.donut.total = this.totalCount;
      // this.options.customTotalValue = this.items.statusCounts.filter(
      //   (e) => (e.title = "Total Visitor")
      // )[0].value;
      // this.options.customTotalValue = this.items.visitorCounts.filter(
      //   (e) => (e.title = "Expected")
      // )[0].value;

      new ApexCharts(
        document.querySelector("#visitors"),
        this.options
      ).render();
    } catch (error) {}
  },
  methods: {},
  created() {
    // try {
    //   this.items.forEach((element) => {
    //     this.totalCount += element.value;
    //   });
    //   this.options.labels = this.items.map((e) => e.title);
    //   this.options.series = this.items.map((e) => e.value);
    //   new ApexCharts(document.querySelector("#pie2"), this.options).render();
    // } catch (error) {}
  },
};
</script>

<style scoped>
/* .apexcharts-legend-series {
    margin: 0px 100px 2px 0px !important;
  } */
/* #pie .apexcharts-legend-series {
    margin: 0px 50px 2px 0px !important;
  } */

/* foreignObject {
    max-width: 280px !important;
  } */
#pie .apexcharts-legend-series {
  margin: 0px 50px 2px 0px !important;
}
</style>
