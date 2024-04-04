<template>
  <div style="width: 101%; min-width: 500px">
    <div class="text-center" style="width: 100%; margin: auto">
      <v-row class="text-center">
        <v-col cols="1"> </v-col>
        <v-col cols="1">
          <div>IN</div>
          <div>10</div>
        </v-col>
        <v-col cols="1">
          <div>Out</div>
          <div>10</div>
        </v-col>
        <v-col cols="1">
          <div>Present</div>
          <div>10</div>
        </v-col>
        <v-col cols="1">
          <div>Absent</div>
          <div>10</div>
        </v-col>
        <v-col cols="1">
          <div>Holiday</div>
          <div>10</div>
        </v-col>
        <v-col cols="1">
          <div>Leave</div>
          <div>10</div>
        </v-col>
        <v-col cols="1">
          <div>Vacation</div>
          <div>10</div>
        </v-col>
        <v-col cols="4">
          <div>Manager On Duty</div>
          <div style="font-size: 30px; color: #203864">Mr. Mohamed Ajbar</div>
          <div style="font-size: 10px">055-222 5641</div>
        </v-col>
      </v-row>
    </div>
  </div>
</template>

<script>
// import DashboardlastMultiStatistics from "../../components/dashboard2/DashboardlastMultiStatistics.vue";
export default {
  components: {},
  data() {
    return {
      device_serial_number: "",
      from_date: "",
      from_date: "",
      from_menu: false,
      topMenu: 0,
      devicesList: [],
    };
  },
  watch: {},
  mounted() {},
  async created() {
    this.branchList = await this.$store.dispatch("fetchDropDowns", {
      key: "branchList",
      endpoint: "branch-list",
      refresh: true,
    });

    const today = new Date();

    this.from_date = today.toISOString().slice(0, 10);
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
    ChangeDevice(serial_number) {
      try {
        this.device_serial_number = serial_number;
        // this.key++;
        // this.keyChart2++;

        //this.getDataFromApi(1);
        //console.log(this.device_serial_number, " this.device_serial_number");
      } catch (e) {}
    },
  },
};
</script>
